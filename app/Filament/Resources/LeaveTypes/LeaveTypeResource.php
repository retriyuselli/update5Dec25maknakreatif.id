<?php

namespace App\Filament\Resources\LeaveTypes;

use App\Filament\Resources\LeaveTypes\Pages\CreateLeaveType;
use App\Filament\Resources\LeaveTypes\Pages\EditLeaveType;
use App\Filament\Resources\LeaveTypes\Pages\ListLeaveTypes;
use App\Models\LeaveType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LeaveTypeResource extends Resource
{
    protected static ?string $model = LeaveType::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Jenis Cuti';

    protected static ?string $modelLabel = 'Jenis Cuti';

    protected static ?string $pluralModelLabel = 'Jenis Cuti';

    protected static string|\UnitEnum|null $navigationGroup = 'Manajemen Cuti';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Jenis Cuti')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Jenis Cuti')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Cuti Tahunan, Cuti Sakit, Cuti Melahirkan')
                            ->unique(LeaveType::class, 'name', ignoreRecord: true),
                        TextInput::make('max_days_per_year')
                            ->label('Maksimal Hari Per Tahun')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(365)
                            ->suffix('hari')
                            ->placeholder('12')
                            ->helperText('Jumlah maksimal hari cuti yang dapat diambil dalam satu tahun'),
                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->placeholder('Masukkan keterangan jenis cuti')
                            ->maxLength(500),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Jenis Cuti')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('max_days_per_year')
                    ->label('Maksimal Hari/Tahun')
                    ->numeric()
                    ->sortable()
                    ->suffix(' hari')
                    ->alignCenter(),
                TextColumn::make('approved_count')
                    ->label('Disetujui')
                    ->getStateUsing(function ($record) {
                        return $record->leaveRequests()->where('status', 'approved')->count();
                    })
                    ->badge()
                    ->color('success')
                    ->alignCenter()
                    ->sortable(false),
                TextColumn::make('pending_count')
                    ->label('Menunggu')
                    ->getStateUsing(function ($record) {
                        return $record->leaveRequests()->where('status', 'pending')->count();
                    })
                    ->badge()
                    ->color('warning')
                    ->alignCenter()
                    ->sortable(false),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->sortable()
                    ->color('info'),
                TextColumn::make('deleted_at')
                    ->label('Dihapus')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('Aktif')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->badge()
                    ->color(fn ($state) => $state ? 'danger' : 'success')
                    ->formatStateUsing(fn ($state) => $state ? 'Dihapus' : 'Aktif'),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make()
                    ->label('Filter Status')
                    ->placeholder('Semua Data')
                    ->trueLabel('Hanya yang Dihapus')
                    ->falseLabel('Tanpa yang Dihapus'),
                Filter::make('max_days_range')
                    ->label('Range Maksimal Hari')
                    ->schema([
                        TextInput::make('max_days_from')
                            ->label('Dari')
                            ->numeric()
                            ->suffix('hari'),
                        TextInput::make('max_days_to')
                            ->label('Sampai')
                            ->numeric()
                            ->suffix('hari'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['max_days_from'],
                                fn (Builder $query, $days): Builder => $query->where('max_days_per_year', '>=', $days),
                            )
                            ->when(
                                $data['max_days_to'],
                                fn (Builder $query, $days): Builder => $query->where('max_days_per_year', '<=', $days),
                            );
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                RestoreAction::make()
                    ->successNotificationTitle('Jenis cuti berhasil dipulihkan'),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Jenis Cuti')
                    ->modalDescription('Apakah Anda yakin ingin menghapus jenis cuti ini? Data akan dipindahkan ke trash dan dapat dipulihkan.')
                    ->modalSubmitActionLabel('Ya, Hapus')
                    ->successNotificationTitle('Jenis cuti berhasil dihapus'),
                ForceDeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Permanen Jenis Cuti')
                    ->modalDescription('Apakah Anda yakin ingin menghapus permanen jenis cuti ini? Data tidak dapat dipulihkan!')
                    ->modalSubmitActionLabel('Ya, Hapus Permanen')
                    ->successNotificationTitle('Jenis cuti berhasil dihapus permanen'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotificationTitle('Jenis cuti terpilih berhasil dipulihkan'),
                    DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Jenis Cuti Terpilih')
                        ->modalDescription('Apakah Anda yakin ingin menghapus jenis cuti yang dipilih? Data akan dipindahkan ke trash dan dapat dipulihkan.')
                        ->modalSubmitActionLabel('Ya, Hapus Semua')
                        ->successNotificationTitle('Jenis cuti terpilih berhasil dihapus'),
                    ForceDeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Permanen Jenis Cuti Terpilih')
                        ->modalDescription('Apakah Anda yakin ingin menghapus permanen jenis cuti yang dipilih? Data tidak dapat dipulihkan!')
                        ->modalSubmitActionLabel('Ya, Hapus Permanen Semua')
                        ->successNotificationTitle('Jenis cuti terpilih berhasil dihapus permanen'),
                ]),
            ])
            ->defaultSort('name', 'asc');
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
            'index' => ListLeaveTypes::route('/'),
            'create' => CreateLeaveType::route('/create'),
            'edit' => EditLeaveType::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Total jenis cuti tersedia';
    }
}
