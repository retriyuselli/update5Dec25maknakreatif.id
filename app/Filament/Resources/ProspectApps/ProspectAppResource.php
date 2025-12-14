<?php

namespace App\Filament\Resources\ProspectApps;

use App\Filament\Resources\ProspectApps\Pages\CreateProspectApp;
use App\Filament\Resources\ProspectApps\Pages\EditProspectApp;
use App\Filament\Resources\ProspectApps\Pages\ListProspectApps;
use App\Filament\Resources\ProspectApps\Pages\ViewProspectApp;
use App\Models\ProspectApp;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use App\Filament\Resources\ProspectApps\Schemas\ProspectAppForm;
use App\Filament\Resources\ProspectApps\Tables\ProspectAppsTable;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProspectAppResource extends Resource
{
    protected static ?string $model = ProspectApp::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document';

    protected static ?string $navigationLabel = 'Aplikasi Prospek';

    protected static ?string $modelLabel = 'Aplikasi Prospek';

    protected static ?string $pluralModelLabel = 'Aplikasi Prospek';

    protected static string|\UnitEnum|null $navigationGroup = 'Penjualan';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return ProspectAppForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProspectAppsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
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
            'index' => ListProspectApps::route('/'),
            'create' => CreateProspectApp::route('/create'),
            'view' => ViewProspectApp::route('/{record}'),
            'edit' => EditProspectApp::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Total aplikasi prospek';
    }
}
