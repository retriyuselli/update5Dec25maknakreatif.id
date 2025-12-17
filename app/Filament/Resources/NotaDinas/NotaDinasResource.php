<?php

namespace App\Filament\Resources\NotaDinas;

use App\Filament\Resources\NotaDinas\Pages\CreateNotaDinas;
use App\Filament\Resources\NotaDinas\Pages\EditNotaDinas;
use App\Filament\Resources\NotaDinas\Pages\ListNotaDinas;
use App\Filament\Resources\NotaDinas\Pages\ViewNd;
use App\Models\NotaDinas;
use App\Models\User;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class NotaDinasResource extends Resource
{
    protected static ?string $model = NotaDinas::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Nota Dinas';

    protected static ?string $modelLabel = 'Nota Dinas';

    protected static ?string $pluralModelLabel = 'Nota Dinas';

    protected static string|\UnitEnum|null $navigationGroup = 'Keuangan';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi ND')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('kategori_nd')
                                ->label('Kategori Nota Dinas')
                                ->options(NotaDinas::getKategoriOptions())
                                ->default('BIS')
                                ->required()
                                ->live()
                                ->afterStateUpdated(function ($state, Set $set) {
                                    $tahun = date('Y');
                                    $nomorBaru = NotaDinas::generateNomorND($state, $tahun);
                                    $set('no_nd', $nomorBaru);
                                }),
                                // ->helperText('Pilih kategori untuk generate nomor otomatis'),

                            DatePicker::make('tanggal')
                                ->label('Tanggal')
                                ->required()
                                ->default(now()),
                        ]),

                        TextInput::make('no_nd')
                            ->label('Nomor ND')
                            ->required()
                            ->readOnly()
                            ->unique(table: 'nota_dinas', column: 'no_nd', ignoreRecord: true)
                            ->placeholder('ND/BIS/001/2024')
                            ->maxLength(255)
                            ->default(function () {
                                return NotaDinas::generateNomorND('BIS');
                            }),
                            // ->helperText('Nomor akan ter-generate otomatis sesuai kategori dan tahun'),
                    ]),
                
                Section::make('Pihak')
                    ->icon('heroicon-o-user-group')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('pengirim_id')
                                ->label('Pengirim')
                                ->relationship('pengirim', 'name')
                                ->default(Auth::id())
                                ->disabled(function (): bool {
                                    /** @var User $user */
                                    $user = Auth::user();

                                    return ! ($user && $user->hasRole('super_admin'));
                                })
                                ->dehydrated()
                                ->required(),

                            Select::make('penerima_id')
                                ->label('Penerima')
                                ->relationship('penerima', 'name')
                                ->searchable()
                                ->preload(),
                            
                            Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'diajukan' => 'Diajukan',
                                        'disetujui' => 'Disetujui',
                                        'ditolak' => 'Ditolak',
                                    ])
                                    ->default('diajukan')
                                    ->required(),
                        ]),
                    ]),

                Section::make('Konten')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('sifat')
                                ->label('Sifat')
                                ->options([
                                    'Segera' => 'Segera',
                                    'Biasa' => 'Biasa',
                                    'Rahasia' => 'Rahasia',
                                ])
                                ->placeholder('Pilih sifat nota dinas')
                                ->required(),

                            TextInput::make('hal')
                                ->label('Hal')
                                ->placeholder('Perihal nota dinas')
                                ->maxLength(255),
                        ]),

                        Textarea::make('catatan')
                            ->label('Catatan')
                            ->placeholder('Jika ada catatan tambahan, tuliskan disini...')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Lampiran')
                    ->icon('heroicon-o-paper-clip')
                    ->schema([
                        FileUpload::make('nd_upload')
                            ->label('Upload File Nota Dinas')
                            ->directory('nota-dinas-uploads')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->maxSize(2048)
                            ->downloadable()
                            ->openable()
                            ->previewable()
                            ->columnSpanFull()
                            ->helperText('PERHATIAN : Setelah ND ditanda tangani, SEGERA masukkan persetujuannya kesini. Max 2MB.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_nd')
                    ->label('Nomor ND')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('kategori_nd')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'BIS' => 'success',
                        'OPS' => 'info',
                        'ADM' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => NotaDinas::getKategoriOptions()[$state] ?? $state
                    )
                    ->sortable(),
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d-m-Y')
                    ->sortable(),
                TextColumn::make('pengirim.name')
                    ->label('Pengirim')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('penerima.name')
                    ->label('Penerima')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sifat')
                    ->label('Sifat')
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Segera' => 'danger',
                        'Biasa' => 'success',
                        'Rahasia' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('hal')
                    ->label('Hal')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('details_count')
                    ->label('Jumlah Detail')
                    ->getStateUsing(function ($record) {
                        return $record->details_count ?? 0;
                    })
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'gray',
                        $state <= 3 => 'warning',
                        $state <= 6 => 'success',
                        default => 'primary',
                    })
                    ->icon(fn (int $state): string => match (true) {
                        $state === 0 => 'heroicon-o-minus-circle',
                        $state <= 3 => 'heroicon-o-exclamation-triangle',
                        default => 'heroicon-o-check-circle',
                    })
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('nd_upload')
                    ->label('File Upload')
                    ->getStateUsing(function ($record) {
                        return $record->nd_upload ? 'Ada' : 'Tidak';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Ada' => 'success',
                        'Tidak' => 'gray',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Ada' => 'heroicon-o-document-check',
                        'Tidak' => 'heroicon-o-document-minus',
                        default => 'heroicon-o-document',
                    })
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'diajukan' => 'warning',
                        'disetujui' => 'success',
                        'dibayar' => 'primary',
                        'ditolak' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'draft' => 'heroicon-o-pencil',
                        'diajukan' => 'heroicon-o-paper-airplane',
                        'disetujui' => 'heroicon-o-check-circle',
                        'dibayar' => 'heroicon-o-banknotes',
                        'ditolak' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-document',
                    }),
                TextColumn::make('approver.name')
                    ->label('Disetujui Oleh')
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('approved_at')
                    ->label('Waktu Persetujuan')
                    ->dateTime('d-m-Y H:i')
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d-m-Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d-m-Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('kategori_nd')
                    ->label('Kategori')
                    ->options(NotaDinas::getKategoriOptions()),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'diajukan' => 'Diajukan',
                        'disetujui' => 'Disetujui',
                        'dibayar' => 'Dibayar',
                        'ditolak' => 'Ditolak',
                    ]),
                SelectFilter::make('pengirim')
                    ->label('Pengirim')
                    ->relationship('pengirim', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('tanggal')
                    ->schema([
                        DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal'),
                        DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '<=', $date),
                            );
                    }),
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(function (NotaDinas $record): bool {
                        /** @var User $user */
                        $user = Auth::user();

                        return $record->status === 'diajukan' && ($user ? $user->hasRole('super_admin') : false);
                    })
                    ->requiresConfirmation()
                    ->action(function (NotaDinas $record): void {
                        $record->update([
                            'status' => 'disetujui',
                            'approved_by' => Auth::id(),
                            'approved_at' => now(),
                        ]);
                    }),
                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(function (NotaDinas $record): bool {
                        /** @var User $user */
                        $user = Auth::user();

                        return $record->status === 'diajukan' && ($user ? $user->hasRole('super_admin') : false);
                    })
                    ->requiresConfirmation()
                    ->action(function (NotaDinas $record): void {
                        $record->update([
                            'status' => 'ditolak',
                        ]);
                    }),
                Action::make('view_approval')
                    ->label('Lihat Approval')
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->url(fn (NotaDinas $record): string => static::getUrl('view-nd', ['record' => $record]))
                    ->openUrlInNewTab(),
                Action::make('download_file')
                    ->label('Download File')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->visible(fn (NotaDinas $record): bool => ! empty($record->nd_upload))
                    ->url(fn (NotaDinas $record): string => asset('storage/'.$record->nd_upload))
                    ->openUrlInNewTab(),
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->icon('heroicon-m-trash')
                    ->tooltip('Hapus Nota Dinas')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Nota Dinas')
                    ->modalDescription('Apakah Anda yakin ingin menghapus Nota Dinas ini? Tindakan ini tidak dapat dibatalkan.')
                    ->modalSubmitActionLabel('Ya, hapus')
                    ->modalIcon('heroicon-o-exclamation-triangle')
                    ->modalIconColor('danger')
                    ->visible(function (NotaDinas $record): bool {
                        $detailCount = $record->details()->count();

                        return $detailCount === 0;
                    })
                    ->before(function (?NotaDinas $record) {
                        if (! $record) {
                            Notification::make()
                                ->danger()
                                ->title('Error')
                                ->body('Data Nota Dinas tidak ditemukan. Silakan refresh halaman dan coba lagi.')
                                ->persistent()
                                ->send();

                            return false;
                        }

                        Notification::make()
                            ->info()
                            ->title('Memproses')
                            ->body('Memvalidasi Nota Dinas untuk penghapusan...')
                            ->send();
                    })
                    ->action(function (?NotaDinas $record) {
                        if (! $record) {
                            Notification::make()
                                ->danger()
                                ->title('Penghapusan Gagal')
                                ->body('Data Nota Dinas tidak ditemukan. Mungkin sudah dihapus atau dipindahkan.')
                                ->persistent()
                                ->send();

                            return false;
                        }

                        // Double check for details before deletion
                        $detailCount = $record->details()->count();
                        if ($detailCount > 0) {
                            Notification::make()
                                ->danger()
                                ->title('Tidak Dapat Menghapus Nota Dinas')
                                ->body("Nota Dinas ini tidak dapat dihapus karena memiliki {$detailCount} detail record. Silakan hapus semua detail terlebih dahulu.")
                                ->persistent()
                                ->send();

                            return false;
                        }

                        try {
                            $record->delete();

                            Notification::make()
                                ->success()
                                ->title('Nota Dinas Dihapus')
                                ->body('Nota Dinas telah berhasil dihapus.')
                                ->send();
                        } catch (Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Penghapusan Gagal')
                                ->body('Terjadi kesalahan saat menghapus Nota Dinas: '.$e->getMessage())
                                ->persistent()
                                ->send();
                        }
                    }),
                RestoreAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Pulihkan Nota Dinas')
                    ->modalDescription('Apakah Anda yakin ingin memulihkan Nota Dinas yang dihapus ini?')
                    ->modalSubmitActionLabel('Ya, pulihkan')
                    ->modalIcon('heroicon-o-arrow-path')
                    ->modalIconColor('success')
                    ->successNotificationTitle('Nota Dinas Dipulihkan'),
                ForceDeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Permanen Nota Dinas')
                    ->modalDescription('Apakah Anda yakin ingin MENGHAPUS PERMANEN Nota Dinas ini? Tindakan ini tidak dapat dibatalkan dan akan juga menghapus semua detail terkait.')
                    ->modalSubmitActionLabel('Ya, hapus permanen')
                    ->modalIcon('heroicon-o-exclamation-triangle')
                    ->modalIconColor('danger')
                    ->before(function (NotaDinas $record) {
                        $detailCount = $record->details()->withTrashed()->count();
                        if ($detailCount > 0) {
                            Notification::make()
                                ->danger()
                                ->title('Peringatan Penghapusan Berantai')
                                ->body("⚠️ Ini akan menghapus permanen Nota Dinas dan {$detailCount} detail terkait. Tindakan ini TIDAK DAPAT DIBATALKAN!")
                                ->persistent()
                                ->send();
                        }
                    })
                    ->action(function (NotaDinas $record) {
                        try {
                            $detailCount = $record->details()->withTrashed()->count();
                            $record->forceDelete(); // Uses our custom method with cascade

                            Notification::make()
                                ->success()
                                ->title('Dihapus Permanen')
                                ->body("Nota Dinas dan {$detailCount} detail terkait telah dihapus permanen.")
                                ->send();
                        } catch (Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Penghapusan Paksa Gagal')
                                ->body('Terjadi kesalahan: '.$e->getMessage())
                                ->persistent()
                                ->send();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Nota Dinas Terpilih')
                        ->modalDescription('Apakah Anda yakin ingin menghapus record Nota Dinas yang dipilih? Hanya record tanpa detail yang akan dihapus.')
                        ->modalSubmitActionLabel('Ya, hapus yang dipilih')
                        ->modalIcon('heroicon-o-exclamation-triangle')
                        ->modalIconColor('danger')
                        ->before(function ($livewire) {
                            $selectedRecords = $livewire->getSelectedTableRecords();
                            $protectedRecords = [];
                            $deletableCount = 0;

                            foreach ($selectedRecords as $record) {
                                $detailCount = $record->details()->count();
                                if ($detailCount > 0) {
                                    $protectedRecords[] = $record->no_nd." ({$detailCount} detail)";
                                } else {
                                    $deletableCount++;
                                }
                            }

                            if (! empty($protectedRecords)) {
                                $message = "Nota Dinas berikut tidak dapat dihapus karena memiliki detail terkait:\n\n";
                                $message .= '• '.implode("\n• ", $protectedRecords);

                                if ($deletableCount > 0) {
                                    $message .= "\n\n{$deletableCount} record tanpa detail akan dihapus.";
                                }

                                Notification::make()
                                    ->warning()
                                    ->title('Beberapa Record Dilindungi')
                                    ->body($message)
                                    ->persistent()
                                    ->send();
                            }
                        })
                        ->action(function ($livewire) {
                            $selectedRecords = $livewire->getSelectedTableRecords();
                            $deletedCount = 0;
                            $protectedCount = 0;

                            foreach ($selectedRecords as $record) {
                                $detailCount = $record->details()->count();
                                if ($detailCount === 0) {
                                    try {
                                        $record->delete();
                                        $deletedCount++;
                                    } catch (Exception $e) {
                                        // Log error but continue with other records
                                    }
                                } else {
                                    $protectedCount++;
                                }
                            }

                            if ($deletedCount > 0) {
                                Notification::make()
                                    ->success()
                                    ->title('Penghapusan Massal Selesai')
                                    ->body("{$deletedCount} record Nota Dinas berhasil dihapus.".
                                           ($protectedCount > 0 ? " {$protectedCount} record dilindungi dari penghapusan." : ''))
                                    ->send();
                            } else {
                                Notification::make()
                                    ->warning()
                                    ->title('Tidak Ada Record Dihapus')
                                    ->body('Semua record yang dipilih memiliki detail terkait dan tidak dapat dihapus.')
                                    ->send();
                            }
                        }),
                    RestoreBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Pulihkan Nota Dinas Terpilih')
                        ->modalDescription('Apakah Anda yakin ingin memulihkan record Nota Dinas yang dihapus dan dipilih?')
                        ->modalSubmitActionLabel('Ya, pulihkan yang dipilih')
                        ->modalIcon('heroicon-o-arrow-path')
                        ->modalIconColor('success')
                        ->successNotificationTitle('Record Dipulihkan')
                        ->action(function ($livewire) {
                            $selectedRecords = $livewire->getSelectedTableRecords();
                            $restoredCount = 0;

                            foreach ($selectedRecords as $record) {
                                try {
                                    $record->restore();
                                    $restoredCount++;
                                } catch (Exception $e) {
                                    // Log error but continue with other records
                                }
                            }

                            Notification::make()
                                ->success()
                                ->title('Pemulihan Massal Selesai')
                                ->body("{$restoredCount} record Nota Dinas berhasil dipulihkan.")
                                ->send();
                        }),
                    ForceDeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Permanen Nota Dinas Terpilih')
                        ->modalDescription('Apakah Anda yakin ingin MENGHAPUS PERMANEN record Nota Dinas yang dipilih? Tindakan ini tidak dapat dibatalkan dan akan juga menghapus semua detail terkait.')
                        ->modalSubmitActionLabel('Ya, hapus permanen')
                        ->modalIcon('heroicon-o-exclamation-triangle')
                        ->modalIconColor('danger')
                        ->before(function ($livewire) {
                            $selectedRecords = $livewire->getSelectedTableRecords();
                            $recordsWithDetails = [];
                            $totalDetails = 0;

                            foreach ($selectedRecords as $record) {
                                $detailCount = $record->details()->withTrashed()->count();
                                if ($detailCount > 0) {
                                    $recordsWithDetails[] = $record->no_nd." ({$detailCount} detail)";
                                    $totalDetails += $detailCount;
                                }
                            }

                            if (! empty($recordsWithDetails)) {
                                $message = "⚠️ PERINGATAN: Nota Dinas berikut memiliki detail terkait yang juga akan dihapus permanen:\n\n";
                                $message .= '• '.implode("\n• ", $recordsWithDetails);
                                $message .= "\n\nTotal detail yang akan dihapus: {$totalDetails}";
                                $message .= "\n\nTindakan ini TIDAK DAPAT DIBATALKAN!";

                                Notification::make()
                                    ->danger()
                                    ->title('Peringatan Penghapusan Berantai')
                                    ->body($message)
                                    ->persistent()
                                    ->send();
                            }
                        })
                        ->action(function ($livewire) {
                            $selectedRecords = $livewire->getSelectedTableRecords();
                            $deletedCount = 0;
                            $totalDetailsDeleted = 0;
                            $errorCount = 0;

                            foreach ($selectedRecords as $record) {
                                try {
                                    $detailCount = $record->details()->withTrashed()->count();
                                    $record->forceDelete(); // This will cascade delete details via our custom method
                                    $deletedCount++;
                                    $totalDetailsDeleted += $detailCount;
                                } catch (Exception $e) {
                                    $errorCount++;
                                    // Log error but continue with other records
                                }
                            }

                            if ($deletedCount > 0) {
                                Notification::make()
                                    ->success()
                                    ->title('Penghapusan Paksa Selesai')
                                    ->body("{$deletedCount} Nota Dinas dan {$totalDetailsDeleted} detail terkait telah dihapus permanen.".
                                           ($errorCount > 0 ? " {$errorCount} record gagal dihapus." : ''))
                                    ->send();
                            } else {
                                Notification::make()
                                    ->danger()
                                    ->title('Penghapusan Paksa Gagal')
                                    ->body('Tidak ada record yang dihapus. Silakan periksa error.')
                                    ->send();
                            }
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount('details')
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\NotaDinasDetailsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNotaDinas::route('/'),
            'create' => CreateNotaDinas::route('/create'),
            'edit' => EditNotaDinas::route('/{record}/edit'),
            'view-nd' => ViewNd::route('/{record}/view-nd'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Total nota dinas';
    }
}
