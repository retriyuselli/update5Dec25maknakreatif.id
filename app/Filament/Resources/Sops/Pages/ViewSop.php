<?php

namespace App\Filament\Resources\Sops\Pages;

use App\Filament\Resources\Sops\SopResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewSop extends ViewRecord
{
    protected static string $resource = SopResource::class;

    protected string $view = 'filament.resources.sop-resource.pages.view-sop';

    protected function getHeaderActions(): array
    {
        $actions = [
            EditAction::make()
                ->label('Edit SOP')
                ->icon('heroicon-o-pencil'),
        ];

        // Tampilkan action duplicate hanya untuk super_admin
        if (Auth::user() && Auth::user()->hasRole('super_admin')) {
            $actions[] = Action::make('duplicate')
                ->label('Duplikat SOP')
                ->icon('heroicon-o-document-duplicate')
                ->action(function () {
                    $newSop = $this->record->replicate();
                    $newSop->title = $this->record->title.' (Copy)';
                    $newSop->version = '1.0';
                    $newSop->created_by = Auth::id();
                    $newSop->updated_by = Auth::id();
                    $newSop->save();
                    $this->redirect(static::$resource::getUrl('edit', ['record' => $newSop]));
                })
                ->requiresConfirmation()
                ->modalHeading('Duplikat SOP')
                ->modalDescription('Apakah Anda yakin ingin menduplikat SOP ini? SOP baru akan dibuat dengan versi 1.0.')
                ->modalSubmitActionLabel('Ya, Duplikat');
        }

        $actions[] = Action::make('print')
            ->label('Print SOP')
            ->icon('heroicon-o-printer')
            ->url(fn () => route('sop.print', $this->record->id))
            ->openUrlInNewTab();

        $actions[] = DeleteAction::make()
            ->label('Hapus SOP')
            ->icon('heroicon-o-trash');

        return $actions;
    }
}
