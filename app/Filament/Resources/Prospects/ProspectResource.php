<?php

namespace App\Filament\Resources\Prospects;

use App\Enums\OrderStatus;
use App\Filament\Resources\Prospects\Pages\CreateProspect;
use App\Filament\Resources\Prospects\Pages\EditProspect;
use App\Filament\Resources\Prospects\Pages\ListProspects;
use App\Models\Prospect;
use Carbon\Carbon;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
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
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProspectResource extends Resource
{
    protected static ?string $model = Prospect::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Penjualan';

    protected static ?string $navigationLabel = 'Prospek';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make('Informasi Acara')
                            ->description('Detail dasar acara dan tempat')
                            ->schema([
                                TextInput::make('name_event')
                                    ->label('Nama Acara')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Pernikahan Pengantin Pria & Pengantin Wanita')
                                    ->columnSpanFull(),

                                Grid::make(3)
                                    ->schema([
                                        DatePicker::make('date_lamaran')
                                            ->label('Tanggal Lamaran')
                                            ->native(false)
                                            ->displayFormat('d M Y'),

                                        DatePicker::make('date_akad')
                                            ->label('Tanggal Akad Nikah')
                                            ->native(false)
                                            ->displayFormat('d M Y'),

                                        DatePicker::make('date_resepsi')
                                            ->label('Tanggal Resepsi')
                                            ->native(false)
                                            ->displayFormat('d M Y'),
                                    ]),

                                TextInput::make('venue')
                                    ->label('Lokasi Venue')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Masukkan detail venue')
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Informasi Klien')
                            ->description('Detail kontak untuk pasangan')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('name_cpp')
                                            ->label('Nama Calon Pengantin Pria')
                                            ->required()
                                            ->maxLength(255),

                                        TextInput::make('name_cpw')
                                            ->label('Nama Calon Pengantin Wanita')
                                            ->required()
                                            ->maxLength(255),
                                    ]),

                                TextInput::make('phone')
                                    ->tel()
                                    ->required()
                                    ->prefix('+62')
                                    // Ubah regex menjadi lebih fleksibel
                                    ->regex('/^[0-9]{8,15}$/') // Lebih fleksibel dari 9-15 menjadi 8-15 digit
                                    ->placeholder('812XXXXXXXX')
                                    ->helperText('Masukkan nomor tanpa angka 0 di depan'),

                                TextInput::make('address')
                                    ->label('Alamat')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make('Keuangan & Manajemen')
                            ->description('Detail harga dan manajemen akun')
                            ->schema([
                                TextInput::make('total_penawaran')
                                    ->label('Total Penawaran')
                                    ->required()
                                    ->prefix('Rp. ')
                                    ->numeric()
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->placeholder('0')
                                    ->helperText('Masukkan total jumlah penawaran'),

                                Select::make('user_id')
                                    ->label('Manajer Akun')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->default(Auth::user()->id)
                                    ->helperText('Pilih manajer akun yang bertanggung jawab'),
                            ]),

                        Section::make('Catatan Tambahan')
                            ->schema([
                                Textarea::make('notes')
                                    ->label('Catatan')
                                    ->placeholder('Masukkan catatan tambahan atau persyaratan khusus')
                                    ->rows(5)
                                    ->default('Tidak ada catatan')
                                    ->required()
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Status Information with Custom Badge for No Order
                TextColumn::make('order_status_display')
                    ->label('Status Pesanan')
                    ->badge()
                    ->getStateUsing(function (?Prospect $record) {
                        if (! $record) {
                            return 'unknown';
                        }

                        if ($record->orders()->exists()) {
                            $latestOrder = $record->orders()->latest()->first();
                            if ($latestOrder && $latestOrder->status) {
                                // Handle enum case
                                return $latestOrder->status instanceof OrderStatus
                                    ? $latestOrder->status->value
                                    : $latestOrder->status;
                            }

                            return 'unknown';
                        }

                        return 'no_order';
                    })
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'processing',
                        'primary' => 'done',
                        'danger' => 'cancelled',
                        'gray' => 'no_order',
                        'secondary' => 'unknown',
                    ])
                    ->formatStateUsing(function ($state): string {
                        // Handle both enum and string values
                        $stateValue = $state instanceof OrderStatus ? $state->value : $state;

                        return match ($stateValue) {
                            'no_order' => 'Warm Prospect',
                            'pending' => 'Pending',
                            'processing' => 'Processing',
                            'done' => 'Done',
                            'cancelled' => 'Cancelled',
                            'unknown' => 'Unknown',
                            default => is_string($stateValue) ? ucfirst($stateValue) : 'Unknown',
                        };
                    }),

                // Client Information
                TextColumn::make('name_event')
                    ->label('Nama Acara')
                    ->searchable()
                    ->sortable()
                    ->description(fn (?Prospect $record): string => $record ? "Venue: {$record->venue}" : ''
                    ),

                TextColumn::make('name_cpp')
                    ->label('Nama Pengantin Pria')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name_cpw')
                    ->label('Nama Pengantin Wanita')
                    ->searchable()
                    ->sortable(),

                // Contact Information
                TextColumn::make('address')
                    ->label('Alamat')
                    ->searchable()
                    ->toggleable()
                    ->wrap(),

                TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Nomor telepon berhasil disalin')
                    ->copyMessageDuration(1500)
                    ->formatStateUsing(fn (?string $state) => $state ? '+62 '.$state : ''),

                // Event Dates
                TextColumn::make('date_lamaran')
                    ->label('Tanggal Lamaran')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('date_akad')
                    ->label('Akad Nikah')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('date_resepsi')
                    ->label('Tanggal Resepsi')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),

                // Financial Information
                TextColumn::make('total_penawaran')
                    ->label('Total Penawaran')
                    ->numeric()
                    ->money('IDR')
                    ->sortable()
                    ->alignEnd(),

                // Management Information
                TextColumn::make('user.name')
                    ->label('Manajer Akun')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-user'),

                // Timestamps (Hidden by Default)
                TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Terakhir Diperbarui')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->label('Tanggal Dihapus')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),

                SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->label('Manajer Akun')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('order_status')
                    ->label('Status Pesanan')
                    ->options([
                        'has_order' => 'Memiliki Pesanan',
                        'no_order' => 'Prospek Hangat',
                        'pending' => 'Menunggu',
                        'processing' => 'Diproses',
                        'done' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            function (Builder $query, $value): Builder {
                                return match ($value) {
                                    'has_order' => $query->has('orders'),
                                    'no_order' => $query->doesntHave('orders'),
                                    'pending' => $query->whereHas('orders', fn ($q) => $q->where('status', OrderStatus::Pending)),
                                    'processing' => $query->whereHas('orders', fn ($q) => $q->where('status', OrderStatus::Processing)),
                                    'done' => $query->whereHas('orders', fn ($q) => $q->where('status', OrderStatus::Done)),
                                    'cancelled' => $query->whereHas('orders', fn ($q) => $q->where('status', OrderStatus::Cancelled)),
                                    default => $query,
                                };
                            }
                        );
                    }),

                Filter::make('wedding_date')
                    ->schema([
                        DatePicker::make('from_date')
                            ->label('Dari Tanggal'),
                        DatePicker::make('until_date')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_resepsi', '>=', $date),
                            )
                            ->when(
                                $data['until_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_resepsi', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from_date'] ?? null) {
                            $indicators['from'] = 'Pernikahan dari '.Carbon::parse($data['from_date'])->toFormattedDateString();
                        }
                        if ($data['until_date'] ?? null) {
                            $indicators['until'] = 'Pernikahan sampai '.Carbon::parse($data['until_date'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->icon('heroicon-m-eye')
                        ->tooltip('Lihat detail prospek'),

                    EditAction::make()
                        ->icon('heroicon-m-pencil')
                        ->tooltip('Edit prospek'),

                    DeleteAction::make()
                        ->icon('heroicon-m-trash')
                        ->tooltip('Hapus prospek')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Prospek')
                        ->modalDescription('Apakah Anda yakin ingin menghapus prospek ini? Tindakan ini tidak dapat dibatalkan.')
                        ->modalSubmitActionLabel('Ya, hapus')
                        ->modalIcon('heroicon-o-exclamation-triangle')
                        ->modalIconColor('danger')
                        ->visible(fn (?Prospect $record): bool => $record && ! $record->orders()->exists())
                        ->before(function (?Prospect $record) {
                            // Validate record exists before showing confirmation
                            if (! $record) {
                                Notification::make()
                                    ->danger()
                                    ->title('Error')
                                    ->body('Data prospek tidak ditemukan. Silakan refresh halaman dan coba lagi.')
                                    ->persistent()
                                    ->send();

                                return false;
                            }

                            // Show loading notification
                            Notification::make()
                                ->info()
                                ->title('Memproses')
                                ->body('Memvalidasi prospek untuk penghapusan...')
                                ->send();
                        })
                        ->action(function (?Prospect $record) {
                            // Comprehensive null and existence checks
                            if (! $record) {
                                Notification::make()
                                    ->danger()
                                    ->title('Penghapusan Gagal')
                                    ->body('Data prospek tidak ditemukan. Mungkin sudah dihapus atau dipindahkan.')
                                    ->persistent()
                                    ->send();

                                return false;
                            }

                            // Refresh record from database to ensure latest state
                            try {
                                $record->refresh();
                            } catch (Exception $e) {
                                Notification::make()
                                    ->danger()
                                    ->title('Penghapusan Gagal')
                                    ->body('Tidak dapat mengakses data prospek. Mungkin sudah dihapus oleh pengguna lain.')
                                    ->persistent()
                                    ->send();

                                return false;
                            }

                            // Double check for associated orders
                            if ($record->orders()->exists()) {
                                $orderCount = $record->orders()->count();
                                Notification::make()
                                    ->danger()
                                    ->title('Penghapusan Tidak Diizinkan')
                                    ->body("Prospek '{$record->name_event}' tidak dapat dihapus karena memiliki {$orderCount} pesanan terkait. Silakan hapus pesanan terlebih dahulu.")
                                    ->persistent()
                                    ->send();

                                return false;
                            }

                            // Attempt deletion with comprehensive error handling
                            try {
                                $eventName = $record->name_event ?? 'Unknown Event';
                                $record->delete();

                                Notification::make()
                                    ->success()
                                    ->title('Prospek Berhasil Dihapus')
                                    ->body("'{$eventName}' telah dihapus dari sistem.")
                                    ->duration(5000)
                                    ->send();

                                return true;

                            } catch (QueryException $e) {
                                // Handle database-specific errors
                                $errorCode = $e->getCode();
                                if ($errorCode === '23000') {
                                    Notification::make()
                                        ->danger()
                                        ->title('Penghapusan Gagal - Batasan Data')
                                        ->body('Prospek ini tidak dapat dihapus karena direferensikan oleh data lain dalam sistem.')
                                        ->persistent()
                                        ->send();
                                } else {
                                    Notification::make()
                                        ->danger()
                                        ->title('Error Database')
                                        ->body('Terjadi error database saat menghapus prospek. Silakan coba lagi nanti.')
                                        ->persistent()
                                        ->send();
                                }

                                return false;

                            } catch (ModelNotFoundException $e) {
                                Notification::make()
                                    ->warning()
                                    ->title('Prospek Sudah Dihapus')
                                    ->body('Prospek ini sepertinya sudah dihapus oleh pengguna lain.')
                                    ->send();

                                return false;

                            } catch (Exception $e) {                        // Log the error for debugging
                                Log::error('Penghapusan prospek gagal', [
                                    'prospect_id' => $record->id ?? 'tidak diketahui',
                                    'error' => $e->getMessage(),
                                    'trace' => $e->getTraceAsString(),
                                ]);

                                Notification::make()
                                    ->danger()
                                    ->title('Error Tidak Terduga')
                                    ->body('Terjadi error tidak terduga saat menghapus prospek. Administrator sistem telah diberitahu.')
                                    ->persistent()
                                    ->send();

                                return false;
                            }
                        }),

                    ForceDeleteAction::make()
                        ->icon('heroicon-m-trash')
                        ->color('danger')
                        ->tooltip('Hapus permanen prospek')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Permanen Prospek')
                        ->modalDescription('Apakah Anda yakin ingin menghapus prospek ini secara permanen? Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data secara permanen.')
                        ->modalSubmitActionLabel('Ya, hapus permanen')
                        ->modalIcon('heroicon-o-exclamation-triangle')
                        ->modalIconColor('danger')
                        ->visible(fn (?Prospect $record): bool => $record && $record->trashed()),

                    RestoreAction::make()
                        ->icon('heroicon-m-arrow-uturn-left')
                        ->color('success')
                        ->tooltip('Pulihkan prospek yang dihapus')
                        ->requiresConfirmation()
                        ->modalHeading('Pulihkan Prospek')
                        ->modalDescription('Apakah Anda yakin ingin memulihkan prospek ini? Prospek akan tersedia kembali dalam sistem.')
                        ->modalSubmitActionLabel('Ya, pulihkan')
                        ->modalIcon('heroicon-o-arrow-uturn-left')
                        ->modalIconColor('success')
                        ->visible(fn (?Prospect $record): bool => $record && $record->trashed()),

                ])->tooltip('Aksi yang tersedia'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->icon('heroicon-m-trash')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Permanen Prospek')
                        ->modalDescription('Apakah Anda yakin ingin menghapus prospek ini secara permanen? Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data secara permanen.')
                        ->modalSubmitActionLabel('Ya, hapus permanen')
                        ->modalIcon('heroicon-o-exclamation-triangle')
                        ->modalIconColor('danger')
                        ->deselectRecordsAfterCompletion()
                        ->before(function (Collection $records) {
                            // Show processing notification
                            Notification::make()
                                ->info()
                                ->title('Memproses Penghapusan Massal')
                                ->body("Memvalidasi {$records->count()} prospek untuk penghapusan...")
                                ->send();
                        })
                        ->action(function (Collection $records) {
                            $preventedDeletions = 0;
                            $deletedCount = 0;
                            $preventedNames = [];
                            $errorDetails = [];

                            foreach ($records as $record) {
                                try {
                                    // Ensure record exists and refresh from database
                                    if (! $record || ! $record->exists) {
                                        $preventedDeletions++;
                                        $preventedNames[] = 'Data Tidak Diketahui';
                                        $errorDetails[] = 'Data tidak ditemukan';

                                        continue;
                                    }

                                    $record->refresh();

                                    if ($record->orders()->exists()) {
                                        $preventedDeletions++;
                                        $preventedNames[] = $record->name_event ?? 'Acara Tidak Diketahui';
                                        $errorDetails[] = 'Memiliki pesanan terkait';
                                    } else {
                                        $record->delete();
                                        $deletedCount++;
                                    }

                                } catch (ModelNotFoundException $e) {
                                    $preventedDeletions++;
                                    $preventedNames[] = 'Sudah Dihapus';
                                    $errorDetails[] = 'Data tidak lagi ada';

                                } catch (QueryException $e) {
                                    $preventedDeletions++;
                                    $preventedNames[] = $record->name_event ?? 'Acara Tidak Diketahui';
                                    $errorDetails[] = 'Pelanggaran batasan database';

                                    // Log database errors
                                    Log::error('Error database penghapusan massal', [
                                        'prospect_id' => $record->id ?? 'tidak diketahui',
                                        'error' => $e->getMessage(),
                                    ]);

                                } catch (Exception $e) {
                                    $preventedDeletions++;
                                    $preventedNames[] = $record->name_event ?? 'Acara Tidak Diketahui';
                                    $errorDetails[] = 'Error tidak terduga';

                                    // Log unexpected errors
                                    Log::error('Error tidak terduga penghapusan massal', [
                                        'prospect_id' => $record->id ?? 'tidak diketahui',
                                        'error' => $e->getMessage(),
                                        'trace' => $e->getTraceAsString(),
                                    ]);
                                }
                            }

                            // Show success notification
                            if ($deletedCount > 0) {
                                Notification::make()
                                    ->success()
                                    ->title('Penghapusan Massal Selesai')
                                    ->body("Berhasil menghapus {$deletedCount} prospek.")
                                    ->duration(5000)
                                    ->send();
                            }

                            // Show warning for prevented deletions
                            if ($preventedDeletions > 0) {
                                $truncatedNames = array_slice($preventedNames, 0, 3);
                                $namesList = implode(', ', $truncatedNames);
                                if (count($preventedNames) > 3) {
                                    $namesList .= ' dan '.(count($preventedNames) - 3).' lainnya';
                                }

                                Notification::make()
                                    ->warning()
                                    ->title('Beberapa Penghapusan Tidak Dapat Diselesaikan')
                                    ->body("Tidak dapat menghapus {$preventedDeletions} prospek: {$namesList}. Alasan umum termasuk memiliki pesanan terkait atau batasan database.")
                                    ->persistent()
                                    ->send();
                            }

                            // Show info if no records were processed
                            if ($deletedCount === 0 && $preventedDeletions === 0) {
                                Notification::make()
                                    ->info()
                                    ->title('Tidak Ada Data untuk Dihapus')
                                    ->body('Tidak ditemukan data yang valid untuk dihapus.')
                                    ->send();
                            }
                        }),

                    Action::make('cannot_delete')
                        ->label('Tidak Dapat Dihapus')
                        ->icon('heroicon-m-shield-exclamation')
                        ->color('gray')
                        ->tooltip('Prospek ini tidak dapat dihapus karena memiliki pesanan terkait')
                        ->visible(fn (?Prospect $record): bool => $record && $record->orders()->exists())
                        ->action(function (Prospect $record) {
                            $orderCount = $record->orders()->count();
                            Notification::make()
                                ->warning()
                                ->title('Tidak Dapat Menghapus Prospek')
                                ->body("'{$record->name_event}' tidak dapat dihapus karena memiliki {$orderCount} pesanan terkait. Silakan hapus pesanan terlebih dahulu.")
                                ->persistent()
                                ->send();
                        }),

                    ForceDeleteBulkAction::make()
                        ->icon('heroicon-m-trash')
                        ->requiresConfirmation()
                        ->color('danger')
                        ->modalHeading('Hapus Permanen Prospek Terpilih')
                        ->modalDescription('Apakah Anda yakin ingin menghapus prospek yang dipilih secara permanen? Tindakan ini tidak dapat dibatalkan.')
                        ->modalSubmitActionLabel('Ya, hapus permanen'),

                    RestoreBulkAction::make()
                        ->icon('heroicon-m-arrow-uturn-left')
                        ->color('success'),
                ])->label('Aksi Terpilih'),
            ])
            ->defaultSort('created_at', 'desc')
            ->persistSortInSession()
            ->striped()
            ->defaultPaginationPageOption(10)
            ->paginationPageOptions([10, 25, 50])
            ->emptyStateHeading('Tidak ada prospek ditemukan')
            ->emptyStateDescription('Buat prospek pertama Anda untuk memulai.')
            ->emptyStateIcon('heroicon-o-users')
            ->poll('30s'); // Auto refresh setiap 30 detik untuk update real-time
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
            'index' => ListProspects::route('/'),
            'create' => CreateProspect::route('/create'),
            'edit' => EditProspect::route('/{record}/edit'),
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
        $modelClass = static::$model;

        return (string) $modelClass::whereDoesntHave('orders')
            ->count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Calon client yang terdaftar';
    }
}
