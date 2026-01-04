<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>MAKNA - Simulasi Paket Pernikahan - PT. Makna Kreatif Indonesia</title>
    <meta name="author" content="themeholy">
    <meta name="description" content="Invar - Invoice HTML Template">
    <meta name="keywords" content="Invar - Invoice HTML Template" />
    <meta name="robots" content="INDEX,FOLLOW">

    <!-- Mobile Specific Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Favicons - Place favicon.ico in the root directory -->
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('assets/img/favicons/apple-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="assets/img/favicons/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="assets/img/favicons/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="assets/img/favicons/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="assets/img/favicons/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="assets/img/favicons/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="assets/img/favicons/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="assets/img/favicons/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/favicons/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="assets/img/favicons/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="assets/img/favicons/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/img/favicons/favicon-16x16.png">
    <link rel="manifest" href="assets/img/favicons/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="assets/img/favicons/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">

    <!--==============================
 Google Fonts
 ============================== -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">


    <!--==============================
 All CSS File
 ============================== -->
    <!-- Bootstrap -->
    <link rel="stylesheet" href="{{ asset('assetssimulasi/css/bootstrap.min.css') }}">
    <!-- Theme Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assetssimulasi/css/style.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        body,
        * {
            font-family: 'Noto Sans', sans-serif !important;
        }

        /* Remove gray background */
        body {
            background-color: #ffffff !important;
        }

        .invoice-container-wrap {
            background-color: #ffffff !important;
            display: flex !important;
            justify-content: center !important;
            align-items: flex-start !important;
            min-height: 100vh !important;
            padding: 0px 0 !important;
        }

        .invoice-container {
            background-color: #ffffff !important;
            box-shadow: none !important;
            border: 0.5px solid #ddd !important;
            margin: 50 !important;
            padding: 20px !important;
            width: 980px !important;
            max-width: 980px !important;
            min-height: 100vh !important;
        }

        th {
            text-transform: uppercase;
        }

        /* Styling for addition items */
        .addition-row {
            background-color: #f8f9fa;
        }

        .addition-amount {
            color: #28a745 !important;
            font-weight: 600 !important;
        }

        .reduction-amount {
            color: #dc3545 !important;
            font-weight: 600 !important;
        }

        /* Fix for excessive spacing in description lists/paragraphs */
        .invoice-table td p,
        .invoice-table td ul,
        .invoice-table td ol {
            margin-bottom: 2px !important;
            margin-top: 0px !important;
        }

        /* General list indentation */
        .invoice-table td ol {
            padding-left: 20px !important;
        }

        /* Extra indentation for bullet points (ul) to look like sub-items */
        .invoice-table td ul {
            padding-left: 45px !important;
        }

        .invoice-table td li {
            margin-bottom: 0px !important;
        }

        /* Force black color for text elements to ensure consistency */
        .invoice-table td p,
        .invoice-table td ul,
        .invoice-table td ol,
        .invoice-table td li,
        .invoice-table td span,
        .invoice-table td div,
        .invoice-table td strong,
        .invoice-table td b {
            color: #000000 !important;
        }

        /* Ensure list markers (dots/numbers) are also black */
        .invoice-table td li::marker {
            color: #000000 !important;
        }

        /* Print/PDF-specific rules */
        @media print {

            body,
            * {
                font-family: 'Noto Sans', sans-serif !important;
                font-size: 10px !important;
                line-height: 1.3 !important;
                color: #000 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .no-print,
            .invoice-buttons {
                display: none !important;
            }

            /* Hide vendor and public price columns for main table only, not for addition/reduction tables */
            .invoice-table:not(.addition-table):not(.reduction-table) .col-vendor-price,
            .invoice-table:not(.addition-table):not(.reduction-table) .col-public-price,
            .total-table .col-public-price {
                display: none !important;
            }

            /* Show publish price column for addition table */
            .addition-table .col-vendor-price {
                display: none !important;
            }

            .addition-table .col-public-price {
                display: table-cell !important;
            }

            /* Show amount column for reduction table */
            .reduction-table .col-public-price {
                display: table-cell !important;
            }

            .invoice-table {
                width: 100% !important;
                border-collapse: collapse !important;
                margin-bottom: 15px !important;
            }

            .invoice-table th,
            .invoice-table td {
                border: 1px solid #ddd !important;
                padding: 8px !important;
                text-align: left !important;
            }

            .invoice-table th {
                background-color: #f8f9fa !important;
                font-weight: bold !important;
                text-transform: uppercase !important;
            }

            .total-table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            .total-table th,
            .total-table td {
                border: 1px solid #ddd !important;
                padding: 6px 8px !important;
            }

            .addition-row {
                background-color: #f8f9fa !important;
            }

            .addition-amount {
                color: #28a745 !important;
                font-weight: 600 !important;
            }

            .reduction-amount {
                color: #dc3545 !important;
                font-weight: 600 !important;
            }

            .signature-area {
                page-break-inside: avoid !important;
                margin-top: 40px !important;
            }

            .address-box,
            .booking-info {
                margin-bottom: 10px !important;
            }

            @page {
                margin: 0.5in !important;
                size: A4 !important;
            }
        }
    </style>

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
                <div class="themeholy-invoice invoice_style2">
                    <div class="download-inner" id="download_section"
                        data-event-name="{{ $simulasi->prospect->name_event ?? '' }}">
                        <!--==============================
 Header Area
==============================-->
                        <header class="themeholy-header header-layout1">
                            <div class="row align-items-center justify-content-between">
                                <div class="col-auto">
                                    {{-- <p class="mb-0"><b>PENAWARAN: 00{{ $simulasi->id }}</b></p> --}}
                                </div>
                                <div class="col-auto">
                                    <div class="header-logo">
                                        @php
                                            $company = \App\Models\Company::first();
                                            if (
                                                $company &&
                                                $company->logo_url &&
                                                \Illuminate\Support\Facades\Storage::disk('public')->exists(
                                                    $company->logo_url,
                                                )
                                            ) {
                                                $logoPath = \Illuminate\Support\Facades\Storage::disk('public')->path(
                                                    $company->logo_url,
                                                );
                                            } else {
                                                $logoPath = public_path('images/logomki.png');
                                            }

                                            // Embed image for PDF reliability
                                            $logoSrc = file_exists($logoPath)
                                                ? 'data:' .
                                                    mime_content_type($logoPath) .
                                                    ';base64,' .
                                                    base64_encode(file_get_contents($logoPath))
                                                : '';
                                        @endphp
                                        @if ($logoSrc)
                                            <a href="{{ route('filament.admin.auth.login') }}" class="cta-button">
                                                <img src="{{ $logoSrc }}" alt="Logo Perusahaan"
                                                    class="company-logo" style="max-height: 100px; width: auto;">
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="header-bottom">
                                <div class="row align-items-center justify-content-between">
                                    <div class="col-auto">
                                        <div class="header-bottom_left">
                                            <p><b>Event Name : </b> {{ $simulasi->prospect->name_event ?? 'N/A' }}</p>
                                            <div class="shape"></div>
                                            <div class="shape"></div>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="header-bottom_right">
                                            <div class="shape"></div>
                                            <div class="shape"></div>
                                            <p><b>Date : </b>{{ $simulasi->created_at->format('d F Y H:i:s') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </header>
                        <div class="row justify-content-between">
                            <div class="col-auto">
                                <div class="booking-info">
                                    <p><b>Created By : </b> {{ $simulasi->user->name ?? 'N/A' }}</p>
                                    <p><b>Base Product : </b> {{ $simulasi->product->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                            {{-- <div class="col-auto">
                                <div class="booking-info">
                                    <p><b>Date Akad : </b> </p>
                                    <p><b>Date Resepsi : </b> </p>
                                </div>
                            </div> --}}
                            <div class="col-auto">
                                <div class="booking-info">
                                    <p><b>Valid Until : </b> {{ $simulasi->created_at->addDays(4)->format('d F Y') }}
                                    </p>
                                    <p><b>Penawaran : </b> 00{{ $simulasi->id }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row gx-0">
                            <div style="100%">
                                <div class="address-box">
                                    <b>Office Information :</b>
                                    <address class="align-justify">
                                        {{ $company->company_name ?? 'PT. Makna Kreatif Indonesia' }}<br>
                                        {{ $company->address ?? 'Jl. Sintraman Jaya I No. 2148, 20 Ilir D II, Kecamatan Kemuning, Kota Palembang, Sumatera Selatan 30137' }}
                                        |
                                        Phone: {{ $company->phone ?? '+62 822-9796-2600' }} <br>
                                    </address>
                                </div>
                            </div>
                        </div>
                        <table class="invoice-table">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th class="col-vendor-price">Vendor</th>
                                    <th class="col-public-price">Public</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($items as $index => $item)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ $item->vendor->name ?? ($item->vendor_id ? 'Vendor ID: ' . $item->vendor_id : 'N/A') }}</strong>
                                            </div>
                                            @if ($item->description)
                                                <div>
                                                    {!! $item->description !!}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="col-vendor-price">
                                            {{ number_format($item->harga_vendor ?? 0, 0, ',', '.') }}</td>
                                        <td class="col-public-price">
                                            {{ number_format($item->harga_publish ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td>
                                            Tidak ada item spesifik yang terdaftar untuk produk ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        @php
                            // Definisikan variabel untuk pengecekan agar lebih bersih
                            $pengurangans = $simulasi->product->pengurangans ?? collect();
                            $penambahanHarga = $simulasi->product->penambahanHarga ?? collect();
                        @endphp

                        {{-- Section Penambahan --}}
                        @if ($penambahanHarga->isNotEmpty())
                            <b>Detail Penambahan :</b>
                            <table class="invoice-table addition-table">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th class="col-vendor-price">Vendor</th>
                                        <th class="col-public-price">Publish</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($penambahanHarga as $penambahan_item)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $penambahan_item->vendor->name ?? 'Penambahan Tanpa Nama' }}</strong>
                                                </div>
                                                @if (!empty($penambahan_item->description))
                                                    <div>
                                                        {!! $penambahan_item->description !!}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="col-vendor-price addition-amount">
                                                + {{ number_format($penambahan_item->harga_vendor ?? 0, 0, ',', '.') }}
                                            </td>
                                            <td class="col-public-price addition-amount">
                                                +
                                                {{ number_format($penambahan_item->harga_publish ?? 0, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif

                        @if ($pengurangans->isNotEmpty())
                            {{-- Bagian ini hanya akan ditampilkan jika ada item pengurangan --}}
                            <b>Detail Pengurangan :</b>
                            <table class="invoice-table reduction-table">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th class="col-public-price">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pengurangans as $pengurangan_item)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $pengurangan_item->description ?? ($pengurangan_item->name ?? 'Pengurangan Tanpa Nama') }}</strong>
                                                </div>
                                                @if (!empty($pengurangan_item->notes))
                                                    <div>{!! $pengurangan_item->notes !!}</div>
                                                @endif
                                            </td>
                                            <td class="col-public-price">
                                                {{ number_format($pengurangan_item->amount, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif

                        <div>
                            <div class="address-notes">
                                <b>Notes (Jika ada) :</b>
                                <p>{!! $simulasi->notes ?? 'Tidak ada catatan tambahan untuk ini.' !!}</p>
                            </div>
                        </div>

                        {{-- Price Calculation --}}
                        @php
                            // Hitung total publish price berdasarkan item-item dalam simulasi
                            $totalPublicPrice = collect($items)->sum(function ($item) {
                                return ($item->harga_publish ?? 0) * ($item->quantity ?? 1);
                            });

                            // Total biaya vendor berdasarkan item-item dalam simulasi
                            $totalVendorPrice = collect($items)->sum(function ($item) {
                                return ($item->harga_vendor ?? 0) * ($item->quantity ?? 1);
                            });

                            // Hitung total penambahan harga
                            $totalAdditionPublish = ($simulasi->product->penambahanHarga ?? collect())->sum(
                                'harga_publish',
                            );
                            $totalAdditionVendor = ($simulasi->product->penambahanHarga ?? collect())->sum(
                                'harga_vendor',
                            );

                            // Harga dasar paket adalah total harga publik dari item dan harga vendor dari item
                            $basePackagePrice = $totalPublicPrice;
                            $baseVendorPrice = $totalVendorPrice;

                            // Hitung total jumlah diskon
                            $calculationTotalReductions = ($simulasi->product->pengurangans ?? collect())->sum(
                                'amount',
                            );

                            // Hitung total jumlah harga publish setelah pengurangan dan penambahan
                            $finalPublicPriceAfterDiscounts =
                                $basePackagePrice + $totalAdditionPublish - $calculationTotalReductions;
                            $finalVendorPriceAfterDiscounts =
                                $baseVendorPrice + $totalAdditionVendor - $calculationTotalReductions;

                            // Profit & Loss for this simulation
                            $calculationProfitLoss = $finalPublicPriceAfterDiscounts - $finalVendorPriceAfterDiscounts;
                        @endphp {{-- Total Calculation Section was here, moved it down for clarity --}}
                        <div class="col-auto">
                            <table class="total-table">
                                <tr>
                                    <th>Total Publish Price :</th>
                                    <td>{{ number_format($basePackagePrice, 0, ',', '.') }}</td>
                                </tr>
                                <tr class="col-public-price">
                                    <th>Total Vendor Price :</th>
                                    <td>{{ number_format($baseVendorPrice, 0, ',', '.') }}</td>
                                </tr>
                                @if ($totalAdditionPublish > 0)
                                    <tr>
                                        <th style="color: #28a745; font-weight: 600;">Total Penambahan :</th>
                                        <td style="color: #28a745; font-weight: 600;">+
                                            {{ number_format($totalAdditionPublish, 0, ',', '.') }}</td>
                                    </tr>
                                @endif
                                @if ($totalAdditionVendor > 0)
                                    <tr class="col-public-price">
                                        <th style="color: #28a745; font-weight: 600;">Total Penambahan Vendor :</th>
                                        <td style="color: #28a745; font-weight: 600;">+
                                            {{ number_format($totalAdditionVendor, 0, ',', '.') }}</td>
                                    </tr>
                                @endif
                                @if ($calculationTotalReductions > 0)
                                    <tr>
                                        <th style="color: #dc3545; font-weight: 600;">Total Pengurangan :</th>
                                        <td style="color: #dc3545; font-weight: 600;">
                                            ({{ number_format($calculationTotalReductions, 0, ',', '.') }})</td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>Total Paket Publishable :</th>
                                    <td>{{ number_format($finalPublicPriceAfterDiscounts, 0, ',', '.') }}</td>
                                </tr>
                                <tr class="col-public-price">
                                    <th>Total Paket Vendorable :</th>
                                    <td>{{ number_format($finalVendorPriceAfterDiscounts, 0, ',', '.') }}</td>
                                </tr>
                                <tr class="col-public-price"
                                    @if ($calculationProfitLoss < 30000000) style="color: red;" @endif> {{-- Menggunakan kelas hide-on-pdf --}}
                                    <th
                                        @if ($calculationProfitLoss < 30000000) style="font-weight: bold; color: red !important;" @endif>
                                        Profit & Loss:</th>
                                    <td
                                        @if ($calculationProfitLoss > 30000000) style="font-weight: bold; color: blue !important;" @endif>
                                        {{ number_format($calculationProfitLoss, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </div>

                        {{-- Note Section was here, moved it up before Pengurangan table --}}
                        <div class="invoice-note col-public-price" style="margin-top: 20px; margin-bottom: 20px;">
                            <p style="width: 100%; overflow: auto; font-size: 12px; text-align: center; "><strong>NOTE
                                    :</strong> Pastikan AM Malakukan Pengecekan Data Sebelum Menyerahkan Kepada Calon
                                Klien.</p>
                        </div>

                        {{-- Signature Section --}}
                        <div class="signature-area"
                            style="margin-top: 60px; width: 100%; overflow: auto; font-size: 12px;">
                            <div style="float: left; width: 40%; text-align: center; margin-left: 5%;">
                                <p style="margin-bottom: 70px;">Hormat Kami,</p>
                                <p style="border-top: 1px solid var(--title-color); margin: 0 10px; padding-top: 5px;">
                                    ( {{ $simulasi->user->name ?? 'Account Manager' }} )
                                </p>
                                <p>{{ $company->company_name ?? 'PT. Makna Kreatif Indonesia' }}</p>
                            </div>
                            <div style="float: right; width: 40%; text-align: center; margin-right: 5%;">
                                <p style="margin-bottom: 70px;">Disetujui Oleh,</p>
                                <p style="border-top: 1px solid var(--title-color); margin: 0 10px; padding-top: 5px;">
                                    (_________________________)</p>
                                <p>Klien</p>
                            </div>
                            <div style="clear: both;"></div>
                        </div>
                        {{-- <p class="company-address">
                            <b>Invar Inc:</b> <br>
                            12th Floor, Plot No.5, IFIC Bank, Gausin Rod, Suite 250-20, Franchisco USA 2022.
                        </p> --}}
                        {{-- Moved the general note outside of the main content flow for PDF if it was part of the invoice-note --}}

                    </div>
                    <div class="invoice-buttons no-print">
                        <button class="print_btn">
                            <svg width="20" height="21" viewBox="0 0 20 21" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M16.25 13H3.75C3.38542 13 3.08594 13.1172 2.85156 13.3516C2.61719 13.5859 2.5 13.8854 2.5 14.25V19.25C2.5 19.6146 2.61719 19.9141 2.85156 20.1484C3.08594 20.3828 3.38542 20.5 3.75 20.5H16.25C16.6146 20.5 16.9141 20.3828 17.1484 20.1484C17.3828 19.9141 17.5 19.6146 17.5 19.25V14.25C17.5 13.8854 17.3828 13.5859 17.1484 13.3516C16.9141 13.1172 16.6146 13 16.25 13ZM16.25 19.25H3.75V14.25H16.25V19.25ZM17.5 8V3.27344C17.5 2.90885 17.3828 2.60938 17.1484 2.375L15.625 0.851562C15.3646 0.617188 15.0651 0.5 14.7266 0.5H5C4.29688 0.526042 3.71094 0.773438 3.24219 1.24219C2.77344 1.71094 2.52604 2.29688 2.5 3V8C1.79688 8.02604 1.21094 8.27344 0.742188 8.74219C0.273438 9.21094 0.0260417 9.79688 0 10.5V14.875C0.0260417 15.2656 0.234375 15.474 0.625 15.5C1.01562 15.474 1.22396 15.2656 1.25 14.875V10.5C1.25 10.1354 1.36719 9.83594 1.60156 9.60156C1.83594 9.36719 2.13542 9.25 2.5 9.25H17.5C17.8646 9.25 18.1641 9.36719 18.3984 9.60156C18.6328 9.83594 18.75 10.1354 18.75 10.5V14.875C18.776 15.2656 18.9844 15.474 19.375 15.5C19.7656 15.474 19.974 15.2656 20 14.875V10.5C19.974 9.79688 19.7266 9.21094 19.2578 8.74219C18.7891 8.27344 18.2031 8.02604 17.5 8ZM16.25 8H3.75V3C3.75 2.63542 3.86719 2.33594 4.10156 2.10156C4.33594 1.86719 4.63542 1.75 5 1.75H14.7266L16.25 3.27344V8ZM16.875 10.1875C16.3021 10.2396 15.9896 10.5521 15.9375 11.125C15.9896 11.6979 16.3021 12.0104 16.875 12.0625C17.4479 12.0104 17.7604 11.6979 17.8125 11.125C17.7604 10.5521 17.4479 10.2396 16.875 10.1875Z"
                                    fill="#00C764" />
                            </svg>
                        </button>
                        {{-- <button id="download_btn" class="download_btn">
                            <svg width="25" height="19" viewBox="0 0 25 19" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M8.94531 11.1797C8.6849 10.8932 8.6849 10.6068 8.94531 10.3203C9.23177 10.0599 9.51823 10.0599 9.80469 10.3203L11.875 12.3516V6.375C11.901 5.98438 12.1094 5.77604 12.5 5.75C12.8906 5.77604 13.099 5.98438 13.125 6.375V12.3516L15.1953 10.3203C15.4818 10.0599 15.7682 10.0599 16.0547 10.3203C16.3151 10.6068 16.3151 10.8932 16.0547 11.1797L12.9297 14.3047C12.6432 14.5651 12.3568 14.5651 12.0703 14.3047L8.94531 11.1797ZM10.625 0.75C11.7969 0.75 12.8646 1.01042 13.8281 1.53125C14.8177 2.05208 15.625 2.76823 16.25 3.67969C16.8229 3.39323 17.4479 3.25 18.125 3.25C19.375 3.27604 20.4036 3.70573 21.2109 4.53906C22.0443 5.34635 22.474 6.375 22.5 7.625C22.5 8.01562 22.4479 8.41927 22.3438 8.83594C23.151 9.2526 23.7891 9.85156 24.2578 10.6328C24.7526 11.4141 25 12.2865 25 13.25C24.974 14.6562 24.4922 15.8411 23.5547 16.8047C22.5911 17.7422 21.4062 18.224 20 18.25H5.625C4.03646 18.1979 2.70833 17.651 1.64062 16.6094C0.598958 15.5417 0.0520833 14.2135 0 12.625C0.0260417 11.375 0.377604 10.2812 1.05469 9.34375C1.73177 8.40625 2.63021 7.72917 3.75 7.3125C3.88021 5.4375 4.58333 3.88802 5.85938 2.66406C7.13542 1.4401 8.72396 0.802083 10.625 0.75ZM10.625 2C9.08854 2.02604 7.78646 2.54688 6.71875 3.5625C5.67708 4.57812 5.10417 5.85417 5 7.39062C4.94792 7.91146 4.67448 8.27604 4.17969 8.48438C3.29427 8.79688 2.59115 9.33073 2.07031 10.0859C1.54948 10.8151 1.27604 11.6615 1.25 12.625C1.27604 13.875 1.70573 14.9036 2.53906 15.7109C3.34635 16.5443 4.375 16.974 5.625 17H20C21.0677 16.974 21.9531 16.6094 22.6562 15.9062C23.3594 15.2031 23.724 14.3177 23.75 13.25C23.75 12.5208 23.5677 11.8698 23.2031 11.2969C22.8385 10.724 22.3568 10.2682 21.7578 9.92969C21.2109 9.59115 21.0026 9.09635 21.1328 8.44531C21.2109 8.21094 21.25 7.9375 21.25 7.625C21.224 6.73958 20.9245 5.9974 20.3516 5.39844C19.7526 4.82552 19.0104 4.52604 18.125 4.5C17.6302 4.5 17.1875 4.60417 16.7969 4.8125C16.1719 5.04688 15.651 4.90365 15.2344 4.38281C14.7135 3.65365 14.0495 3.08073 13.2422 2.66406C12.4609 2.22135 11.5885 2 10.625 2Z"
                                    fill="#2D7CFE" />
                            </svg>
                        </button> --}}
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

</body>

</html>
