<?php

namespace App\Filament\Pages;

use App\Models\DataPembayaran;
use App\Models\Expense;
use App\Models\ExpenseOps;
use App\Models\Order;
use App\Models\PendapatanLain;
use App\Models\PengeluaranLain;
use Barryvdh\DomPDF\Facade\Pdf;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Exception;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LaporanKeuangan extends Page
{
    use HasPageShield;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected string $view = 'filament.pages.laporan-keuangan';

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    public $transaksi = [];

    public $tanggal_awal;

    public $tanggal_akhir;

    public $total_masuk = 0;

    public $total_keluar = 0;

    public $filter_jenis = [];

    public $filter_status = [];

    public $filter_keyword = '';

    public function mount()
    {
        // Set tanggal awal dan akhir ke bulan berjalan
        $this->tanggal_awal = now()->startOfMonth()->toDateString();
        $this->tanggal_akhir = now()->endOfMonth()->toDateString();
        $this->transaksi = $this->getTransaksiGabungan();
        $this->total_masuk = $this->hitungTotalMasuk();
        $this->total_keluar = $this->hitungTotalKeluar();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('viewProfitLossReport')
                ->label('Laporan L/R')
                ->icon('heroicon-o-document-text')
                ->color('warning')
                ->modalHeading('Preview Laporan Laba Rugi')
                ->modalWidth('7xl')
                ->modalContent(function () {
                    // Get orders based on current date filters
                    $startDate = $this->tanggal_awal ?? now()->startOfMonth()->toDateString();
                    $endDate = $this->tanggal_akhir ?? now()->endOfMonth()->toDateString();

                    // Query orders using All Event dates (Lamaran, Akad, Reception)
                    $query = Order::with(['prospect', 'dataPembayaran', 'expenses'])
                        ->whereHas('prospect', function ($prospectQuery) use ($startDate, $endDate) {
                            $prospectQuery->where(function ($dateQuery) use ($startDate, $endDate) {
                                // Filter by Lamaran Date
                                $dateQuery->whereBetween('date_lamaran', [$startDate, $endDate])
                                    // OR Filter by Akad Date
                                    ->orWhereBetween('date_akad', [$startDate, $endDate])
                                    // OR Filter by Reception Date
                                    ->orWhereBetween('date_resepsi', [$startDate, $endDate]);
                            });
                        });

                    $orders = $query->get();

                    // --- Sanitize Potential UTF-8 Issues ---
                    foreach ($orders as $order) {
                        if ($order->prospect && ! mb_check_encoding($order->prospect->name_event ?? '', 'UTF-8')) {
                            Log::warning('Malformed UTF-8 in prospect->name_event for Order ID: '.$order->id.' | Data: '.($order->prospect->name_event ?? 'NULL'));
                            $order->prospect->name_event = iconv('UTF-8', 'UTF-8//IGNORE', $order->prospect->name_event ?? '');
                        }
                    }

                    if ($orders->isEmpty()) {
                        Notification::make()
                            ->warning()
                            ->title('Tidak Ada Order')
                            ->body('Tidak ada data order yang memiliki event (Lamaran/Akad/Reception) dalam periode tanggal saat ini untuk membuat laporan L/R.')
                            ->send();

                        return;
                    }

                    // Calculate totals for profit/loss report
                    $totalPaymentsReceived = $orders->sum(function ($order) {
                        return $order->dataPembayaran->sum('nominal');
                    });
                    $totalOrderValue = $orders->sum('grand_total');
                    $totalActualExpenses = $orders->sum(function ($order) {
                        return $order->expenses->sum('amount');
                    });

                    // Get additional expenses data (ExpenseOps and PengeluaranLain)
                    $expenseOps = ExpenseOps::with('vendor')->whereBetween('date_expense', [$startDate, $endDate])
                        ->orderBy('date_expense', 'desc')
                        ->get();

                    $pengeluaranLain = PengeluaranLain::with('vendor')->whereBetween('date_expense', [$startDate, $endDate])
                        ->orderBy('date_expense', 'desc')
                        ->get();

                    // Get pendapatan lain data
                    $pendapatanLain = PendapatanLain::with('vendor')->whereBetween('tgl_bayar', [$startDate, $endDate])
                        ->orderBy('tgl_bayar', 'desc')
                        ->get();

                    $totalExpenseOps = $expenseOps->sum('amount');
                    $totalPengeluaranLain = $pengeluaranLain->sum('amount');
                    $totalPendapatanLain = $pendapatanLain->sum('nominal');

                    // Net profit calculation (grand_total - actual expenses)
                    $netProfitCalculation = $totalOrderValue - $totalActualExpenses;

                    // Prepare data for the PDF view
                    $reportData = [
                        'orders' => $orders,
                        'totalIncome' => $totalPaymentsReceived,
                        'totalExpenses' => $totalOrderValue, // Grand Total column
                        'sumAllOrdersPengeluaran' => $totalActualExpenses,
                        'netProfit' => $netProfitCalculation,
                        // Additional expenses data
                        'expenseOps' => $expenseOps,
                        'pengeluaranLain' => $pengeluaranLain,
                        'pendapatanLain' => $pendapatanLain,
                        'totalExpenseOps' => $totalExpenseOps,
                        'totalPengeluaranLain' => $totalPengeluaranLain,
                        'totalPendapatanLain' => $totalPendapatanLain,
                        'filterStartDate' => $startDate,
                        'filterEndDate' => $endDate,
                        'generatedDate' => now()->format('d M Y H:i'),
                    ];

                    // Return web preview view
                    return view('filament.components.profit-loss-preview', $reportData);
                })
                ->tooltip('Preview laporan Laba Rugi berdasarkan tanggal event (Lamaran/Akad/Reception) dari filter saat ini.')
                ->visible(function () {
                    $user = Auth::user();

                    // Hanya super_admin dan Finance yang bisa melihat tombol ini
                    return $user && ($user->roles->contains('name', 'super_admin') || $user->roles->contains('name', 'Finance'));
                }),
        ];
    }

    public function downloadPdfReport()
    {
        // Log untuk debugging
        Log::info('downloadPdfReport method called', [
            'tanggal_awal' => $this->tanggal_awal,
            'tanggal_akhir' => $this->tanggal_akhir,
            'filter_jenis' => $this->filter_jenis,
        ]);
        // Get orders based on current date filters
        $startDate = $this->tanggal_awal ?? now()->startOfMonth()->toDateString();
        $endDate = $this->tanggal_akhir ?? now()->endOfMonth()->toDateString();

        $query = Order::with(['prospect', 'dataPembayaran', 'expenses'])
            ->whereHas('prospect', function ($prospectQuery) use ($startDate, $endDate) {
                $prospectQuery->where(function ($dateQuery) use ($startDate, $endDate) {
                    $dateQuery->whereBetween('date_lamaran', [$startDate, $endDate])
                        ->orWhereBetween('date_akad', [$startDate, $endDate])
                        ->orWhereBetween('date_resepsi', [$startDate, $endDate]);
                });
            });

        $orders = $query->get();

        foreach ($orders as $order) {
            if ($order->prospect && ! mb_check_encoding($order->prospect->name_event ?? '', 'UTF-8')) {
                Log::warning('Malformed UTF-8 in prospect->name_event for Order ID: '.$order->id);
                $order->prospect->name_event = iconv('UTF-8', 'UTF-8//IGNORE', $order->prospect->name_event ?? '');
            }
        }

        if ($orders->isEmpty()) {
            Notification::make()
                ->warning()
                ->title('Tidak Ada Data')
                ->body('Tidak ada data untuk di-download.')
                ->send();

            return;
        }

        $filters = is_array($this->filter_jenis) ? $this->filter_jenis : (array) $this->filter_jenis;
        $hasFilter = ! empty($filters);

        $includeMasukWedding = ! $hasFilter || in_array('Masuk (Wedding)', $filters);
        $includeKeluarWedding = ! $hasFilter || in_array('Keluar (Wedding)', $filters);
        $includeOps = ! $hasFilter || in_array('Keluar (Operasional)', $filters);
        $includePendapatanLain = ! $hasFilter || in_array('Masuk (Lain-lain)', $filters);
        $includePengeluaranLain = ! $hasFilter || in_array('Keluar (Lain-lain)', $filters);

        $totalPaymentsReceived = $includeMasukWedding
            ? $orders->sum(function ($order) {
                return $order->dataPembayaran->sum('nominal');
            })
            : 0;

        $totalOrderValue = $includeMasukWedding ? $orders->sum('grand_total') : 0;

        $totalActualExpenses = $includeKeluarWedding
            ? $orders->sum(function ($order) {
                return $order->expenses->sum('amount');
            })
            : 0;

        $expenseOps = $includeOps
            ? ExpenseOps::with('vendor')->whereBetween('date_expense', [$startDate, $endDate])
                ->orderBy('date_expense', 'desc')->get()
            : collect([]);

        $pengeluaranLain = $includePengeluaranLain
            ? PengeluaranLain::with('vendor')->whereBetween('date_expense', [$startDate, $endDate])
                ->orderBy('date_expense', 'desc')->get()
            : collect([]);

        $pendapatanLain = $includePendapatanLain
            ? PendapatanLain::with('vendor')->whereBetween('tgl_bayar', [$startDate, $endDate])
                ->orderBy('tgl_bayar', 'desc')->get()
            : collect([]);

        $totalExpenseOps = $expenseOps->sum('amount');
        $totalPengeluaranLain = $pengeluaranLain->sum('amount');
        $totalPendapatanLain = $pendapatanLain->sum('nominal');
        $netProfitCalculation = $totalOrderValue - $totalActualExpenses;

        $reportData = [
            'orders' => $orders,
            'totalIncome' => $totalPaymentsReceived,
            'totalExpenses' => $totalOrderValue,
            'sumAllOrdersPengeluaran' => $totalActualExpenses,
            'netProfit' => $netProfitCalculation,
            'expenseOps' => $expenseOps,
            'pengeluaranLain' => $pengeluaranLain,
            'pendapatanLain' => $pendapatanLain,
            'totalExpenseOps' => $totalExpenseOps,
            'totalPengeluaranLain' => $totalPengeluaranLain,
            'totalPendapatanLain' => $totalPendapatanLain,
            'filterStartDate' => $startDate,
            'filterEndDate' => $endDate,
            'generatedDate' => now()->format('d M Y H:i'),
            'filterJenis' => $filters,
            'includeMasukWedding' => $includeMasukWedding,
            'includeKeluarWedding' => $includeKeluarWedding,
        ];

        try {
            // Generate PDF
            $pdf = Pdf::loadView('pdf.profit_loss_report', $reportData);
            $pdfOutput = $pdf->output();
            $fileName = 'laporan_laba_rugi_'.now()->format('YmdHis').'.pdf';

            // Log untuk debugging
            Log::info('PDF generated successfully', [
                'filename' => $fileName,
                'size_bytes' => strlen($pdfOutput),
                'size_kb' => round(strlen($pdfOutput) / 1024, 2),
            ]);

            // Encode ke base64
            $base64Content = base64_encode($pdfOutput);

            Log::info('Base64 encoding completed', [
                'original_size' => strlen($pdfOutput),
                'encoded_size' => strlen($base64Content),
            ]);

            // Use JavaScript to trigger download dengan multiple dispatch methods
            $this->dispatch('downloadPdf', [
                'content' => $base64Content,
                'filename' => $fileName,
            ]);

            // Juga coba dengan JS langsung untuk memastikan event ter-trigger
            $this->js("
                console.log('PHP: Dispatching downloadPdf event for file: $fileName');
                console.log('PHP: Content length: ".strlen($base64Content)."');
                
                // Try multiple approaches
                if (window.handlePdfDownload) {
                    console.log('PHP: Calling handlePdfDownload directly');
                    window.handlePdfDownload({
                        content: '$base64Content',
                        filename: '$fileName'
                    });
                } else {
                    console.log('PHP: handlePdfDownload not available, using event dispatch');
                    window.dispatchEvent(new CustomEvent('downloadPdf', {
                        detail: {
                            content: '$base64Content',
                            filename: '$fileName'
                        }
                    }));
                }
            ");

            Notification::make()
                ->success()
                ->title('PDF Berhasil Dibuat')
                ->body('File sedang didownload: '.$fileName.' ('.round(strlen($pdfOutput) / 1024, 2).' KB)')
                ->send();

        } catch (Exception $e) {
            Log::error('PDF generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Gagal membuat PDF: '.$e->getMessage())
                ->send();
        }
    }

    // Method untuk testing - dapat dihapus setelah testing selesai
    public function testDownloadPdf()
    {
        Log::info('testDownloadPdf called');

        // Set tanggal ke periode yang ada datanya
        $this->tanggal_awal = '2025-10-01';
        $this->tanggal_akhir = '2025-10-31';

        // Call method download
        $this->downloadPdfReport();

        Log::info('testDownloadPdf completed');
    }

    // Method untuk test langsung tanpa modal
    public function debugDownloadPdf()
    {
        try {
            Log::info('debugDownloadPdf started');

            // Set test data
            $this->tanggal_awal = '2025-10-01';
            $this->tanggal_akhir = '2025-10-31';

            // Generate simple test PDF
            $testData = [
                'orders' => collect([]),
                'totalIncome' => 0,
                'totalExpenses' => 0,
                'sumAllOrdersPengeluaran' => 0,
                'netProfit' => 0,
                'expenseOps' => collect([]),
                'pengeluaranLain' => collect([]),
                'totalExpenseOps' => 0,
                'totalPengeluaranLain' => 0,
                'filterStartDate' => '2025-10-01',
                'filterEndDate' => '2025-10-31',
                'generatedDate' => now()->format('d M Y H:i'),
            ];

            $pdf = Pdf::loadView('pdf.profit_loss_report', $testData);
            $pdfOutput = $pdf->output();
            $fileName = 'test_laporan_laba_rugi_'.now()->format('YmdHis').'.pdf';

            // Langsung return file untuk download
            return response()->streamDownload(function () use ($pdfOutput) {
                echo $pdfOutput;
            }, $fileName, [
                'Content-Type' => 'application/pdf',
            ]);

        } catch (Exception $e) {
            Log::error('debugDownloadPdf failed: '.$e->getMessage());

            Notification::make()
                ->danger()
                ->title('Debug PDF Error')
                ->body('Error: '.$e->getMessage())
                ->send();
        }
    }

    public function getTransaksiGabungan(): Collection
    {
        $start = $this->tanggal_awal;
        $end = $this->tanggal_akhir;

        $uangMasuk = DataPembayaran::whereBetween('tgl_bayar', [$start, $end])
            ->select(
                DB::raw('tgl_bayar as tanggal'),
                DB::raw('nominal as jumlah'),
                DB::raw('"Masuk (Wedding)" as jenis'),
                DB::raw('keterangan as deskripsi'),
                DB::raw('order_id'),
                DB::raw('(
                    SELECT name_event 
                    FROM prospects 
                    WHERE prospects.id = (
                        SELECT prospect_id 
                        FROM orders 
                        WHERE orders.id = data_pembayarans.order_id
                        LIMIT 1
                    ) 
                    LIMIT 1
                ) as prospect_name'),
                DB::raw('NULL as vendor_name'), // Kolom vendor_name untuk konsistensi union
                DB::raw('(
                    SELECT CONCAT(name, " (", no_rekening, ")")
                    FROM payment_methods
                    WHERE payment_methods.id = data_pembayarans.payment_method_id
                    LIMIT 1
                ) as payment_method_details')
            );

        $pengeluaranWedding = Expense::whereBetween('date_expense', [$start, $end])
            ->select(
                DB::raw('date_expense as tanggal'),
                DB::raw('amount as jumlah'),
                DB::raw('"Keluar (Wedding)" as jenis'),
                DB::raw('note as deskripsi'),
                DB::raw('order_id'),
                DB::raw('(
                    SELECT name_event 
                    FROM prospects 
                    WHERE prospects.id = (
                        SELECT prospect_id 
                        FROM orders 
                        WHERE orders.id = expenses.order_id
                        LIMIT 1
                    ) 
                    LIMIT 1
                ) as prospect_name'), // Mengambil prospect_name dari order terkait
                DB::raw('(
                    SELECT name 
                    FROM vendors 
                    WHERE vendors.id = expenses.vendor_id
                    LIMIT 1
                ) as vendor_name'), // Menambahkan nama vendor
                DB::raw('(
                    SELECT CONCAT(name, " (", no_rekening, ")")
                    FROM payment_methods
                    WHERE payment_methods.id = expenses.payment_method_id
                    LIMIT 1
                ) as payment_method_details')
            );

        $pengeluaranOps = ExpenseOps::whereBetween('date_expense', [$start, $end])
            ->select(
                DB::raw('date_expense as tanggal'),
                DB::raw('amount as jumlah'),
                DB::raw('"Keluar (Operasional)" as jenis'),
                DB::raw('note as deskripsi'),
                DB::raw('NULL as order_id'),
                DB::raw('NULL as prospect_name'), // ExpenseOps tidak memiliki prospect_name
                DB::raw('name as vendor_name'), // Menggunakan name dari ExpenseOps sebagai vendor_name
                DB::raw('(
                    SELECT CONCAT(name, " (", no_rekening, ")")
                    FROM payment_methods
                    WHERE payment_methods.id = expense_ops.payment_method_id
                    LIMIT 1
                ) as payment_method_details')
            );

        $pendapatanLain = PendapatanLain::whereBetween('tgl_bayar', [$start, $end])
            ->select(
                DB::raw('tgl_bayar as tanggal'),
                DB::raw('nominal as jumlah'),
                DB::raw('"Masuk (Lain-lain)" as jenis'),
                DB::raw('keterangan as deskripsi'),
                DB::raw('NULL as order_id'),
                DB::raw('NULL as prospect_name'),
                DB::raw('NULL as vendor_name'),
                DB::raw('(
                    SELECT CONCAT(name, " (", no_rekening, ")")
                    FROM payment_methods
                    WHERE payment_methods.id = pendapatan_lains.payment_method_id
                    LIMIT 1
                ) as payment_method_details')
            );

        $pengeluaranLain = PengeluaranLain::whereBetween('date_expense', [$start, $end])
            ->select(
                DB::raw('date_expense as tanggal'),
                DB::raw('amount as jumlah'),
                DB::raw('"Keluar (Lain-lain)" as jenis'),
                DB::raw('note as deskripsi'),
                DB::raw('NULL as order_id'),
                DB::raw('NULL as prospect_name'),
                DB::raw('NULL as vendor_name'),
                DB::raw('(
                    SELECT CONCAT(name, " (", no_rekening, ")")
                    FROM payment_methods
                    WHERE payment_methods.id = pengeluaran_lains.payment_method_id
                    LIMIT 1
                ) as payment_method_details')
            );

        $all = $uangMasuk
            ->unionAll($pendapatanLain)
            ->unionAll($pengeluaranWedding)
            ->unionAll($pengeluaranOps)
            ->unionAll($pengeluaranLain);

        $data = $all->orderBy('tanggal', 'desc')->get();

        // Hitung saldo berjalan dengan urutan yang benar (dari tanggal terlama ke terbaru)
        $dataUrut = $data->sortBy('tanggal');
        $saldo = 0;

        $dataUrut = $dataUrut->map(function ($item) use (&$saldo) {
            if (str_contains($item->jenis, 'Masuk')) {
                $saldo += $item->jumlah;
            } else {
                $saldo -= $item->jumlah;
            }
            $item->saldo = $saldo;

            return $item;
        });

        // Kembalikan ke urutan desc untuk tampilan
        return $dataUrut->sortByDesc('tanggal')->values();
    }

    public function updated($propertyName)
    {
        // Tidak perlu filter otomatis pada update, gunakan filter() saja
    }

    public function filter()
    {
        $transaksi = $this->getTransaksiGabungan();

        // Filter jenis transaksi (mendukung multiple selection)
        if (! empty($this->filter_jenis)) {
            $transaksi = $transaksi->filter(function ($item) {
                return in_array($item->jenis, $this->filter_jenis);
            });
        }

        // Filter status khusus untuk transaksi Wedding
        if (! empty($this->filter_status)) {
            $transaksi = $transaksi->filter(function ($item) {
                // Hanya filter status untuk transaksi Wedding
                if (str_contains($item->jenis, 'Wedding') && isset($item->order_id)) {
                    $orderStatus = $this->getOrderStatus($item->order_id);

                    return $orderStatus && in_array($orderStatus, $this->filter_status);
                }

                // Untuk transaksi non-Wedding, tidak ada filter status
                return true;
            });
        }

        // Filter keyword di deskripsi dan nama prospect/event
        if ($this->filter_keyword) {
            $transaksi = $transaksi->filter(function ($item) {
                $keyword = $this->filter_keyword;

                // Cek apakah keyword ada di kolom deskripsi
                $inDeskripsi = stripos($item->deskripsi, $keyword) !== false;

                // Cek apakah keyword ada di kolom prospect_name (jika tidak kosong)
                $inProspect = ! empty($item->prospect_name) && stripos($item->prospect_name, $keyword) !== false;

                // Cek apakah keyword ada di kolom vendor_name (jika tidak kosong)
                $inVendor = ! empty($item->vendor_name) && stripos($item->vendor_name, $keyword) !== false;

                // Cek apakah keyword ada di kolom payment_method_details
                $inPaymentMethod = ! empty($item->payment_method_details) && stripos($item->payment_method_details, $keyword) !== false;

                return $inDeskripsi || $inProspect || $inVendor || $inPaymentMethod;
            });
        }

        // Hitung ulang saldo berjalan setelah filter diterapkan
        $this->transaksi = $this->hitungSaldoBerjalan($transaksi);
        $this->total_masuk = $this->hitungTotalMasuk();
        $this->total_keluar = $this->hitungTotalKeluar();
    }

    public function resetFilters()
    {
        $this->filter_jenis = [];
        $this->filter_status = [];
        $this->filter_keyword = '';
        $this->tanggal_awal = now()->startOfMonth()->toDateString();
        $this->tanggal_akhir = now()->endOfMonth()->toDateString();

        $this->filter();
    }

    public function hitungTotalMasuk()
    {
        return collect($this->transaksi)
            ->filter(function ($item) {
                return str_contains($item->jenis, 'Masuk');
            })
            ->sum('jumlah');
    }

    public function hitungTotalKeluar()
    {
        return collect($this->transaksi)
            ->filter(function ($item) {
                return str_contains($item->jenis, 'Keluar');
            })
            ->sum('jumlah');
    }

    public function hitungSaldoBerjalan($transaksi)
    {
        // Urutkan berdasarkan tanggal dari terlama ke terbaru
        $dataUrut = $transaksi->sortBy('tanggal');
        $saldo = 0;

        $dataUrut = $dataUrut->map(function ($item) use (&$saldo) {
            if (str_contains($item->jenis, 'Masuk')) {
                $saldo += $item->jumlah;
            } else {
                $saldo -= $item->jumlah;
            }
            $item->saldo = $saldo;

            return $item;
        });

        // Kembalikan ke urutan desc untuk tampilan
        return $dataUrut->sortByDesc('tanggal')->values();
    }

    public function downloadPdf()
    {
        // Cek jumlah data sebelum download
        $currentData = collect($this->transaksi);
        $dataCount = $currentData->count();

        // Beri peringatan jika data terlalu banyak
        if ($dataCount > 1000) {
            session()->flash('warning', 'Data yang akan di-download sangat banyak ('.$dataCount.' record). PDF akan dibatasi maksimal 1000 record teratas. Gunakan filter yang lebih spesifik untuk hasil yang lebih baik.');
        } elseif ($dataCount > 500) {
            session()->flash('info', 'Data yang akan di-download cukup banyak ('.$dataCount.' record). Proses generate PDF mungkin membutuhkan waktu lebih lama.');
        }

        // Buat URL dengan parameter untuk download
        $params = [
            'tanggal_awal' => $this->tanggal_awal,
            'tanggal_akhir' => $this->tanggal_akhir,
            'filter_jenis' => $this->filter_jenis,
            'filter_keyword' => $this->filter_keyword,
        ];

        $url = route('laporan-keuangan.download-pdf', $params);

        // Menggunakan JavaScript untuk membuka link download
        $this->dispatch('download-pdf', url: $url);
    }

    // Static method untuk handle download dari route
    public static function handleDownloadPdf(Request $request)
    {
        // Buat instance baru dari class ini
        $instance = new static;

        // Set parameter dari request
        $instance->tanggal_awal = $request->get('tanggal_awal', now()->startOfMonth()->toDateString());
        $instance->tanggal_akhir = $request->get('tanggal_akhir', now()->endOfMonth()->toDateString());
        $instance->filter_jenis = $request->get('filter_jenis', []);
        $instance->filter_keyword = $request->get('filter_keyword', '');

        // Dapatkan data berdasarkan filter yang sedang aktif
        $transaksi = $instance->getTransaksiGabungan();

        // Filter jenis transaksi (mendukung multiple selection)
        if (! empty($instance->filter_jenis)) {
            $transaksi = $transaksi->filter(function ($item) use ($instance) {
                return in_array($item->jenis, $instance->filter_jenis);
            });
        }

        // Filter keyword di deskripsi dan nama prospect/event
        if ($instance->filter_keyword) {
            $transaksi = $transaksi->filter(function ($item) use ($instance) {
                $keyword = $instance->filter_keyword;

                // Cek apakah keyword ada di kolom deskripsi
                $inDeskripsi = stripos($item->deskripsi, $keyword) !== false;

                // Cek apakah keyword ada di kolom prospect_name (jika tidak kosong)
                $inProspect = ! empty($item->prospect_name) && stripos($item->prospect_name, $keyword) !== false;

                // Cek apakah keyword ada di kolom payment_method_details
                $inPaymentMethod = ! empty($item->payment_method_details) && stripos($item->payment_method_details, $keyword) !== false;

                return $inDeskripsi || $inProspect || $inPaymentMethod;
            });
        }

        // Hitung ulang saldo berjalan setelah filter diterapkan dengan method yang sama
        $transaksi = $instance->hitungSaldoBerjalan($transaksi);

        // Jika data terlalu banyak (lebih dari 500 record), batasi untuk performa
        $totalRecords = $transaksi->count();
        $maxRecords = 1000; // Batas maksimal record untuk PDF

        if ($totalRecords > $maxRecords) {
            // Ambil data terbaru saja
            $transaksi = $transaksi->take($maxRecords);
            $isLimited = true;
        } else {
            $isLimited = false;
        }

        // Hitung total masuk dan keluar dari data yang sudah difilter
        $totalMasuk = $transaksi->filter(function ($item) {
            return str_contains($item->jenis, 'Masuk');
        })->sum('jumlah');

        $totalKeluar = $transaksi->filter(function ($item) {
            return str_contains($item->jenis, 'Keluar');
        })->sum('jumlah');

        $saldoAkhir = $totalMasuk - $totalKeluar;

        // Data untuk PDF
        $data = [
            'transaksi' => $transaksi,
            'tanggal_awal' => $instance->tanggal_awal,
            'tanggal_akhir' => $instance->tanggal_akhir,
            'filter_jenis' => $instance->filter_jenis,
            'filter_keyword' => $instance->filter_keyword,
            'total_masuk' => $totalMasuk,
            'total_keluar' => $totalKeluar,
            'saldo_akhir' => $saldoAkhir,
            'generated_at' => now()->format('d/m/Y H:i:s'),
            'total_records' => $totalRecords,
            'is_limited' => $isLimited,
            'max_records' => $maxRecords,
        ];

        // Generate PDF dengan error handling
        try {
            $pdf = Pdf::loadView('pdf.laporan-keuangan', $data);
            $pdf->setPaper('A4', 'landscape');

            // Set options untuk handle data banyak
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'Noto Sans',
                'isRemoteEnabled' => true,
                'chroot' => public_path(),
                'dpi' => 72, // Turunkan DPI untuk performa lebih baik
            ]);

            // Set memory limit dan max execution time untuk data banyak
            ini_set('memory_limit', '512M');
            ini_set('max_execution_time', 300); // 5 menit

            // Buat nama file yang informatif
            $fileName = 'Laporan_Keuangan_'.
                       str_replace('-', '', $instance->tanggal_awal).'_'.
                       str_replace('-', '', $instance->tanggal_akhir).'_'.
                       now()->format('YmdHis').'.pdf';

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $fileName);

        } catch (Exception $e) {
            // Jika error karena data terlalu banyak, coba dengan data yang lebih sedikit
            if ($totalRecords > 500) {
                $transaksi = $transaksi->take(500);
                $data['transaksi'] = $transaksi;
                $data['is_limited'] = true;
                $data['max_records'] = 500;
                $data['error_message'] = 'Data dibatasi 500 record teratas karena terlalu banyak';

                try {
                    $pdf = Pdf::loadView('pdf.laporan-keuangan', $data);
                    $pdf->setPaper('A4', 'landscape');
                    $pdf->setOptions([
                        'isHtml5ParserEnabled' => true,
                        'isPhpEnabled' => true,
                        'defaultFont' => 'Noto Sans',
                        'dpi' => 72,
                    ]);

                    $fileName = 'Laporan_Keuangan_Limited_'.
                               str_replace('-', '', $instance->tanggal_awal).'_'.
                               str_replace('-', '', $instance->tanggal_akhir).'_'.
                               now()->format('YmdHis').'.pdf';

                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, $fileName);

                } catch (Exception $e2) {
                    // Jika masih error, return error response
                    return response()->json([
                        'error' => 'Gagal generate PDF: '.$e2->getMessage(),
                        'message' => 'Data terlalu banyak untuk di-generate. Silakan gunakan filter yang lebih spesifik.',
                    ], 500);
                }
            }

            // Return error response untuk error lainnya
            return response()->json([
                'error' => 'Gagal generate PDF: '.$e->getMessage(),
                'message' => 'Terjadi kesalahan saat membuat PDF. Silakan coba lagi.',
            ], 500);
        }
    }

    protected function getOrderStatus($orderId)
    {
        if (! $orderId) {
            return null;
        }

        $order = Order::find($orderId);

        return $order && $order->status ? $order->status->value : null;
    }
}
