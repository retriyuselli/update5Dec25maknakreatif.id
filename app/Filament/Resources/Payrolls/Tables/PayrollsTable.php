<?php

namespace App\Filament\Resources\Payrolls\Tables;

use App\Models\Payroll;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PayrollsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $user = Auth::user();
                if (! $user) {
                    return;
                }
                if ($user->hasRole('super_admin') || $user->can('ViewAny:Payroll')) {
                    return;
                }
                $query->where('user_id', $user->id);
            })
            ->heading('Data Payroll')
            ->description('Kelola data payroll karyawan. Default menampilkan data bulan berjalan, gunakan filter atau tombol aksi cepat untuk melihat periode lain.')
            ->columns([
                ImageColumn::make('user.avatar_url')
                    ->label('Avatar')
                    ->circular()
                    ->defaultImageUrl(function ($record) {
                        $name = $record->user?->name ?? 'User';
                        $initials = collect(explode(' ', $name))
                            ->map(fn ($word) => strtoupper(substr($word, 0, 1)))
                            ->take(2)
                            ->implode('');

                        return "https://ui-avatars.com/api/?name={$initials}&background=3b82f6&color=ffffff&size=128";
                    }),

                TextColumn::make('user.name')
                    ->label('Nama Karyawan')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->description(fn ($record): string => $record->user?->email ?? ''),

                TextColumn::make('periode')
                    ->label('Periode')
                    ->getStateUsing(fn ($record): string => $record->period_name)
                    ->badge()
                    ->color('primary')
                    ->sortable(['period_year', 'period_month']),

                TextColumn::make('user.status.status_name')
                    ->label('Status Jabatan')
                    ->badge()
                    ->color(function ($state): string {
                        return match ($state) {
                            'Admin' => 'danger',
                            'Finance' => 'warning',
                            'HRD' => 'info',
                            'Account Manager' => 'primary',
                            'Staff' => 'success',
                            default => 'gray',
                        };
                    })
                    ->placeholder('No Status'),

                TextColumn::make('user.department')
                    ->label('Departemen')
                    ->badge()
                    ->color(function ($state): string {
                        return match ($state) {
                            'bisnis' => 'success',
                            'operasional' => 'primary',
                            default => 'gray',
                        };
                    })
                    ->formatStateUsing(function ($state): string {
                        return match ($state) {
                            'bisnis' => 'Bisnis',
                            'operasional' => 'Operasional',
                            default => $state,
                        };
                    }),

                TextColumn::make('gaji_pokok')
                    ->label('Gaji Pokok')
                    ->money('IDR')
                    ->sortable()
                    ->weight(FontWeight::Medium)
                    ->color('info')
                    ->placeholder('Rp 0'),

                TextColumn::make('tunjangan')
                    ->label('Tunjangan')
                    ->money('IDR')
                    ->sortable()
                    ->weight(FontWeight::Medium)
                    ->color('warning')
                    ->placeholder('Rp 0'),

                TextColumn::make('pengurangan')
                    ->label('Pengurangan')
                    ->money('IDR')
                    ->sortable()
                    ->weight(FontWeight::Medium)
                    ->color('danger')
                    ->placeholder('Rp 0')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('monthly_salary')
                    ->label('Total Gaji')
                    ->money('IDR')
                    ->sortable()
                    ->weight(FontWeight::SemiBold)
                    ->color('success')
                    ->description(function ($record): string {
                        $gajiPokok = number_format($record->gaji_pokok ?? 0, 0, ',', '.');
                        $tunjangan = number_format($record->tunjangan ?? 0, 0, ',', '.');
                        $bonus = number_format($record->bonus ?? 0, 0, ',', '.');
                        $pengurangan = number_format($record->pengurangan ?? 0, 0, ',', '.');

                        return "({$gajiPokok} + {$tunjangan} + {$bonus}) - {$pengurangan}";
                    }),

                TextColumn::make('annual_salary')
                    ->label('Gaji Tahunan')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('bonus')
                    ->label('Bonus')
                    ->money('IDR')
                    ->sortable()
                    ->placeholder('Rp 0')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('total_compensation')
                    ->label('Total Kompensasi')
                    ->money('IDR')
                    ->sortable()
                    ->getStateUsing(function ($record): float {
                        return $record->total_compensation; // Menggunakan accessor dari model
                    })
                    ->weight(FontWeight::Bold)
                    ->color('success'),

                TextColumn::make('last_review_date')
                    ->label('Review Terakhir')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('Belum pernah')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('next_review_date')
                    ->label('Review Berikutnya')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('Belum dijadwalkan')
                    ->color(function ($record): string {
                        if (! $record->next_review_date) {
                            return 'gray';
                        }
                        $nextReview = $record->next_review_date;
                        $daysUntil = now()->diffInDays($nextReview, false);

                        if ($daysUntil < 0) {
                            return 'danger';
                        } // Overdue
                        if ($daysUntil <= 7) {
                            return 'warning';
                        } // Soon

                        return 'success'; // Good
                    })
                    ->badge(function ($record): bool {
                        if (! $record->next_review_date) {
                            return false;
                        }
                        $daysUntil = now()->diffInDays($record->next_review_date, false);

                        return $daysUntil <= 7; // Show badge if within 7 days
                    }),

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
                SelectFilter::make('user')
                    ->label('Karyawan')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('department')
                    ->label('Departemen')
                    ->options([
                        'bisnis' => 'Bisnis',
                        'operasional' => 'Operasional',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (isset($data['value']) && $data['value']) {
                            return $query->whereHas('user', function (Builder $query) use ($data) {
                                $query->where('department', $data['value']);
                            });
                        }

                        return $query;
                    }),

                SelectFilter::make('status')
                    ->label('Status Jabatan')
                    ->relationship('user.status', 'status_name')
                    ->preload(),

                Filter::make('monthly_salary_range')
                    ->label('Range Gaji Bulanan')
                    ->schema([
                        TextInput::make('monthly_salary_from')
                            ->label('Dari')
                            ->numeric()
                            ->prefix('Rp'),
                        TextInput::make('monthly_salary_to')
                            ->label('Sampai')
                            ->numeric()
                            ->prefix('Rp'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['monthly_salary_from'],
                                fn (Builder $query, $salary): Builder => $query->where('monthly_salary', '>=', $salary),
                            )
                            ->when(
                                $data['monthly_salary_to'],
                                fn (Builder $query, $salary): Builder => $query->where('monthly_salary', '<=', $salary),
                            );
                    }),

                Filter::make('review_due')
                    ->label('Review Mendekati')
                    ->query(fn (Builder $query): Builder => $query->whereDate('next_review_date', '<=', now()->addDays(30)))
                    ->toggle(),

                SelectFilter::make('period_month')
                    ->label('Bulan Periode')
                    ->placeholder('Semua bulan')
                    ->options([
                        '1' => 'Januari',
                        '2' => 'Februari',
                        '3' => 'Maret',
                        '4' => 'April',
                        '5' => 'Mei',
                        '6' => 'Juni',
                        '7' => 'Juli',
                        '8' => 'Agustus',
                        '9' => 'September',
                        '10' => 'Oktober',
                        '11' => 'November',
                        '12' => 'Desember',
                    ])
                    ->default(now()->month)
                    ->query(function (Builder $query, array $data): Builder {
                        if (isset($data['value']) && $data['value']) {
                            return $query->where('period_month', $data['value']);
                        }

                        return $query;
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['value']) {
                            return null;
                        }

                        $months = [
                            '1' => 'Januari', '2' => 'Februari', '3' => 'Maret', '4' => 'April',
                            '5' => 'Mei', '6' => 'Juni', '7' => 'Juli', '8' => 'Agustus',
                            '9' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
                        ];

                        return 'Bulan: '.$months[$data['value']];
                    }),

                SelectFilter::make('period_year')
                    ->label('Tahun Periode')
                    ->placeholder('Semua tahun')
                    ->options(function () {
                        $currentYear = now()->year;
                        $years = [];
                        for ($year = $currentYear - 2; $year <= $currentYear + 1; $year++) {
                            $years[$year] = $year;
                        }

                        return $years;
                    })
                    ->default(now()->year)
                    ->query(function (Builder $query, array $data): Builder {
                        if (isset($data['value']) && $data['value']) {
                            return $query->where('period_year', $data['value']);
                        }

                        return $query;
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['value']) {
                            return null;
                        }

                        return 'Tahun: '.$data['value'];
                    }),
            ])
            ->headerActions([
                Action::make('current_month')
                    ->label('Bulan Ini')
                    ->icon('heroicon-o-calendar')
                    ->color('primary')
                    ->action(function () {
                        return redirect()->route('filament.admin.resources.payrolls.index', [
                            'tableFilters' => [
                                'period_month' => ['value' => now()->month],
                                'period_year' => ['value' => now()->year],
                            ],
                        ]);
                    }),

                Action::make('last_month')
                    ->label('Bulan Lalu')
                    ->icon('heroicon-o-arrow-left')
                    ->color('gray')
                    ->action(function () {
                        $lastMonth = now()->subMonth();

                        return redirect()->route('filament.admin.resources.payrolls.index', [
                            'tableFilters' => [
                                'period_month' => ['value' => $lastMonth->month],
                                'period_year' => ['value' => $lastMonth->year],
                            ],
                        ]);
                    }),

                Action::make('two_months_ago')
                    ->label('2 Bulan Lalu')
                    ->icon('heroicon-o-arrow-left')
                    ->color('gray')
                    ->action(function () {
                        $twoMonthsAgo = now()->subMonths(2);

                        return redirect()->route('filament.admin.resources.payrolls.index', [
                            'tableFilters' => [
                                'period_month' => ['value' => $twoMonthsAgo->month],
                                'period_year' => ['value' => $twoMonthsAgo->year],
                            ],
                        ]);
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label('Lihat')
                        ->color('info'),

                    EditAction::make()
                        ->label('Edit')
                        ->color('warning'),

                    Action::make('salary_raise')
                        ->label('Kenaikan Gaji')
                        ->icon('heroicon-o-arrow-trending-up')
                        ->color('success')
                        ->visible(function () {
                            $user = Auth::user();

                            return $user && $user->can('Update:Payroll');
                        })
                        ->schema([
                            Select::make('raise_type')
                                ->label('Jenis Kenaikan')
                                ->options([
                                    'percentage' => 'Persentase (%)',
                                    'amount' => 'Nominal (Rp)',
                                ])
                                ->required()
                                ->live(),

                            TextInput::make('raise_value')
                                ->label('Nilai Kenaikan')
                                ->required()
                                ->numeric()
                                ->suffix(fn (Get $get): string => $get('raise_type') === 'percentage' ? '%' : '')
                                ->prefix(fn (Get $get): string => $get('raise_type') === 'amount' ? 'Rp ' : ''),

                            Textarea::make('raise_reason')
                                ->label('Alasan Kenaikan')
                                ->placeholder('Contoh: Promosi, review tahunan, kinerja excellent')
                                ->required(),
                        ])
                        ->action(function (array $data, $record): void {
                            $user = Auth::user();
                            if (! $user || ! $user->can('Update:Payroll')) {
                                Notification::make()
                                    ->title('Akses Ditolak')
                                    ->body('Anda tidak memiliki izin untuk melakukan kenaikan gaji.')
                                    ->danger()
                                    ->send();

                                return;
                            }
                            $currentSalary = $record->monthly_salary;

                            if ($data['raise_type'] === 'percentage') {
                                $newSalary = $currentSalary * (1 + ($data['raise_value'] / 100));
                            } else {
                                $newSalary = $currentSalary + $data['raise_value'];
                            }

                            $record->update([
                                'monthly_salary' => $newSalary,
                                'last_review_date' => now(),
                                'next_review_date' => now()->addYear(),
                                'notes' => ($record->notes ? $record->notes."\n\n" : '').
                                          '['.now()->format('d/m/Y').'] Kenaikan gaji: '.
                                          'Rp '.number_format($currentSalary, 0, ',', '.').' → '.
                                          'Rp '.number_format($newSalary, 0, ',', '.').
                                          ' ('.$data['raise_reason'].')',
                            ]);

                            Notification::make()
                                ->title('Kenaikan Gaji Berhasil')
                                ->body("Gaji {$record->user->name} berhasil dinaikkan menjadi Rp ".number_format($newSalary, 0, ',', '.'))
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Kenaikan Gaji Karyawan')
                        ->modalDescription('Pastikan data kenaikan gaji sudah benar sebelum menyimpan.')
                        ->modalSubmitActionLabel('Terapkan Kenaikan'),

                    Action::make('slip_gaji')
                        ->label('Slip Gaji')
                        ->icon('heroicon-o-document-text')
                        ->color('primary')
                        ->url(fn ($record) => route('payroll.slip-gaji.download', $record))
                        ->openUrlInNewTab(),

                    Action::make('duplicate')
                        ->label('Duplikasi')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('info')
                        ->visible(function () {
                            $user = Auth::user();

                            return $user && $user->can('Replicate:Payroll');
                        })
                        ->schema([
                            Select::make('target_month')
                                ->label('Bulan Tujuan')
                                ->options([
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                                ])
                                ->default(function ($record) {
                                    $nextMonth = $record->period_month + 1;

                                    return $nextMonth > 12 ? 1 : $nextMonth;
                                })
                                ->required()
                                ->live()
                                ->helperText('Pilih bulan untuk payroll baru'),

                            Select::make('target_year')
                                ->label('Tahun Tujuan')
                                ->options(function () {
                                    $currentYear = now()->year;
                                    $years = [];
                                    for ($year = $currentYear - 1; $year <= $currentYear + 2; $year++) {
                                        $years[$year] = $year;
                                    }

                                    return $years;
                                })
                                ->default(function ($record) {
                                    return $record->period_month == 12 ? $record->period_year + 1 : $record->period_year;
                                })
                                ->required()
                                ->live()
                                ->helperText('Pilih tahun untuk payroll baru'),

                            Toggle::make('copy_bonus')
                                ->label('Salin Bonus')
                                ->default(false)
                                ->helperText('Apakah bonus ikut disalin? (biasanya bonus tidak rutin setiap bulan)'),

                            Toggle::make('reset_review_dates')
                                ->label('Reset Tanggal Review')
                                ->default(true)
                                ->helperText('Reset tanggal review terakhir dan berikutnya'),

                            Textarea::make('duplicate_notes')
                                ->label('Catatan Duplikasi')
                                ->placeholder('Catatan tambahan untuk payroll yang diduplikasi...')
                                ->rows(3)
                                ->helperText('Catatan opsional untuk payroll baru'),
                        ])
                        ->action(function (array $data, $record): void {
                            $user = Auth::user();
                            if (! $user || ! $user->can('Replicate:Payroll')) {
                                Notification::make()
                                    ->title('Akses Ditolak')
                                    ->body('Anda tidak memiliki izin untuk menduplikasi payroll.')
                                    ->danger()
                                    ->send();

                                return;
                            }
                            // Check if payroll already exists for target period
                            $existingPayroll = Payroll::where('user_id', $record->user_id)
                                ->where('period_month', $data['target_month'])
                                ->where('period_year', $data['target_year'])
                                ->first();

                            if ($existingPayroll) {
                                $months = [
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                                ];
                                $monthName = $months[$data['target_month']];

                                Notification::make()
                                    ->title('Duplikasi Gagal')
                                    ->body("Payroll untuk {$record->user->name} pada {$monthName} {$data['target_year']} sudah ada!")
                                    ->danger()
                                    ->send();

                                return;
                            }

                            // Create duplicate payroll
                            $newPayroll = $record->replicate();
                            $newPayroll->period_month = $data['target_month'];
                            $newPayroll->period_year = $data['target_year'];

                            // Handle bonus
                            if (! $data['copy_bonus']) {
                                $newPayroll->bonus = 0;
                            }

                            // Handle review dates
                            if ($data['reset_review_dates']) {
                                $newPayroll->last_review_date = null;
                                $newPayroll->next_review_date = null;
                            }

                            // Add duplicate notes
                            $originalPeriod = $record->period_name;
                            $duplicateNote = '['.now()->format('d/m/Y H:i')."] Diduplikasi dari payroll {$originalPeriod}";

                            if ($data['duplicate_notes']) {
                                $newPayroll->notes = $duplicateNote."\n\n".$data['duplicate_notes'];
                            } else {
                                $newPayroll->notes = $duplicateNote;
                            }

                            // Save new payroll
                            $newPayroll->save();

                            $months = [
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                            ];
                            $targetPeriod = $months[$data['target_month']].' '.$data['target_year'];

                            Notification::make()
                                ->title('Duplikasi Berhasil')
                                ->body("Payroll {$record->user->name} berhasil diduplikasi ke periode {$targetPeriod}")
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Duplikasi Payroll')
                        ->modalDescription('Duplikasi akan membuat payroll baru dengan data yang sama untuk periode berbeda.')
                        ->modalSubmitActionLabel('Duplikasi Payroll')
                        ->modalIcon('heroicon-o-document-duplicate'),

                    DeleteAction::make()
                        ->label('Hapus'),
                ])
                    ->label('Aksi')
                    ->color('primary')
                    ->icon('heroicon-o-ellipsis-vertical')
                    ->size('sm')
                    ->button(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus Terpilih'),

                    BulkAction::make('bulk_review_update')
                        ->label('Update Review Massal')
                        ->icon('heroicon-o-calendar')
                        ->color('info')
                        ->form([
                            DatePicker::make('next_review_date')
                                ->label('Tanggal Review Berikutnya')
                                ->required()
                                ->native(false)
                                ->displayFormat('d/m/Y'),
                        ])
                        ->action(function (array $data, $records): void {
                            $count = 0;
                            foreach ($records as $record) {
                                $record->update([
                                    'next_review_date' => $data['next_review_date'],
                                ]);
                                $count++;
                            }

                            Notification::make()
                                ->title('Review Update Berhasil')
                                ->body("Tanggal review untuk {$count} karyawan berhasil diupdate.")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('bulk_duplicate')
                        ->label('Duplikasi Massal')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('success')
                        ->visible(function () {
                            $user = Auth::user();

                            return $user && $user->can('Replicate:Payroll');
                        })
                        ->form([
                            Select::make('target_month')
                                ->label('Bulan Tujuan')
                                ->options([
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                                ])
                                ->default(now()->addMonth()->month)
                                ->required()
                                ->helperText('Pilih bulan untuk payroll baru'),

                            Select::make('target_year')
                                ->label('Tahun Tujuan')
                                ->options(function () {
                                    $currentYear = now()->year;
                                    $years = [];
                                    for ($year = $currentYear; $year <= $currentYear + 2; $year++) {
                                        $years[$year] = $year;
                                    }

                                    return $years;
                                })
                                ->default(now()->addMonth()->year)
                                ->required()
                                ->helperText('Pilih tahun untuk payroll baru'),

                            Toggle::make('copy_bonus')
                                ->label('Salin Bonus')
                                ->default(false)
                                ->helperText('Apakah bonus ikut disalin?'),

                            Toggle::make('skip_existing')
                                ->label('Lewati yang Sudah Ada')
                                ->default(true)
                                ->helperText('Lewati karyawan yang sudah memiliki payroll di periode target'),
                        ])
                        ->action(function (array $data, $records): void {
                            $user = Auth::user();
                            if (! $user || ! $user->can('Replicate:Payroll')) {
                                Notification::make()
                                    ->title('Akses Ditolak')
                                    ->body('Anda tidak memiliki izin untuk melakukan duplikasi massal.')
                                    ->danger()
                                    ->send();

                                return;
                            }
                            $successCount = 0;
                            $skippedCount = 0;
                            $skippedNames = [];

                            foreach ($records as $record) {
                                // Check if payroll already exists
                                $existingPayroll = Payroll::where('user_id', $record->user_id)
                                    ->where('period_month', $data['target_month'])
                                    ->where('period_year', $data['target_year'])
                                    ->first();

                                if ($existingPayroll && $data['skip_existing']) {
                                    $skippedCount++;
                                    $skippedNames[] = $record->user->name;

                                    continue;
                                }

                                if ($existingPayroll && ! $data['skip_existing']) {
                                    $skippedCount++;
                                    $skippedNames[] = $record->user->name.' (sudah ada)';

                                    continue;
                                }

                                // Create duplicate
                                $newPayroll = $record->replicate();
                                $newPayroll->period_month = $data['target_month'];
                                $newPayroll->period_year = $data['target_year'];

                                if (! $data['copy_bonus']) {
                                    $newPayroll->bonus = 0;
                                }

                                // Reset review dates for bulk duplicate
                                $newPayroll->last_review_date = null;
                                $newPayroll->next_review_date = null;

                                // Add bulk duplicate note
                                $originalPeriod = $record->period_name;
                                $newPayroll->notes = '['.now()->format('d/m/Y H:i')."] Diduplikasi secara massal dari payroll {$originalPeriod}";

                                $newPayroll->save();
                                $successCount++;
                            }

                            $months = [
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                            ];
                            $targetPeriod = $months[$data['target_month']].' '.$data['target_year'];

                            $message = "Berhasil menduplikasi {$successCount} payroll ke periode {$targetPeriod}";

                            if ($skippedCount > 0) {
                                $message .= ". Dilewati: {$skippedCount} record";
                                if (count($skippedNames) <= 3) {
                                    $message .= ' ('.implode(', ', $skippedNames).')';
                                }
                            }

                            Notification::make()
                                ->title('Duplikasi Massal Selesai')
                                ->body($message)
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Duplikasi Payroll Massal')
                        ->modalDescription('Duplikasi akan membuat payroll baru untuk semua karyawan terpilih dengan periode yang ditentukan.')
                        ->modalSubmitActionLabel('Duplikasi Semua')
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->striped()
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 100])
            ->defaultSort('created_at', 'desc')
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession()
            ->extremePaginationLinks()
            ->selectCurrentPageOnly()
            ->recordTitleAttribute('user.name')
            ->searchOnBlur()
            ->deferLoading()
            ->emptyStateHeading('Tidak ada data payroll')
            ->emptyStateDescription('Belum ada data payroll untuk periode yang dipilih. Coba ubah filter bulan/tahun atau buat data payroll baru.')
            ->emptyStateIcon('heroicon-o-banknotes');
    }
}
