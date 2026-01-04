<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Laporan Pembayaran</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 20px;
            background-color: #f4f7f6;
            color: #333;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 0.9em;
        }

        .filter-form {
            margin-bottom: 25px;
            padding: 15px;
            background-color: #eef3f7;
            /* Slightly different background for the filter */
            border-radius: 6px;
            display: flex;
            gap: 15px;
            /* Spacing between filter elements */
            align-items: center;
            flex-wrap: wrap;
            /* Allow wrapping on smaller screens */
        }

        .filter-form label {
            font-weight: 500;
            margin-right: 5px;
            color: #333;
        }

        .filter-form select,
        .filter-form button {
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.9em;
        }

        .filter-form button {
            background-color: #3498db;
            color: white;
            cursor: pointer;
            border-color: #3498db;
            transition: background-color 0.2s ease-in-out;
        }

        .filter-form button:hover {
            background-color: #2980b9;
        }

        .report-title-header {
            text-align: center;
            margin-bottom: 1.5rem;
            /* mb-6 */
            padding-bottom: 1rem;
            /* pb-4 */
            border-bottom: 1px solid #e5e7eb;
            /* border-gray-200 */
        }

        .report-company-name {
            /* Ini akan digantikan oleh logo dan detail perusahaan di bawahnya */
            /* font-size: 1.1em;
            font-weight: 500;
            color: #4A5568;
            margin-bottom: 8px; */
        }

        .company-logo {
            max-height: 2.5rem;
            /* max-h-10 */
            margin-left: auto;
            margin-right: auto;
            margin-bottom: 1rem;
            /* mb-4 */
        }

        h1.report-main-title {
            /* Ganti nama kelas agar lebih spesifik */
            font-size: 1.25rem;
            /* text-xl */
            font-weight: 700;
            /* font-bold */
            text-transform: uppercase;
            letter-spacing: 0.05em;
            /* tracking-wide */
            color: #1f2937;
            /* text-gray-800 */
            text-align: center;
            margin-top: 0;
            margin-bottom: 0;
            /* Dihapus karena border sudah ada di .report-title-header */
            border-bottom: none;
            /* Dihapus karena border sudah ada di .report-title-header */
            padding-bottom: 0;
            /* Dihapus */
        }

        .company-address {
            font-size: 0.75rem;
            /* text-xs */
            color: #4b5563;
            /* text-gray-600 */
            margin-top: 0.25rem;
            /* mt-1 atau mt-0 */
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px 12px;
            text-align: left;
        }

        th {
            background-color: #3498db;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #e8f4f8;
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
        }

        .total-row td {
            font-weight: bold;
            background-color: #eaf2f8;
        }

        /* Tambahkan styling lain sesuai kebutuhan Anda */
    </style>
</head>

<body>
    <div class="container">
        {{-- Untuk debugging data yang diterima view --}}
        {{-- @dump($dataPembayarans) --}}
        {{-- @if ($dataPembayarans->isNotEmpty()) @dump($dataPembayarans->first()) @endif --}}

        <div class="report-title-header">
            @php
                $company = null;
                if (\Illuminate\Support\Facades\Schema::hasTable('companies')) {
                    $company = \App\Models\Company::query()->first();
                }

                $logoSrc =
                    $company && $company->logo_url
                        ? \Illuminate\Support\Facades\Storage::disk('public')->url($company->logo_url)
                        : asset('images/logomki.png');
            @endphp
            @if ($logoSrc)
                <img src="{{ $logoSrc }}" alt="Nama Perusahaan Anda" class="company-logo">
            @endif

            <h1 class="report-main-title">
                Laporan Data Pembayaran
                @if (isset($selectedMonth) && isset($selectedYear) && $selectedMonth && $selectedYear)
                    <br><small style="font-size: 0.65em; font-weight: 400; color: #555;">(Periode:
                        {{ $months[$selectedMonth] }} {{ $selectedYear }})</small>
                @elseif(isset($selectedYear) && $selectedYear && (!isset($selectedMonth) || !$selectedMonth))
                    <br><small style="font-size: 0.65em; font-weight: 400; color: #555;">(Periode: Tahun
                        {{ $selectedYear }})</small>
                @else
                    <br><small style="font-size: 0.65em; font-weight: 400; color: #555; text-transform: none;">(Semua
                        Periode)</small>
                @endif
            </h1>
            @if ($company)
                <p class="company-address">
                    {{ $company->address }}
                    @if ($company->city)
                        , {{ $company->city }}
                    @endif
                    @if ($company->province)
                        , {{ $company->province }}
                    @endif
                    @if ($company->postal_code)
                        {{ $company->postal_code }}
                    @endif
                </p>
                <p class="company-address" style="margin-top:0;">
                    {{ $company->company_name }}
                    @if ($company->email)
                        | {{ $company->email }}
                    @endif
                    @if ($company->phone)
                        | {{ $company->phone }}
                    @endif
                </p>
            @else
                <p class="company-address">Jl. Sintraman Jaya I No. 2148, 20 Ilir D II, <br>
                    Kecamatan Kemuning, Kota Palembang, Sumatera Selatan 30137</p>
                <p class="company-address" style="margin-top:0;">PT. Makna Kreatif Indonesia | maknawedding@gmail.com |
                    +62
                    822-9796-2600</p>
            @endif
        </div>

        <form action="{{ route('data-pembayaran.html-report') }}" method="GET" class="filter-form">
            <div>
                <label for="month">Bulan:</label>
                <select name="month" id="month">
                    <option value="">-- Semua Bulan --</option>
                    @foreach ($months as $monthNum => $monthName)
                        <option value="{{ $monthNum }}"
                            {{ isset($selectedMonth) && $selectedMonth == $monthNum ? 'selected' : '' }}>
                            {{ $monthName }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="year">Tahun:</label>
                <select name="year" id="year">
                    <option value="">-- Semua Tahun --</option>
                    @foreach ($years as $year)
                        <option value="{{ $year }}"
                            {{ isset($selectedYear) && $selectedYear == $year ? 'selected' : '' }}>
                            {{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status">Status Order:</label>
                <select name="status" id="status">
                    <option value="">-- Semua Status --</option>
                    @if (isset($orderStatuses)) {{-- Pastikan $orderStatuses tersedia --}}
                        @foreach ($orderStatuses as $status)
                            <option value="{{ $status->value }}"
                                {{ isset($selectedStatus) && $selectedStatus == $status->value ? 'selected' : '' }}>
                                {{ $status->getLabel() }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div>
                <label for="search_event">Nama Event:</label>
                <input type="text" name="search_event" id="search_event" value="{{ $searchEvent ?? '' }}"
                    placeholder="Cari berdasarkan nama event">
            </div>
            <button type="submit">Terapkan Filter</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Event</th>
                    <th style="text-align: right;">Jumlah Pembayaran</th>
                    <th>Tanggal Pembayaran</th>
                    <th>Status Order</th>
                    <th>Metode Pembayaran</th>
                    <th>Bukti Pembayaran</th>
                    <th>Catatan/Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @php $totalPembayaran = 0; @endphp
                @forelse($dataPembayarans as $pembayaran)
                    <tr>
                        <td>{{ $pembayaran->id }}</td>
                        <td>{{ $pembayaran->order->name ?? 'N/A' }}</td>
                        <td style="text-align: right;"> {{ number_format($pembayaran->nominal ?? 0, 0, ',', '.') }}
                        </td>
                        <td>{{ $pembayaran->tgl_bayar ? \Carbon\Carbon::parse($pembayaran->tgl_bayar)->locale('id')->isoFormat('D MMMM YYYY') : 'N/A' }}
                        </td>
                        <td>
                            @if ($pembayaran->order && $pembayaran->order->status)
                                {{ $pembayaran->order->status->getLabel() ?? ($pembayaran->order->status->value ?? 'N/A') }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            @if ($pembayaran->paymentMethod)
                                @if ($pembayaran->paymentMethod->is_cash)
                                    Cash
                                @else
                                    {{ $pembayaran->paymentMethod->bank_name ?? 'N/A' }}
                                @endif
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            @if ($pembayaran->image)
                                @php
                                    $proofUrl = \Illuminate\Support\Facades\Storage::url($pembayaran->image);
                                @endphp
                                <a href="{{ $proofUrl }}" target="_blank" rel="noopener noreferrer">
                                    <img src="{{ $proofUrl }}" alt="Payment Proof"
                                        style="width: 40px; height: 40px; object-fit: cover; border-radius: 0;">
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $pembayaran->keterangan ?? '-' }}</td>
                    </tr>
                    @php $totalPembayaran += ($pembayaran->nominal ?? 0); @endphp
                @empty
                    <tr>
                        <td colspan="8" class="no-data">Tidak ada data pembayaran yang ditemukan.</td>
                    </tr>
                @endforelse
                {{-- Baris Total (Opsional) --}}
                @if ($dataPembayarans->isNotEmpty())
                    <tr class="total-row">
                        <td colspan="2" style="text-align: right;"><strong>Total Keseluruhan:</strong></td>
                        <td style="text-align: right;"><strong>
                                {{ number_format($totalPembayaran, 0, ',', '.') }}</strong></td>
                        <td colspan="5"></td>
                    </tr>
                @endif
            </tbody>
        </table>
        <div class="footer">
            Laporan ini dihasilkan pada: {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM YYYY, HH:mm:ss') }}
        </div>
    </div>
</body>

</html>
