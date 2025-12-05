<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan</title>
    <style>
        /* Laporan Keuangan PDF Styles - Landscape Format */

        @import url("https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&display=swap");

        @page {
            size: A4 landscape;
            margin: 0.5in;
        }

        body {
            font-family: "Noto Sans", Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 5px;
            color: #333;
            line-height: 1;
            width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #007bff;
        }

        .header h1 {
            margin: 0;
            color: #007bff;
            font-size: 20px;
            font-weight: 700;
            font-family: "Noto Sans", Arial, sans-serif;
        }

        .header p {
            margin: 2px 0;
            color: #666;
            font-size: 10px;
        }

        .filter-info {
            background-color: #f8f9fa;
            padding: 8px 12px;
            border-radius: 4px;
            margin-bottom: 12px;
            font-size: 9px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .filter-info h3 {
            margin: 0 0 5px 0;
            color: #495057;
            font-size: 11px;
            width: 100%;
            font-weight: 600;
            font-family: "Noto Sans", Arial, sans-serif;
        }

        .filter-row {
            display: flex;
            gap: 20px;
            align-items: center;
            flex-wrap: wrap;
            width: 100%;
        }

        .filter-item {
            margin-bottom: 0;
            white-space: nowrap;
        }

        .filter-item strong {
            color: #495057;
            font-weight: 600;
            font-family: "Noto Sans", Arial, sans-serif;
        }

        /* Flexbox Summary Layout - 4 Column Horizontal (PDF Compatible) */
        .grid {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
        }

        .grid-cols-4 {
            width: 100% !important;
        }

        .gap-4 {
            gap: 1rem !important;
        }

        .summary-item {
            background: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 12px 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            width: 25% !important;
            max-width: none;
            min-width: 0;
            flex: 1 1 25% !important;
            margin-right: 10px;
        }

        .summary-item:last-child {
            margin-right: 0;
        }

        .summary-item.masuk {
            border-left: 3px solid #10b981;
        }

        .summary-item.keluar {
            border-left: 3px solid #ef4444;
        }

        .summary-item.saldo {
            border-left: 3px solid #3b82f6;
        }

        .summary-item.total {
            border-left: 3px solid #6b7280;
        }

        .summary-header {
            display: flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 6px;
        }

        .summary-icon {
            font-size: 12px;
            display: inline-block;
            width: 16px;
            text-align: center;
        }

        .summary-header h4 {
            margin: 0;
            font-size: 7px;
            color: #374151;
            font-weight: 600;
            font-family: "Noto Sans", Arial, sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            flex: 1;
            line-height: 1.1;
        }

        .summary-value {
            text-align: center;
        }

        .summary-value .amount {
            font-size: 10px;
            font-weight: 700;
            font-family: "Noto Sans", Arial, sans-serif;
            margin-bottom: 1px;
            line-height: 1.1;
        }

        .summary-item.masuk .amount {
            color: #059669;
        }

        .summary-item.keluar .amount {
            color: #dc2626;
        }

        .summary-item.saldo .amount {
            color: #2563eb;
        }

        .summary-item.total .amount {
            color: #7c3aed;
        }

        .summary-value .description {
            font-size: 6px;
            font-weight: 500;
            font-family: "Noto Sans", Arial, sans-serif;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.2px;
        }

        .description.positive {
            color: #059669;
        }

        .description.negative {
            color: #dc2626;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 9px;
            table-layout: fixed;
        }

        /* Lebar kolom yang disesuaikan untuk landscape format - 8 kolom */
        th:nth-child(1),
        td:nth-child(1) {
            width: 8%;
            min-width: 75px;
        }

        /* Tanggal - fix untuk format dd/mm/yyyy */
        th:nth-child(2),
        td:nth-child(2) {
            width: 10%;
        }

        /* Jenis */
        th:nth-child(3),
        td:nth-child(3) {
            width: 18%;
        }

        /* Deskripsi */
        th:nth-child(4),
        td:nth-child(4) {
            width: 11%;
        }

        /* Vendor */
        th:nth-child(5),
        td:nth-child(5) {
            width: 16%;
        }

        /* Prospect/Event */
        th:nth-child(6),
        td:nth-child(6) {
            width: 13%;
        }

        /* Rekening */
        th:nth-child(7),
        td:nth-child(7) {
            width: 12%;
            min-width: 95px;
        }

        /* Jumlah - fix untuk nominal panjang */
        th:nth-child(8),
        td:nth-child(8) {
            width: 12%;
            min-width: 95px;
        }

        /* Saldo - fix untuk nominal panjang */

        th,
        td {
            padding: 6px 4px;
            text-align: left;
            border: 1px solid #dee2e6;
            word-wrap: break-word;
            overflow: hidden;
            vertical-align: top;
        }

        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
            font-size: 9px;
            font-family: "Noto Sans", Arial, sans-serif;
        }

        td {
            font-size: 8px;
        }

        /* CSS khusus untuk kolom tanggal */
        th:nth-child(1),
        td:nth-child(1) {
            white-space: nowrap;
            text-align: center;
            font-weight: 500;
        }

        /* CSS khusus untuk kolom Jumlah dan Saldo */
        th:nth-child(7),
        td:nth-child(7) {
            white-space: nowrap;
            text-align: right;
            font-weight: 500;
            font-family: 'Courier New', monospace;
        }

        /* CSS khusus untuk kolom Saldo - menggunakan Noto Sans */
        th:nth-child(8),
        td:nth-child(8) {
            white-space: nowrap;
            text-align: right;
            font-weight: 500;
            font-family: "Noto Sans", Arial, sans-serif;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: 600;
            text-align: center;
            display: inline-block;
            min-width: 55px;
            font-family: "Noto Sans", Arial, sans-serif;
        }

        .badge-masuk {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .badge-keluar {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .no-data {
            text-align: center;
            padding: 25px;
            color: #6c757d;
            font-style: italic;
            font-size: 10px;
        }

        .footer {
            margin-top: 15px;
            text-align: right;
            font-size: 8px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            padding-top: 8px;
        }

        .page-break {
            page-break-before: always;
        }

        .ringkasan {
            margin-top: 12px;
            padding: 8px 12px;
            background-color: #f8f9fa;
            border-radius: 4px;
            font-size: 9px;
            border: 1px solid #dee2e6;
        }

        /* Landscape-specific optimizations */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .grid {
                margin-bottom: 10px;
            }

            table {
                page-break-inside: avoid;
                margin-bottom: 10px;
            }

            tr {
                page-break-inside: avoid;
            }

            .filter-info {
                margin-bottom: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>LAPORAN KEUANGAN</h1>
        <p>Periode: {{ \Carbon\Carbon::parse($tanggal_awal)->format('d F Y') }} -
            {{ \Carbon\Carbon::parse($tanggal_akhir)->format('d F Y') }}</p>
        <p>Digenerate pada: {{ $generated_at }}</p>
    </div>

    <div class="filter-info">
        <h3>Filter Yang Diterapkan:</h3>
        <div class="filter-row">
            <div class="filter-item">
                <strong>Periode:</strong> {{ \Carbon\Carbon::parse($tanggal_awal)->format('d/m/Y') }} -
                {{ \Carbon\Carbon::parse($tanggal_akhir)->format('d/m/Y') }}
            </div>
            <div class="filter-item">
                @php $jenisLabel = is_array($filter_jenis) ? implode(', ', $filter_jenis) : ($filter_jenis ?: 'Semua Jenis'); @endphp
                <strong>Jenis Transaksi:</strong> {{ $jenisLabel }}
            </div>
        </div>
        @if ($filter_keyword)
            <div class="filter-row">
                <div class="filter-item">
                    <strong>Kata Kunci:</strong> "{{ $filter_keyword }}"
                </div>
            </div>
        @endif
    </div>

    <!-- Summary Cards - Table Layout for Horizontal Display (Portrait) -->
    <table style="width: 100%; margin-bottom: 15px; border-collapse: separate; border-spacing: 6px;">
        <tr>
            <!-- Total Masuk -->
            <td
                style="width: 25%; background: #ffffff; border: 1px solid #d1d5db; border-left: 3px solid #10b981; border-radius: 4px; padding: 8px 6px; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1); text-align: center; vertical-align: top;">
                <h4 style="margin: 0 0 4px 0; font-size: 7px; font-weight: 600; color: #374151;">TOTAL MASUK</h4>
                <div style="font-size: 9px; font-weight: 700; color: #059669;">
                    Rp {{ number_format($total_masuk, 0, ',', '.') }}
                </div>
            </td>

            <!-- Total Keluar -->
            <td
                style="width: 25%; background: #ffffff; border: 1px solid #d1d5db; border-left: 3px solid #ef4444; border-radius: 4px; padding: 8px 6px; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1); text-align: center; vertical-align: top;">
                <h4 style="margin: 0 0 4px 0; font-size: 7px; font-weight: 600; color: #374151;">TOTAL KELUAR</h4>
                <div style="font-size: 9px; font-weight: 700; color: #dc2626;">
                    Rp {{ number_format($total_keluar, 0, ',', '.') }}
                </div>
            </td>

            <!-- Saldo Akhir -->
            <td
                style="width: 25%; background: #ffffff; border: 1px solid #d1d5db; border-left: 3px solid #3b82f6; border-radius: 4px; padding: 8px 6px; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1); text-align: center; vertical-align: top;">
                <h4 style="margin: 0 0 4px 0; font-size: 7px; font-weight: 600; color: #374151;">SALDO AKHIR</h4>
                <div style="font-size: 9px; font-weight: 700; color: {{ $saldo_akhir >= 0 ? '#059669' : '#dc2626' }};">
                    Rp {{ number_format($saldo_akhir, 0, ',', '.') }}
                </div>
                <div style="font-size: 6px; color: {{ $saldo_akhir >= 0 ? '#059669' : '#dc2626' }};">
                    {{ $saldo_akhir >= 0 ? 'Surplus' : 'Defisit' }}
                </div>
            </td>

            <!-- Total Transaksi -->
            <td
                style="width: 25%; background: #ffffff; border: 1px solid #d1d5db; border-left: 3px solid #6b7280; border-radius: 4px; padding: 8px 6px; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1); text-align: center; vertical-align: top;">
                <h4 style="margin: 0 0 4px 0; font-size: 7px; font-weight: 600; color: #374151;">TOTAL TRANSAKSI</h4>
                <div style="font-size: 10px; font-weight: 700; color: #374151;">
                    {{ count($transaksi) }}
                </div>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th class="text-center">Tanggal</th>
                <th>Jenis</th>
                <th>Deskripsi</th>
                <th>Vendor</th>
                <th>Prospect/Event</th>
                <th>Rekening</th>
                <th class="text-right">Jumlah</th>
                <th class="text-right">Saldo</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksi as $item)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                    <td>{{ $item->jenis }}</td>
                    <td>{{ $item->deskripsi }}</td>
                    <td>{{ $item->vendor_name ?? '-' }}</td>
                    <td>{{ $item->prospect_name ?? '-' }}</td>
                    <td>{{ $item->payment_method_details ?? '-' }}</td>
                    <td class="text-right">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                    <td class="text-right"><strong>{{ number_format($item->saldo ?? 0, 0, ',', '.') }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="no-data">
                        Tidak ada data transaksi yang sesuai dengan filter yang diterapkan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if (count($transaksi) > 0)
        <div class="ringkasan">
            <strong>Ringkasan:</strong><br>
            @if (isset($is_limited) && $is_limited)
                Menampilkan {{ count($transaksi) }} dari {{ $total_records }} transaksi (dibatasi {{ $max_records }}
                record teratas)<br>
            @else
                Total {{ count($transaksi) }} transaksi ditemukan<br>
            @endif
            Periode: {{ \Carbon\Carbon::parse($tanggal_awal)->format('d F Y') }} -
            {{ \Carbon\Carbon::parse($tanggal_akhir)->format('d F Y') }}
            @if (isset($error_message))
                <br><em style="color: #dc3545;">{{ $error_message }}</em>
            @endif
        </div>
    @endif

    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis oleh sistem Makna Online<br>
            Tanggal Generate: {{ $generated_at }}</p>
    </div>
</body>

</html>
