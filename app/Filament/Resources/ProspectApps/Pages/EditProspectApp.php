<?php

namespace App\Filament\Resources\ProspectApps\Pages;

use App\Filament\Resources\ProspectApps\ProspectAppResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditProspectApp extends EditRecord
{
    protected static string $resource = ProspectAppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateProposal')
                ->label('Generate Proposal')
                ->icon('heroicon-o-document-text')
                ->color('success')
                ->url(fn () => route('prospect-app.proposal.pdf', $this->record))
                ->openUrlInNewTab(),
            DeleteAction::make(),
        ];
    }
}
