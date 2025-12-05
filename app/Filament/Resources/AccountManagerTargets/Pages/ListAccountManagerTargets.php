<?php

namespace App\Filament\Resources\AccountManagerTargets\Pages;

use App\Filament\Resources\AccountManagerTargets\AccountManagerTargetResource;
use App\Models\AccountManagerTarget;
use App\Models\LeaveRequest;
use App\Models\Order;
use App\Models\Payroll;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ListAccountManagerTargets extends ListRecords
{
    protected static string $resource = AccountManagerTargetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate_monthly_report')
                ->label('Generate Report Bulanan')
                ->icon('heroicon-o-document-chart-bar')
                ->color('success')
                ->schema([
                    Select::make('user_id')
                        ->label('Account Manager')
                        ->options(function () {
                            $user = Auth::user();
                            $isSuperAdmin = $user && $user->roles->where('name', 'super_admin')->count() > 0;

                            if ($isSuperAdmin) {
                                // Super admin bisa pilih semua Account Manager
                                return User::whereHas('roles', function ($query) {
                                    $query->where('name', 'Account Manager');
                                })->pluck('name', 'id');
                            } else {
                                // User biasa hanya bisa pilih diri sendiri
                                return [$user->id => $user->name];
                            }
                        })
                        ->required()
                        ->searchable()
                        ->placeholder('Pilih Account Manager'),

                    Select::make('year')
                        ->label('Tahun')
                        ->options(function () {
                            $years = [];
                            for ($year = 2024; $year <= Carbon::now()->year; $year++) {
                                $years[$year] = $year;
                            }

                            return $years;
                        })
                        ->default(Carbon::now()->year)
                        ->required(),

                    Select::make('month')
                        ->label('Bulan')
                        ->options([
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                        ])
                        ->default(Carbon::now()->month)
                        ->required(),
                ])
                ->modalHeading(function (): string {
                    return 'Generate Report Bulanan Account Manager';
                })
                ->modalDescription('Pilih Account Manager dan periode untuk preview report lengkap pencapaian sales, data payroll, dan riwayat cuti.')
                ->modalSubmitActionLabel('Preview Report')
                ->modalWidth('lg')
                ->action(function (array $data): void {
                    // Validasi data yang diperlukan
                    if (empty($data['user_id']) || empty($data['year']) || empty($data['month'])) {
                        Notification::make()
                            ->title('Data Tidak Lengkap')
                            ->body('Silakan lengkapi semua field yang diperlukan.')
                            ->warning()
                            ->send();

                        return;
                    }

                    // Authorization check
                    $user = Auth::user();
                    $isSuperAdmin = $user && $user->roles->where('name', 'super_admin')->count() > 0;

                    if (! $isSuperAdmin && (int) $data['user_id'] !== $user->id) {
                        Notification::make()
                            ->title('Akses Ditolak')
                            ->body('Anda hanya dapat melihat report Anda sendiri.')
                            ->danger()
                            ->send();

                        return;
                    }

                    // Preview langsung dengan memanggil URL preview
                    $accountManager = User::find($data['user_id']);
                    $monthName = Carbon::createFromDate(null, $data['month'], 1)->format('F');

                    // Generate preview content
                    $previewContent = $this->getReportPreviewContent($data['user_id'], $data['year'], $data['month']);

                    // Show preview in notification
                    Notification::make()
                        ->title('Report Berhasil Digenerate!')
                        ->body('Preview Report - '.($accountManager ? $accountManager->name : 'Unknown').' ('.$monthName.' '.$data['year'].')')
                        ->success()
                        ->actions([
                            Action::make('preview')
                                ->button()
                                ->label('Lihat Preview')
                                ->url(route('account-manager.report.html', [
                                    'userId' => $data['user_id'],
                                    'year' => $data['year'],
                                    'month' => $data['month'],
                                ]))
                                ->openUrlInNewTab(),
                            Action::make('download')
                                ->button()
                                ->label('Download')
                                ->url(route('account-manager.report.html', [
                                    'userId' => $data['user_id'],
                                    'year' => $data['year'],
                                    'month' => $data['month'],
                                ]))
                                ->color('success'),
                        ])
                        ->persistent()
                        ->send();
                }),

            Action::make('autoGenerate')
                ->label('Auto Generate dari Order')
                ->icon('heroicon-o-arrow-path')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Auto Generate Target Account Manager')
                ->modalDescription('Fitur ini akan mengambil data dari Order dan membuat target Account Manager berdasarkan achieved_amount dari data Order yang ada. Data yang sudah ada akan diperbarui. Proses ini akan membuat target untuk semua Account Manager dari tahun 2024 sampai bulan berjalan.')
                ->modalSubmitActionLabel('Generate Sekarang')
                ->action(function () {
                    $this->autoGenerateTargets();
                }),

            CreateAction::make(),
        ];
    }

    public function getTableRecordKey(Model|array $record): string
    {
        // Ensure we always return a valid string key
        return (string) ($record->getKey() ?? $record->id ?? 'unknown');
    }

    /**
     * Auto generate Account Manager Targets berdasarkan data Order
     */
    public function autoGenerateTargets(): void
    {
        try {
            DB::beginTransaction();

            // Ambil semua Account Manager (user dengan role Account Manager)
            $accountManagers = User::whereHas('roles', function ($query) {
                $query->where('name', 'Account Manager');
            })->get();

            // Validasi: pastikan ada Account Manager
            if ($accountManagers->isEmpty()) {
                Notification::make()
                    ->title('Tidak Ada Account Manager!')
                    ->body('Tidak ditemukan user dengan role Account Manager. Pastikan role sudah dibuat dan user sudah di-assign.')
                    ->warning()
                    ->send();

                return;
            }

            // Validasi: pastikan ada data Order yang valid
            $totalValidOrders = Order::whereNotNull('closing_date')
                ->where('total_price', '>', 0)
                ->whereIn('user_id', $accountManagers->pluck('id'))
                ->count();

            if ($totalValidOrders == 0) {
                Notification::make()
                    ->title('Tidak Ada Data Order Valid!')
                    ->body('Tidak ditemukan Order dengan closing_date dan total_price yang valid untuk Account Manager yang ada. Pastikan data Order sudah lengkap.')
                    ->warning()
                    ->send();

                return;
            }

            $generatedCount = 0;
            $updatedCount = 0;
            $totalTargets = 0;

            // Hitung total target yang akan diproses untuk progress
            $currentYear = Carbon::now()->year;
            $startYear = 2024;
            $totalMonths = 0;
            for ($year = $startYear; $year <= $currentYear; $year++) {
                $maxMonth = ($year == $currentYear) ? Carbon::now()->month : 12;
                $totalMonths += $maxMonth;
            }
            $totalTargets = $accountManagers->count() * $totalMonths;

            foreach ($accountManagers as $am) {
                // Generate target untuk semua bulan dari 2024 sampai sekarang
                for ($year = $startYear; $year <= $currentYear; $year++) {
                    $maxMonth = ($year == $currentYear) ? Carbon::now()->month : 12;

                    for ($month = 1; $month <= $maxMonth; $month++) {
                        // Hitung achieved amount dari Orders menggunakan total_price
                        // karena grand_total di database sering bernilai NULL
                        $achievedAmount = Order::where('user_id', $am->id)
                            ->whereNotNull('closing_date')
                            ->whereYear('closing_date', $year)
                            ->whereMonth('closing_date', $month)
                            ->sum('total_price') ?? 0;

                        // Hitung status berdasarkan pencapaian
                        $targetAmount = 1000000000.00; // Default target 1 milyar

                        $status = 'pending';
                        if ($achievedAmount >= $targetAmount) {
                            $status = 'achieved';
                        } elseif ($achievedAmount >= ($targetAmount * 0.8)) {
                            $status = 'on_track';
                        } elseif ($achievedAmount > 0) {
                            $status = 'behind';
                        }

                        // Check apakah record sudah ada (termasuk soft deleted)
                        $existingTarget = AccountManagerTarget::withTrashed()->where([
                            'user_id' => $am->id,
                            'year' => $year,
                            'month' => $month,
                        ])->first();

                        if ($existingTarget) {
                            // Jika target soft deleted, restore dulu
                            if ($existingTarget->trashed()) {
                                $existingTarget->restore();
                            }

                            // Update existing record
                            $existingTarget->update([
                                'target_amount' => $targetAmount,
                                'achieved_amount' => $achievedAmount,
                                'status' => $status,
                            ]);
                            $updatedCount++;
                        } else {
                            // Create new record dengan error handling
                            try {
                                AccountManagerTarget::create([
                                    'user_id' => $am->id,
                                    'year' => $year,
                                    'month' => $month,
                                    'target_amount' => $targetAmount,
                                    'achieved_amount' => $achievedAmount,
                                    'status' => $status,
                                ]);
                                $generatedCount++;
                            } catch (QueryException $e) {
                                // Jika tetap error duplicate, coba update (termasuk soft deleted)
                                if ($e->getCode() == 23000) {
                                    $existingTarget = AccountManagerTarget::withTrashed()->where([
                                        'user_id' => $am->id,
                                        'year' => $year,
                                        'month' => $month,
                                    ])->first();

                                    if ($existingTarget) {
                                        $existingTarget->update([
                                            'target_amount' => $targetAmount,
                                            'achieved_amount' => $achievedAmount,
                                            'status' => $status,
                                        ]);
                                        $updatedCount++;
                                    }
                                } else {
                                    throw $e; // Re-throw jika bukan duplicate error
                                }
                            }
                        }
                    }
                }
            }

            DB::commit();

            // Hitung statistik akhir
            $totalAchieved = AccountManagerTarget::where('achieved_amount', '>', 0)->count();
            $totalRevenue = AccountManagerTarget::sum('achieved_amount');

            Notification::make()
                ->title('Auto Generate Berhasil! 🎉')
                ->body("✅ {$generatedCount} target baru dibuat\n✅ {$updatedCount} target diperbarui\n📊 {$totalAchieved} target memiliki pencapaian\n💰 Total pencapaian: Rp ".number_format($totalRevenue))
                ->success()
                ->send();

        } catch (Exception $e) {
            DB::rollBack();

            Notification::make()
                ->title('Auto Generate Gagal! ❌')
                ->body('Terjadi kesalahan: '.$e->getMessage().'. Silakan coba lagi atau hubungi administrator.')
                ->danger()
                ->send();
        }
    }

    /**
     * Generate comprehensive Account Manager report
     */
    public function generateAccountManagerReport(int $userId, int $year, int $month)
    {
        try {
            // Get Account Manager user data
            $accountManager = User::with(['roles'])->find($userId);

            if (! $accountManager || ! $accountManager->hasRole('Account Manager')) {
                Notification::make()
                    ->title('User tidak ditemukan!')
                    ->body('Account Manager tidak ditemukan atau tidak memiliki role yang sesuai.')
                    ->danger()
                    ->send();

                return;
            }

            // Get target data for the period
            $target = AccountManagerTarget::where('user_id', $userId)
                ->where('year', $year)
                ->where('month', $month)
                ->first();

            // Get orders data for the period
            $orders = Order::where('user_id', $userId)
                ->whereNotNull('closing_date')
                ->whereYear('closing_date', $year)
                ->whereMonth('closing_date', $month)
                ->with(['prospect'])
                ->get();

            // Calculate sales statistics
            $totalRevenue = $orders->sum('total_price');
            $totalOrders = $orders->count();
            $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

            // Get payroll data (assuming we have Payroll model)
            $payrollData = $this->getPayrollData($userId, $year, $month);

            // Get leave data (assuming we have LeaveRequest model)
            $leaveData = $this->getLeaveData($userId, $year, $month);

            // Generate the report view
            return response()->streamDownload(function () use ($accountManager, $target, $orders, $payrollData, $leaveData, $year, $month, $totalRevenue, $totalOrders, $averageOrderValue) {
                echo view('reports.account-manager-report', [
                    'accountManager' => $accountManager,
                    'target' => $target,
                    'orders' => $orders,
                    'payrollData' => $payrollData,
                    'leaveData' => $leaveData,
                    'year' => $year,
                    'month' => $month,
                    'monthName' => Carbon::create()->month($month)->format('F'),
                    'totalRevenue' => $totalRevenue,
                    'totalOrders' => $totalOrders,
                    'averageOrderValue' => $averageOrderValue,
                    'achievementPercentage' => $target ? ($target->target_amount > 0 ? ($totalRevenue / $target->target_amount) * 100 : 0) : 0,
                ])->render();
            }, "AM_Report_{$accountManager->name}_{$year}_{$month}.html", [
                'Content-Type' => 'text/html',
            ]);

        } catch (Exception $e) {
            Notification::make()
                ->title('Error generating report!')
                ->body('Terjadi kesalahan: '.$e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * Get report preview content for modal
     */
    public function getReportPreviewContent(int $userId, int $year, int $month)
    {
        try {
            // Get Account Manager user data
            $accountManager = User::with(['roles'])->find($userId);

            if (! $accountManager || ! $accountManager->hasRole('Account Manager')) {
                return '<div style="text-align: center; padding: 40px; color: #ef4444;">Account Manager tidak ditemukan atau tidak memiliki role yang sesuai.</div>';
            }

            // Get target data for the period
            $target = AccountManagerTarget::where('user_id', $userId)
                ->where('year', $year)
                ->where('month', $month)
                ->first();

            // Get orders data for the period
            $orders = Order::where('user_id', $userId)
                ->whereNotNull('closing_date')
                ->whereYear('closing_date', $year)
                ->whereMonth('closing_date', $month)
                ->with(['prospect'])
                ->get();

            // Calculate sales statistics
            $totalRevenue = $orders->sum('total_price');
            $totalOrders = $orders->count();
            $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

            // Get payroll data
            $payrollData = $this->getPayrollData($userId, $year, $month);

            // Get leave data
            $leaveData = $this->getLeaveData($userId, $year, $month);

            $achievementPercentage = $target ? ($target->target_amount > 0 ? ($totalRevenue / $target->target_amount) * 100 : 0) : 0;

            return view('filament.components.account-manager-report-preview', [
                'accountManager' => $accountManager,
                'target' => $target,
                'orders' => $orders,
                'payrollData' => $payrollData,
                'leaveData' => $leaveData,
                'year' => $year,
                'month' => $month,
                'monthName' => Carbon::createFromDate(null, $month, 1)->format('F'),
                'totalRevenue' => $totalRevenue,
                'totalOrders' => $totalOrders,
                'averageOrderValue' => $averageOrderValue,
                'achievementPercentage' => $achievementPercentage,
            ]);

        } catch (Exception $e) {
            return '<div style="text-align: center; padding: 40px; color: #ef4444;">Terjadi kesalahan: '.$e->getMessage().'</div>';
        }
    }

    /**
     * Get payroll data for the account manager
     */
    private function getPayrollData(int $userId, int $year, int $month)
    {
        // Check if we have Payroll model
        if (class_exists('\App\Models\Payroll')) {
            return Payroll::where('user_id', $userId)
                ->where('period_year', $year)
                ->where('period_month', $month)
                ->first();
        }

        return null;
    }

    /**
     * Get leave data for the account manager
     */
    private function getLeaveData(int $userId, int $year, int $month)
    {
        // Check if we have LeaveRequest model
        if (class_exists('\App\Models\LeaveRequest')) {
            return LeaveRequest::where('user_id', $userId)
                ->where(function ($query) use ($year, $month) {
                    $query->whereYear('start_date', $year)
                        ->whereMonth('start_date', $month);
                })
                ->orWhere(function ($query) use ($year, $month) {
                    $query->whereYear('end_date', $year)
                        ->whereMonth('end_date', $month);
                })
                ->with('leaveType')
                ->get();
        }

        return collect();
    }

    protected function getHeaderWidgets(): array
    {
        return AccountManagerTargetResource::getWidgets();
    }

    /**
     * Stream Account Manager PDF for preview
     */
    public function streamAccountManagerPdf(int $userId, int $year, int $month)
    {
        // Get account manager data
        $accountManager = User::find($userId);
        if (! $accountManager) {
            abort(404, 'Account Manager tidak ditemukan.');
        }

        // Get PDF-specific report data
        $reportData = $this->getPdfReportData($userId, $year, $month);

        // Convert month to name
        $monthName = Carbon::createFromDate($year, $month, 1)->format('F');

        // Generate PDF
        $pdf = Pdf::loadView('account-manager-pdf', [
            'accountManager' => $accountManager,
            'year' => $year,
            'month' => $month,
            'monthName' => $monthName,
            'reportData' => $reportData,
        ]);

        // Stream PDF (show in browser)
        $fileName = 'account_manager_report_'.$accountManager->name.'_'.$monthName.'_'.$year.'.pdf';

        return $pdf->stream($fileName);
    }

    /**
     * Download Account Manager PDF
     */
    public function downloadAccountManagerPdf(int $userId, int $year, int $month)
    {
        // Get account manager data
        $accountManager = User::find($userId);
        if (! $accountManager) {
            abort(404, 'Account Manager tidak ditemukan.');
        }

        // Get PDF-specific report data
        $reportData = $this->getPdfReportData($userId, $year, $month);

        // Convert month to name
        $monthName = Carbon::createFromDate($year, $month, 1)->format('F');

        // Generate PDF
        $pdf = Pdf::loadView('account-manager-pdf', [
            'accountManager' => $accountManager,
            'year' => $year,
            'month' => $month,
            'monthName' => $monthName,
            'reportData' => $reportData,
        ]);

        // Download PDF
        $fileName = 'account_manager_report_'.$accountManager->name.'_'.$monthName.'_'.$year.'.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Get PDF-specific report data
     */
    public function getPdfReportData(int $userId, int $year, int $month)
    {
        // Get account manager
        $accountManager = User::find($userId);
        if (! $accountManager) {
            return null;
        }

        // Get target for the specified period
        $target = AccountManagerTarget::where('user_id', $userId)
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        // Get orders data
        $orders = Order::whereHas('user', function ($query) use ($userId) {
            $query->where('id', $userId);
        })->whereYear('closing_date', $year)
            ->whereMonth('closing_date', $month)
            ->with(['prospect', 'user'])
            ->get();

        // Calculate metrics
        $totalOrders = $orders->count();
        $totalRevenue = $orders->sum('total_price');
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        $achievementPercentage = $target && $target->target_amount > 0
            ? ($totalRevenue / $target->target_amount) * 100
            : 0;

        // Get payroll data
        $payrollData = null;
        if (class_exists(Payroll::class)) {
            $payrollData = Payroll::where('user_id', $userId)
                ->where('period_year', $year)
                ->where('period_month', $month)
                ->first();
        }

        // Get leave data
        $leaveData = collect();
        if (class_exists(LeaveRequest::class)) {
            $leaveData = LeaveRequest::where('user_id', $userId)
                ->where(function ($query) use ($year, $month) {
                    $query->whereYear('start_date', $year)
                        ->whereMonth('start_date', $month);
                })
                ->orWhere(function ($query) use ($year, $month) {
                    $query->whereYear('end_date', $year)
                        ->whereMonth('end_date', $month);
                })
                ->with('leaveType')
                ->get();
        }

        return [
            'accountManager' => $accountManager,
            'target' => $target,
            'orders' => $orders,
            'totalOrders' => $totalOrders,
            'totalRevenue' => $totalRevenue,
            'averageOrderValue' => $averageOrderValue,
            'achievementPercentage' => $achievementPercentage,
            'payrollData' => $payrollData,
            'leaveData' => $leaveData,
        ];
    }
}
