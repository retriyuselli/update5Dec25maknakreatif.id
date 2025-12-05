    {{-- Memuat file CSS khusus untuk halaman laporan keuangan --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/payment/paymentmethod.css') }}">

    <div class="laporan-keuangan-container">
        <!-- Financial Summary Cards -->
        <div class="financial-summary-cards">
            <!-- Saldo Awal -->
            <div class="summary-card saldo-awal-card">
                <div class="card-content">
                    <div class="card-info">
                        <p class="card-label">Saldo Awal</p>
                        <p class="card-amount">
                            Rp {{ number_format($breakdown['saldo_awal'], 0, ',', '.') }}
                        </p>
                        <p class="card-description">
                            Sejak
                            {{ $breakdown['tanggal_pembukuan'] ? \Carbon\Carbon::parse($breakdown['tanggal_pembukuan'])->format('d M Y') : 'pembukaan' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Total Uang Masuk -->
            <div class="summary-card uang-masuk-card">
                <div class="card-content">
                    <div class="card-info">
                        <p class="card-label">Total Uang Masuk</p>
                        <p class="card-amount">
                            Rp {{ number_format($breakdown['uang_masuk']['total'], 0, ',', '.') }}
                        </p>
                        <p class="card-description">
                            Transaksi aktual (tidak termasuk saldo awal)
                        </p>
                    </div>
                </div>
            </div>

            <!-- Total Uang Keluar -->
            <div class="summary-card uang-keluar-card">
                <div class="card-content">
                    <div class="card-info">
                        <p class="card-label">Total Uang Keluar</p>
                        <p class="card-amount">
                            Rp {{ number_format($breakdown['uang_keluar']['total'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Saldo Akhir -->
            <div class="summary-card saldo-akhir-card">
                <div class="card-content">
                    <div class="card-info">
                        <p class="card-label">Saldo Akhir</p>
                        <p class="card-amount">
                            Rp {{ number_format($breakdown['saldo_akhir'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div> <!-- Breakdown Detail -->
        <div class="breakdown-detail-container">
            <!-- Breakdown Pemasukan -->
            <div class="breakdown-card">
                <div class="breakdown-header">
                    <h3 class="breakdown-title">
                        <svg class="breakdown-title-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Breakdown Pemasukan
                    </h3>
                </div>
                <div class="breakdown-content">
                    <div class="breakdown-item breakdown-pemasukan-item">
                        <div class="breakdown-item-info">
                            <div class="breakdown-item-dot" style="background-color: #3b82f6;"></div>
                            <span class="breakdown-item-label">Pembayaran Wedding</span>
                        </div>
                        <span class="breakdown-item-amount">
                            Rp {{ number_format($breakdown['uang_masuk']['wedding'], 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="breakdown-item breakdown-pemasukan-item">
                        <div class="breakdown-item-info">
                            <div class="breakdown-item-dot" style="background-color: #22c55e;"></div>
                            <span class="breakdown-item-label">Pendapatan Lain</span>
                        </div>
                        <span class="breakdown-item-amount">
                            Rp {{ number_format($breakdown['uang_masuk']['lainnya'], 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="breakdown-total">
                        <div class="breakdown-total-content">
                            <span class="breakdown-total-label">Total Pemasukan</span>
                            <span class="breakdown-total-amount" style="color: #16a34a;">
                                Rp {{ number_format($breakdown['uang_masuk']['total'], 0, ',', '.') }}
                            </span>
                        </div>
                        <p class="breakdown-total-note">
                            * Hanya transaksi aktual, tidak termasuk saldo awal
                        </p>
                    </div>
                </div>
            </div>

            <!-- Breakdown Pengeluaran -->
            <div class="breakdown-card">
                <div class="breakdown-header">
                    <h3 class="breakdown-title">
                        <svg class="breakdown-title-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                        </svg>
                        Breakdown Pengeluaran
                    </h3>
                </div>
                <div class="breakdown-content">
                    <div class="breakdown-item breakdown-pengeluaran-item">
                        <div class="breakdown-item-info">
                            <div class="breakdown-item-dot" style="background-color: #ef4444;"></div>
                            <span class="breakdown-item-label">Expense Wedding</span>
                        </div>
                        <span class="breakdown-item-amount">
                            Rp {{ number_format($breakdown['uang_keluar']['wedding'], 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="breakdown-item breakdown-pengeluaran-item">
                        <div class="breakdown-item-info">
                            <div class="breakdown-item-dot" style="background-color: #f97316;"></div>
                            <span class="breakdown-item-label">Expense Operasional</span>
                        </div>
                        <span class="breakdown-item-amount">
                            Rp {{ number_format($breakdown['uang_keluar']['operasional'], 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="breakdown-item breakdown-pengeluaran-item">
                        <div class="breakdown-item-info">
                            <div class="breakdown-item-dot" style="background-color: #a855f7;"></div>
                            <span class="breakdown-item-label">Pengeluaran Lain</span>
                        </div>
                        <span class="breakdown-item-amount">
                            Rp {{ number_format($breakdown['uang_keluar']['lainnya'], 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="breakdown-total">
                        <div class="breakdown-total-content">
                            <span class="breakdown-total-label">Total Pengeluaran</span>
                            <span class="breakdown-total-amount" style="color: #dc2626;">
                                Rp {{ number_format($breakdown['uang_keluar']['total'], 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Trend Chart -->
        <div class="monthly-trend-container">
            <div class="monthly-trend-header">
                <h3 class="monthly-trend-title">
                    <svg class="breakdown-title-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 00-2 2h-2a2 2 0 00-2-2z" />
                    </svg>
                    Trend Keuangan Bulanan
                </h3>
            </div>
            <div class="monthly-trend-content">
                <div class="monthly-trend-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Bulan</th>
                                <th>Wedding Income</th>
                                <th>Pendapatan Lain</th>
                                <th>Total Pemasukan</th>
                                <th>Wedding Expense</th>
                                <th>Expense Ops</th>
                                <th>Pengeluaran Lain</th>
                                <th>Total Pengeluaran</th>
                                <th>Net Income</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($monthlyData as $data)
                                @php
                                    // Calculate breakdown for each month
                                    $monthStart = \Carbon\Carbon::createFromFormat(
                                        'M Y',
                                        $data['month'],
                                    )->startOfMonth();
                                    $monthEnd = $monthStart->copy()->endOfMonth();

                                    // Wedding Income (DataPembayaran)
                                    $weddingIncome = \App\Models\DataPembayaran::where('payment_method_id', $record->id)
                                        ->whereBetween('tgl_bayar', [$monthStart, $monthEnd])
                                        ->whereNull('deleted_at')
                                        ->sum('nominal');

                                    // Pendapatan Lain
                                    $pendapatanLain = \App\Models\PendapatanLain::where(
                                        'payment_method_id',
                                        $record->id,
                                    )
                                        ->whereBetween('tgl_bayar', [$monthStart, $monthEnd])
                                        ->whereNull('deleted_at')
                                        ->sum('nominal');

                                    // Wedding Expense (Expenses from Orders)
                                    $weddingExpense = \App\Models\Expense::where('payment_method_id', $record->id)
                                        ->whereBetween('date_expense', [$monthStart, $monthEnd])
                                        ->whereNull('deleted_at')
                                        ->sum('amount');

                                    // Expense Operasional
                                    $expenseOps = \App\Models\ExpenseOps::where('payment_method_id', $record->id)
                                        ->whereBetween('date_expense', [$monthStart, $monthEnd])
                                        ->whereNull('deleted_at')
                                        ->sum('amount');

                                    // Pengeluaran Lain
                                    $pengeluaranLain = \App\Models\PengeluaranLain::where(
                                        'payment_method_id',
                                        $record->id,
                                    )
                                        ->whereBetween('date_expense', [$monthStart, $monthEnd])
                                        ->whereNull('deleted_at')
                                        ->sum('amount');

                                    // Total untuk verifikasi
                                    $totalIncome = $weddingIncome + $pendapatanLain;
                                    $totalExpense = $weddingExpense + $expenseOps + $pengeluaranLain;

                                    // Hanya tampilkan jika ada transaksi (income > 0 atau expense > 0)
                                    $hasTransactions = $totalIncome > 0 || $totalExpense > 0;
                                @endphp

                                @if ($hasTransactions)
                                    <tr>
                                        <td style="font-weight: 500; color: #1f2937;">
                                            {{ $data['month'] }}
                                        </td>
                                        <!-- Wedding Income -->
                                        <td style="color: #3b82f6; font-size: 0.9em;">
                                            {{ number_format($weddingIncome, 0, ',', '.') }}
                                        </td>
                                        <!-- Pendapatan Lain -->
                                        <td style="color: #22c55e; font-size: 0.9em;">
                                            {{ number_format($pendapatanLain, 0, ',', '.') }}
                                        </td>
                                        <!-- Total Pemasukan -->
                                        <td style="color: #16a34a; font-weight: 600;">
                                            {{ number_format($totalIncome, 0, ',', '.') }}
                                        </td>
                                        <!-- Wedding Expense -->
                                        <td style="color: #ef4444; font-size: 0.9em;">
                                            {{ number_format($weddingExpense, 0, ',', '.') }}
                                        </td>
                                        <!-- Expense Operasional -->
                                        <td style="color: #f97316; font-size: 0.9em;">
                                            {{ number_format($expenseOps, 0, ',', '.') }}
                                        </td>
                                        <!-- Pengeluaran Lain -->
                                        <td style="color: #a855f7; font-size: 0.9em;">
                                            {{ number_format($pengeluaranLain, 0, ',', '.') }}
                                        </td>
                                        <!-- Total Pengeluaran -->
                                        <td style="color: #dc2626; font-weight: 600;">
                                            {{ number_format($totalExpense, 0, ',', '.') }}
                                        </td>
                                        <!-- Net Income -->
                                        <td
                                            style="font-weight: 700; color: {{ $totalIncome - $totalExpense >= 0 ? '#16a34a' : '#dc2626' }};">
                                            {{ $totalIncome - $totalExpense >= 0 ? '+' : '' }}
                                            {{ number_format($totalIncome - $totalExpense, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach

                            @if (collect($monthlyData)->every(function ($data) use ($record) {
                                    $monthStart = \Carbon\Carbon::createFromFormat('M Y', $data['month'])->startOfMonth();
                                    $monthEnd = $monthStart->copy()->endOfMonth();
                            
                                    $totalIncome =
                                        \App\Models\DataPembayaran::where('payment_method_id', $record->id)->whereBetween('tgl_bayar', [$monthStart, $monthEnd])->whereNull('deleted_at')->sum('nominal') +
                                        \App\Models\PendapatanLain::where('payment_method_id', $record->id)->whereBetween('tgl_bayar', [$monthStart, $monthEnd])->whereNull('deleted_at')->sum('nominal');
                            
                                    $totalExpense =
                                        \App\Models\Expense::where('payment_method_id', $record->id)->whereBetween('date_expense', [$monthStart, $monthEnd])->whereNull('deleted_at')->sum('amount') +
                                        \App\Models\ExpenseOps::where('payment_method_id', $record->id)->whereBetween('date_expense', [$monthStart, $monthEnd])->whereNull('deleted_at')->sum('amount') +
                                        \App\Models\PengeluaranLain::where('payment_method_id', $record->id)->whereBetween('date_expense', [$monthStart, $monthEnd])->whereNull('deleted_at')->sum('amount');
                            
                                    return $totalIncome <= 0 && $totalExpense <= 0;
                                }))
                                <tr>
                                    <td colspan="9"
                                        style="text-align: center; color: #6b7280; font-style: italic; padding: 2rem;">
                                        Belum ada transaksi untuk payment method ini
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Financial Health Status dengan Formula Perhitungan -->
        <div class="status-keuangan-container">
            <div class="status-keuangan-header">
                <h3 class="status-keuangan-title">
                    <svg class="breakdown-title-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Status Kesehatan Keuangan & Formula Perhitungan
                </h3>
            </div>
            <div class="status-keuangan-content">
                <!-- Formula Perhitungan -->
                <div class="formula-box">
                    <h4 class="formula-title">📐 Formula Perhitungan Saldo:</h4>
                    <div class="formula-calculation">
                        <div class="formula-row">
                            <span style="color: #6b7280;">Saldo Awal</span>
                            <span style="font-weight: 700;">Rp
                                {{ number_format($breakdown['saldo_awal'], 0, ',', '.') }}</span>
                        </div>
                        <div class="formula-operator plus">+</div>
                        <div class="formula-row">
                            <span style="color: #16a34a;">Total Uang Masuk</span>
                            <span style="font-weight: 700; color: #16a34a;">Rp
                                {{ number_format($breakdown['uang_masuk']['total'], 0, ',', '.') }}</span>
                        </div>
                        <div class="formula-operator minus">-</div>
                        <div class="formula-row">
                            <span style="color: #dc2626;">Total Uang Keluar</span>
                            <span style="font-weight: 700; color: #dc2626;">Rp
                                {{ number_format($breakdown['uang_keluar']['total'], 0, ',', '.') }}</span>
                        </div>
                        <div class="formula-result">
                            <div class="formula-row">
                                <span style="color: #1e40af;">Saldo Akhir</span>
                                <span style="color: #1e40af;">Rp
                                    {{ number_format($breakdown['saldo_akhir'], 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                    <p class="formula-note">
                        * Formula: Saldo Akhir = Saldo Awal + Total Uang Masuk - Total Uang Keluar
                    </p>
                </div>

                <div class="status-grid">
                    <!-- Status Perubahan -->
                    <div class="status-item status-{{ $breakdown['status'] }}">
                        <div class="status-icon">
                            @if ($breakdown['status'] === 'naik')
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            @elseif($breakdown['status'] === 'turun')
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                                </svg>
                            @else
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 12H4" />
                                </svg>
                            @endif
                        </div>
                        <h4 class="status-label">Status Perubahan</h4>
                        <p class="status-value">
                            {{ ucfirst($breakdown['status']) }}
                        </p>
                    </div>

                    <!-- Perubahan Nilai -->
                    <div class="status-item status-blue">
                        <div class="status-icon">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                            </svg>
                        </div>
                        <h4 class="status-label">Perubahan Saldo</h4>
                        <p class="status-value"
                            style="color: {{ $breakdown['perubahan'] >= 0 ? '#16a34a' : '#dc2626' }};">
                            {{ $breakdown['perubahan'] >= 0 ? '+' : '' }}Rp
                            {{ number_format($breakdown['perubahan'], 0, ',', '.') }}
                        </p>
                    </div>

                    <!-- Periode Pembukuan -->
                    <div class="status-item status-indigo">
                        <div class="status-icon">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h4 class="status-label">Periode Pembukuan</h4>
                        <p class="status-value" style="color: #6b7280;">
                            {{ $breakdown['tanggal_pembukuan'] ? \Carbon\Carbon::parse($breakdown['tanggal_pembukuan'])->format('d M Y') : 'Tidak ditentukan' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
