<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Slip Gaji - {{ $user->name }} - PT. Makna Kreatif Indonesia</title>
    <meta name="author" content="PT. Makna Kreatif Indonesia">
    <meta name="description" content="Slip Gaji - PT. Makna Kreatif Indonesia">
    <meta name="keywords" content="Slip Gaji, Payroll, PT. Makna Kreatif Indonesia" />
    <meta name="robots" content="NOINDEX,NOFOLLOW">

    <!-- Mobile Specific Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Favicons - Place favicon.ico in the root directory -->
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('assets/img/favicons/apple-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('assets/img/favicons/apple-icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('assets/img/favicons/apple-icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/img/favicons/apple-icon-76x76.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('assets/img/favicons/apple-icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('assets/img/favicons/apple-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('assets/img/favicons/apple-icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('assets/img/favicons/apple-icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/favicons/apple-icon-180x180.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('assets/img/favicons/android-icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/favicons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('assets/img/favicons/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/favicons/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('assets/img/favicons/manifest.json') }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ asset('assets/img/favicons/ms-icon-144x144.png') }}">
    <meta name="theme-color" content="#ffffff">

    <!--==============================
	  Google Fonts
	============================== -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        * {
            font-family: 'Noto Sans', sans-serif !important;
        }
        
        body {
            font-family: 'Noto Sans', sans-serif !important;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Noto Sans', sans-serif !important;
            font-weight: 600;
        }
        
        .big-title {
            font-family: 'Noto Sans', sans-serif !important;
            font-weight: 800;
        }
        
        th, td {
            font-family: 'Noto Sans', sans-serif !important;
        }
        
        th {
            text-transform: uppercase;
            font-weight: 600;
        }
        
        .table-title {
            font-family: 'Noto Sans', sans-serif !important;
            font-weight: 700;
        }
        
        address {
            font-family: 'Noto Sans', sans-serif !important;
            font-weight: 400;
        }
        
        .invoice-note {
            font-family: 'Noto Sans', sans-serif !important;
            font-weight: 400;
        }
        
        .company-address {
            font-family: 'Noto Sans', sans-serif !important;
            font-weight: 400;
        }
        
        /* Logo positioning - merapat ke kiri 0px */
        .header-logo {
            margin-left: 0 !important;
            padding-left: 0 !important;
        }
        
        .header-logo img {
            margin-left: 0 !important;
            padding-left: 0 !important;
        }
        
        .themeholy-header {
            padding-left: 0 !important;
            margin-left: 0 !important;
        }
        
        .row {
            margin-left: 0 !important;
        }
        
        .col-auto:first-child {
            padding-left: 0 !important;
            margin-left: 0 !important;
        }

        /* Print/PDF-specific rules */
        @media print {
            * {
                font-family: 'Noto Sans', sans-serif !important;
                font-size: 10px !important;
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            
            .no-print {
                display: none !important;
            }
            
            body {
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
                line-height: 1.4 !important;
            }
            
            .invoice-container-wrap,
            .invoice-container {
                width: 100% !important;
                max-width: none !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
            }
            
            .download-inner {
                padding: 15mm !important;
                margin: 0 !important;
                page-break-inside: avoid;
            }
            
            h1, h2, h3, h4, h5, h6 {
                page-break-after: avoid;
                font-weight: 600 !important;
            }
            
            .big-title {
                font-size: 18px !important;
                font-weight: 800 !important;
            }
            
            table {
                width: 100% !important;
                border-collapse: collapse !important;
                page-break-inside: avoid;
                margin-bottom: 15px !important;
            }
            
            th, td {
                border: 1px solid #ddd !important;
                padding: 8px !important;
                font-size: 9px !important;
                vertical-align: top !important;
            }
            
            th {
                background-color: #f8f9fa !important;
                font-weight: 600 !important;
                text-transform: uppercase !important;
            }
            
            .table-title {
                font-size: 12px !important;
                font-weight: 700 !important;
                margin-bottom: 8px !important;
                margin-top: 15px !important;
            }
            
            .invoice-note {
                font-size: 8px !important;
                margin-top: 20px !important;
                page-break-inside: avoid;
            }
            
            address {
                font-size: 9px !important;
                line-height: 1.4 !important;
            }
            
            .header-logo img {
                max-height: 60px !important;
                width: auto !important;
            }
            
            .row {
                margin: 0 !important;
            }
            
            .col-auto {
                padding: 0 5px !important;
            }
            
            /* Prevent page breaks in important sections */
            .themeholy-header,
            .invoice-note {
                page-break-inside: avoid;
            }
            
            /* Ensure backgrounds and colors print */
            * {
                background: transparent !important;
                color: black !important;
                text-shadow: none !important;
                filter: none !important;
                -ms-filter: none !important;
            }
            
            /* Keep essential backgrounds */
            th {
                background-color: #f8f9fa !important;
                color: black !important;
            }
            
            .status-approved {
                background-color: #d4edda !important;
                color: #155724 !important;
                border: 1px solid #c3e6cb !important;
            }
        }
    </style>


    <!--==============================
	    All CSS File
	============================== -->
    <!-- Bootstrap -->
    <link rel="stylesheet" href="{{ asset('assetssimulasi/css/bootstrap.min.css') }}">
    <!-- Theme Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assetssimulasi/css/style.css') }}">

</head>

<body>


    <!--[if lte IE 9]>
    	<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
  <![endif]-->


    <!--********************************
   		Code Start From Here 
	******************************** -->

    <div class="invoice-container-wrap">
        <div class="invoice-container">
            <main>
                <!--==============================
Invoice Area
==============================-->
                <div class="themeholy-invoice invoice_style15">
                    <div class="download-inner" id="download_section" data-employee-name="{{ $user->name }}">
                        <!--==============================
	Header Area
==============================-->
                        <header class="themeholy-header header-layout11">
                            <div class="row align-items-center justify-content-between">
                                <div class="col-auto">
                                    <div class="header-logo">
                                        <a href="#"><img src="{{ asset('images/logomki.png') }}" alt="PT. Makna Kreatif Indonesia"></a>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <h1 class="big-title">SLIP GAJI123</h1>
                                    <span><b>Karyawan :</b> {{ $user->name }}</span>
                                    <span><b>Periode :</b> {{ now()->format('F Y') }}</span>
                                </div>
                            </div>
                        </header>
                        <div class="row justify-content-between my-4">
                            <div class="col-auto">
                                <div class="invoice-left">
                                    <b>Informasi Perusahaan :</b>
                                    <address>
                                        PT. Makna Kreatif Indonesia <br>
                                        Jl. Sintraman Jaya I No.2148, 20 Ilir D II, <br>
                                        Kec. Kemuning, Kota Palembang, Sumatera Selatan 30137
                                    </address>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="invoice-right">
                                    <b>Kontak :</b>
                                    <address>
                                        Email: info@maknawedding.id <br>
                                        Telp: +62 822-9796-2600 <br>
                                        Website: https://paketpernikahan.co.id
                                    </address>
                                </div>
                            </div>
                        </div>

                        <hr class="style1">

                        <p class="table-title"><b>Informasi Karyawan :</b></p>
                        <table class="invoice-table table-style8">
                            <tbody>
                                <tr>
                                    <th>Nama Lengkap</th>
                                    <td>{{ $user->name }}</td>
                                    <th>Email</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th>No Tlp</th>
                                    <td>{{ $user->phone_number ?? 'Tidak ada status' }}</td>
                                    <th>Departemen</th>
                                    <td>{{ ucfirst($user->department ?? 'Tidak ada departemen') }}</td>
                                </tr>
                                @if($record->last_review_date)
                                <tr>
                                    <th>Review Terakhir</th>
                                    <td>{{ \Carbon\Carbon::parse($record->last_review_date)->format('d/m/Y') }}</td>
                                    @if($record->next_review_date)
                                    <th>Review Berikutnya</th>
                                    <td>{{ \Carbon\Carbon::parse($record->next_review_date)->format('d/m/Y') }}</td>
                                    @else
                                    <th></th>
                                    <td></td>
                                    @endif
                                </tr>
                                @endif
                            </tbody>
                        </table>                                                
                        <p class="table-title"><b>Rincian Gaji :</b></p>
                        <table class="invoice-table table-stripe3">
                            <thead>
                                <tr>
                                    <th colspan="2">Komponen</th>
                                    <th class="text-end">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Gaji Pokok</td>
                                    <td class="text-end" colspan="2">Rp {{ number_format($record->gaji_pokok ?? 0, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>Tunjangan Jabatan</td>
                                    <td class="text-end" colspan="2">Rp {{ number_format($record->tunjangan ?? 0, 0, ',', '.') }}</td>
                                </tr>
                                @if($record->bonus && $record->bonus > 0)
                                <tr>
                                    <td>Bonus</td>
                                    <td class="text-end" colspan="2">Rp {{ number_format($record->bonus, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                                @if($record->pengurangan && $record->pengurangan > 0)
                                <tr>
                                    <td>Pengurangan (BPJS & Lainnya)</td>
                                    <td class="text-end" colspan="2">- Rp {{ number_format($record->pengurangan, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                                @php
                                    // Total diterima sudah dihitung di monthly_salary (gaji_pokok + tunjangan - pengurangan)
                                    $totalDiterima = $record->monthly_salary + ($record->bonus ?? 0);
                                @endphp
                            </tbody>
                            <tfoot>
                                <tr style="background-color: #d4edda; font-weight: bold;">
                                    <td class="text-start" colspan="2"><b>Total Diterima</b></td>
                                    <td><b>Rp {{ number_format($record->monthly_salary, 0, ',', '.') }}</b></td>
                                </tr>
                            </tfoot>
                        </table>

                        <p class="table-title mt-4"><b>Ringkasan Gaji Tahunan :</b></p>
                        <table class="invoice-table table-style8">
                            <tbody>
                                <tr>
                                    <th>Gaji Bulanan</th>
                                    <td>Rp {{ number_format($record->monthly_salary, 0, ',', '.') }}</td>
                                    <th>Gaji Tahunan</th>
                                    <td>Rp {{ number_format($record->annual_salary, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Bonus Tahunan</th>
                                    <td>Rp {{ number_format($record->bonus ?? 0, 0, ',', '.') }}</td>
                                    <th>Total Kompensasi</th>
                                    <td><strong>Rp {{ number_format($record->total_compensation, 0, ',', '.') }}</strong></td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="row justify-content-between">
                            <div class="col-auto">
                                <b>Informasi Gaji :</b>
                                <p class="mb-0">Periode: {{ now()->format('F Y') }} <br>
                                    Dibuat: {{ now()->format('d/m/Y H:i') }} WIB</p>
                            </div>
                        </div>

                        @if($record->notes)
                        <div class="mt-4">
                            <p class="table-title"><b>Catatan & Riwayat :</b></p>
                            @php
                                $noteEntries = collect(explode("\n\n", $record->notes))
                                    ->filter(fn($entry) => !empty(trim($entry)))
                                    ->map(function($entry, $index) {
                                        $lines = explode("\n", trim($entry));
                                        $firstLine = $lines[0] ?? '';
                                        
                                        if (preg_match('/\[(\d{2}\/\d{2}\/\d{4})\]/', $firstLine, $matches)) {
                                            $date = $matches[1];
                                            $content = str_replace($matches[0], '', $firstLine);
                                            $content = trim($content);
                                            
                                            if (count($lines) > 1) {
                                                $content .= "\n" . implode("\n", array_slice($lines, 1));
                                            }
                                            
                                            return [
                                                'date' => $date,
                                                'content' => trim($content),
                                                'has_date' => true
                                            ];
                                        } else {
                                            return [
                                                'date' => null,
                                                'content' => $entry,
                                                'has_date' => false
                                            ];
                                        }
                                    })
                                    ->reverse();
                            @endphp
                            
                            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                                @foreach($noteEntries as $index => $entry)
                                    <div style="margin-bottom: 10px; padding: 10px; background: white; border-radius: 5px; border-left: 4px solid #007bff;">
                                        @if($entry['has_date'])
                                            <strong>📅 {{ $entry['date'] }}:</strong><br>
                                        @else
                                            <strong>📄 Catatan Umum:</strong><br>
                                        @endif
                                        <span style="white-space: pre-line;">{{ $entry['content'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <p class="invoice-note mt-3 text-xs">
                            Slip gaji ini dibuat secara otomatis oleh sistem dan data terakhir <br>diperbarui pada {{ $record->updated_at->format('d F Y H:i') }} WIB.
                        </p>
                        <div class="body-shape1"></div>
                    </div>
                    <div class="text-xs invoice-buttons no-print">
                        <button class="print_btn">
                            <svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16.25 13H3.75C3.38542 13 3.08594 13.1172 2.85156 13.3516C2.61719 13.5859 2.5 13.8854 2.5 14.25V19.25C2.5 19.6146 2.61719 19.9141 2.85156 20.1484C3.08594 20.3828 3.38542 20.5 3.75 20.5H16.25C16.6146 20.5 16.9141 20.3828 17.1484 20.1484C17.3828 19.9141 17.5 19.6146 17.5 19.25V14.25C17.5 13.8854 17.3828 13.5859 17.1484 13.3516C16.9141 13.1172 16.6146 13 16.25 13ZM16.25 19.25H3.75V14.25H16.25V19.25ZM17.5 8V3.27344C17.5 2.90885 17.3828 2.60938 17.1484 2.375L15.625 0.851562C15.3646 0.617188 15.0651 0.5 14.7266 0.5H5C4.29688 0.526042 3.71094 0.773438 3.24219 1.24219C2.77344 1.71094 2.52604 2.29688 2.5 3V8C1.79688 8.02604 1.21094 8.27344 0.742188 8.74219C0.273438 9.21094 0.0260417 9.79688 0 10.5V14.875C0.0260417 15.2656 0.234375 15.474 0.625 15.5C1.01562 15.474 1.22396 15.2656 1.25 14.875V10.5C1.25 10.1354 1.36719 9.83594 1.60156 9.60156C1.83594 9.36719 2.13542 9.25 2.5 9.25H17.5C17.8646 9.25 18.1641 9.36719 18.3984 9.60156C18.6328 9.83594 18.75 10.1354 18.75 10.5V14.875C18.776 15.2656 18.9844 15.474 19.375 15.5C19.7656 15.474 19.974 15.2656 20 14.875V10.5C19.974 9.79688 19.7266 9.21094 19.2578 8.74219C18.7891 8.27344 18.2031 8.02604 17.5 8ZM16.25 8H3.75V3C3.75 2.63542 3.86719 2.33594 4.10156 2.10156C4.33594 1.86719 4.63542 1.75 5 1.75H14.7266L16.25 3.27344V8ZM16.875 10.1875C16.3021 10.2396 15.9896 10.5521 15.9375 11.125C15.9896 11.6979 16.3021 12.0104 16.875 12.0625C17.4479 12.0104 17.7604 11.6979 17.8125 11.125C17.7604 10.5521 17.4479 10.2396 16.875 10.1875Z" fill="#00C764" />
                            </svg>
                        </button>
                        <button id="download_btn" class="download_btn">
                            <svg width="25" height="19" viewBox="0 0 25 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.94531 11.1797C8.6849 10.8932 8.6849 10.6068 8.94531 10.3203C9.23177 10.0599 9.51823 10.0599 9.80469 10.3203L11.875 12.3516V6.375C11.901 5.98438 12.1094 5.77604 12.5 5.75C12.8906 5.77604 13.099 5.98438 13.125 6.375V12.3516L15.1953 10.3203C15.4818 10.0599 15.7682 10.0599 16.0547 10.3203C16.3151 10.6068 16.3151 10.8932 16.0547 11.1797L12.9297 14.3047C12.6432 14.5651 12.3568 14.5651 12.0703 14.3047L8.94531 11.1797ZM10.625 0.75C11.7969 0.75 12.8646 1.01042 13.8281 1.53125C14.8177 2.05208 15.625 2.76823 16.25 3.67969C16.8229 3.39323 17.4479 3.25 18.125 3.25C19.375 3.27604 20.4036 3.70573 21.2109 4.53906C22.0443 5.34635 22.474 6.375 22.5 7.625C22.5 8.01562 22.4479 8.41927 22.3438 8.83594C23.151 9.2526 23.7891 9.85156 24.2578 10.6328C24.7526 11.4141 25 12.2865 25 13.25C24.974 14.6562 24.4922 15.8411 23.5547 16.8047C22.5911 17.7422 21.4062 18.224 20 18.25H5.625C4.03646 18.1979 2.70833 17.651 1.64062 16.6094C0.598958 15.5417 0.0520833 14.2135 0 12.625C0.0260417 11.375 0.377604 10.2812 1.05469 9.34375C1.73177 8.40625 2.63021 7.72917 3.75 7.3125C3.88021 5.4375 4.58333 3.88802 5.85938 2.66406C7.13542 1.4401 8.72396 0.802083 10.625 0.75ZM10.625 2C9.08854 2.02604 7.78646 2.54688 6.71875 3.5625C5.67708 4.57812 5.10417 5.85417 5 7.39062C4.94792 7.91146 4.67448 8.27604 4.17969 8.48438C3.29427 8.79688 2.59115 9.33073 2.07031 10.0859C1.54948 10.8151 1.27604 11.6615 1.25 12.625C1.27604 13.875 1.70573 14.9036 2.53906 15.7109C3.34635 16.5443 4.375 16.974 5.625 17H20C21.0677 16.974 21.9531 16.6094 22.6562 15.9062C23.3594 15.2031 23.724 14.3177 23.75 13.25C23.75 12.5208 23.5677 11.8698 23.2031 11.2969C22.8385 10.724 22.3568 10.2682 21.7578 9.92969C21.2109 9.59115 21.0026 9.09635 21.1328 8.44531C21.2109 8.21094 21.25 7.9375 21.25 7.625C21.224 6.73958 20.9245 5.9974 20.3516 5.39844C19.7526 4.82552 19.0104 4.52604 18.125 4.5C17.6302 4.5 17.1875 4.60417 16.7969 4.8125C16.1719 5.04688 15.651 4.90365 15.2344 4.38281C14.7135 3.65365 14.0495 3.08073 13.2422 2.66406C12.4609 2.22135 11.5885 2 10.625 2Z" fill="#2D7CFE" />
                            </svg>
                        </button>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <!-- Invoice Conainter End -->

    <!--==============================
    All Js File
============================== -->
    <!-- Jquery -->
    <script src="{{ asset('assetssimulasi/js/vendor/jquery-3.6.0.min.js') }}"></script>
    <!-- Bootstrap -->
    <script src="{{ asset('assetssimulasi/js/bootstrap.min.js') }}"></script>
    <!-- PDF Generator -->
    <script src="{{ asset('assetssimulasi/js/jspdf.min.js') }}"></script>
    <script src="{{ asset('assetssimulasi/js/html2canvas.min.js') }}"></script>
    <!-- Main Js File -->
    <script src="{{ asset('assetssimulasi/js/main.js') }}"></script>

    <script>
        // Override download functionality for payroll slip
        $(document).ready(function() {
            $('#download_btn').off('click').on('click', function () {
                var downloadSection = document.getElementById('download_section');
                var employeeName = downloadSection.dataset.employeeName;
                
                var sanitizedName = employeeName.trim().replace(/\s+/g, '_').replace(/[^\w.-]/g, '');
                var finalFilename = 'Slip_Gaji_' + sanitizedName + '_' + '{{ now()->format("Y-m") }}' + '.pdf';
                
                var opt = {
                    margin: [30, 25, 30, 25],
                    filename: finalFilename,
                    image: { type: 'png', quality: 1.0 },
                    html2canvas: {
                        scale: 2,
                        useCORS: true,
                        scrollY: 0,
                        windowWidth: document.body.scrollWidth,
                        letterRendering: true,
                        allowTaint: false,
                        backgroundColor: '#ffffff'
                    },
                    pagebreak: { mode: ['avoid-all', 'css', 'legacy'] },
                    jsPDF: { unit: 'pt', format: 'a4', orientation: 'portrait' }
                };

                // Hide buttons before generating PDF
                $('.invoice-buttons').hide();
                
                html2pdf().set(opt).from(downloadSection).save()
                    .then(function() {
                        $('.invoice-buttons').show();
                    })
                    .catch(function(error) {
                        $('.invoice-buttons').show();
                        console.error('Error generating PDF:', error);
                    });
            });
        });
    </script>

</body>

</html>