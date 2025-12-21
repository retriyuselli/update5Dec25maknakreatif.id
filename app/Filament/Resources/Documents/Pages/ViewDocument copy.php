<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Filament\Resources\Documents\DocumentResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class ViewDocument extends ViewRecord
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
            Action::make('edit')
                ->label('Edit')
                ->url(fn ($record) => DocumentResource::getUrl('edit', ['record' => $record])),
        ];
    }
}
