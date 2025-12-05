<?php

namespace App\Filament\Resources\Statuses;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Statuses\Pages\ListStatuses;
use App\Filament\Resources\Statuses\Pages\CreateStatus;
use App\Filament\Resources\Statuses\Pages\EditStatus;
use App\Filament\Resources\StatusResource\Pages;
use App\Models\Status;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StatusResource extends Resource
{
    protected static ?string $model = Status::class;

    protected static string | \UnitEnum | null $navigationGroup = 'SDM';

    protected static ?string $navigationLabel = 'Status Karyawan';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-check-badge';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('status_name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('status_name')
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
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->striped()
            ->defaultPaginationPageOption(10)
            ->paginationPageOptions([10, 25, 50]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStatuses::route('/'),
            'create' => CreateStatus::route('/create'),
            'edit' => EditStatus::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
