<?php

namespace App\Filament\Resources\Employees;

use App\Filament\Resources\Employees\Pages\CreateEmployee;
use App\Filament\Resources\Employees\Pages\EditEmployee;
use App\Filament\Resources\Employees\Pages\ListEmployees;
use App\Filament\Resources\Employees\RelationManagers\OrdersRelationManager;
use App\Filament\Resources\Employees\Widgets\EmployeeOverviewWidget;
use App\Models\Employee;
use Carbon\Carbon;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user';

    protected static string|\UnitEnum|null $navigationGroup = 'SDM';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Karyawan';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Informasi Karyawan')
                    ->tabs([
                        Tab::make('Informasi Dasar')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Section::make('Detail Personal')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('name')
                                                    ->required()
                                                    ->placeholder('Nama lengkap (depan dan belakang)')
                                                    ->maxLength(255)
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(function ($state, Set $set) {
                                                        $set('slug', Str::slug($state));
                                                    }),

                                                TextInput::make('slug')
                                                    ->disabled()
                                                    ->dehydrated()
                                                    ->maxLength(255),

                                                DatePicker::make('date_of_birth')
                                                    ->label('Tanggal Lahir')
                                                    ->required()
                                                    ->maxDate(now()->subYears(18))
                                                    ->displayFormat('d M Y'),

                                                FileUpload::make('photo')
                                                    ->label('Foto Profil')
                                                    ->image()
                                                    ->openable()
                                                    ->downloadable()
                                                    ->directory('employee-photos')
                                                    ->imageCropAspectRatio('1:1')
                                                    ->imageResizeMode('cover'),
                                            ]),
                                    ]),

                                Section::make('Informasi Kontak')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('email')
                                                    ->email()
                                                    ->required()
                                                    ->unique(ignoreRecord: true)
                                                    ->maxLength(255),

                                                TextInput::make('phone')
                                                    ->tel()
                                                    ->required()
                                                    ->maxLength(20)
                                                    ->prefix('+62')
                                                    ->telRegex('/^[0-9]{9,15}$/')
                                                    ->placeholder('8xxxxxxxxx'),

                                                TextInput::make('instagram')
                                                    ->prefix('@')
                                                    ->maxLength(255),

                                                Textarea::make('address')
                                                    ->required()
                                                    ->rows(2)
                                                    ->columnSpanFull(),
                                            ]),
                                    ]),
                            ]),

                        Tab::make('Detail Kepegawaian')
                            ->icon('heroicon-o-briefcase')
                            ->schema([
                                Section::make('Posisi & Peran')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Select::make('position')
                                                    ->required()
                                                    ->options([
                                                        'Account Manager' => 'Account Manager',
                                                        'Event Manager' => 'Event Manager',
                                                        'Crew' => 'Crew',
                                                        'Finance' => 'Finance',
                                                        'Founder' => 'Founder',
                                                        'Co Founder' => 'Co Founder',
                                                        'Direktur' => 'Direktur',
                                                        'Wakil Direktur' => 'Wakil Direktur',
                                                        'Other' => 'Other',
                                                    ])
                                                    ->searchable(),

                                                Select::make('user_id')
                                                    ->relationship('user', 'name')
                                                    ->label('Akun Pengguna Terkait')
                                                    ->preload()
                                                    ->searchable()
                                                    ->createOptionForm([
                                                        TextInput::make('name')
                                                            ->required(),
                                                        TextInput::make('email')
                                                            ->required()
                                                            ->email(),
                                                        TextInput::make('password')
                                                            ->password()
                                                            ->required()
                                                            ->confirmed(),
                                                        TextInput::make('password_confirmation')
                                                            ->password()
                                                            ->required(),
                                                    ]),

                                                DatePicker::make('date_of_join')
                                                    ->label('Tanggal Bergabung')
                                                    ->required()
                                                    ->displayFormat('d M Y')
                                                    ->default(now()),

                                                DatePicker::make('date_of_out')
                                                    ->label('Tanggal Berhenti')
                                                    ->displayFormat('d M Y')
                                                    ->minDate(fn (Get $get) => $get('date_of_join')),
                                            ]),
                                    ]),

                                Section::make('Kompensasi & Perbankan')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('salary')
                                                    ->numeric()
                                                    ->required()
                                                    ->prefix('Rp')
                                                    ->mask(RawJs::make('$money($input)'))
                                                    ->stripCharacters(','),

                                                TextInput::make('bank_name')
                                                    ->required()
                                                    ->maxLength(255),

                                                TextInput::make('no_rek')
                                                    ->label('Nomor Rekening')
                                                    ->required()
                                                    ->numeric()
                                                    ->minLength(10)
                                                    ->maxLength(20),
                                            ]),
                                    ]),
                            ]),

                        Tab::make('Dokumen & Catatan')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Grid::make(1)
                                    ->schema([
                                        FileUpload::make('kontrak')
                                            ->label('Kontrak Kerja')
                                            ->directory('employee-contracts')
                                            ->acceptedFileTypes(['application/pdf'])
                                            ->openable()
                                            ->downloadable(),

                                        Textarea::make('note')
                                            ->label('Additional Notes')
                                            ->placeholder('Add any special considerations or notes about this employee')
                                            ->rows(3),

                                        // Ubah semua bagian yang menggunakan record secara langsung saat create
                                        TextInput::make('created_at_display')
                                            ->label('Dibuat')
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->afterStateHydrated(function ($component, $state, ?Employee $record): void {
                                                $component->state($record?->created_at?->diffForHumans());
                                            })
                                            ->hidden(fn (?Employee $record) => $record === null),

                                        TextInput::make('updated_at_display')
                                            ->label('Diperbarui')
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->afterStateHydrated(function ($component, $state, ?Employee $record): void {
                                                $component->state($record?->updated_at?->diffForHumans());
                                            })
                                            ->hidden(fn (?Employee $record) => $record === null),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Basic column untuk foto profil
                ImageColumn::make('photo')
                    ->label('Photo')
                    ->circular()
                    ->defaultImageUrl(fn (Employee $record) => $record->name ? 'https://ui-avatars.com/api/?name='.urlencode($record->name).'&color=FFFFFF&background=6366F1' : null),

                // Column nama yang sederhana tanpa manipulasi data kompleks
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->description(fn (Employee $record): string => $record->position ?? ''),

                // // Kolom email dan telepon sebagai kolom terpisah
                // Tables\Columns\TextColumn::make('email')
                //     ->label('Email')
                //     ->searchable()
                //     ->sortable()
                //     ->description(fn (Employee $record): string =>
                //         $record->phone ?? '')
                //     ->icon('heroicon-m-envelope')
                //     ->wrap(),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->prefix('+62')
                    ->description(fn (Employee $record): string => $record->email ?? '')
                    ->searchable(),

                // Date columns yang lebih aman
                TextColumn::make('date_of_join')
                    ->label('Join Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('date_of_out')
                    ->label('End Date')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('Active'),

                // Status sebagai boolean sederhana
                IconColumn::make('active_status')
                    ->label('Status')
                    ->boolean()
                    ->getStateUsing(function ($record) {
                        // Employee aktif jika sudah join dan belum out atau tanggal out di masa depan
                        if (empty($record->date_of_join)) {
                            return false;
                        }

                        $joinDate = $record->date_of_join instanceof Carbon
                            ? $record->date_of_join
                            : Carbon::parse($record->date_of_join);

                        // Jika belum join
                        if ($joinDate->isFuture()) {
                            return false;
                        }

                        // Jika tidak ada tanggal keluar
                        if (empty($record->date_of_out)) {
                            return true;
                        }

                        $outDate = $record->date_of_out instanceof Carbon
                            ? $record->date_of_out
                            : Carbon::parse($record->date_of_out);

                        // Aktif jika tanggal keluar masih di masa depan
                        return $outDate->isFuture();
                    })
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                // Finansial
                TextColumn::make('salary')
                    ->label('Salary')
                    ->money('IDR')
                    ->sortable(),

                // Data bank sebagai kolom terpisah
                TextColumn::make('bank_name')
                    ->label('Bank')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('no_rek')
                    ->label('Account Number')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Timestamps
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filter posisi
                SelectFilter::make('position')
                    ->options([
                        'Account Manager' => 'Account Manager',
                        'Event Manager' => 'Event Manager',
                        'Crew' => 'Crew',
                        'Finance' => 'Finance',
                        'Founder' => 'Founder',
                        'Co Founder' => 'Co Founder',
                        'Direktur' => 'Direktur',
                        'Wakil Direktur' => 'Wakil Direktur',
                        'Other' => 'Other',
                    ])
                    ->multiple(),

                // Filter status aktif/nonaktif
                TernaryFilter::make('active')
                    ->label('Employment Status')
                    ->placeholder('All Employees')
                    ->trueLabel('Active Employees')
                    ->falseLabel('Former Employees')
                    ->queries(
                        true: fn (Builder $query) => $query->where(function ($query) {
                            $query->where('date_of_join', '<=', now())
                                ->where(function ($query) {
                                    $query->whereNull('date_of_out')
                                        ->orWhere('date_of_out', '>=', now());
                                });
                        }),
                        false: fn (Builder $query) => $query->where('date_of_out', '<', now()),
                        blank: fn (Builder $query) => $query
                    ),
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
            ->defaultSort('date_of_join', 'desc')
            ->striped()
            ->defaultPaginationPageOption(10)
            ->paginationPageOptions([10, 25, 50]);
    }

    public static function getRelations(): array
    {
        return [
            OrdersRelationManager::class,
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::query()
            ->where('date_of_join', '<=', now())
            ->where(function (Builder $query) {
                $query->whereNull('date_of_out')
                    ->orWhere('date_of_out', '>=', now());
            })
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmployees::route('/'),
            'create' => CreateEmployee::route('/create'),
            'edit' => EditEmployee::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getWidgets(): array
    {
        return [
            EmployeeOverviewWidget::class,
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email', 'phone', 'position'];
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Karyawan aktif';
    }
}
