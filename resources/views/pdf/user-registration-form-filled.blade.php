<!doctype html>
<html class="no-js" lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>{{ $title }} - PT. Makna Kreatif Indonesia</title>
    <meta name="author" content="PT. Makna Kreatif Indonesia">
    <meta name="description" content="{{ $title }} - PT. Makna Kreatif Indonesia">
    <meta name="keywords" content="Form Pendaftaran, Karyawan, PT. Makna Kreatif Indonesia" />
    <meta name="robots" content="NOINDEX,NOFOLLOW">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Noto Sans', sans-serif !important;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Noto Sans', sans-serif !important;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
            background: white;
            padding: 20px;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Noto Sans', sans-serif !important;
            font-weight: 600;
        }
        
        .big-title {
            font-family: 'Noto Sans', sans-serif !important;
            font-weight: 800;
            font-size: 18pt;
            color: #059669;
        }
        
        .table-title {
            font-family: 'Noto Sans', sans-serif !important;
            font-weight: 700;
            font-size: 12pt;
            margin-bottom: 8px;
            margin-top: 15px;
            color: #047857;
        }

        /* Header styling */
        .themeholy-header {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #059669;
        }
        
        .header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }
        
        .header-logo img {
            max-height: 60px;
            width: auto;
        }
        
        .header-info {
            text-align: right;
        }
        
        .header-info span {
            display: block;
            margin-bottom: 5px;
            font-size: 10pt;
        }

        /* Company info section */
        .company-info {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
            font-size: 9pt;
        }
        
        .company-info address {
            font-style: normal;
            line-height: 1.4;
        }

        /* Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        th, td {
            border: 1px solid #10b981;
            padding: 8px;
            font-size: 9pt;
            vertical-align: top;
        }
        
        th {
            background-color: #f0fdf4;
            font-weight: 600;
            text-transform: uppercase;
            color: #047857;
        }
        
        .form-table th {
            width: 25%;
            text-align: left;
        }
        
        .form-table td {
            width: 25%;
            background: white;
        }

        /* Filled values styling */
        .form-value {
            padding: 5px 8px;
            font-size: 9pt;
            color: #047857;
            font-weight: 500;
            min-height: 25px;
            border: 1px solid #10b981;
            background: #f0fdf4;
        }
        
        .form-value.empty-field {
            color: #9ca3af;
            font-style: italic;
            background: #f9fafb;
            border-color: #d1d5db;
        }

        /* Checkbox styling for filled */
        .checkbox-filled {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 4px 8px;
            background: #10b981;
            color: white;
            border-radius: 4px;
            font-size: 9pt;
            font-weight: 500;
        }

        /* Helper text */
        .helper-text {
            font-size: 8pt;
            color: #6b7280;
            font-style: italic;
            margin-top: 2px;
        }

        /* Status section */
        .status-section {
            background: #f0fdf4;
            border: 2px solid #10b981;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }
        
        .status-title {
            background: #10b981;
            color: white;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 10px;
            font-size: 12pt;
            font-weight: 700;
        }

        /* Footer */
        .invoice-note {
            font-size: 8pt;
            color: #6b7280;
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
        }

        /* Print specific styles */
        @media print {
            body {
                padding: 15mm;
                font-size: 9pt;
            }
            
            .big-title {
                font-size: 16pt;
            }
            
            .table-title {
                font-size: 11pt;
            }
            
            th, td {
                font-size: 8pt;
                padding: 6px;
            }
        }
    </style>
</head>
<body>
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Noto Sans', Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #333;
            background: white;
        }
        
        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #059669;
            padding-bottom: 20px;
        }
        
        .company-logo {
            font-size: 24pt;
            font-weight: 700;
            color: #059669;
            margin-bottom: 5px;
        }
        
        .form-title {
            font-size: 18pt;
            font-weight: 600;
            color: #047857;
            margin: 10px 0;
        }
        
        .form-info {
            display: flex;
            justify-content: space-between;
            margin: 15px 0;
            font-size: 10pt;
            color: #666;
        }
        
        .section {
            margin-bottom: 25px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .section-header {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            padding: 12px 15px;
            font-size: 12pt;
            font-weight: 600;
            margin: 0;
        }
        
        .section-content {
            padding: 20px;
            background: #f0fdf4;
        }
        
        .form-row {
            display: flex;
            margin-bottom: 15px;
            gap: 20px;
        }
        
        .form-group {
            flex: 1;
            min-height: 40px;
        }
        
        .form-group.full-width {
            flex: none;
            width: 100%;
        }
        
        .form-label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 5px;
            font-size: 10pt;
        }
        
        .form-value {
            width: 100%;
            min-height: 30px;
            border: 1.5px solid #10b981;
            border-radius: 4px;
            padding: 8px 12px;
            font-size: 10pt;
            background: white;
            box-sizing: border-box;
            color: #047857;
            font-weight: 500;
        }
        
        .form-value.textarea {
            min-height: 60px;
            line-height: 1.5;
        }
        
        .form-value.large-textarea {
            min-height: 80px;
        }
        
        .checkbox-filled {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 4px 8px;
            background: #10b981;
            color: white;
            border-radius: 4px;
            font-size: 10pt;
            font-weight: 500;
        }
        
        .helper-text {
            font-size: 9pt;
            color: #6b7280;
            margin-top: 3px;
            font-style: italic;
        }
        
        .page-footer {
            position: fixed;
            bottom: 15px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 8pt;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
        
        .empty-field {
            color: #9ca3af;
            font-style: italic;
        }
        
        @media print {
            .container {
                padding: 15px;
            }
            
            .page-footer {
                position: fixed;
                bottom: 0;
            }
        }
    </style>
</head>
<body>
    <!--==============================
    Header Area
    ==============================-->
    <header class="themeholy-header">
        <div class="header-row">
            <div class="header-logo">
                <img src="{{ asset('images/logomki.png') }}" alt="PT. Makna Kreatif Indonesia">
            </div>
            <div class="header-info">
                <h1 class="big-title">{{ $title }}</h1>
                <span><b>No. Form:</b> {{ $form_number }}</span>
                <span><b>Tanggal:</b> {{ $generated_date }}</span>
            </div>
        </div>
    </header>

    <!--==============================
    Company Information
    ==============================-->
    <div class="company-info">
        <div>
            <b>Informasi Perusahaan :</b>
            <address>
                PT. Makna Kreatif Indonesia <br>
                Jl. Sintraman Jaya I No.2148, 20 Ilir D II, <br>
                Kec. Kemuning, Kota Palembang, Sumatera Selatan 30137
            </address>
        </div>
        <div>
            <b>Kontak :</b>
            <address>
                Email: info@maknawedding.id <br>
                Telp: +62 822-9796-2600 <br>
                Website: https://paketpernikahan.co.id
            </address>
        </div>
    </div>

    <hr style="border: 1px solid #ddd; margin: 20px 0;">

    <!--==============================
    Section 1: Informasi Dasar
    ==============================-->
    <p class="table-title"><b>📋 INFORMASI DASAR</b></p>
    <table class="form-table">
        <tbody>
            <tr>
                <th>Nama Lengkap *</th>
                <td><div class="form-value">{{ $name ?? 'Tidak diisi' }}</div></td>
                <th>Email *</th>
                <td><div class="form-value">{{ $email ?? 'Tidak diisi' }}</div></td>
            </tr>
            <tr>
                <th>Nomor Telepon</th>
                <td><div class="form-value {{ empty($phone_number) ? 'empty-field' : '' }}">{{ $phone_number ?? 'Tidak diisi' }}</div></td>
                <th>Tanggal Lahir</th>
                <td><div class="form-value {{ empty($date_of_birth) ? 'empty-field' : '' }}">{{ $date_of_birth ? \Carbon\Carbon::parse($date_of_birth)->format('d/m/Y') : 'Tidak diisi' }}</div></td>
            </tr>
            <tr>
                <th>Jenis Kelamin</th>
                <td>
                    @if($gender == 'male')
                        <span class="checkbox-filled">✓ Laki-laki</span>
                    @elseif($gender == 'female')
                        <span class="checkbox-filled">✓ Perempuan</span>
                    @else
                        <span class="form-value empty-field">Tidak dipilih</span>
                    @endif
                </td>
                <th>Departemen *</th>
                <td>
                    @if($department == 'bisnis')
                        <span class="checkbox-filled">✓ Bisnis</span>
                    @elseif($department == 'operasional')
                        <span class="checkbox-filled">✓ Operasional</span>
                    @else
                        <span class="form-value empty-field">Tidak dipilih</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Alamat Lengkap</th>
                <td colspan="3"><div class="form-value {{ empty($address) ? 'empty-field' : '' }}">{{ $address ?? 'Tidak diisi' }}</div></td>
            </tr>
        </tbody>
    </table>

    <!--==============================
    Section 2: Informasi Pekerjaan
    ==============================-->
    <p class="table-title"><b>💼 INFORMASI PEKERJAAN</b></p>
    <table class="form-table">
        <tbody>
            <tr>
                <th>Tanggal Mulai Kerja</th>
                <td><div class="form-value {{ empty($hire_date) ? 'empty-field' : '' }}">{{ $hire_date ? \Carbon\Carbon::parse($hire_date)->format('d/m/Y') : 'Tidak diisi' }}</div></td>
                <th>Status Jabatan</th>
                <td><div class="form-value empty-field">Akan diisi oleh HRD</div></td>
            </tr>
            <tr>
                <th>Role/Hak Akses</th>
                <td><div class="form-value empty-field">Akan ditentukan oleh Admin</div></td>
                <th>Status Akun</th>
                <td><span class="checkbox-filled">✓ Aktif - Dapat mengakses sistem</span></td>
            </tr>
        </tbody>
    </table>

    <!--==============================
    Section 3: Kontak Darurat & Catatan
    ==============================-->
    <p class="table-title"><b>🚨 KONTAK DARURAT & CATATAN</b></p>
    <table class="form-table">
        <tbody>
            <tr>
                <th>Kontak Darurat</th>
                <td colspan="3"><div class="form-value {{ empty($emergency_contact) ? 'empty-field' : '' }}">{!! empty($emergency_contact) ? 'Tidak diisi' : nl2br(e($emergency_contact)) !!}</div></td>
            </tr>
            <tr>
                <th>Catatan Karyawan (Internal)</th>
                <td colspan="3"><div class="form-value {{ empty($notes) ? 'empty-field' : '' }}">{!! empty($notes) ? 'Tidak ada catatan' : nl2br(e($notes)) !!}</div></td>
            </tr>
        </tbody>
    </table>

    <!--==============================
    Status Verifikasi
    ==============================-->
    <div class="status-section">
        <div class="status-title">✅ FORMULIR TELAH DIISI</div>
        <p style="font-size: 10pt; margin-bottom: 10px;">Form ini telah diisi dan siap untuk diproses oleh HRD</p>
        <div style="font-size: 9pt; color: #047857;">
            <p><strong>Langkah Selanjutnya:</strong></p>
            <p>• Input data ke sistem Filament<br>
            • Upload dokumen yang diperlukan<br>
            • Set role dan hak akses sesuai jabatan<br>
            • Aktivasi akun pengguna</p>
        </div>
    </div>

    <!--==============================
    Footer Note
    ==============================-->
    <p class="invoice-note">
        {{ $company }} - {{ $title }} | Generated: {{ $generated_date }} | {{ $form_number }}
        <br>
        <em>Formulir ini sudah terisi dan siap diproses oleh bagian HRD</em>
        <br>
        Data sudah terverifikasi dan dapat langsung diinput ke sistem untuk pembuatan akun karyawan.
    </p>
</body>
</html>