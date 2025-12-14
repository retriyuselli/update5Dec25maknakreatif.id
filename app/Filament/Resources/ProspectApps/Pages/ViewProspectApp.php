<?php

namespace App\Filament\Resources\ProspectApps\Pages;

use App\Filament\Resources\ProspectApps\ProspectAppResource;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewProspectApp extends ViewRecord
{
    protected static string $resource = ProspectAppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Edit Application')
                ->icon('heroicon-o-pencil'),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Contact Information')
                    ->schema([
                        TextEntry::make('full_name')
                            ->label('Full Name')
                            ->weight('bold')
                            ->size('lg'),

                        TextEntry::make('email')
                            ->label('Email Address')
                            ->copyable()
                            ->icon('heroicon-o-envelope'),

                        TextEntry::make('phone')
                            ->label('Phone Number')
                            ->copyable()
                            ->icon('heroicon-o-phone'),

                        TextEntry::make('position')
                            ->label('Job Position')
                            ->placeholder('Not specified'),
                    ])
                    ->columns(2),

                Section::make('Company Information')
                    ->schema([
                        TextEntry::make('company_name')
                            ->label('Company Name')
                            ->weight('bold'),

                        TextEntry::make('industry.industry_name')
                            ->label('Industry')
                            ->badge()
                            ->color('info'),

                        TextEntry::make('name_of_website')
                            ->label('Website')
                            ->url(fn ($state) => $state)
                            ->openUrlInNewTab()
                            ->placeholder('Not provided'),

                        TextEntry::make('user_size')
                            ->label('Company Size')
                            ->badge()
                            ->color('gray'),
                    ])
                    ->columns(2),

                Section::make('Application Details')
                    ->schema([
                        TextEntry::make('reason_for_interest')
                            ->label('Reason for Interest')
                            ->placeholder('No reason provided'),

                        TextEntry::make('status')
                            ->label('Application Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                default => 'gray'
                            })
                            ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                        TextEntry::make('submitted_at')
                            ->label('Submission Date')
                            ->dateTime('F j, Y \a\t g:i A'),
                    ])
                    ->columns(2),

                Section::make('Timeline')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime('F j, Y \a\t g:i A'),

                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime('F j, Y \a\t g:i A'),

                        TextEntry::make('deleted_at')
                            ->label('Deleted At')
                            ->dateTime('F j, Y \a\t g:i A')
                            ->placeholder('Not deleted'),
                    ])
                    ->columns(3)
                    ->collapsible(),
            ]);
    }

    public function getTitle(): string
    {
        return 'View Application: '.$this->record->full_name;
    }
}
