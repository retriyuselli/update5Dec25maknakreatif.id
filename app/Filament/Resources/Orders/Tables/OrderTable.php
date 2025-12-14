<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\Employee;
use App\Models\Order;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class OrderTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->poll('5s')
            ->columns([
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'processing',
                        'danger' => 'cancelled',
                        'primary' => 'done',
                    ]),

                TextColumn::make('payment_status')
                    ->label('Pembayaran')
                    ->getStateUsing(function (Order $record): string {
                        $paid = $record->bayar ?? 0;
                        $total = $record->grand_total ?? 0;
                        if ($total == 0) {
                            return '0%';
                        }
                        $percentage = min(round(($paid / $total) * 100), 100);
                        return $percentage.'%';
                    })
                    ->color(fn (Order $record): string => $record->is_paid ? 'success' : ($record->bayar > 0 ? 'warning' : 'danger'))
                    ->alignment(Alignment::Center)
                    ->badge()
                    ->toggleable(),

                TextColumn::make('number')
                    ->label('Nomor Pesanan')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()->copyable()->copyMessage('Nomor pesanan berhasil disalin')
                    ->sortable()->tooltip('Klik untuk menyalin nomor pesanan')
                    ->description(fn (Order $record): string => "No : {$record->no_kontrak}")
                    ->weight(FontWeight::Bold),

                TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyable()
                    ->copyMessage('Order slug copied successfully'),

                TextColumn::make('prospect.name_event')
                    ->label('Nama Acara')
                    ->sortable()
                    ->searchable()
                    ->wrap()
                    ->copyable()
                    ->copyMessage('Nama acara berhasil disalin'),

                TextColumn::make('closing_date')
                    ->label('Tanggal Closing')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('prospect.date_lamaran')
                    ->label('Lamaran')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->date('d M Y')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),

                TextColumn::make('prospect.date_akad')
                    ->label('Akad')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->date('d M Y')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),

                TextColumn::make('prospect.date_resepsi')
                    ->label('Resepsi')
                    ->date('d M Y')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->toggleable()
                    ->sortable(),

                TextColumn::make('employee.name')
                    ->label('Manajer Acara')
                    ->searchable()
                    ->sortable()
                    ->color('success')
                    ->description(fn (Order $record): string => "MA: {$record->user?->name}"),

                TextColumn::make('grand_total')
                    ->label('Grand Total')->money('IDR')
                    ->alignEnd()
                    ->description(fn (Order $record): string => $record->promo > 0 || $record->pengurangan > 0 ? 'Pengurangan: -'.number_format($record->promo + $record->pengurangan, 0, ',', '.') : '')
                    ->color('success'),

                TextColumn::make('bayar')
                    ->label('Jumlah Dibayar')
                    ->numeric()
                    ->money('IDR')
                    ->alignment(Alignment::Right)
                    ->color('success')
                    ->toggleable(),

                TextColumn::make('sisa')
                    ->label('Sisa Tagihan')
                    ->numeric()
                    ->money('IDR')
                    ->alignment(Alignment::Right)
                    ->color('danger')
                    ->toggleable(),

                TextColumn::make('tot_pengeluaran')
                    ->label('Total Pengeluaran')
                    ->numeric()
                    ->money('IDR')
                    ->alignment(Alignment::Right)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('laba_kotor')
                    ->label('Laba/Rugi')
                    ->numeric()
                    ->money('IDR')
                    ->alignment(Alignment::Right)
                    ->color(fn (Order $record) => $record->laba_kotor > 0 ? 'success' : 'danger')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->weight(FontWeight::Bold),

                TextColumn::make('items.product.name')
                    ->label('Produk')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->listWithLineBreaks()
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('event_dates')
                    ->schema([
                        \Filament\Forms\Components\Select::make('date_type')
                            ->label('Filter By Event')
                            ->options([
                                'all' => 'All Events',
                                'date_lamaran' => 'Lamaran Date',
                                'date_akad' => 'Akad Date',
                                'date_resepsi' => 'Reception Date',
                            ])
                            ->default('all')
                            ->required(),

                        \Filament\Forms\Components\DatePicker::make('from_date')
                            ->label('From')
                            ->default(now()->startOfMonth())
                            ->displayFormat('d M Y'),

                        \Filament\Forms\Components\DatePicker::make('until_date')
                            ->label('Until')
                            ->default(now()->endOfMonth())
                            ->displayFormat('d M Y'),
                    ])
                    ->columns(1)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['date_type'] && ($data['from_date'] || $data['until_date']), function (Builder $query) use ($data) {
                            return $query->whereHas('prospect', function ($query) use ($data) {
                                if ($data['date_type'] === 'all') {
                                    $query->where(function ($subQuery) use ($data) {
                                        $subQuery->when($data['from_date'], function ($q) use ($data) {
                                            $q->orWhere(function ($q) use ($data) {
                                                $q->whereDate('date_lamaran', '>=', $data['from_date'])
                                                  ->when($data['until_date'], fn ($q) => $q->whereDate('date_lamaran', '<=', $data['until_date']));
                                            });
                                        });
                                        $subQuery->when($data['from_date'], function ($q) use ($data) {
                                            $q->orWhere(function ($q) use ($data) {
                                                $q->whereDate('date_akad', '>=', $data['from_date'])
                                                  ->when($data['until_date'], fn ($q) => $q->whereDate('date_akad', '<=', $data['until_date']));
                                            });
                                        });
                                        $subQuery->when($data['from_date'], function ($q) use ($data) {
                                            $q->orWhere(function ($q) use ($data) {
                                                $q->whereDate('date_resepsi', '>=', $data['from_date'])
                                                  ->when($data['until_date'], fn ($q) => $q->whereDate('date_resepsi', '<=', $data['until_date']));
                                            });
                                        });
                                    });
                                    if ($data['sort_order'] ?? null) {
                                        $query->orderByRaw(
                                            "LEAST(
                                                COALESCE(date_lamaran, '9999-12-31'),
                                                COALESCE(date_akad, '9999-12-31'),
                                                COALESCE(date_resepsi, '9999-12-31')
                                            ) ".$data['sort_order'],
                                        );
                                    }
                                } else {
                                    $dateField = $data['date_type'];
                                    $query->when($data['from_date'], function ($q) use ($data, $dateField) {
                                        $q->whereDate($dateField, '>=', $data['from_date']);
                                    });
                                    $query->when($data['until_date'], function ($q) use ($data, $dateField) {
                                        $q->whereDate($dateField, '<=', $data['until_date']);
                                    });
                                    if ($data['sort_order'] ?? null) {
                                        $query->orderBy($dateField, $data['sort_order']);
                                    }
                                }
                                if (! ($data['include_completed'] ?? true)) {
                                    if ($data['date_type'] === 'all') {
                                        $query->where(function ($q) {
                                            $now = now();
                                            $q->whereDate('date_lamaran', '>=', $now)
                                              ->orWhereDate('date_akad', '>=', $now)
                                              ->orWhereDate('date_resepsi', '>=', $now);
                                        });
                                    } else {
                                        $query->whereDate($data['date_type'], '>=', now());
                                    }
                                }
                            });
                        });
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['date_type'] ?? null) {
                            $eventType = match ($data['date_type']) {
                                'all' => 'All Events',
                                'date_lamaran' => 'Lamaran',
                                'date_akad' => 'Akad',
                                'date_resepsi' => 'Reception',
                                default => '',
                            };
                            if ($data['from_date'] ?? null) {
                                $indicators[] = 'From: '.Carbon::parse($data['from_date'])->format('d M Y');
                            }
                            if ($data['until_date'] ?? null) {
                                $indicators[] = 'Until: '.Carbon::parse($data['until_date'])->format('d M Y');
                            }
                            if (! empty($indicators)) {
                                array_unshift($indicators, $eventType);
                            }
                            if (! ($data['include_completed'] ?? true)) {
                                $indicators[] = 'Upcoming Only';
                            }
                        }
                        return $indicators;
                    })
                    ->columnSpanFull(),

                Filter::make('has_contract_document')
                    ->label('Has Contract Document')
                    ->query(fn (Builder $query) => $query->whereNotNull('doc_kontrak'))
                    ->toggle(),

                Filter::make('no_contract_document')
                    ->label('No Contract Document')
                    ->query(fn (Builder $query) => $query->whereNull('doc_kontrak'))
                    ->toggle(),

                Filter::make('team')
                    ->schema([
                        \Filament\Forms\Components\Select::make('employee_id')
                            ->label('Event Manager')
                            ->relationship('employee', 'name')
                            ->searchable()
                            ->preload(),
                        \Filament\Forms\Components\Select::make('user_id')
                            ->label('Account Manager')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['employee_id'] ?? null, fn ($query, $id) => $query->where('employee_id', $id))
                            ->when($data['user_id'] ?? null, fn ($query, $id) => $query->where('user_id', $id));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['employee_id'] ?? null) {
                            $employee = Employee::find($data['employee_id']);
                            $indicators['em'] = 'EM: '.($employee?->name ?? 'Unknown');
                        }
                        if ($data['user_id'] ?? null) {
                            $user = User::find($data['user_id']);
                            $indicators['am'] = 'AM: '.($user?->name ?? 'Unknown');
                        }
                        return $indicators;
                    }),

                Filter::make('closing_date_filter')
                    ->schema([])
                    ->query(function (Builder $query, array $data): Builder {
                        if (isset($data['year']) && is_numeric($data['year'])) {
                            $query->whereYear('closing_date', (int) $data['year']);
                        }
                        if (isset($data['month']) && is_numeric($data['month'])) {
                            $monthNum = (int) $data['month'];
                            if ($monthNum >= 1 && $monthNum <= 12) {
                                $query->whereMonth('closing_date', $monthNum);
                            }
                        }
                        return $query;
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if (isset($data['year']) && $data['year'] !== '' && is_numeric($data['year'])) {
                            $indicators[] = 'Closing Year: '.$data['year'];
                        }
                        if (isset($data['month']) && $data['month'] !== '' && is_numeric($data['month'])) {
                            $monthNum = (int) $data['month'];
                            if ($monthNum >= 1 && $monthNum <= 12) {
                                $indicators[] = 'Closing Month: '.Carbon::create()->month($monthNum)->format('F');
                            }
                        }
                        return $indicators;
                    }),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->tooltip('Edit detail pesanan')
                        ->visible(function (Order $record): bool {
                            if ($record->trashed()) {
                                return false;
                            }
                            if ($record->status === OrderStatus::Done) {
                                $user = Auth::user();
                                return $user && method_exists($user, 'hasRole') && $user->hasRole('super_admin');
                            }
                            return true;
                        }),

                    ViewAction::make()
                        ->tooltip('Lihat detail pesanan')
                        ->visible(function (Order $record): bool {
                            if ($record->trashed()) {
                                return true;
                            }
                            if ($record->status === OrderStatus::Done) {
                                $user = Auth::user();
                                return ! ($user && method_exists($user, 'hasRole') && $user->hasRole('super_admin'));
                            }
                            return false;
                        }),

                    RestoreAction::make()
                        ->tooltip('Pulihkan pesanan')
                        ->successNotificationTitle('Pesanan berhasil dipulihkan')
                        ->visible(fn (Order $record): bool => $record->trashed()),

                    DeleteAction::make()
                        ->tooltip('Hapus pesanan')
                        ->visible(fn (Order $record): bool => ! $record->trashed())
                        ->action(function (Order $record) {
                            if ($record->items()->exists()) {
                                Notification::make()
                                    ->danger()
                                    ->title('Penghapusan Gagal')
                                    ->body("Pesanan '{$record->number}' tidak dapat dihapus karena memiliki item terkait.")
                                    ->send();
                                return;
                            }
                            $record->delete();
                            Notification::make()
                                ->success()
                                ->title('Pesanan Dihapus')
                                ->body("Pesanan '{$record->number}' berhasil dihapus.")
                                ->send();
                        }),

                    ForceDeleteAction::make()
                        ->tooltip('Hapus permanen pesanan')
                        ->successNotificationTitle('Pesanan berhasil dihapus permanen')
                        ->modalHeading('Hapus Permanen Pesanan')
                        ->modalDescription('Apakah Anda yakin ingin menghapus pesanan ini secara permanen? Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait.')
                        ->modalSubmitActionLabel('Ya, hapus permanen')
                        ->visible(fn (Order $record): bool => $record->trashed())
                        ->requiresConfirmation()
                        ->action(function (Order $record) {
                            $record->items()->forceDelete();
                            $record->dataPembayaran()->forceDelete();
                            $record->expenses()->forceDelete();
                            $record->forceDelete();
                            Notification::make()
                                ->success()
                                ->title('Pesanan Dihapus Permanen')
                                ->body("Pesanan '{$record->number}' dan semua data terkait telah dihapus secara permanen.")
                                ->send();
                        }),

                    Action::make('Invoice Actions')
                        ->label('Aksi Invoice')
                        ->icon('heroicon-o-document-text')
                        ->color('primary')
                        ->url(fn ($record) => OrderResource::getUrl('invoice', ['record' => $record->id]))
                        ->visible(fn (Order $record): bool => ! $record->trashed()),
                ])
                    ->tooltip('Aksi Pesanan')
                    ->icon('heroicon-m-ellipsis-vertical'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus pesanan yang dipilih')
                        ->modalDescription('Apakah Anda yakin ingin menghapus pesanan yang dipilih? Tindakan ini tidak dapat dibatalkan.')
                        ->modalSubmitActionLabel('Ya, hapus')
                        ->action(function (EloquentCollection $records) {
                            $preventedDeletions = 0;
                            $deletedCount = 0;
                            $preventedOrderNumbers = [];
                            foreach ($records as $record) {
                                if ($record->items()->exists()) {
                                    $preventedDeletions++;
                                    $preventedOrderNumbers[] = $record->number;
                                } else {
                                    $record->delete();
                                    $deletedCount++;
                                }
                            }
                            if ($deletedCount > 0) {
                                Notification::make()
                                    ->success()
                                    ->title('Orders Deleted')
                                    ->body("Successfully deleted {$deletedCount} order(s).")
                                    ->send();
                            }
                            if ($preventedDeletions > 0) {
                                Notification::make()
                                    ->danger()
                                    ->title('Some Deletions Prevented')
                                    ->body("Could not delete {$preventedDeletions} order(s) due to existing items: ".implode(', ', $preventedOrderNumbers))
                                    ->persistent()
                                    ->send();
                            }
                        }),

                    RestoreBulkAction::make(),

                    ForceDeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Permanen Pesanan')
                        ->modalDescription('Apakah Anda yakin ingin menghapus pesanan yang dipilih secara permanen? Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait.')
                        ->modalSubmitActionLabel('Ya, hapus permanen')
                        ->action(function (EloquentCollection $records) {
                            $deletedCount = 0;
                            foreach ($records as $record) {
                                $record->items()->forceDelete();
                                $record->dataPembayaran()->forceDelete();
                                $record->expenses()->forceDelete();
                                $record->forceDelete();
                                $deletedCount++;
                            }
                            Notification::make()
                                ->success()
                                ->title('Pesanan Dihapus Permanen')
                                ->body("Berhasil menghapus {$deletedCount} pesanan secara permanen beserta semua data terkait.")
                                ->send();
                        }),

                    BulkAction::make('updateStatus')
                        ->label('Perbarui Status')
                        ->icon('heroicon-o-check-circle')
                        ->requiresConfirmation()
                        ->modalHeading('Perbarui Status Pesanan')
                        ->modalDescription('Pilih status baru untuk pesanan yang dipilih.')
                        ->form([
                            \Filament\Forms\Components\Select::make('status')
                                ->label('New Status')
                                ->options(OrderStatus::class)
                                ->required(),
                        ])
                        ->action(function (array $data, EloquentCollection $records) {
                            $records->each->update(['status' => $data['status']]);
                            Notification::make()
                                ->title('Orders Status Updated')
                                ->body("The status of {$records->count()} orders has been updated to {$data['status']}.")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    ExportBulkAction::make(),
                ])->label('Aksi Massal'),
            ])
            ->striped()
            ->defaultPaginationPageOption(10)
            ->paginationPageOptions([10, 25, 50])
            ->emptyStateIcon('heroicon-o-document-text')
            ->emptyStateHeading('Tidak Ada Pesanan')
            ->emptyStateDescription('Tidak ada pesanan yang sesuai dengan kriteria Anda. Anda dapat membuat pesanan baru dengan mengklik tombol di bawah ini.')
            ->emptyStateActions([
                Action::make('create')
                    ->label('Buat Pesanan')
                    ->icon('heroicon-o-plus')
                    ->url(OrderResource::getUrl('create'))
                    ->color('primary'),
            ]);
    }
}
