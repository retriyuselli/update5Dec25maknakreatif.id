<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Laba Rugi #{{ $order->prospect->name_event }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: a4 portrait;
            margin: 1cm 1.5cm 3cm 2cm;
            /* top, right, bottom, left */
        }

        body {
            color: #000000;
            font-family: 'Poppins', Arial, sans-serif;
            font-size: 18px;
            font-weight: 400;
            line-height: 1;
            margin: 0;
            font-smoothing: antialiased;
            padding: 0;
            line-height: 1.4;
            /* atau pertimbangkan 1.5 */
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            max-width: 100%;
        }

        /* Header */
        .header {
            border-bottom: 1px solid #ddd;
            margin-bottom: 10px;
            padding-bottom: 10px;
            text-align: center;
        }

        .header img {
            max-height: 50px;
            width: auto;
            vertical-align: middle;
        }

        .header h2 {
            font-size: 16px;
            font-weight: bold;
            margin: 1px 0;
        }

        .header p {
            font-size: 16px;
            margin: 1px 0;
        }

        /* Table Base */
        table {
            border-collapse: collapse;
            margin-bottom: 5px;
            width: 100%;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        table.bordered th,
        table.bordered td {
            border: 1px solid #ddd;
        }

        /* Profit Loss Specific Styles */
        .profit-row {
            background-color: #d4edda;
            border: 2px solid #28a745;
        }

        .loss-row {
            background-color: #f8d7da;
            border: 2px solid #dc3545;
        }

        .profit-text {
            color: #155724;
            font-weight: bold;
        }

        .loss-text {
            color: #721c24;
            font-weight: bold;
        }

        .analysis-box {
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border: 2px solid #ddd;
        }

        .analysis-box.profit {
            background-color: #f8fff9;
            border-color: #28a745;
        }

        .analysis-box.loss {
            background-color: #fff8f8;
            border-color: #dc3545;
        }

        /* Section Styling */
        .section-container {
            margin: 20px 0;
        }

        .sub-section-title {
            font-size: 1.1em;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            font-weight: bold;
        }

        /* Additional Table Styling */
        .total-row {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        /* Font Size Controls */
        .bordered {
            font-size: 16px;
        }

        /* Invoice Title */
        .invoice-title {
            margin: 10px 0;
            text-align: center;
        }

        .invoice-title h1 {
            font-size: 25px;
            margin-bottom: 0;
            text-transform: uppercase;
        }

        .invoice-title h4 {
            font-size: 16px;
            font-weight: normal;
            margin-top: 1px;
        }

        /* Invoice Details */
        .invoice-details td {
            border: none;
            padding: 20px 0;
            vertical-align: top;
            width: 50%;
        }

        .invoice-details address {
            font-size: 16px;
            font-style: normal;
            line-height: 1;
        }

        /* Items Table */
        .items-table {
            display: table;
            page-break-inside: auto;
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000000;
            margin-bottom: 5px;
            /* Consistent spacing */
        }

        .items-table tr,
        .items-table td,
        .items-table th {
            break-inside: avoid !important;
            page-break-after: auto;
            page-break-inside: avoid !important;
        }

        .items-table thead th {
            background-color: #eceff1;
            /* Light grey-blue background for header */
            color: #37474f;
            /* Dark grey-blue text */
            font-weight: bold;
            /* Poppins Semibold */
            padding: 5px 5px;
            text-align: left;
            /* Header cells have a stronger bottom border and a right border */
            border-bottom: 1px solid #90a4ae;
            /* Darker separator for header */
            border-right: 1px solid #cfd8dc;
            /* Light vertical separator */
            text-transform: uppercase;
            font-size: 16px;
            letter-spacing: 0.5px;
            vertical-align: middle;
        }

        /* Remove the right border from the last header cell */
        .items-table thead th:last-child {
            border-right: none;
        }

        .items-table tbody td {
            padding: 10px 10px;
            /* Body cells have a lighter bottom border and a right border */
            border-bottom: 1px solid #cfd8dc;
            /* Light horizontal separator for rows */
            border-right: 1px solid #cfd8dc;
            /* Light vertical separator */
            vertical-align: top;
            font-size: 16px;
            color: #000000;
            /* Slightly softer text color */
        }

        /* Remove the right border from the last cell in a body row */
        .items-table tbody td:last-child {
            border-right: none;
        }

        /* The last row of items should not have a bottom border */
        .items-table tbody tr:last-child td {
            border-bottom: none;
        }

        .items-table thead {
            display: table-header-group;
        }

        .items-table tfoot {
            display: table-footer-group;
        }

        /* Vendor Items Table */
        .vendor-item {
            font-size: 16px;
            margin-bottom: 5px;
        }

        /* Totals Table */
        .total-table {
            margin-left: 50%;
            margin-top: 20px;
            width: 50%;
        }

        .total-table th {
            font-weight: bold;
        }

        .total-table td:last-child {
            text-align: right;
        }

        /* Payment History */
        .payment-history {
            margin-top: 20px;
        }

        /* Warning Box */
        .warning {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            color: #721c24;
            margin: 20px 0;
            padding: 15px;
        }

        /* Footer */
        .footer {
            border-top: 1px solid #ddd;
            font-size: 16px;
            margin-top: 10px;
            padding-top: 20px;
            page-break-inside: auto;
        }

        .footer td {
            color: #000000;
            page-break-inside: auto;
        }

        /* Page Break */
        .page-break {
            page-break-before: auto;
        }

        /* Helpers */
        .bold {
            font-weight: 600;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .small {
            font-size: 14px;
        }

        .info-description {
            color: #000000;
            font-size: 16px;
            line-height: 1;
            margin-top: 2px;
            white-space: normal;
        }

        .vendor-description {
            color: #000000;
            font-size: 16px;
            line-height: 1;
            margin-top: 2px;
            white-space: normal;
        }

        hr {
            border: none;
            border-top: 1px solid #ddd;
            margin: 10px 0;
        }

        /* Badge Simulation */
        .badge {
            border-radius: .25rem;
            display: inline-block;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            padding: .25em .4em;
            text-align: center;
            vertical-align: baseline;
            white-space: nowrap;
        }

        .bg-success {
            background-color: #28a745;
            color: #fff;
        }

        .bg-warning {
            background-color: #ffc107;
            color: #212529;
        }

        /* Watermark */
        .watermark {
            color: rgba(0, 0, 0, 0.1);
            font-size: 150px;
            font-weight: bold;
            left: 50%;
            letter-spacing: 5px;
            pointer-events: none;
            position: fixed;
            text-transform: uppercase;
            top: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            white-space: nowrap;
            z-index: -1000;
        }

        .watermark.paid {
            color: rgba(40, 167, 69, 0.15);
        }

        .watermark.pending {
            color: rgba(255, 193, 7, 0.15);
        }

        @media print {
            .items-table {
                page-break-inside: auto;
            }

            .items-table tr,
            .items-table td,
            .items-table th {
                page-break-inside: avoid !important;
            }

            .items-table thead {
                display: table-header-group;
            }

            .items-table tfoot {
                display: table-footer-group;
            }
        }
    </style>

</head>

<body>
    <!-- Watermark -->
    @php
        $profitLoss = $order->laba_kotor ?? 0;
    @endphp
    @if ($profitLoss >= 0)
        <div class="watermark paid">Profit</div>
    @else
        <div class="watermark pending">Loss</div>
    @endif

    <!-- Header -->
    <table class="header" style="width: 100%;">
        <tr>
            <td style="width: 60%; text-align: left; vertical-align: top;">
                <h2>{{ config('app.name', 'Your Company') }}</h2>
                <p>{{ config('invoice.address', 'Your Company Address') }}</p>
                <p>Phone : {{ config('invoice.phone', '+123456789') }}</p>
                <p>Email : {{ config('invoice.email', 'info@yourcompany.com') }}</p>
            </td>
            <td style="width: 40%; text-align: right; vertical-align: middle;">
                {{-- Embed image using Base64 for reliable PDF rendering --}}
                @php
                    $logoPath = public_path(config('invoice.logo', 'images/logo.png'));
                    if (file_exists($logoPath)) {
                        $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
                        $logoData = file_get_contents($logoPath);
                        $logoBase64 = 'data:image/' . $logoType . ';base64,' . base64_encode($logoData);
                    } else {
                        $logoBase64 = ''; /* Handle missing logo */
                    }
                @endphp
                @if ($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Company Logo">
                @else
                    {{-- Optional: Display text or placeholder if logo is missing --}}
                    <span>Logo</span>
                @endif
            </td>
        </tr>
    </table>

    <!-- Invoice Title -->
    <div class="invoice-title">
        <h1>LAPORAN LABA RUGI</h1>
        <h4>#{{ $order->prospect->name_event }}</h4>
    </div>

    <!-- Invoice Details -->
    <table class="invoice-details">
        <tr>
            <td>
                <div class="bold">Billed To :</div>
                <address>
                    Event : {{ $order->prospect->name_event }}<br>
                    Nama : {{ $order->prospect->name_cpp }} & {{ $order->prospect->name_cpw }}<br>
                    Alamat : {{ $order->prospect->address }}<br>
                    No. Tlp : {{ $order->prospect->phone ? '+62' . $order->prospect->phone : 'N/A' }}<br>
                    Venue : {{ $order->prospect->venue ?? 'N/A' }} / {{ $order->pax ?? 'N/A' }} Pax<br>
                    Account Manager : {{ $order->employee->name ?? 'N/A' }}<br>
                </address>
            </td>
            <td class="text-right">
                <div class="bold">Laporan Information :</div>
                <address>
                    Tanggal Laporan : {{ $generatedDate ?? now()->format('d F Y H:i') }}<br>
                    Status Pembayaran : @if ($order->is_paid) <span style="color: #28a745; font-weight: bold;">Lunas</span> @else <span style="color: #dc3545; font-weight: bold;">Belum Lunas</span> @endif<br>
                    Tgl Lamaran :
                    {{ $order->prospect->date_lamaran ? \Carbon\Carbon::parse($order->prospect->date_lamaran)->format('d F Y') : '-' }}<br>
                    Tgl Akad :
                    {{ $order->prospect->date_akad ? \Carbon\Carbon::parse($order->prospect->date_akad)->format('d F Y') : '-' }}<br>
                    Tgl Resepsi:
                    {{ $order->prospect->date_resepsi ? \Carbon\Carbon::parse($order->prospect->date_resepsi)->format('d F Y') : '-' }}<br>
                </address>
            </td>
        </tr>
    </table>

    <!-- Financial Summary Table -->
    <div class="billing-summary" style="margin-top: 30px;">
        <table class="bordered">
            <thead>
                <tr>
                    <th colspan="2">RINGKASAN KEUANGAN</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Total Paket Awal</td>
                    <td class="text-right">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                </tr>

                @if ($order->promo > 0)
                    <tr>
                        <td>Diskon</td>
                        <td class="text-right">- Rp {{ number_format($order->promo, 0, ',', '.') }}</td>
                    </tr>
                @endif

                @if ($order->penambahan > 0)
                    <tr>
                        <td>Penambahan</td>
                        <td class="text-rose-800 text-right">Rp {{ number_format($order->penambahan, 0, ',', '.') }}</td>
                    </tr>
                @endif

                @if ($order->pengurangan > 0)
                    <tr>
                        <td>Pengurangan</td>
                        <td class="text-right">Rp {{ number_format($order->pengurangan, 0, ',', '.') }}</td>
                    </tr>
                @endif

                <tr>
                    <td class="bold">Grand Total (Pendapatan)</td>
                    <td class="text-right bold">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Sudah Dibayar</td>
                    <td class="text-right">Rp {{ number_format($order->bayar, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Sisa Tagihan (Balance Due)</td>
                    <td class="text-right">Rp {{ number_format($order->sisa, 0, ',', '.') }}</td>
                </tr>
                @php
                    $totalExpenses = $order->expenses->sum('amount') ?? 0;
                    $profitLoss = $order->laba_kotor ?? ($order->grand_total - $totalExpenses);
                @endphp
                <tr>
                    <td class="bold">Total Pengeluaran</td>
                    <td class="text-right bold">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</td>
                </tr>
                <tr class="{{ $profitLoss >= 0 ? 'profit-row' : 'loss-row' }}">
                    <td class="bold {{ $profitLoss >= 0 ? 'profit-text' : 'loss-text' }}">
                        {{ $profitLoss >= 0 ? 'LABA KOTOR' : 'RUGI KOTOR' }}
                    </td>
                    <td class="text-right bold {{ $profitLoss >= 0 ? 'profit-text' : 'loss-text' }}">
                        <strong>Rp {{ number_format($profitLoss, 0, ',', '.') }}</strong>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Detail Pengurangan per Produk dalam Order -->
    @php
        $allProductPengurangans = collect();
        if ($order->items && $order->items->count() > 0) {
            foreach ($order->items as $orderItem) {
                if ($orderItem->product && $orderItem->product->pengurangans->count() > 0) {
                    foreach ($orderItem->product->pengurangans as $pengurangan) {
                        // Menambahkan nama produk ke objek pengurangan untuk referensi
                        $pengurangan->product_name = $orderItem->product->name;
                        $allProductPengurangans->push($pengurangan);
                    }
                }
            }
        }
    @endphp

    @if ($allProductPengurangans->isNotEmpty())
        <div class="section-container" style="margin-top: 20px;">
            <h3 class="sub-section-title"
                style="font-size: 1.1em; margin-bottom: 10px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">
                Rincian Item Pengurangan Produk</h3>
            <table class="bordered">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 5%;">No</th>
                        <th>Deskripsi Pengurangan</th>
                        <th class="text-right" style="width: 20%;">Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($allProductPengurangans as $index => $itemPengurangan)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                {{ $itemPengurangan->description ?? 'N/A' }}
                                @if ($itemPengurangan->notes)
                                    <div style="margin-left: 30px; color: #555; margin-top: 5px;">
                                        <i>{!! strip_tags($itemPengurangan->notes, '<li><strong><em><ul><li><br><span><div>') !!}</i>
                                    </div>
                                @endif
                            </td>
                            <td class="text-right">Rp {{ number_format($itemPengurangan->amount ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Pengeluaran Vendor -->
    <div class="section-container" style="margin-top: 20px;">
        <h3 class="sub-section-title" style="font-size: 1.1em; margin-bottom: 10px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">
            Rincian Pengeluaran Vendor
        </h3>
        <table class="bordered">
            <thead>
                <tr>
                    <th class="text-center" style="width: 5%;">No</th>
                    <th style="width: 15%;">Tanggal</th>
                    <th style="width: 25%;">Vendor</th>
                    <th style="width: 15%;">No ND</th>
                    <th class="text-right" style="width: 20%;">Jumlah</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($order->expenses as $index => $expense)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $expense->date_expense ? \Carbon\Carbon::parse($expense->date_expense)->format('d M Y') : '-' }}</td>
                        <td>{{ $expense->vendor->name ?? 'N/A' }}</td>
                        <td>{{ $expense->no_nd ? 'ND-0' . $expense->no_nd : '-' }}</td>
                        <td class="text-right">Rp {{ number_format($expense->amount ?? 0, 0, ',', '.') }}</td>
                        <td>{{ $expense->description ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center" style="font-style: italic; color: #666;">
                            Tidak ada data pengeluaran vendor.
                        </td>
                    </tr>
                @endforelse
                @if($order->expenses->count() > 0)
                    <tr style="background-color: #f8f9fa;">
                        <td colspan="4" class="text-right bold">TOTAL PENGELUARAN:</td>
                        <td class="text-right bold">Rp {{ number_format($order->expenses->sum('amount'), 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Payment History -->
    @if (count($order->dataPembayaran) > 0)
        <div class="payment-history">
            <h3>Riwayat Pembayaran Diterima</h3>
            <table class="bordered">
                <thead>
                    <tr style="background-color: #f8f9fa;">
                        <th>Tanggal</th>
                        <th>Jumlah</th>
                        <th>Metode Pembayaran</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->dataPembayaran as $payment)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($payment->tgl_bayar)->format('d F Y') }}</td>
                            <td class="text-right">Rp {{ number_format($payment->nominal, 0, ',', '.') }}</td>
                            <td>{{ $payment->paymentMethod->name ?? 'N/A' }}</td>
                            <td>{{ $payment->keterangan }}</td>
                        </tr>
                    @endforeach
                    <tr style="background-color: #e3f2fd;">
                        <td class="bold text-right">TOTAL DITERIMA:</td>
                        <td class="text-right bold">Rp {{ number_format($order->dataPembayaran->sum('nominal'), 0, ',', '.') }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    @else
        <p style="margin-top: 20px; font-style: italic;">Belum ada pembayaran yang diterima.</p>
    @endif

    <!-- Analisis Laba Rugi -->
    @php
        $totalRevenue = $order->grand_total;
        $totalExpenses = $order->expenses->sum('amount') ?? 0;
        $profitLoss = $totalRevenue - $totalExpenses;
        $profitMargin = $totalRevenue > 0 ? ($profitLoss / $totalRevenue) * 100 : 0;
    @endphp
    <div class="analysis-box {{ $profitLoss >= 0 ? 'profit' : 'loss' }}">
        <h3 class="{{ $profitLoss >= 0 ? 'profit-text' : 'loss-text' }}" style="text-align: center; margin-bottom: 15px;">
            ANALISIS LABA RUGI
        </h3>
        <table class="bordered">
            <tr>
                <td style="width: 60%;"><strong>Total Pendapatan (Revenue):</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td><strong>Total Pengeluaran (Expenses):</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($totalExpenses, 0, ',', '.') }}</strong></td>
            </tr>
            <tr class="{{ $profitLoss >= 0 ? 'profit-row' : 'loss-row' }}">
                <td class="{{ $profitLoss >= 0 ? 'profit-text' : 'loss-text' }}"><strong>{{ $profitLoss >= 0 ? 'LABA BERSIH' : 'RUGI BERSIH' }}:</strong></td>
                <td class="text-right {{ $profitLoss >= 0 ? 'profit-text' : 'loss-text' }}"><strong>Rp {{ number_format($profitLoss, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td><strong>Margin {{ $profitLoss >= 0 ? 'Laba' : 'Rugi' }}:</strong></td>
                <td class="text-right {{ $profitLoss >= 0 ? 'profit-text' : 'loss-text' }}"><strong>{{ number_format($profitMargin, 2) }}%</strong></td>
            </tr>
        </table>
        
        <div style="text-align: center; margin-top: 15px;">
            @if($profitLoss >= 0)
                <p class="profit-text">✓ Proyek ini menghasilkan keuntungan</p>
            @else
                <p class="loss-text">⚠ Proyek ini mengalami kerugian</p>
            @endif
        </div>
    </div>

    <!-- Footer -->
    <table class="footer" style="width: 100%;">
        <tr>
            <td style="width: 65%; vertical-align: top;">
                <div class="bold">Catatan Laporan</div>
                <ul>
                    <li>Laporan ini menampilkan analisis laba rugi berdasarkan data transaksi yang tercatat dalam sistem.</li>
                    <li>Total pendapatan dihitung dari grand total paket yang telah disepakati dengan klien.</li>
                    <li>Total pengeluaran mencakup semua pembayaran yang dilakukan kepada vendor terkait.</li>
                    <li>Margin laba/rugi dihitung berdasarkan persentase dari total pendapatan.</li>
                    <li>Untuk pertanyaan lebih lanjut, hubungi bagian keuangan.</li>
                </ul>
            </td>
            <td style="width: 35%; text-align: right; vertical-align: top;">
                <p style="margin-bottom: 10px;">Laporan digenerate pada:</p>
                <p style="font-weight: bold;">{{ $generatedDate ?? now()->format('d F Y H:i') }}</p>
                <p style="margin-top: 20px;">Disetujui oleh:</p>
                <p style="margin-top: 60px;">____________________</p>
                @php
                    // Mengambil karyawan dengan posisi 'Finance'.
                    $financeApprover = \App\Models\Employee::where('position', 'Finance')->orderBy('name')->first();
                    $approverName = $financeApprover ? $financeApprover->name : 'Finance Department';
                @endphp
                <p>{{ $approverName }}</p>
            </td>
        </tr>
    </table>
</body>

</html>
