<?php

namespace App\Filament\Resources\AccountManagerTargets;

use App\Filament\Resources\AccountManagerTargets\Pages\CreateAccountManagerTarget;
use App\Filament\Resources\AccountManagerTargets\Pages\ListAccountManagerTargets;
use App\Filament\Resources\AccountManagerTargets\Widgets\AmOverview;
use App\Filament\Resources\AccountManagerTargets\Widgets\AmPerformanceChart;
use App\Filament\Resources\AccountManagerTargets\Widgets\TopPerformersWidget;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\AccountManagerTarget;
use App\Models\LeaveRequest;
use App\Models\Order;
use App\Models\Payroll;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class AccountManagerTargetResource extends Resource
{
    protected static ?string $model = AccountManagerTarget::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-flag';

    protected static string|\UnitEnum|null $navigationGroup = 'Penjualan';

    protected static ?string $navigationLabel = 'Target Manajer Akun';

    protected static ?string $modelLabel = 'Target Account Manager';

    protected static ?string $pluralModelLabel = 'Target Account Manager';

    /**
     * Check if user can access this resource
     */
    public static function canAccess(): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        // Check if user has super_admin or Account Manager role
        $roleNames = $user->roles->pluck('name');

        return $roleNames->contains('super_admin') || $roleNames->contains('Account Manager');
    }

    /**
     * Check if user can view any records
     */
    public static function canViewAny(): bool
    {
        return static::canAccess();
    }

    /**
     * Check if user can view specific record
     */
    public static function canView(Model $record): bool
    {
        return static::canAccess();
    }

    /**
     * Check if user can create records
     */
    public static function canCreate(): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        // Only super_admin can create
        return $user->roles->where('name', 'super_admin')->count() > 0;
    }

    /**
     * Check if user can edit records
     */
    public static function canEdit(Model $record): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        // Only super_admin can edit
        return $user->roles->where('name', 'super_admin')->count() > 0;
    }

    /**
     * Check if user can delete records
     */
    public static function canDelete(Model $record): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        // Only super_admin can delete
        return $user->roles->where('name', 'super_admin')->count() > 0;
    }

    /**
     * Get the Eloquent query builder for the resource
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['user'])
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc');

        $user = Auth::user();

        // If user is Account Manager, only show their own targets
        if ($user) {
            $isAccountManager = $user->roles->where('name', 'Account Manager')->count() > 0;
            $isSuperAdmin = $user->roles->where('name', 'super_admin')->count() > 0;

            if ($isAccountManager && ! $isSuperAdmin) {
                $query->where('user_id', $user->id);
            }
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name', function (Builder $query) {
                        return $query->whereHas('roles', function ($q) {
                            $q->where('name', 'Account Manager');
                        });
                    })
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('year')
                    ->options(function () {
                        $currentYear = Carbon::now()->year;
                        $years = [];
                        for ($i = -2; $i <= 3; $i++) {
                            $year = $currentYear + $i;
                            $years[$year] = $year;
                        }

                        return $years;
                    })
                    ->default(Carbon::now()->year)
                    ->required(),
                Select::make('month')
                    ->options(function () {
                        $months = [];
                        for ($m = 1; $m <= 12; $m++) {
                            $months[$m] = Carbon::createFromDate(null, $m, 1)->format('F');
                        }

                        return $months;
                    })
                    ->required(),
                TextInput::make('target_amount')
                    ->required()
                    ->numeric()
                    ->prefix('IDR')
                    ->default(1000000000.00)
                    ->placeholder('1.000.000.000'),
                TextInput::make('achieved_amount')
                    ->numeric()
                    ->prefix('IDR')
                    ->default(0.00)
                    ->readOnly()
                    ->helperText('Otomatis dihitung dari orders'),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'achieved' => 'Achieved',
                        'failed' => 'Failed',
                        'overachieved' => 'Overachieved',
                    ])
                    ->default('pending')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Account Manager')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('year')
                    ->label('Tahun')
                    ->sortable(),
                TextColumn::make('month')
                    ->label('Bulan (Angka)')
                    ->sortable(),
                TextColumn::make('month_name')
                    ->label('Nama Bulan')
                    ->getStateUsing(function ($record) {
                        return Carbon::createFromDate(null, $record->month, 1)->format('F');
                    }),
                TextColumn::make('target_amount')
                    ->label('Target')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('achieved_amount')
                    ->label('Pencapaian')
                    ->money('IDR')
                    ->sortable()
                    ->action(function ($record) {
                        // Redirect ke OrderResource dengan filter
                        $url = OrderResource::getUrl('index', [
                            'tableFilters' => [
                                'team' => [
                                    'user_id' => $record->user_id,
                                ],
                                'closing_date_filter' => [
                                    'year' => $record->year,
                                    'month' => $record->month,
                                ],
                            ],
                        ]);

                        return Redirect::to($url);
                    })
                    ->color('primary')
                    ->tooltip('Klik untuk melihat detail order yang berkontribusi pada pencapaian ini'),

                TextColumn::make('order_count')
                    ->label('Jumlah Order')
                    ->getStateUsing(function ($record) {
                        return Order::where('user_id', $record->user_id)
                            ->whereNotNull('closing_date')
                            ->whereYear('closing_date', $record->year)
                            ->whereMonth('closing_date', $record->month)
                            ->where('total_price', '>', 0)
                            ->count();
                    })
                    ->badge()
                    ->color('info')
                    ->alignCenter()
                    ->sortable(false)
                    ->tooltip('Jumlah order yang berkontribusi pada pencapaian ini'),
                TextColumn::make('achievement_percentage')
                    ->label('Persentase (%)')
                    ->getStateUsing(function ($record) {
                        if ($record->target_amount > 0) {
                            return round(($record->achieved_amount / $record->target_amount) * 100, 2);
                        }

                        return 0;
                    })
                    ->suffix('%'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(function ($record) {
                        if ($record->target_amount > 0) {
                            $percentage = ($record->achieved_amount / $record->target_amount) * 100;

                            if ($percentage >= 100) {
                                return 'Achieved';
                            }
                            if ($percentage >= 75) {
                                return 'On Track';
                            }
                            if ($percentage >= 50) {
                                return 'Behind';
                            }

                            return 'Failed';
                        }

                        return 'Failed';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Achieved' => 'success',
                        'On Track' => 'warning',
                        'Behind' => 'danger',
                        'Failed' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('year', 'desc')
            ->filters([
                TrashedFilter::make(),

                SelectFilter::make('user_id')
                    ->relationship('user', 'name', function (Builder $query) {
                        $query->whereHas('roles', function ($q) {
                            $q->where('name', 'Account Manager');
                        });

                        $user = Auth::user();
                        // If user is Account Manager (not super_admin), only show themselves
                        if ($user) {
                            $isAccountManager = $user->roles->where('name', 'Account Manager')->count() > 0;
                            $isSuperAdmin = $user->roles->where('name', 'super_admin')->count() > 0;

                            if ($isAccountManager && ! $isSuperAdmin) {
                                $query->where('id', $user->id);
                            }
                        }

                        return $query;
                    })
                    ->label('Account Manager')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('year')
                    ->options(function () {
                        $currentYear = Carbon::now()->year;
                        $years = [];

                        // Mulai dari 2024 sampai tahun sekarang + 1 tahun ke depan
                        for ($year = 2024; $year <= ($currentYear + 1); $year++) {
                            $years[$year] = $year;
                        }

                        return $years;
                    })
                    ->label('Tahun'),

                SelectFilter::make('month')
                    ->options(function () {
                        $months = [];
                        for ($m = 1; $m <= 12; $m++) {
                            $months[$m] = Carbon::createFromDate(null, $m, 1)->format('F');
                        }

                        return $months;
                    })
                    ->label('Bulan'),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('edit_target')
                        ->label('Edit Target')
                        ->icon('heroicon-o-pencil')
                        ->color('warning')
                        ->visible(function (): bool {
                            $user = Auth::user();

                            return $user && $user->roles->where('name', 'super_admin')->count() > 0;
                        })
                        ->schema([
                            TextInput::make('target_amount')
                                ->label('Target Amount')
                                ->numeric()
                                ->prefix('IDR')
                                ->required()
                                ->placeholder('1.000.000.000'),
                        ])
                        ->fillForm(fn (AccountManagerTarget $record): array => [
                            'target_amount' => $record->target_amount,
                        ])
                        ->action(function (array $data, AccountManagerTarget $record): void {
                            $record->update([
                                'target_amount' => $data['target_amount'],
                            ]);

                            Notification::make()
                                ->title('Target updated successfully')
                                ->success()
                                ->send();
                        }),

                    RestoreAction::make(),
                    ForceDeleteAction::make(),

                    Action::make('refresh_data')
                        ->label('Sync dari Order')
                        ->icon('heroicon-o-arrow-path')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Sync Data dari Order')
                        ->modalDescription('Sinkronkan achieved_amount dan status berdasarkan data Order terbaru.')
                        ->action(function (AccountManagerTarget $record) {
                            // Hitung achieved amount berdasarkan Order menggunakan total_price
                            $achieved = Order::where('user_id', $record->user_id)
                                ->whereNotNull('closing_date')
                                ->whereYear('closing_date', $record->year)
                                ->whereMonth('closing_date', $record->month)
                                ->sum('total_price') ?? 0;

                            // Hitung status berdasarkan pencapaian
                            $targetAmount = $record->target_amount;
                            $status = 'pending';

                            if ($achieved >= $targetAmount) {
                                $status = 'achieved';
                            } elseif ($achieved >= ($targetAmount * 0.8)) {
                                $status = 'on_track';
                            } elseif ($achieved > 0) {
                                $status = 'behind';
                            }

                            $record->update([
                                'achieved_amount' => $achieved,
                                'status' => $status,
                            ]);

                            Notification::make()
                                ->title('Data berhasil disinkronkan')
                                ->body('Achieved amount: '.number_format($achieved, 0, ',', '.').' | Status: '.$status)
                                ->success()
                                ->send();
                        }),

                    Action::make('view_orders')
                        ->label('Lihat Order')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->modalHeading(fn (AccountManagerTarget $record) => 'Order untuk '.$record->user->name.' - '.
                            Carbon::createFromDate(null, $record->month, 1)->format('F').' '.$record->year
                        )
                        ->modalContent(function (AccountManagerTarget $record) {
                            $orders = Order::where('user_id', $record->user_id)
                                ->whereNotNull('closing_date')
                                ->whereYear('closing_date', $record->year)
                                ->whereMonth('closing_date', $record->month)
                                ->with('prospect')
                                ->get();

                            if ($orders->isEmpty()) {
                                return view('filament.components.empty-state')
                                    ->with('message', 'Tidak ada order untuk periode ini');
                            }

                            return view('filament.components.order-list', compact('orders'));
                        })
                        ->modalWidth('7xl')
                        ->modalCancelActionLabel('Tutup')
                        ->modalSubmitAction(false),

                    Action::make('generate_report')
                        ->label('Preview Report')
                        ->icon('heroicon-o-document-text')
                        ->color('info')
                        ->visible(function (AccountManagerTarget $record) {
                            $user = Auth::user();
                            // Super admin dapat melihat semua report
                            if ($user && $user->roles->where('name', 'super_admin')->count() > 0) {
                                return true;
                            }

                            // User biasa hanya bisa melihat report mereka sendiri
                            return $record->user_id === $user->id;
                        })
                        ->modalHeading(fn (AccountManagerTarget $record) => 'Preview Report - '.$record->user->name.' ('.
                            Carbon::createFromDate(null, $record->month, 1)->format('F').' '.$record->year.')'
                        )
                        ->modalContent(function (AccountManagerTarget $record) {
                            $user = Auth::user();

                            // Double check authorization
                            $isSuperAdmin = $user && $user->roles->where('name', 'super_admin')->count() > 0;
                            if (! $isSuperAdmin && $record->user_id !== $user->id) {
                                return '<div style="text-align: center; padding: 40px; color: #ef4444; font-family: sans-serif;">
                                    <div style="font-size: 18px; margin-bottom: 10px;">🚫 Akses Ditolak</div>
                                    <div>Anda tidak memiliki akses untuk melihat report ini.</div>
                                </div>';
                            }
                            // Get Account Manager user data
                            $accountManager = User::with(['roles'])->find($record->user_id);

                            // Get orders data for the period
                            $orders = Order::where('user_id', $record->user_id)
                                ->whereNotNull('closing_date')
                                ->whereYear('closing_date', $record->year)
                                ->whereMonth('closing_date', $record->month)
                                ->with(['prospect'])
                                ->get();

                            // Calculate sales statistics
                            $totalRevenue = $orders->sum('total_price');
                            $totalOrders = $orders->count();
                            $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

                            // Get payroll data
                            $payrollData = null;
                            if (class_exists('\App\Models\Payroll')) {
                                $payrollData = Payroll::where('user_id', $record->user_id)
                                    ->where('period_year', $record->year)
                                    ->where('period_month', $record->month)
                                    ->first();
                            }

                            // Get leave data
                            $leaveData = collect();
                            if (class_exists('\App\Models\LeaveRequest')) {
                                $leaveData = LeaveRequest::where('user_id', $record->user_id)
                                    ->where(function ($query) use ($record) {
                                        $query->whereYear('start_date', $record->year)
                                            ->whereMonth('start_date', $record->month);
                                    })
                                    ->orWhere(function ($query) use ($record) {
                                        $query->whereYear('end_date', $record->year)
                                            ->whereMonth('end_date', $record->month);
                                    })
                                    ->with('leaveType')
                                    ->get();
                            }

                            $achievementPercentage = $record->target_amount > 0 ? ($totalRevenue / $record->target_amount) * 100 : 0;

                            return view('filament.components.account-manager-report-preview', [
                                'accountManager' => $accountManager,
                                'target' => $record,
                                'orders' => $orders,
                                'payrollData' => $payrollData,
                                'leaveData' => $leaveData,
                                'year' => $record->year,
                                'month' => $record->month,
                                'monthName' => Carbon::createFromDate(null, $record->month, 1)->format('F'),
                                'totalRevenue' => $totalRevenue,
                                'totalOrders' => $totalOrders,
                                'averageOrderValue' => $averageOrderValue,
                                'achievementPercentage' => $achievementPercentage,
                            ]);
                        })
                        ->modalWidth('7xl')
                        ->modalSubmitAction(
                            Action::make('download')
                                ->label('Download HTML')
                                ->icon('heroicon-o-arrow-down-tray')
                                ->color('success')
                                ->action(function (AccountManagerTarget $record) {
                                    $user = Auth::user();
                                    $isSuperAdmin = $user && $user->roles->where('name', 'super_admin')->count() > 0;

                                    // Authorization check untuk download
                                    if (! $isSuperAdmin && $record->user_id !== $user->id) {
                                        Notification::make()
                                            ->title('Akses Ditolak')
                                            ->body('Anda tidak memiliki akses untuk mendownload report ini.')
                                            ->danger()
                                            ->send();

                                        return;
                                    }

                                    return redirect()->route('account-manager.report.html', [
                                        'userId' => $record->user_id,
                                        'year' => $record->year,
                                        'month' => $record->month,
                                    ]);
                                })
                        )
                        ->modalCancelActionLabel('Tutup')
                        ->tooltip('Preview laporan sebelum download'),
                ])
                    ->label('Actions')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->size('sm')
                    ->color('gray')
                    ->button(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),

                    BulkAction::make('refresh_all')
                        ->label('Sync All Selected')
                        ->icon('heroicon-o-arrow-path')
                        ->requiresConfirmation()
                        ->modalHeading('Sync Selected Records')
                        ->modalDescription('Sinkronkan achieved_amount dan status untuk semua record yang dipilih berdasarkan data Order terbaru.')
                        ->action(function ($records) {
                            $syncedCount = 0;

                            foreach ($records as $record) {
                                // Hitung achieved amount berdasarkan Order menggunakan total_price
                                $achieved = Order::where('user_id', $record->user_id)
                                    ->whereNotNull('closing_date')
                                    ->whereYear('closing_date', $record->year)
                                    ->whereMonth('closing_date', $record->month)
                                    ->sum('total_price') ?? 0;

                                // Hitung status berdasarkan pencapaian
                                $targetAmount = $record->target_amount;
                                $status = 'pending';

                                if ($achieved >= $targetAmount) {
                                    $status = 'achieved';
                                } elseif ($achieved >= ($targetAmount * 0.8)) {
                                    $status = 'on_track';
                                } elseif ($achieved > 0) {
                                    $status = 'behind';
                                }

                                $record->update([
                                    'achieved_amount' => $achieved,
                                    'status' => $status,
                                ]);

                                $syncedCount++;
                            }

                            Notification::make()
                                ->title('Semua record berhasil disinkronkan')
                                ->body("{$syncedCount} record telah diperbarui dengan data Order terbaru.")
                                ->success()
                                ->send();
                        }),
                ]),
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
            'index' => ListAccountManagerTargets::route('/'),
            'create' => CreateAccountManagerTarget::route('/create'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            AmOverview::class,
            AmPerformanceChart::class,
            TopPerformersWidget::class,
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
