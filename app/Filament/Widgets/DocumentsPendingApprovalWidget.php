<?php

namespace App\Filament\Widgets;

use App\Models\Document;
use App\Models\DocumentApproval;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class DocumentsPendingApprovalWidget extends BaseWidget
{
    use HasWidgetShield {
        canView as canViewShield;
    }
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $heading = 'Dokumen yang telah disetujui';

    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        // Cek permission dari Shield terlebih dahulu
        if (! static::canViewShield()) {
            return false;
        }

        // Cek apakah ada data yang perlu ditampilkan
        return Document::query()
            ->where(function (Builder $query) {
                $query->whereHas('approvals', function (Builder $q) {
                    $q->where('user_id', Auth::id())
                        ->where('status', 'pending');
                })
                ->orWhereHas('recipientsList', function (Builder $q) {
                    $q->where('users.id', Auth::id());
                });
            })
            ->exists();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Document::query()
                    ->where(function (Builder $query) {
                        $query->whereHas('approvals', function (Builder $q) {
                            $q->where('user_id', Auth::id())
                                ->where('status', 'pending');
                        })
                        ->orWhereHas('recipientsList', function (Builder $q) {
                            $q->where('users.id', Auth::id());
                        });
                    })
                    ->with(['category', 'creator'])
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('document_number')
                    ->label('Number'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Title'),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category'),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Created By'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime(),
            ])
            ->recordActions([
                Action::make('review')
                    ->label('Review')
                    ->visible(fn () => Auth::user()?->hasRole('super_admin'))
                    ->url(fn (Document $record): string => route('filament.admin.resources.documents.edit', ['record' => $record->id]))
                    ->icon('heroicon-m-eye'),
                Action::make('print')
                    ->label('Preview PDF')
                    ->icon('heroicon-m-printer')
                    ->url(fn (Document $record) => route('document.stream', $record))
                    ->openUrlInNewTab(),
            ]);
    }
}
