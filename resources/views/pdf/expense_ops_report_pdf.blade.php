<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Pengeluaran Operasional</title>
    <style>
        /* Define Poppins font locally for DomPDF */
        @font-face {
            font-family: 'Poppins';
            font-style: normal;
            font-weight: 300;
            /* Light */
            src: url({{ storage_path('app/fonts/poppins/Poppins-Light.ttf') }}) format('truetype');
        }

        @font-face {
            font-family: 'Poppins';
            font-style: normal;
            font-weight: 400;
            /* Regular */
            src: url({{ storage_path('app/fonts/poppins/Poppins-Regular.ttf') }}) format('truetype');
        }

        @font-face {
            font-family: 'Poppins';
            font-style: normal;
            font-weight: 500;
            /* Medium */
            src: url({{ storage_path('app/fonts/poppins/Poppins-Medium.ttf') }}) format('truetype');
        }

        @font-face {
            font-family: 'Poppins';
            font-style: normal;
            font-weight: 600;
            /* SemiBold */
            src: url({{ storage_path('app/fonts/poppins/Poppins-SemiBold.ttf') }}) format('truetype');
        }

        @font-face {
            font-family: 'Poppins';
            font-style: normal;
            font-weight: 700;
            /* Bold */
            src: url({{ storage_path('app/fonts/poppins/Poppins-Bold.ttf') }}) format('truetype');
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background-color: #fff;
            /* White background for PDF */
            color: #212529;
            line-height: 1;
            font-size: 12px;
            /* Default font size for PDF */
            -webkit-font-smoothing: antialiased;
            /* Better font rendering */
            -moz-osx-font-smoothing: grayscale;
        }

        /* Ensure all major text elements inherit Poppins */
        * {
            font-family: 'Poppins', sans-serif;
        }

        .container {
            width: 100%;
            /* Full width for PDF */
            margin: 20px auto;
            /* Adjust margin as needed */
            padding: 0 20px;
            /* Padding for content */
            box-sizing: border-box;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 1em;
            /* Make table font size same as body by default */
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
            font-size: 1.5em;
            /* Slightly larger title */
            font-weight: 700;
            font-family: 'Poppins', sans-serif !important;
            /* Ensure Poppins is used */
            letter-spacing: 0.05em;
            color: #343a40;
            text-align: center;
            margin-top: 0;
            margin-bottom: 0.5rem;
        }

        .company-address {
            font-size: 0.9em;
            /* Slightly larger address */
            color: #6c757d;
            margin-top: 0.25rem;
            text-align: center;
            /* Center address for PDF */
        }

        th,
        td {
            border: 1px solid #e0e0e0;
            /* Lighter border for PDF */
            padding: 8px 10px;
            /* Adjusted padding */
            text-align: left;
            vertical-align: middle;
            word-wrap: break-word;
            /* Prevent long text from breaking layout */
        }

        th {
            background-color: #dde1e5;
            color: #343a40;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-family: 'Poppins', sans-serif !important;
            /* Ensure Poppins is used */
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

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 0.9em;
            color: #7f8c8d;
            width: 100%;
            position: fixed;
            /* If you want footer at the bottom of each page */
            bottom: 0;
            left: 0;
        }

        .total-row td {
            font-weight: bold;
            background-color: #eaf2f8;
        }

        .image-proof {
            max-width: 70px;
            /* Smaller for PDF */
            max-height: 70px;
            display: block;
            /* Helps with layout in DomPDF */
            margin: auto;
            /* Center if needed */
        }

        small.periode-text {
            font-size: 0.85em;
            font-weight: 400;
            color: #6c757d;
            text-transform: none;
        }

        .signature-table {
            width: 100%;
            margin-top: 50px;
            /* Space above signature table */
            border-collapse: collapse;
            /* Remove cell spacing */
            page-break-inside: avoid;
            /* Try to keep signature table on one page */
        }

        .signature-table td {
            width: 50%;
            text-align: center;
            padding-top: 10px;
            padding-bottom: 70px;
            /* Space for signature */
            border: none;
            /* No borders for signature cells */
            font-size: 1em;
            /* Match body font size */
            vertical-align: top;
            /* Align text to the top */
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
                    // Embedding as base64 is generally more reliable for DomPDF
                    $logoMime = mime_content_type($logoPath);
                    if ($logoMime) {
                        $logoSrc = 'data:' . $logoMime . ';base64,' . base64_encode(file_get_contents($logoPath));
                    }
                }
            @endphp
            @if ($logoSrc)
                <img src="{{ $logoSrc }}" alt="Logo Perusahaan" class="company-logo">
            @endif

            <h1 class="report-main-title">
                Laporan Pengeluaran Operasional
                @if (isset($selectedMonth) && isset($selectedYear) && $selectedMonth && $selectedYear)
                    <br><small class="periode-text">(Periode: {{ $months[$selectedMonth] }} {{ $selectedYear }})</small>
                @elseif(isset($selectedYear) && $selectedYear && (!isset($selectedMonth) || !$selectedMonth))
                    <br><small class="periode-text">(Periode: Tahun {{ $selectedYear }})</small>
                @else
                    <br><small class="periode-text">(Semua Periode)</small>
                @endif
            </h1>
            <p class="company-address">Jl. Sintraman Jaya I No. 2148, 20 Ilir D II, Kec. Kemuning, Kota Palembang,
                Sumatera Selatan 30137</p>
            <p class="company-address" style="margin-top:0;">PT. Makna Kreatif Indonesia | maknawedding@gmail.com | +62
                822-9796-2600</p>
        </div>

        {{-- Filter form is removed for PDF view --}}

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Pengeluaran</th>
                    <th style="text-align: right;">Jumlah</th>
                    <th>Tanggal</th>
                    {{-- <th>Kategori</th> --}}
                    <th>Catatan</th>
                    <th>Bukti</th>
                </tr>
            </thead>
            <tbody>
                @php $totalPengeluaran = 0; @endphp
                @forelse($expenseOps as $expense)
                    <tr>
                        <td>{{ $expense->id }}</td>
                        <td>{{ $expense->name ?? 'N/A' }}</td>
                        <td style="text-align: right;">{{ number_format($expense->amount ?? 0, 0, ',', '.') }}</td>
                        <td>{{ $expense->date_expense ? \Carbon\Carbon::parse($expense->date_expense)->locale('id')->isoFormat('D MMMM YYYY') : 'N/A' }}
                        </td>
                        {{-- <td>{{ $expense->expenseOpsCategory->name ?? 'N/A' }}</td> --}}
                        <td>{{ $expense->note ?? '-' }}</td>
                        <td>
                            @if ($expense->image)
                                {{-- Untuk PDF, menampilkan gambar langsung mungkin lebih baik daripada link --}}
                                {{-- Pastikan path Storage::url() bisa diakses oleh DomPDF atau embed base64 jika perlu --}}
                                @php
                                    $imagePath = storage_path('app/public/' . $expense->image); // Assuming images are in storage/app/public
                                    $imageSrc = '';
                                    if (file_exists($imagePath)) {
                                        $imageMime = mime_content_type($imagePath);
                                        if ($imageMime) {
                                            $imageSrc =
                                                'data:' .
                                                $imageMime .
                                                ';base64,' .
                                                base64_encode(file_get_contents($imagePath));
                                        }
                                    }
                                @endphp
                                @if ($imageSrc)
                                    <img src="{{ $imageSrc }}" alt="Bukti" class="image-proof">
                                @else
                                    <small>(Gambar tidak tersedia)</small>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @php $totalPengeluaran += ($expense->amount ?? 0); @endphp
                @empty
                    <tr>
                        <td colspan="6" class="no-data">Tidak ada data pengeluaran operasional yang ditemukan.</td>
                    </tr>
                @endforelse
                @if ($expenseOps->isNotEmpty())
                    <tr class="total-row">
                        <td colspan="2" style="text-align: right;"><strong>Total Keseluruhan:</strong></td>
                        <td style="text-align: right;">
                            <strong>{{ number_format($totalPengeluaran, 0, ',', '.') }}</strong></td>
                        <td colspan="3"></td>
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
