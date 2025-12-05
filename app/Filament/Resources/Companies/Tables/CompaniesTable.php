<?php

namespace App\Filament\Resources\Companies\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CompaniesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company_name')
                    ->searchable(),
                TextColumn::make('business_license')
                    ->searchable(),
                TextColumn::make('owner_name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('city')
                    ->searchable(),
                TextColumn::make('province')
                    ->searchable(),
                TextColumn::make('postal_code')
                    ->searchable(),
                TextColumn::make('website')
                    ->searchable(),
                TextColumn::make('logo_url')
                    ->searchable(),
                TextColumn::make('favicon_url')
                    ->searchable(),
                TextColumn::make('established_year'),
                TextColumn::make('employee_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('legal_entity_type')
                    ->searchable(),
                TextColumn::make('deed_of_establishment')
                    ->searchable(),
                TextColumn::make('deed_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('notary_name')
                    ->searchable(),
                TextColumn::make('notary_license_number')
                    ->searchable(),
                TextColumn::make('nib_number')
                    ->searchable(),
                TextColumn::make('nib_issued_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('nib_valid_until')
                    ->date()
                    ->sortable(),
                TextColumn::make('npwp_number')
                    ->searchable(),
                TextColumn::make('npwp_issued_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('tax_office')
                    ->searchable(),
                TextColumn::make('legal_document_status')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
