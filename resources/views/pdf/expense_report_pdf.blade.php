<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pengeluaran</title>
    <style>
        /* Define Poppins font locally for DomPDF */
        @font-face {
            font-family: 'Poppins';
            font-style: normal;
            font-weight: 300; /* Light */
            src: url({{ storage_path('app/fonts/poppins/Poppins-Light.ttf') }}) format('truetype');
        }
        @font-face {
            font-family: 'Poppins';
            font-style: normal;
            font-weight: 400; /* Regular */
            src: url({{ storage_path('app/fonts/poppins/Poppins-Regular.ttf') }}) format('truetype');
        }
        @font-face {
            font-family: 'Poppins';
            font-style: normal;
            font-weight: 500; /* Medium */
            src: url({{ storage_path('app/fonts/poppins/Poppins-Medium.ttf') }}) format('truetype');
        }
        @font-face {
            font-family: 'Poppins';
            font-style: normal;
            font-weight: 600; /* SemiBold */
            src: url({{ storage_path('app/fonts/poppins/Poppins-SemiBold.ttf') }}) format('truetype');
        }
        @font-face {
            font-family: 'Poppins';
            font-style: normal;
            font-weight: 700; /* Bold */
            src: url({{ storage_path('app/fonts/poppins/Poppins-Bold.ttf') }}) format('truetype');
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background-color: #fff; /* White background for PDF */
            color: #212529;
            line-height: 1;
            font-size: 12px; /* Adjusted for potentially more content */
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        * {
            font-family: 'Poppins', sans-serif;
        }
        .container {
            width: 100%;
            margin: 20px auto;
            padding: 0 20px; /* Padding for content */
            box-sizing: border-box;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 1em;
        }
        .report-title-header {
            text-align: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #dee2e6;
        }
        .company-logo {
            max-height: 2.5rem;
            margin-left: auto;
            margin-right: auto;
            margin-bottom: 1rem;
        }
        h1.report-main-title {
            font-size: 1.5em; /* Slightly larger title */
            font-weight: 700;
            font-family: 'Poppins', sans-serif; /* Ensure Poppins is used */
            letter-spacing: 0.05em;
            color: #343a40;
            text-align: center;
            margin-top: 0;
            margin-bottom: 0.5rem;
        }
        .company-address {
            font-size: 0.9em; /* Slightly larger address */
            color: #6c757d;
            margin-top: 0.25rem;
            text-align: center;
        }
        th, td {
            border: 1px solid #e0e0e0;
            padding: 8px 10px; /* Slightly reduced padding for more data */
            text-align: left;
            vertical-align: middle;
            word-wrap: break-word;
        }
        th {
            background-color: #dde1e5;
            color: #343a40;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .no-data {
            text-align: center;
            color: #7f8c8d;
            padding: 20px;
            font-style: italic;
        }
        .total-row td {
            font-weight: 700;
            background-color: #eaf2f8; /* Consistent with ops report */
        }
        small.periode-text {
            font-size: 0.85em; font-weight: 400; color: #6c757d; text-transform: none;
        }
        .signature-table {
            width: 100%;
            margin-top: 50px; /* Space above signature table */
            border-collapse: collapse; /* Remove cell spacing */
            page-break-inside: avoid; /* Try to keep signature table on one page */
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 0.9em;
            color: #7f8c8d;
            width: 100%;
            position: fixed; /* If you want footer at the bottom of each page */
            bottom: 0;
            left: 0;
        }
        .signature-table td {
            width: 50%;
            text-align: center;
            padding-top: 10px;
            padding-bottom: 70px; /* Space for signature */
            border: none;
            font-size: 1em;
            vertical-align: top;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="report-title-header">
            @php
                $logoPath = public_path('images/logomki.png');
                $logoSrc = '';
                if (file_exists($logoPath)) {
                    $logoMime = mime_content_type($logoPath);
                    if ($logoMime) {
                        $logoSrc = 'data:' . $logoMime . ';base64,' . base64_encode(file_get_contents($logoPath));
                    }
                }
            @endphp
            @if($logoSrc)<img src="{{ $logoSrc }}" alt="Logo Perusahaan" class="company-logo">@endif

            <h1 class="report-main-title">
                Laporan Pengeluaran Klien
                @if(isset($selectedMonth) && isset($selectedYear) && $selectedMonth && $selectedYear)
                    <br><small class="periode-text">(Periode: {{ $months[$selectedMonth] }} {{ $selectedYear }})</small>
                @elseif(isset($selectedYear) && $selectedYear && (!isset($selectedMonth) || !$selectedMonth))
                    <br><small class="periode-text">(Periode: Tahun {{ $selectedYear }})</small>
                @else
                    <br><small class="periode-text">(Semua Periode)</small>
                @endif
                @php
                    $statusLabel = '';
                    if (isset($selectedOrderStatus) && $selectedOrderStatus && isset($orderStatuses)) {
                        foreach ($orderStatuses as $statusEnum) {
                            if ($statusEnum->value == $selectedOrderStatus) {
                                $statusLabel = $statusEnum->getLabel();
                                break;
                            }
                        }
                    }
                @endphp
                @if($statusLabel)
                    <br><small class="periode-text" style="font-size: 0.8em;">(Status Order: {{ $statusLabel }})</small>
                @endif
            </h1>
            <p class="company-address">Jl. Sintraman Jaya I No. 2148, 20 Ilir D II, Kec. Kemuning, Kota Palembang, Sumatera Selatan 30137</p>
            <p class="company-address" style="margin-top:0;">PT. Makna Kreatif Indonesia | maknawedding@gmail.com | +62 822-9796-2600</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Pengeluaran</th>
                    <th style="text-align: right;">Jumlah</th>
                    <th>Tanggal</th>
                    <th>No. ND</th>
                    <th>Vendor</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @php $totalPengeluaran = 0; @endphp
                @forelse($expenses as $expense)
                    <tr>
                        <td>{{ $expense->id }}</td>
                        <td>{{ $expense->order->name ?? ($expense->name ?? 'N/A') }}</td>
                        <td style="text-align: right;">{{ number_format($expense->amount ?? 0, 0, ',', '.') }}</td>
                        <td>{{ $expense->date_expense ? (\Carbon\Carbon::parse($expense->date_expense)->locale('id')->isoFormat('D MMMM YYYY')) : 'N/A' }}</td>
                        <td style="text-align: center;">{{ $expense->no_nd ?? '-' }}</td>
                        <td>{{ $expense->vendor->name ?? 'N/A' }}</td>
                        <td>{{ $expense->note ?? '-' }}</td>
                    </tr>
                    @php $totalPengeluaran += ($expense->amount ?? 0); @endphp
                @empty
                    <tr>
                        <td colspan="6" class="no-data">Tidak ada data pengeluaran yang ditemukan.</td>
                    </tr>
                @endforelse
                @if($expenses->isNotEmpty())
                <tr class="total-row">
                    <td colspan="2" style="text-align: right;"><strong>Total Keseluruhan:</strong></td>
                    <td style="text-align: right;"><strong>{{ number_format($totalPengeluaran, 0, ',', '.') }}</strong></td>
                    <td colspan="4"></td>
                </tr>
                @endif
            </tbody>
        </table>

        <table class="signature-table">
            <tr>
                <td>
                    Mengetahui,<br>CEO<br><br><br><br><br>
                    <strong>( Rama Dhona Utama )</strong>
                </td>
                <td>
                    Disiapkan Oleh,<br>Finance<br><br><br><br><br>
                    <strong>( _________________________ )</strong>
                </td>
            </tr>
        </table>

        <div class="footer">
            Laporan ini dihasilkan pada: {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM YYYY, HH:mm:ss') }}
        </div>
    </div>
</body>
</html>