<?php

namespace App\Filament\Resources\Sops;

use App\Filament\Resources\Sops\Pages\CreateSop;
use App\Filament\Resources\Sops\Pages\EditSop;
use App\Filament\Resources\Sops\Pages\ListSops;
use App\Filament\Resources\Sops\Pages\ViewSop;
use App\Models\Sop;
use App\Models\SopCategory;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class SopResource extends Resource
{
    protected static ?string $model = Sop::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'SOP';

    protected static ?string $modelLabel = 'SOP';

    protected static ?string $pluralModelLabel = 'SOP';

    protected static string|\UnitEnum|null $navigationGroup = 'Administrasi';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Umum')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('title')
                                    ->label('Judul SOP')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Masukkan judul SOP')
                                    ->columnSpanFull(),

                                Select::make('category_id')
                                    ->label('Kategori')
                                    ->options(SopCategory::active()->pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->preload(),

                                TextInput::make('version')
                                    ->label('Versi')
                                    ->default('1.0')
                                    ->required()
                                    ->maxLength(10),
                            ]),

                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->required()
                            ->rows(4)
                            ->placeholder('Masukkan deskripsi SOP'),

                        Textarea::make('keywords')
                            ->label('Kata Kunci')
                            ->placeholder('Masukkan kata kunci untuk pencarian (pisahkan dengan koma)')
                            ->helperText('Kata kunci akan membantu user menemukan SOP ini lebih mudah'),
                    ]),

                Section::make('Langkah-langkah')
                    ->schema([
                        Repeater::make('steps')
                            ->label('Langkah')
                            ->schema([
                                TextInput::make('step_number')
                                    ->label('No. Langkah')
                                    ->numeric()
                                    ->required()
                                    ->default(function ($livewire) {
                                        $steps = $livewire->data['steps'] ?? [];

                                        return count($steps) + 1;
                                    }),

                                TextInput::make('title')
                                    ->label('Judul Langkah')
                                    ->required()
                                    ->placeholder('Masukkan judul langkah'),

                                RichEditor::make('description')
                                    ->label('Deskripsi Langkah')
                                    ->required()
                                    ->placeholder('Jelaskan secara detail langkah ini'),

                                RichEditor::make('notes')
                                    ->label('Catatan')
                                    ->placeholder('Catatan tambahan untuk langkah ini (opsional)'),
                            ])
                            ->columns(1)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Langkah baru')
                            ->addActionLabel('Tambah Langkah')
                            ->defaultItems(1)
                            ->reorderable()
                            ->required(),
                    ]),

                Section::make('Dokumen Pendukung')
                    ->schema([
                        FileUpload::make('supporting_documents')
                            ->label('File Pendukung')
                            ->multiple()
                            ->acceptedFileTypes(['application/pdf', 'image/*', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->directory('sop-documents')
                            ->visibility('private')
                            ->downloadable()
                            ->openable()
                            ->previewable(false)
                            ->helperText('Upload dokumen pendukung seperti PDF, gambar, atau dokumen Word'),
                    ]),

                Section::make('Pengaturan')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                DatePicker::make('effective_date')
                                    ->label('Tanggal Berlaku')
                                    ->default(now())
                                    ->required(),

                                DatePicker::make('review_date')
                                    ->label('Tanggal Review')
                                    ->helperText('Tanggal untuk review SOP ini'),

                                Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true)
                                    ->helperText('SOP yang tidak aktif tidak akan ditampilkan ke user'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Select::make('created_by')
                                    ->label('Dibuat Oleh')
                                    ->relationship('creator', 'name')
                                    ->default(Auth::id())
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->disabled(fn (?Sop $record) => $record !== null)
                                    ->dehydrated()
                                    ->helperText('User yang membuat SOP ini'),

                                Select::make('updated_by')
                                    ->label('Diperbarui Oleh')
                                    ->relationship('updater', 'name')
                                    ->default(Auth::id())
                                    ->searchable()
                                    ->preload()
                                    ->disabled()
                                    ->dehydrated()
                                    ->helperText('User yang terakhir memperbarui SOP ini')
                                    ->visible(fn (?Sop $record) => $record !== null),
                            ]),
                    ]),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul SOP')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => $record->category?->color ?? 'gray'),

                TextColumn::make('version')
                    ->label('Versi')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                TextColumn::make('steps_count')
                    ->label('Langkah')
                    ->alignCenter()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('effective_date')
                    ->label('Tanggal Berlaku')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('review_date')
                    ->label('Review')
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn ($record) => $record->needsReview() ? 'danger' : 'success')
                    ->badge(fn ($record) => $record->needsReview()),

                TextColumn::make('creator.name')
                    ->label('Dibuat Oleh')
                    ->toggleable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->options(SopCategory::active()->pluck('name', 'id'))
                    ->multiple(),

                TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif'),

                Filter::make('needs_review')
                    ->label('Perlu Review')
                    ->query(fn (Builder $query): Builder => $query->whereDate('review_date', '<', now())),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Preview')
                    ->icon('heroicon-o-eye'),

                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil'),

                Action::make('duplicate')
                    ->label('Duplikat')
                    ->icon('heroicon-o-document-duplicate')
                    ->action(function (Sop $record) {
                        $newSop = $record->replicate();
                        $newSop->title = $record->title.' (Copy)';
                        $newSop->version = '1.0';
                        $newSop->created_by = Auth::id();
                        $newSop->updated_by = Auth::id();
                        $newSop->save();

                        return redirect()->route('filament.admin.resources.sops.edit', $newSop);
                    })
                    ->requiresConfirmation(),

                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateActions([
                CreateAction::make(),
            ]);
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
            'index' => ListSops::route('/'),
            'create' => CreateSop::route('/create'),
            'edit' => EditSop::route('/{record}/edit'),
            'view' => ViewSop::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Total SOP aktif/nonaktif';
    }
}
