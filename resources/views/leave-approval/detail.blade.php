<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Persetujuan Cuti - {{ $record->user->name }} - PT. Makna Kreatif Indonesia</title>
    <meta name="author" content="PT. Makna Kreatif Indonesia">
    <meta name="description" content="Detail Persetujuan Cuti - PT. Makna Kreatif Indonesia">
    <meta name="keywords" content="Persetujuan Cuti, Leave Approval, PT. Makna Kreatif Indonesia" />
    <meta name="robots" content="NOINDEX,NOFOLLOW">
    
    <!-- Favicons -->
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

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- HTML2PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="{{ asset('assetssimulasi/js/vendor/jquery-3.6.0.min.js') }}"></script>
    
    <!-- Bootstrap -->
    <link rel="stylesheet" href="{{ asset('assetssimulasi/css/bootstrap.min.css') }}">
    <!-- Theme Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assetssimulasi/css/style.css') }}">
    
    <style>
        * {
            font-family: 'Poppins', sans-serif !important;
        }
        
        body {
            font-family: 'Poppins', sans-serif !important;
            background-color: #f8f9fa;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif !important;
            font-weight: 600;
        }
        
        .big-title {
            font-family: 'Poppins', sans-serif !important;
            font-weight: 800;
        }
        
        th, td {
            font-family: 'Poppins', sans-serif !important;
        }
        
        th {
            text-transform: uppercase;
            font-weight: 600;
        }
        
        .table-title {
            font-family: 'Poppins', sans-serif !important;
            font-weight: 700;
        }
        
        address {
            font-family: 'Poppins', sans-serif !important;
            font-weight: 400;
        }
        
        .invoice-note {
            font-family: 'Poppins', sans-serif !important;
            font-weight: 400;
        }
        
        .company-address {
            font-family: 'Poppins', sans-serif !important;
            font-weight: 400;
        }

        /* Print/PDF-specific rules */
        @media print {
            * {
                font-family: 'Poppins', sans-serif !important;
                font-size: 10px !important;
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            
            .no-print {
                display: none !important;
            }
            
            body {
                background-color: white !important;
                margin: 0 !important;
                padding: 0 !important;
                line-height: 1.4 !important;
            }
            
            .container,
            .approval-container {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 15mm !important;
                box-shadow: none !important;
            }
            
            .invoice-container,
            .invoice-content {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
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
                line-height: 1.3 !important;
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
            
            .badge {
                font-size: 8px !important;
                padding: 3px 6px !important;
            }
            
            .alert {
                font-size: 9px !important;
                padding: 8px !important;
                margin: 10px 0 !important;
            }
            
            /* Prevent page breaks in important sections */
            .themeholy-header,
            .invoice-note,
            .status-approved {
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
            
            .bg-primary {
                background-color: #007bff !important;
                color: white !important;
            }
            
            .bg-warning {
                background-color: #ffc107 !important;
                color: #212529 !important;
            }
            
            .bg-success {
                background-color: #28a745 !important;
                color: white !important;
            }
            
            .alert-warning {
                background-color: #fff3cd !important;
                color: #856404 !important;
                border: 1px solid #ffeaa7 !important;
            }
        }
        
        .action-buttons {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        .btn-action {
            margin-left: 10px;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-back {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-back:hover {
            background-color: #5a6268;
        }
        
        .btn-print {
            background-color: #28a745;
            color: white;
        }
        
        .btn-print:hover {
            background-color: #218838;
        }
        
        .btn-download {
            background-color: #007bff;
            color: white;
        }
        
        .btn-download:hover {
            background-color: #0056b3;
        }
        
        .approval-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        
        .status-approved {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
    <!-- Action Buttons -->
    <div class="action-buttons no-print">
        <button onclick="window.history.back()" class="btn-action btn-back">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </button>
        
        <button onclick="window.print()" class="btn-action btn-print print_btn">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H3a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H7a2 2 0 00-2 2v4a2 2 0 002 2z"></path>
            </svg>
            Cetak
        </button>
        
        <button id="download_btn" class="btn-action btn-download download_btn">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Download PDF
        </button>
    </div>

    <!-- Main Content -->
    <div class="approval-container" id="download_section" data-employee-name="{{ $record->user->name }}">
        <div class="invoice-container themeholy-invoice">
            <main class="main-invoice">
                <div class="invoice-content">
                    <!-- Header with Logo -->
                    <header class="themeholy-header header-layout11">
                        <div class="row align-items-center justify-content-between">
                            <div class="col-auto">
                                <div class="header-logo">
                                    <img src="{{ asset('images/logomki.png') }}" alt="PT. Makna Kreatif Indonesia">
                                </div>
                            </div>
                            <div class="col-auto">
                                <h1 class="big-title">DETAIL PERSETUJUAN CUTI</h1>
                                <span><b>Karyawan :</b> {{ $record->user->name }}</span>
                                <span><b>Periode :</b> {{ \Carbon\Carbon::parse($record->start_date)->format('F Y') }}</span>
                            </div>
                        </div>
                    </header>

                    <!-- Company Info -->
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

                    <!-- Status Badge -->
                    <div class="text-center mb-4">
                        <div class="status-approved badge rounded-pill px-3 py-2">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" style="display: inline-block; margin-right: 5px;">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            PERMOHONAN CUTI DISETUJUI
                        </div>
                    </div>

                    <!-- Informasi Karyawan -->
                    <p class="table-title"><b>Informasi Karyawan :</b></p>
                    <table class="invoice-table table-style8">
                        <tbody>
                            <tr>
                                <th>Nama Lengkap</th>
                                <td>{{ $record->user->name }}</td>
                                <th>Email</th>
                                <td>{{ $record->user->email }}</td>
                            </tr>
                            <tr>
                                <th>ID Karyawan</th>
                                <td>{{ $record->user->employee_id ?? 'N/A' }}</td>
                                <th>Departemen</th>
                                <td>{{ ucfirst($record->user->department ?? 'N/A') }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Detail Permohonan Cuti -->
                    <p class="table-title"><b>Detail Permohonan Cuti :</b></p>
                    <table class="invoice-table table-style8">
                        <tbody>
                            <tr>
                                <th>Jenis Cuti</th>
                                <td>{{ $record->leaveType->name }}</td>
                                <th>Total Hari</th>
                                <td><strong>{{ $record->total_days }} hari</strong></td>
                            </tr>
                            <tr>
                                <th>Tanggal Mulai</th>
                                <td>{{ \Carbon\Carbon::parse($record->start_date)->format('d F Y') }}</td>
                                <th>Tanggal Selesai</th>
                                <td>{{ \Carbon\Carbon::parse($record->end_date)->format('d F Y') }}</td>
                            </tr>
                            @if($record->reason)
                            <tr>
                                <th>Alasan Cuti</th>
                                <td colspan="3">{{ $record->reason }}</td>
                            </tr>
                            @endif

                            @if($record->leaveType->name === 'Cuti Pengganti')
                            <tr>
                                <th>Tanggal Pengganti</th>
                                <td>{{ $record->substitution_date ? \Carbon\Carbon::parse($record->substitution_date)->format('d F Y') : '-' }}</td>
                                <th>Alasan Pengganti</th>
                                <td>{{ $record->substitution_notes ?? '-' }}</td>
                            </tr>
                            @if($record->leaveBalanceHistory)
                            <tr>
                                <th>Sumber Top Up</th>
                                <td colspan="3">
                                    {{ \Carbon\Carbon::parse($record->leaveBalanceHistory->transaction_date)->format('d F Y') }} - 
                                    {{ $record->leaveBalanceHistory->reason }} 
                                    (<strong>+{{ $record->leaveBalanceHistory->amount }} hari</strong>)
                                </td>
                            </tr>
                            @endif
                            @endif
                        </tbody>
                    </table>

                    <!-- Informasi Persetujuan -->
                    <p class="table-title"><b>Informasi Persetujuan :</b></p>
                    <table class="invoice-table table-style8">
                        <tbody>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <span class="status-approved badge rounded-pill px-2 py-1">
                                        <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20" style="display: inline-block; margin-right: 3px;">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Disetujui
                                    </span>
                                </td>
                                <th>Disetujui Oleh</th>
                                <td><strong>{{ $record->approver->name ?? 'System Admin' }}</strong></td>
                            </tr>
                            <tr>
                                <th>Tanggal Disetujui</th>
                                <td>{{ $record->updated_at->format('d F Y, H:i') }} WIB</td>
                                <th>Tanggal Pengajuan</th>
                                <td>{{ $record->created_at->format('d F Y, H:i') }} WIB</td>
                            </tr>
                            @if($record->approval_notes)
                            <tr>
                                <th>Catatan Persetujuan</th>
                                <td colspan="3" class="bg-light p-2 rounded">{{ $record->approval_notes }}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>

                    <!-- Dampak pada Saldo Cuti -->
                    <p class="table-title"><b>Dampak pada Saldo Cuti :</b></p>
                    @if($leaveBalance)
                    <table class="invoice-table table-stripe3">
                        <thead>
                            <tr>
                                <th>Hak Cuti</th>
                                <th>Terpakai</th>
                                <th>Sisa Cuti</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center">
                                    <div class="badge bg-primary text-white p-2">
                                        <strong>{{ $leaveBalance->allocated_days }} hari</strong>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="badge bg-warning text-dark p-2">
                                        <strong>{{ $leaveBalance->used_days }} hari</strong>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="badge bg-success text-white p-2">
                                        <strong>{{ $leaveBalance->remaining_days }} hari</strong>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="text-muted small mt-2">
                        <p>* Saldo cuti akan otomatis terupdate setelah persetujuan ini</p>
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" style="display: inline-block; margin-right: 5px;">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        Data saldo cuti tidak ditemukan. Mohon hubungi HR untuk memastikan saldo cuti telah di-generate.
                    </div>
                    @endif

                    <!-- Dokumen Pendukung (jika ada) -->
                    @if(!empty($record->documents))
                    <p class="table-title mt-4"><b>Dokumen Pendukung :</b></p>
                    <div class="row">
                        @foreach($record->documents as $document)
                        <div class="col-md-6 mb-2">
                            <div class="card">
                                <div class="card-body py-2">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: inline-block; margin-right: 5px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <a href="{{ asset('storage/' . $document) }}" target="_blank" class="text-decoration-none">
                                        {{ basename($document) }}
                                    </a>
                                    <small class="text-muted">(Klik untuk download)</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <!-- Footer Note -->
                    <p class="invoice-note mt-4">
                        <svg width="14" height="18" viewBox="0 0 14 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3.64581 13.7917H10.3541V12.5417H3.64581V13.7917ZM3.64581 10.25H10.3541V9.00002H3.64581V10.25ZM1.58331 17.3334C1.24998 17.3334 0.958313 17.2084 0.708313 16.9584C0.458313 16.7084 0.333313 16.4167 0.333313 16.0834V1.91669C0.333313 1.58335 0.458313 1.29169 0.708313 1.04169C0.958313 0.791687 1.24998 0.666687 1.58331 0.666687H9.10415L13.6666 5.22919V16.0834C13.6666 16.4167 13.5416 16.7084 13.2916 16.9584C13.0416 17.2084 12.75 17.3334 12.4166 17.3334H1.58331ZM8.47915 5.79169V1.91669H1.58331V16.0834H12.4166V5.79169H8.47915ZM1.58331 1.91669V5.79169V1.91669V16.0834V1.91669Z" fill="#2D7CFE" />
                        </svg>
                        <b>CATATAN: </b>Detail persetujuan cuti ini dibuat secara otomatis oleh sistem pada {{ now()->format('d F Y H:i') }} WIB. Dokumen ini merupakan bukti sah persetujuan cuti karyawan.
                    </p>

                    <div class="body-shape1"></div>
                </div>
            </main>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="{{ asset('assetssimulasi/js/bootstrap.min.js') }}"></script>
    <!-- PDF Generator -->
    <script src="{{ asset('assetssimulasi/js/jspdf.min.js') }}"></script>
    <script src="{{ asset('assetssimulasi/js/html2canvas.min.js') }}"></script>
    <!-- Main JS -->
    <script src="{{ asset('assetssimulasi/js/main.js') }}"></script>

    <script>
        // Override download functionality for leave approval detail
        $(document).ready(function() {
            $('#download_btn').off('click').on('click', function () {
                var downloadSection = document.getElementById('download_section');
                var employeeName = downloadSection.dataset.employeeName;
                
                var sanitizedName = employeeName.trim().replace(/\s+/g, '_').replace(/[^\w.-]/g, '');
                var finalFilename = 'Detail_Persetujuan_Cuti_' + sanitizedName + '_' + '{{ now()->format("Y-m-d") }}' + '.pdf';
                
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
                $('.action-buttons').hide();
                
                html2pdf().set(opt).from(downloadSection).save()
                    .then(function() {
                        $('.action-buttons').show();
                    })
                    .catch(function(error) {
                        $('.action-buttons').show();
                        console.error('Error generating PDF:', error);
                    });
            });
        });
    </script>

</body>
</html>
