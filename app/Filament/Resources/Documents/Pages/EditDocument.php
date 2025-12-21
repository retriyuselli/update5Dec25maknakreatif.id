<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Filament\Resources\Documents\DocumentResource;
use App\Models\DocumentApproval;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EditDocument extends EditRecord
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label('Download PDF')
                ->icon('heroicon-o-printer')
                ->action(function ($record) {
                    $filename = 'document-' . Str::slug($record->document_number) . '.pdf';
                    return response()->streamDownload(function () use ($record) {
                        echo Pdf::loadView('documents.pdf', ['record' => $record])->output();
                    }, $filename);
                }),
            Action::make('submit')
                ->label('Submit for Approval')
                ->color('warning')
                ->icon('heroicon-o-paper-airplane')
                ->visible(fn ($record) => $record->status === 'draft')
                ->requiresConfirmation()
                ->action(function ($record) {
                    $record->update(['status' => 'pending']);
                    
                    // Create Approval Entry for current user (Demo: Self-Approval)
                    // In real app, look up Department Manager or defined workflow
                    DocumentApproval::create([
                        'document_id' => $record->id,
                        'user_id' => Auth::id(), 
                        'step_order' => 1,
                        'status' => 'pending',
                    ]);
                    
                    Notification::make()
                        ->title('Submitted successfully')
                        ->success()
                        ->send();
                }),

            Action::make('approve')
                ->label('Approve')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->visible(fn ($record) => $record->status === 'pending' && $this->canApprove($record))
                ->requiresConfirmation()
                ->action(function ($record) {
                    $approval = DocumentApproval::where('document_id', $record->id)
                        ->where('user_id', Auth::id())
                        ->where('status', 'pending')
                        ->first();
                        
                    if ($approval) {
                        $approval->update([
                            'status' => 'approved',
                            'signed_at' => now(),
                        ]);
                    }
                    
                    // Check if all approvals are done
                    $pendingCount = DocumentApproval::where('document_id', $record->id)
                        ->where('status', 'pending')
                        ->count();
                        
                    if ($pendingCount === 0) {
                        $record->update(['status' => 'approved']);
                        Notification::make()
                            ->title('Document Approved')
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Approval Recorded')
                            ->success()
                            ->send();
                    }
                }),

            Action::make('reject')
                ->label('Reject')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->visible(fn ($record) => $record->status === 'pending' && $this->canApprove($record))
                ->form([
                    Textarea::make('note')
                        ->label('Rejection Note')
                        ->required(),
                ])
                ->action(function ($record, array $data) {
                     $approval = DocumentApproval::where('document_id', $record->id)
                        ->where('user_id', Auth::id())
                        ->where('status', 'pending')
                        ->first();
                        
                    if ($approval) {
                        $approval->update([
                            'status' => 'rejected',
                            'note' => $data['note'],
                        ]);
                    }
                    
                    $record->update(['status' => 'rejected']);
                    
                    Notification::make()
                        ->title('Document Rejected')
                        ->danger()
                        ->send();
                }),

            Action::make('publish')
                ->label('Publish')
                ->color('info')
                ->icon('heroicon-o-globe-alt')
                ->visible(fn ($record) => $record->status === 'approved')
                ->requiresConfirmation()
                ->action(function ($record) {
                    $record->update(['status' => 'published']);
                    Notification::make()
                        ->title('Document Published')
                        ->success()
                        ->send();
                }),

            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    public function canApprove($record): bool
    {
        return DocumentApproval::where('document_id', $record->id)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->exists();
    }
}
