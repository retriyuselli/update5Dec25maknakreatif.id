<?php

namespace App\Filament\Resources\JournalBatches\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\JournalBatches\JournalBatchResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJournalBatch extends EditRecord
{
    protected static string $resource = JournalBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
