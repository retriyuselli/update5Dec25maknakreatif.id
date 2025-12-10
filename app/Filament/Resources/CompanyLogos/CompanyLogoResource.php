<?php

namespace App\Filament\Resources\CompanyLogos;

use App\Filament\Resources\CompanyLogos\Pages\CreateCompanyLogo;
use App\Filament\Resources\CompanyLogos\Pages\EditCompanyLogo;
use App\Filament\Resources\CompanyLogos\Pages\ListCompanyLogos;
use App\Models\CompanyLogo;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CompanyLogoResource extends Resource
{
    protected static ?string $model = CompanyLogo::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationLabel = 'Logo Perusahaan';

    protected static string|\UnitEnum|null $navigationGroup = 'Administrasi';

    protected static ?string $pluralModelLabel = 'Logo Perusahaan';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Perusahaan')
                    ->schema([
                        TextInput::make('company_name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nama Perusahaan'),
                        TextInput::make('website_url')
                            ->url()
                            ->maxLength(255)
                            ->label('URL Situs')
                            ->placeholder('https://example.com'),
                        FileUpload::make('logo_path')
                            ->label('Logo Perusahaan')
                            ->image()
                            ->directory('company-logos')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/svg+xml', 'image/webp'])
                            ->maxSize(2048)
                            ->hint('Ukuran disarankan: 200x100px, Maks: 2MB'),
                        TextInput::make('alt_text')
                            ->maxLength(255)
                            ->label('Teks Alt')
                            ->hint('Teks alternatif untuk logo'),
                    ])->columns(1),

                Section::make('Pengaturan Tampilan')
                    ->schema([
                        Select::make('category')
                            ->required()
                            ->options([
                                'client' => 'Klien',
                                'partner' => 'Mitra',
                                'vendor' => 'Vendor',
                                'sponsor' => 'Sponsor',
                            ])
                            ->default('client'),
                        Select::make('partnership_type')
                            ->required()
                            ->options([
                                'free' => 'Gratis',
                                'premium' => 'Premium',
                                'enterprise' => 'Enterprise',
                            ])
                            ->default('free'),
                        TextInput::make('display_order')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->label('Urutan Tampilan')
                            ->hint('Angka lebih kecil tampil lebih dahulu'),
                        Toggle::make('is_active')
                            ->required()
                            ->default(true)
                            ->label('Aktif'),
                    ])->columns(1),

                Section::make('Informasi Tambahan')
                    ->schema([
                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                        TextInput::make('contact_email')
                            ->email()
                            ->maxLength(255)
                            ->label('Email Kontak'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo_path')
                    ->label('Logo')
                    ->size(60)
                    ->circular(),
                TextColumn::make('company_name')
                    ->searchable()
                    ->sortable()
                    ->label('Company Name'),
                TextColumn::make('category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'client' => 'primary',
                        'partner' => 'success',
                        'vendor' => 'warning',
                        'sponsor' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('partnership_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'free' => 'secondary',
                        'premium' => 'warning',
                        'enterprise' => 'success',
                        default => 'gray',
                    })
                    ->label('Type'),
                TextColumn::make('display_order')
                    ->numeric()
                    ->sortable()
                    ->label('Order'),
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                TextColumn::make('website_url')
                    ->searchable()
                    ->limit(30)
                    ->label('Website')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('contact_email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Contact'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options([
                        'client' => 'Client',
                        'partner' => 'Partner',
                        'vendor' => 'Vendor',
                        'sponsor' => 'Sponsor',
                    ]),
                SelectFilter::make('partnership_type')
                    ->options([
                        'free' => 'Free',
                        'premium' => 'Premium',
                        'enterprise' => 'Enterprise',
                    ]),
                TernaryFilter::make('is_active')
                    ->label('Active Status'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('display_order');
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
            'index' => ListCompanyLogos::route('/'),
            'create' => CreateCompanyLogo::route('/create'),
            'edit' => EditCompanyLogo::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
