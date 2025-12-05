<?php

namespace App\Filament\Resources\Payrolls\Schemas;

use App\Models\Payroll;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Illuminate\Database\Eloquent\Builder;

class PayrollForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Payroll')
                    ->tabs([
                        Tab::make('Karyawan & Periode')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Section::make('Informasi Karyawan')
                                    ->description('Pilih karyawan dan periode payroll')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                Select::make('user_id')
                                                    ->label('Karyawan')
                                                    ->relationship('user', 'name', function (Builder $query) {
                                                        return $query->with('status')
                                                            ->whereHas('roles', function (Builder $query) {
                                                                $query->where('name', 'Office');
                                                            });
                                                    })
                                                    ->searchable()
                                                    ->preload()
                                                    ->required()
                                                    ->live()
                                                    ->getOptionLabelUsing(function ($value): ?string {
                                                        $user = User::find($value);

                                                        return $user?->name;
                                                    })
                                                    ->getOptionLabelFromRecordUsing(function (User $record): string {
                                                        $statusName = $record->status?->status_name ?? $record->department ?? 'No Status';
                                                        $email = $record->email ? " - {$record->email}" : '';

                                                        return "{$record->name} ({$statusName}){$email}";
                                                    })
                                                    ->helperText('Pilih karyawan dengan role Office yang akan dibuatkan payroll')
                                                    ->columnSpan(2),

                                                Group::make([
                                                    Select::make('period_month')
                                                        ->label('Bulan Periode')
                                                        ->options([
                                                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                                                        ])
                                                        ->default(now()->month)
                                                        ->required()
                                                        ->live()
                                                        ->helperText('Pilih bulan periode payroll'),

                                                    Select::make('period_year')
                                                        ->label('Tahun Periode')
                                                        ->options(function () {
                                                            $currentYear = now()->year;
                                                            $years = [];
                                                            for ($year = $currentYear - 1; $year <= $currentYear + 1; $year++) {
                                                                $years[$year] = $year;
                                                            }

                                                            return $years;
                                                        })
                                                        ->default(now()->year)
                                                        ->required()
                                                        ->live()
                                                        ->helperText('Pilih tahun periode payroll'),
                                                ])
                                                    ->columnSpan(1),
                                            ]),

                                        Placeholder::make('employee_info')
                                            ->label('Info Karyawan')
                                            ->content(function (Get $get): string {
                                                $userId = $get('user_id');
                                                if (! $userId) {
                                                    return 'Pilih karyawan untuk melihat informasi';
                                                }

                                                $user = User::with('status')->find($userId);
                                                if (! $user) {
                                                    return 'Karyawan tidak ditemukan';
                                                }

                                                $hireDate = $user->hire_date?->format('d/m/Y') ?? 'No Date';

                                                $month = $get('period_month');
                                                $year = $get('period_year');

                                                $existingPayroll = null;
                                                if ($month && $year) {
                                                    $existingPayroll = Payroll::where('user_id', $userId)
                                                        ->where('period_month', $month)
                                                        ->where('period_year', $year)
                                                        ->first();
                                                }

                                                $info = "📅 Mulai kerja: {$hireDate}";

                                                if ($existingPayroll) {
                                                    $months = [
                                                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                                                    ];
                                                    $monthName = $months[$month];
                                                    $info .= "\n⚠️ Payroll untuk {$monthName} {$year} sudah ada!";
                                                }

                                                return $info;
                                            })
                                            ->visible(fn (Get $get): bool => (bool) $get('user_id')),
                                    ])->columns(1),
                            ]),

                        Tab::make('Gaji')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Section::make('Informasi Gaji')
                                    ->description('Pengaturan gaji bulanan dan tahunan')
                                    ->schema([
                                        Grid::make(4)
                                            ->schema([
                                                TextInput::make('gaji_pokok')
                                                    ->label('Gaji Pokok')
                                                    ->required()
                                                    ->numeric()
                                                    ->prefix('Rp')
                                                    ->suffixIcon('heroicon-m-currency-dollar')
                                                    ->placeholder('4000000')
                                                    ->extraAttributes(['class' => 'bg-blue-50 text-right'])
                                                    ->stripCharacters(',')
                                                    ->live(onBlur: true)
                                                    ->dehydrateStateUsing(fn ($state): ?float => static::parseCurrencyToFloat($state))
                                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state, $record) {
                                                        $gajiPokok = static::parseCurrencyToFloat($state) ?? 0;
                                                        $tunjangan = static::parseCurrencyToFloat($get('tunjangan')) ?? 0;
                                                        $bonus = static::parseCurrencyToFloat($get('bonus')) ?? 0;
                                                        $pengurangan = static::parseCurrencyToFloat($get('pengurangan')) ?? 0;
                                                        $monthlySalary = ($gajiPokok + $tunjangan + $bonus) - $pengurangan;

                                                        $set('monthly_salary', number_format($monthlySalary, 0, ',', '.'));

                                                        $tempPayroll = new Payroll;
                                                        $tempPayroll->monthly_salary = $monthlySalary;

                                                        $set('annual_salary', number_format($monthlySalary * 12, 0, ',', '.'));
                                                        $set('total_compensation', number_format($monthlySalary * 12, 0, ',', '.'));
                                                    })
                                                    ->helperText('Gaji pokok tanpa tunjangan'),

                                                TextInput::make('tunjangan')
                                                    ->label('Tunjangan')
                                                    ->numeric()
                                                    ->prefix('Rp')
                                                    ->suffixIcon('heroicon-m-plus')
                                                    ->placeholder('1000000')
                                                    ->default(0)
                                                    ->extraAttributes(['class' => 'bg-gray-50 text-right'])
                                                    ->stripCharacters(',')
                                                    ->live(onBlur: true)
                                                    ->dehydrateStateUsing(fn ($state): ?float => static::parseCurrencyToFloat($state))
                                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state, $record) {
                                                        $gajiPokok = static::parseCurrencyToFloat($get('gaji_pokok')) ?? 0;
                                                        $tunjangan = static::parseCurrencyToFloat($state) ?? 0;
                                                        $bonus = static::parseCurrencyToFloat($get('bonus')) ?? 0;
                                                        $pengurangan = static::parseCurrencyToFloat($get('pengurangan')) ?? 0;
                                                        $monthlySalary = $gajiPokok + $tunjangan + $bonus - $pengurangan;

                                                        $set('monthly_salary', number_format($monthlySalary, 0, ',', '.'));

                                                        $tempPayroll = new Payroll;
                                                        $tempPayroll->monthly_salary = $monthlySalary;

                                                        $set('annual_salary', number_format($monthlySalary * 12, 0, ',', '.'));
                                                        $set('total_compensation', number_format($monthlySalary * 12, 0, ',', '.'));
                                                    })
                                                    ->helperText('Tunjangan dan benefit lainnya'),

                                                TextInput::make('pengurangan')
                                                    ->label('Pengurangan')
                                                    ->numeric()
                                                    ->prefix('Rp')
                                                    ->suffixIcon('heroicon-m-minus')
                                                    ->placeholder('BPJS, keterlambatan dan lainnya')
                                                    ->default(0)
                                                    ->extraAttributes(['class' => 'bg-gray-50 text-right'])
                                                    ->live(onBlur: true)
                                                    ->dehydrateStateUsing(fn ($state): ?float => static::parseCurrencyToFloat($state))
                                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state, $record) {
                                                        $gajiPokok = static::parseCurrencyToFloat($get('gaji_pokok')) ?? 0;
                                                        $tunjangan = static::parseCurrencyToFloat($get('tunjangan')) ?? 0;
                                                        $bonus = static::parseCurrencyToFloat($get('bonus')) ?? 0;
                                                        $pengurangan = static::parseCurrencyToFloat($state) ?? 0;
                                                        $monthlySalary = $gajiPokok + $tunjangan + $bonus - $pengurangan;

                                                        $set('monthly_salary', number_format($monthlySalary, 0, ',', '.'));

                                                        $tempPayroll = new Payroll;
                                                        $tempPayroll->monthly_salary = $monthlySalary;

                                                        $set('annual_salary', number_format($monthlySalary * 12, 0, ',', '.'));
                                                        $set('total_compensation', number_format($monthlySalary * 12, 0, ',', '.'));
                                                    })
                                                    ->helperText('BPJS, keterlambatan dan lainnya'),

                                                TextInput::make('monthly_salary')
                                                    ->label('Total Gaji Bulanan')
                                                    ->prefix('Rp')
                                                    ->suffixIcon('heroicon-m-calculator')
                                                    ->readOnly()
                                                    ->dehydrated(false)
                                                    ->mask(RawJs::make('$money($input)'))
                                                    ->afterStateHydrated(function (TextInput $component, $state, $record) {
                                                        if ($record) {
                                                            $monthly = (float) ($record->gaji_pokok ?? 0)
                                                                + (float) ($record->tunjangan ?? 0)
                                                                + (float) ($record->bonus ?? 0)
                                                                - (float) ($record->pengurangan ?? 0);

                                                            $component->state((string) (int) $monthly);
                                                        }
                                                    })
                                                    ->helperText('Otomatis: (Gaji Pokok + Tunjangan + Bonus) - Pengurangan')
                                                    ->extraAttributes(['class' => 'bg-blue-50'])
                                                    ->disabled(),
                                            ]),

                                        Grid::make(1)
                                            ->schema([
                                                TextInput::make('annual_salary')
                                                    ->label('Gaji Tahunan')
                                                    ->prefix('Rp')
                                                    ->suffixIcon('heroicon-m-calendar')
                                                    ->readOnly()
                                                    ->dehydrated(false)
                                                    ->mask(RawJs::make('$money($input)'))
                                                    ->afterStateHydrated(function (TextInput $component, $state, $record) {
                                                        if ($record) {
                                                            $monthly = (float) ($record->gaji_pokok ?? 0)
                                                                + (float) ($record->tunjangan ?? 0)
                                                                + (float) ($record->bonus ?? 0)
                                                                - (float) ($record->pengurangan ?? 0);

                                                            $component->state((string) (int) ($monthly * 12));
                                                        }
                                                    })
                                                    ->helperText('Otomatis dihitung oleh sistem: Gaji Bulanan × 12 bulan')
                                                    ->extraAttributes(['class' => 'bg-gray-50'])
                                                    ->disabled(),
                                            ]),

                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('bonus')
                                                    ->label('Bonus')
                                                    ->numeric()
                                                    ->prefix('Rp')
                                                    ->suffixIcon('heroicon-m-gift')
                                                    ->placeholder('1000000')
                                                    ->default(0)
                                                    ->mask(RawJs::make('$money($input)'))
                                                    ->dehydrateStateUsing(fn ($state): ?float => static::parseCurrencyToFloat($state))
                                                    ->live()
                                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                        $gajiPokok = static::parseCurrencyToFloat($get('gaji_pokok')) ?? 0;
                                                        $tunjangan = static::parseCurrencyToFloat($get('tunjangan')) ?? 0;
                                                        $bonus = static::parseCurrencyToFloat($state) ?? 0;
                                                        $pengurangan = static::parseCurrencyToFloat($get('pengurangan')) ?? 0;
                                                        $monthlySalary = $gajiPokok + $tunjangan + $bonus - $pengurangan;

                                                        $set('monthly_salary', $monthlySalary);

                                                        $tempPayroll = new Payroll;
                                                        $tempPayroll->monthly_salary = $monthlySalary;

                                                        $set('annual_salary', number_format($monthlySalary * 12, 0, ',', '.'));
                                                        $set('total_compensation', number_format($monthlySalary * 12, 0, ',', '.'));
                                                    })
                                                    ->helperText('Bonus bulanan (termasuk dalam gaji bulanan)'),

                                                TextInput::make('total_compensation')
                                                    ->label('Total Kompensasi')
                                                    ->prefix('Rp')
                                                    ->suffixIcon('heroicon-m-calculator')
                                                    ->readOnly()
                                                    ->dehydrated(false)
                                                    ->mask(RawJs::make('$money($input)'))
                                                    ->stripCharacters(',')
                                                    ->live()
                                                    ->afterStateHydrated(function (TextInput $component, $state, $record) {
                                                        if ($record) {
                                                            $monthly = (float) ($record->gaji_pokok ?? 0)
                                                                + (float) ($record->tunjangan ?? 0)
                                                                + (float) ($record->bonus ?? 0)
                                                                - (float) ($record->pengurangan ?? 0);

                                                            $component->state((string) (int) ($monthly * 12));
                                                        }
                                                    })
                                                    ->helperText('Total: Gaji Tahunan + Bonus (dihitung otomatis)')
                                                    ->extraAttributes(['class' => 'bg-gray-50'])
                                                    ->disabled(),
                                            ]),
                                    ]),
                            ]),

                        Tab::make('Review')
                            ->icon('heroicon-o-calendar-days')
                            ->schema([
                                Section::make('Informasi Review')
                                    ->description('Jadwal review gaji dan performa')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                DatePicker::make('last_review_date')
                                                    ->label('Tanggal Review Terakhir')
                                                    ->native(false)
                                                    ->displayFormat('d/m/Y')
                                                    ->helperText('Kapan terakhir kali direview'),

                                                DatePicker::make('next_review_date')
                                                    ->label('Tanggal Review Berikutnya')
                                                    ->native(false)
                                                    ->displayFormat('d/m/Y')
                                                    ->helperText('Jadwal review berikutnya')
                                                    ->afterOrEqual('today'),
                                            ]),

                                        Textarea::make('notes')
                                            ->label('Catatan')
                                            ->placeholder('Catatan tambahan mengenai payroll ini...')
                                            ->rows(3)
                                            ->maxLength(1000)
                                            ->helperText('Catatan internal (maksimal 1000 karakter)'),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    private static function parseCurrencyToFloat(?string $value): float
    {
        if ($value === null) {
            return 0;
        }

        // Hapus semua karakter non-digit (titik/koma ribuan dan simbol)
        $clean = preg_replace('/\D+/', '', $value);

        if ($clean === '' || $clean === null) {
            return 0;
        }

        // Kembalikan sebagai float dari digit utuh Rupiah (tanpa desimal)
        return (float) $clean;
    }
}
