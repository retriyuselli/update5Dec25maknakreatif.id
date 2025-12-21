<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Filament\Resources\Documents\DocumentResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ViewDocument extends ViewRecord
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label('Preview PDF')
                ->icon('heroicon-o-printer')
                ->url(fn ($record) => route('document.stream', $record))
                ->openUrlInNewTab(),
            Action::make('edit')
                ->label('Edit')
                ->visible(fn () => auth()->user()?->hasRole('super_admin'))
                ->url(fn ($record) => DocumentResource::getUrl('edit', ['record' => $record])),
        ];
    }
}
