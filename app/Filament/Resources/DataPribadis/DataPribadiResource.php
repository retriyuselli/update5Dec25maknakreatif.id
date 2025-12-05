<?php

namespace App\Filament\Resources\DataPribadis;

use App\Filament\Resources\DataPribadis\Pages\CreateDataPribadi;
use App\Filament\Resources\DataPribadis\Pages\EditDataPribadi;
use App\Filament\Resources\DataPribadis\Pages\ListDataPribadis;
use App\Models\DataPribadi;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DataPribadiResource extends Resource
{
    protected static ?string $model = DataPribadi::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-identification';

    protected static string|\UnitEnum|null $navigationGroup = 'SDM';

    protected static ?string $navigationLabel = 'Data Tim';

    protected static ?string $recordTitleAttribute = 'nama_lengkap'; // Atribut untuk judul record

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Personal')
                    ->columns(2)
                    ->schema([
                        TextInput::make('nama_lengkap')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Masukkan nama lengkap'),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->placeholder('contoh@domain.com')
                            ->unique(ignoreRecord: true),
                        TextInput::make('nomor_telepon')
                            ->tel()
                            ->prefix('+62')
                            ->placeholder('81234567890')
                            ->telRegex('/^[0-9]{9,15}$/')
                            ->maxLength(20),
                        DatePicker::make('tanggal_lahir')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        Select::make('jenis_kelamin')
                            ->options([
                                'Laki-laki' => 'Laki-laki',
                                'Perempuan' => 'Perempuan',
                            ])
                            ->placeholder('Pilih jenis kelamin'),
                        FileUpload::make('foto')
                            ->image()
                            ->imageEditor()
                            ->maxSize(1024) // 1MB
                            ->columnSpanFull()
                            ->helperText('Unggah foto profil (maks. 1MB).'),
                        Textarea::make('alamat')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Masukkan alamat lengkap'),
                    ]),
                Section::make('Informasi Pekerjaan')
                    ->columns(2)
                    ->schema([
                        TextInput::make('pekerjaan')
                            ->maxLength(255)
                            ->placeholder('Masukkan pekerjaan saat ini'),
                        TextInput::make('gaji')
                            ->numeric()
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->placeholder('0'),
                        Textarea::make('motivasi_kerja')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Jelaskan motivasi kerja Anda'),
                        RichEditor::make('pelatihan')
                            ->columnSpanFull()
                            ->placeholder('Pelatihan yang pernah diikuti di Makna'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => $record->nama_lengkap ? 'https://ui-avatars.com/api/?name='.urlencode($record->nama_lengkap).'&color=FFFFFF&background=0D83DD' : null),
                TextColumn::make('nama_lengkap')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->icon('heroicon-s-envelope'),
                TextColumn::make('nomor_telepon')
                    ->searchable()
                    ->prefix('+62')
                    ->icon('heroicon-s-phone'),
                TextColumn::make('tanggal_lahir')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('jenis_kelamin')
                    ->badge()
                    ->colors([
                        'success' => 'Laki-laki',
                        'warning' => 'Perempuan',
                    ])
                    ->searchable(),
                TextColumn::make('pekerjaan')
                    ->searchable(),
                TextColumn::make('gaji')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
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
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->defaultPaginationPageOption(10)
            ->paginationPageOptions([10, 25, 50])
            ->emptyStateIcon('heroicon-o-user-group')
            ->emptyStateHeading('Tidak ada data pribadi ditemukan')
            ->emptyStateDescription('Silakan buat data pribadi baru untuk memulai.')
            ->emptyStateActions([
                Action::make('create')
                    ->label('Buat Data Pribadi Baru')
                    ->url(static::getUrl('create'))
                    ->icon('heroicon-o-plus')
                    ->button(),
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
            'index' => ListDataPribadis::route('/'),
            'create' => CreateDataPribadi::route('/create'),
            'edit' => EditDataPribadi::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'primary';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Data crew freelance';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['nama_lengkap', 'email', 'nomor_telepon', 'pekerjaan'];
    }
}
