<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proposal Aplikasi #{{ $prospectApp->id }} - {{ $prospectApp->company_name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: a4 portrait;
            margin: 1cm 1cm 1cm 2cm;
        }

        body {
            color: #000000;
            font-family: 'Poppins', Arial, sans-serif;
            font-size: 18px;
            font-weight: 400;
            line-height: 1;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            max-width: 100%;
        }

        /* Header */
        .header {
            border-bottom: 1px solid #ddd;
            margin-bottom: 1px;
            padding-bottom: 1px;
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
            margin: 0;
            line-height: 1;
        }

        .header h1 {
            font-size: 25px;
            margin: 0;
            font-weight: bold;
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

        /* Document Title */
        .document-title {
            margin: 10px 0;
            text-align: center;
        }

        .document-title h1 {
            font-size: 25px;
            margin-bottom: 0;
            text-transform: uppercase;
        }

        .document-title h4 {
            font-size: 16px;
            font-weight: normal;
            margin-top: 1px;
        }

        /* Document Details */
        .document-details td {
            border: none;
            padding: 20px 0;
            vertical-align: top;
            width: 50%;
        }

        .document-details address {
            font-size: 16px;
            font-style: normal;
            line-height: 1;
        }

        /* Info Table */
        .info-table {
            display: table;
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000000;
            margin-bottom: 20px;
        }

        .info-table thead th {
            background-color: #eceff1;
            color: #37474f;
            font-weight: bold;
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #90a4ae;
            border-right: 1px solid #cfd8dc;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 0.5px;
            vertical-align: middle;
        }

        .info-table thead th:last-child {
            border-right: none;
        }

        .info-table tbody td {
            padding: 10px 10px;
            border-bottom: 1px solid #cfd8dc;
            border-right: 1px solid #cfd8dc;
            vertical-align: top;
            font-size: 16px;
            color: #000000;
        }

        .info-table tbody td:last-child {
            border-right: none;
        }

        .info-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Section styling */
        .section {
            margin-bottom: 20px;
        }

        .section-title {
            background-color: #2c3e50;
            color: white;
            padding: 8px 12px;
            margin: 20px 0 10px 0;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* Text utilities */
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
            /* margin-top: 2px; */
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

        .bg-danger {
            background-color: #dc3545;
            color: #fff;
        }

        .bg-info {
            background-color: #17a2b8;
            color: #fff;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 50px;
            background-color: #f8f9fa;
            border-top: 1px solid #ddd;
            padding: 10px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }

        /* Page break */
        .page-break {
            page-break-after: always;
        }

        /* Watermark */
        .watermark {
            color: rgba(0, 0, 0, 0.1);
            font-size: 120px;
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

        .watermark.lunas {
            color: rgba(40, 167, 69, 0.15);
            /* Green for Lunas */
        }

        .watermark.belum-lunas {
            color: rgba(220, 53, 69, 0.15);
            /* Red for Belum Lunas */
        }
    </style>
</head>

<body>
    @php
        $isPaid = $prospectApp->harga > 0 && $prospectApp->harga == $prospectApp->bayar;
    @endphp

    <div class="watermark {{ $isPaid ? 'lunas' : 'belum-lunas' }}">
        {{ $isPaid ? 'Lunas' : 'Belum Lunas' }}
    </div>
    <!-- Header -->
    <div class="header">
        <h1>PT. MAKNA KREATIF INDONESIA</h1>
        <h2>Wedding Organizer Financial System - {{ config('app.name', 'WOFINS') }}</h2>
        <p>Email: office@wofins.id | Phone: +62 813 7318 3794</p>
    </div>

    <!-- Document Title -->
    <div class="document-title">
        <h1>INVOICE Aplikasi Financial System</h1>
        <h4>Application User: #{{ $prospectApp->company_name }}</h4>
    </div>

    <!-- Document Details -->
    <table class="document-details">
        <tr>
            <td>
                <address>
                    <strong>Kepada:</strong><br>
                    Ibu / Bpk {{ $prospectApp->full_name }}<br>
                    {{ $prospectApp->position }}<br>
                    Email: {{ $prospectApp->email }}<br>
                    Phone: +62{{ $prospectApp->phone }}<br>
                    @if ($prospectApp->name_of_website)
                        Website: {{ $prospectApp->name_of_website }}<br>
                    @endif
                </address>
            </td>
            <td class="text-right">
                <address>
                    <strong>Detail Aplikasi:</strong><br>
                    Tanggal Pengajuan:
                    {{ $prospectApp->submitted_at ? $prospectApp->submitted_at->format('d M Y') : $prospectApp->created_at->format('d M Y') }}<br>
                    Industri: {{ $prospectApp->industry->industry_name ?? 'Tidak Ditentukan' }}<br>
                    @if ($prospectApp->user_size)
                        Ukuran Perusahaan: {{ $prospectApp->user_size }} karyawan<br>
                    @endif
                </address>
            </td>
        </tr>
    </table>

    <!-- Company Information Section -->
    <div class="section">
        <div class="section-title">Informasi Perusahaan</div>
        <table class="info-table">
            <thead>
                <tr>
                    <th style="width: 30%;">Detail</th>
                    <th>Informasi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Nama Perusahaan</strong></td>
                    <td>{{ $prospectApp->company_name }}</td>
                </tr>
                <tr>
                    <td><strong>Industri</strong></td>
                    <td>{{ $prospectApp->industry->industry_name ?? 'Tidak Ditentukan' }}</td>
                </tr>
                @if ($prospectApp->name_of_website)
                    <tr>
                        <td><strong>Website/Domain</strong></td>
                        <td>{{ $prospectApp->name_of_website }}</td>
                    </tr>
                @endif
                @if ($prospectApp->user_size)
                    <tr>
                        <td><strong>Ukuran Perusahaan</strong></td>
                        <td>{{ $prospectApp->user_size }} karyawan</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Contact Information Section -->
    <div class="section">
        <div class="section-title">Detail Tagihan</div>
        <table class="info-table">
            <thead>
                <tr>
                    <th style="width: 30%;">Deskripsi</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Total Paket Awal</strong></td>
                    <td>Rp. {{ number_format($prospectApp->harga, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td><strong>Telah Dibayarkan</strong></td>
                    <td>Rp. {{ number_format($prospectApp->bayar, 0, ',', '.') }}
                        @if (($prospectApp->bayar ?? 0) > 0 && $prospectApp->tgl_bayar)
                            Tanggal {{ $prospectApp->tgl_bayar->format('d M Y') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td><strong>Sisa Pembayaran</strong></td>
                    <td>Rp. {{ number_format($prospectApp->harga - $prospectApp->bayar, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td><strong>Status Pembayaran</strong></td>
                    <td>
                        @if ($prospectApp->harga > 0 && $prospectApp->harga == $prospectApp->bayar)
                            <span class="badge bg-success">LUNAS</span>
                        @else
                            <span class="badge bg-warning">BELUM LUNAS</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- <!-- Interest & Requirements Section -->
    @if ($prospectApp->reason_for_interest)
        <div class="section">
            <div class="section-title">Alasan Minat & Kebutuhan</div>
            <div style="padding: 15px; border: 1px solid #ddd; background-color: #f9f9f9;">
                <div class="info-description">
                    {{ $prospectApp->reason_for_interest }}
                </div>
            </div>
        </div>
    @endif --}}

    <!-- Proposal Services Section -->
    <div class="section">
        <div class="section-title">Layanan yang Diusulkan</div>
        <table class="info-table">
            <thead>
                <tr>
                    <th style="width: 20%;">Service</th>
                    <th style="width: 60%;">Deskripsi</th>
                    <th style="width: 20%;">Harga</th>
                </tr>
            </thead>
            <tbody>
                @if ($prospectApp->service)
                    <tr>
                        <td>
                            @if ($prospectApp->service === 'wofins')
                                Wofins Planning
                            @elseif($prospectApp->service === 'eo_management')
                                EO Management
                            @else
                                {{ $prospectApp->service }}
                            @endif
                        </td>
                        <td>
                            @if ($prospectApp->service === 'wofins')
                                - Sistem manajemen keuangan untuk wedding organizer<br>
                                - Domain: {{ $prospectApp->name_of_website ?? 'Tidak Ditentukan' }} selama 2 tahun<br>
                                - Mantenance dan support selama 2 tahun
                                - Hosting dan domain gratis selama 2 tahun
                            @elseif($prospectApp->service === 'eo_management')
                                Manajemen acara untuk event organizer
                            @else
                                {{ $prospectApp->service }}
                            @endif
                        </td>
                        <td class="text-right">
                            @if ($prospectApp->harga)
                                Rp {{ number_format($prospectApp->harga, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @else
                    <tr>
                        <td colspan="3" class="text-center">Belum ada layanan yang dipilih.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Next Steps Section -->
    <div class="section">
        <div class="section-title">Langkah Selanjutnya</div>
        <div style="padding: 15px; border: 1px solid #ddd; background-color: #f9f9f9;">
            <ol style="margin: 0; padding-left: 20px;">
                <li><strong>Konsultasi Gratis:</strong> Tim kami akan menghubungi Anda dalam 24 jam untuk diskusi lebih
                    lanjut</li>
                <li><strong>Analisis Kebutuhan:</strong> Evaluasi mendalam terhadap kebutuhan spesifik perusahaan Anda
                </li>
                <li><strong>Proposal Detail:</strong> Penyusunan proposal teknis dan commercial yang sesuai</li>
                <li><strong>Implementasi:</strong> Eksekusi project sesuai timeline yang disepakati</li>
                <li><strong>Pembayaran:</strong> Dapat dilakukan melalui transfer rekening Bank BCA 2910777800 atas
                    nama Perk Hastana Indonesia</li>
            </ol>
        </div>
    </div>

    <!-- Contact Information -->
    <div class="section">
        <div class="section-title">Informasi Kontak</div>
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; padding: 10px; border: 1px solid #ddd;">
                    <strong>Sales Manager</strong><br>
                    Satra<br>
                    Email: sales@wofins.id<br>
                    Phone: +62 822-9796-2600
                </td>
                <td style="width: 50%; padding: 10px; border: 1px solid #ddd;">
                    <strong>Technical Manager</strong><br>
                    Rama Dhona Utama<br>
                    Email: tech@wofins.id<br>
                    Phone: +62 813 7318 3794
                </td>
            </tr>
        </table>
    </div>

    <!-- Footer Information -->
    <hr>
    <div class="text-center small">
        <p><strong>Terima kasih atas kepercayaan Anda!</strong></p>
        <p>Dari WO, untuk WO. Karena rapi sejak dini = SIAP JADI BESAR</p>
        <p>Dokumen ini dibuat secara otomatis pada {{ now()->format('d M Y H:i') }}</p>
        <p>© {{ date('Y') }} {{ config('app.name', 'Wedding Organizer Financial System') }}. All rights reserved.
        </p>
    </div>

</body>

</html>
