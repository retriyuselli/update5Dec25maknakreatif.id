<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Form Pendataan Karyawan - PT. Makna Kreatif Indonesia</title>
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
    <link rel="icon" type="image/png" sizes="192x192"
        href="{{ asset('assets/img/favicons/android-icon-192x192.png') }}">
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
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!--==============================
 Font Awesome Icons
 ============================== -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.6.0/css/all.css">

    <style>
        * {
            font-family: 'Noto Sans', sans-serif !important;
        }

        .body {
            font-family: 'Noto Sans', sans-serif !important;
            margin: 0;
            padding: 15px !important;
            background: #f5f5f5;
        }

        /* A4 Paper Size Setup */
        body {
            font-family: 'Noto Sans', sans-serif !important;
            margin: 5;
            padding: 15px !important;
            background: #f5f5f5;
        }

        .h1,
        .h2,
        .h3,
        .h4,
        .h5,
        .h6 {
            font-family: 'Noto Sans', sans-serif !important;
            font-weight: 600;
        }

        .big-title {
            font-family: 'Noto Sans', sans-serif !important;
            font-weight: 800;
            text-align: right;
            margin: 0;
            font-size: 18px;
        }

        .th,
        td {
            font-family: 'Noto Sans', sans-serif !important;
        }

        .th {
            text-transform: uppercase;
            font-weight: 600;
            padding: 4px 6px !important;
            width: 25% !important;
        }

        .table-title {
            font-family: 'Noto Sans', sans-serif !important;
            font-weight: 700;
        }

        .address {
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

        /* Table Header Compact Style */
        .invoice-table th {
            padding: 4px 6px !important;
            width: 25% !important;
            white-space: nowrap;
            font-size: 12px !important;
        }

        .invoice-table td {
            padding: 8px !important;
        }

        /* Container Positioning */
        .invoice-container-wrap {
            padding: 0 !important;
            margin: 0 !important;
        }

        .invoice-container {
            padding: 0 !important;
            margin: 0 !important;
        }

        .download-inner {
            padding: 20px !important;
            margin: 0 auto !important;
            text-align: center;
            /* A4 size: 210mm x 297mm = 794px x 1123px at 96dpi */
            width: 794px !important;
            max-width: 794px !important;
            min-height: 1123px !important;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .themeholy-header {
            margin-bottom: 10px !important;
            text-align: center;
        }

        /* Center All Content */
        .invoice-container-wrap {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding: 20px 0;
        }

        .invoice-container {
            /* A4 container setup */
            width: 794px !important;
            max-width: 794px !important;
            background: white;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            margin: 0 auto;
        }

        .themeholy-invoice {
            text-align: center;
        }

        .table {
            margin: 0 auto !important;
        }

        .table-title {
            text-align: center !important;
        }

        .invoice-left {
            text-align: left !important;
        }

        .invoice-right {
            text-align: right !important;
        }

        .address-right {
            text-align: right !important;
        }

        .address-left {
            text-align: left !important;
        }

        /* Utility Classes */
        .text-center {
            text-align: center !important;
        }

        .text-right {
            text-align: right !important;
        }

        .text-left {
            text-align: left !important;
        }

        /* Button Styling */
        .invoice-buttons {
            text-align: right;
            margin: 20px 0;
        }

        .print_btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
            border: none !important;
            border-radius: 8px !important;
            padding: 12px 16px !important;
            margin: 0 10px !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4) !important;
        }
        
        .download_btn {
            background: #fff;
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 12px 16px;
            margin: 0 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .print_btn:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%) !important;
            transform: translateY(-3px) !important;
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6) !important;
        }

        .download_btn:hover {
            background: #f0f9ff;
            border-color: #2D7CFE;
            transform: translateY(-2px);
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

        .big-title {
            font-size: 17px !important;
            font-weight: 800 !important;
        }

        /* Force title alignment in print */
        .col .big-title,
        h1.big-title {
            text-align: right !important;
        }

        .col.text-right,
        .text-right {
            text-align: right !important;
        }

        /* Header specific alignment */
        .themeholy-header .col.text-right {
            text-align: right !important;
        }

        .themeholy-header .big-title {
            text-align: right !important;
            display: block !important;
            width: 100% !important;
        }

        /* Ultra specific title alignment for print */
        .themeholy-header .row .col.text-right .big-title,
        .themeholy-header .row .col .big-title,
        .header-layout11 .col .big-title,
        div.col.text-right h1.big-title {
            text-align: right !important;
            float: right !important;
            margin-left: auto !important;
            margin-right: 0 !important;
        }

        /* Force parent container to right */
        .themeholy-header .row .col.text-right {
            text-align: right !important;
            justify-content: flex-end !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: flex-end !important;
        }

        .table {
            width: 100% !important;
            border-collapse: collapse !important;
            page-break-inside: avoid;
            margin: 0 auto 12px auto !important;
            border: 0.5px solid #333 !important;
        }

        .th,
        td {
            border: 0.5px solid #333 !important;
            padding: 4px 6px !important;
            vertical-align: top !important;
            font-size: 12px !important;
        }

        .th {
            font-size: 12px !important;
        }

        .th {
            width: 25% !important;
            padding: 4px 6px !important;
        }

        /* Ensure all table borders are consistent */
        .invoice-table,
        .invoice-table th,
        .invoice-table td {
            border: 1px #5e5e5e !important;
        }

        /* Specific inline style overrides */
        .td[style*="border-bottom"] {
            border-bottom: 1px #5e5e5e !important;
        }

        .div[style*="border-bottom"] {
            border-bottom: 1px #5e5e5e !important;
        }

        .th {
            background-color: #f8f9fa !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
        }

        .table-title {
            font-weight: 400 !important;
            margin-bottom: 5px !important;
            margin-top: 5px !important;
        }

        .invoice-note {
            margin-top: 8px !important;
            page-break-inside: avoid;
        }

        .address {
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
            page-break-inside: auto !important;
        }

        /* Ensure backgrounds and colors print */
        * {
            background: transparent !important;
            color: black !important;
            text-shadow: none !important;
            filter: none !important;
            -ms-filter: none !important;
        }

        /* Keep table header backgrounds */
        .th {
            background: #f8f9fa !important;
            color: black !important;
        }

        /* Keep essential backgrounds */
        .th {
            background-color: #f8f9fa !important;
            color: black !important;
        }

        .status-approved {
            background-color: #d4edda !important;
            color: #155724 !important;
            border: 1px solid #c3e6cb !important;
        }

        /* Floating Print Button */
        .floating-print-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .floating-print-btn:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .floating-print-btn:active {
            transform: scale(0.95);
        }

        /* Hide print button when printing */
        @media print {
            .floating-print-btn {
                display: none !important;
            }

            /* Reduce font size by 2px for printing */
            * {
                font-size: calc(1em - 2px) !important;
            }

            body {
                font-size: 12px !important;
            }

            h1 {
                font-size: 22px !important;
            }

            h2 {
                font-size: 18px !important;
            }

            h3 {
                font-size: 16px !important;
            }

            h4 {
                font-size: 14px !important;
            }

            h5 {
                font-size: 12px !important;
            }

            h6 {
                font-size: 10px !important;
            }

            .big-title {
                font-size: 20px !important;
            }

            .invoice-table th,
            .invoice-table td {
                font-size: 10px !important;
            }

            .invoice-note {
                font-size: 10px !important;
            }

            .company-address {
                font-size: 11px !important;
            }

            small {
                font-size: 8px !important;
            }

            /* Ensure company info and contact sections are visible in print */
            .invoice-left,
            .invoice-right {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                color: black !important;
            }

            .row {
                display: flex !important;
                flex-wrap: wrap !important;
            }

            .col-auto {
                display: block !important;
                flex: 0 0 auto !important;
                width: auto !important;
            }

            address {
                display: block !important;
                visibility: visible !important;
                font-style: normal !important;
                line-height: 1.4 !important;
                color: black !important;
            }

            /* Force display of company information */
            .justify-content-between {
                display: flex !important;
                justify-content: space-between !important;
            }

            /* Super specific selectors to force visibility */
            .row.justify-content-between.my-1 {
                display: flex !important;
                visibility: visible !important;
                margin: 10px 0 !important;
                page-break-inside: avoid !important;
            }

            .row.justify-content-between.my-1 .col-auto {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                width: 48% !important;
            }

            .row.justify-content-between.my-1 .col-auto:first-child {
                float: left !important;
            }

            .row.justify-content-between.my-1 .col-auto:last-child {
                float: right !important;
                text-align: right !important;
            }

            /* Force visibility of specific content */
            .row.justify-content-between.my-1 .invoice-left,
            .row.justify-content-between.my-1 .invoice-right {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                color: black !important;
                font-size: 11px !important;
            }

            .row.justify-content-between.my-1 address {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                color: black !important;
                font-style: normal !important;
                margin: 5px 0 !important;
                line-height: 1.3 !important;
            }

            /* Override any Bootstrap hiding */
            .my-1 {
                margin-top: 0.25rem !important;
                margin-bottom: 0.25rem !important;
            }

            /* Adjust font size for company info and contact sections only */
            .row.justify-content-between.my-1 .invoice-left b,
            .row.justify-content-between.my-1 .invoice-right b {
                font-size: 12px !important; /* Decreased by 1px from 13px */
            }

            .row.justify-content-between.my-1 .invoice-left address,
            .row.justify-content-between.my-1 .invoice-right address {
                font-size: 12px !important; /* Decreased by 1px from 13px */
            }

            .row.justify-content-between.my-1 .invoice-left,
            .row.justify-content-between.my-1 .invoice-right {
                font-size: 12px !important; /* Decreased by 1px from 13px */
            }

            /* A4 Paper Size for Print */
            @page {
                size: A4 portrait;
                margin: 15mm 10mm 15mm 10mm;
            }

            body {
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
            }

            .invoice-container-wrap {
                padding: 0 !important;
                margin: 0 !important;
                box-shadow: none !important;
                min-height: auto !important;
            }

            .invoice-container {
                width: 100% !important;
                max-width: 100% !important;
                box-shadow: none !important;
                margin: 0 !important;
            }

            .download-inner {
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
                box-shadow: none !important;
                min-height: auto !important;
            }

            .invoice-container-wrap,
            .invoice-container,
            main,
            .themeholy-invoice,
            .download-inner {
                page-break-inside: avoid !important;
                page-break-before: avoid !important;
                page-break-after: avoid !important;
            }

            /* Compact spacing for single page */
            .invoice-table {
                margin: 8px 0 !important;
            }

            .invoice-table th,
            .invoice-table td {
                padding: 4px 6px !important;
                line-height: 1.2 !important;
            }

            .invoice-note {
                margin-top: 4px !important;
                font-size: 9px !important;
            }

            .themeholy-header {
                margin-bottom: 8px !important;
            }

            .row.justify-content-between.my-1 {
                margin: 6px 0 !important;
            }

            .table-title {
                margin: 6px 0 4px 0 !important;
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                color: black !important;
                font-size: 12px !important;
            }

            /* Ensure table titles and their bold text are visible */
            .table-title b,
            p.table-title b {
                display: inline !important;
                visibility: visible !important;
                opacity: 1 !important;
                color: black !important;
                font-weight: bold !important;
                font-size: 12px !important;
            }

            /* Force visibility for paragraph elements with table-title class */
            p.table-title {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                color: black !important;
                margin: 6px 0 4px 0 !important;
            }

            /* Special font size reduction for signature section only */
            p.table-title:contains("Tanda Tangan"),
            .table-title b:contains("Tanda Tangan") {
                font-size: 7px !important; /* Reduced by 5px from 12px */
            }

            /* Target signature section specifically */
            p.table-title:last-of-type,
            p.table-title:last-of-type b {
                font-size: 7px !important; /* Reduced by 5px from 12px */
            }

            /* Reduce header spacing */
            h1.big-title {
                margin: 0 !important;
                padding: 0 !important;
                line-height: 1 !important;
            }

            /* Compact table rows */
            .invoice-table tr {
                height: auto !important;
            }

            .invoice-table td {
                height: 25px !important;
            }

            /* Signature area compact */
            .invoice-table td[style*="height: 80px"] {
                height: 60px !important;
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
                    <div class="download-inner" id="download_section"
                        data-employee-name="{{ $data['name'] ?? 'Nama Karyawan' }}}">
                        <!--==============================
 Header Area
==============================-->
                        <header class="themeholy-header header-layout11">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div class="header-logo">
                                        <a href="#"><img src="{{ asset('images/logomki.png') }}"
                                                alt="PT. Makna Kreatif Indonesia"></a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto text-center">
                                <span><b class="big-title">FORMULIR PENDATAAN KARYAWAN</b>{{ $generated_date }}</span>
                            </div>
                        </header>

                        <div class="row justify-content-between my-1" style="display: flex !important; visibility: visible !important; margin: 10px 0 !important;">
                            <div class="col-auto" style="display: block !important; visibility: visible !important; width: 48% !important; float: left !important;">
                                <div class="invoice-left" style="display: block !important; visibility: visible !important; color: black !important;">
                                    <b style="color: black !important;">Informasi Perusahaan :</b>
                                    <address style="display: block !important; visibility: visible !important; color: black !important; font-style: normal !important; margin: 5px 0 !important; line-height: 1.3 !important;">
                                        PT. Makna Kreatif Indonesia <br>
                                        Jl. Sintraman Jaya I No.2148, 20 Ilir D II, <br>
                                        Kec. Kemuning, Kota Palembang, Sumatera Selatan 30137
                                    </address>
                                </div>
                            </div>
                            <div class="col-auto" style="display: block !important; visibility: visible !important; width: 48% !important; float: right !important; text-align: right !important;">
                                <div class="invoice-right" style="display: block !important; visibility: visible !important; color: black !important; text-align: right !important;">
                                    <b style="color: black !important;">Kontak :</b>
                                    <address style="display: block !important; visibility: visible !important; color: black !important; font-style: normal !important; margin: 5px 0 !important; line-height: 1.3 !important; text-align: right !important;">
                                        Email: info@maknawedding.id <br>
                                        Telp: +62 822-9796-2600 <br>
                                        Website: https://maknakreatif.id
                                    </address>
                                </div>
                            </div>
                        </div>
                        <p class="table-title mt-3" style="display: block !important; visibility: visible !important; color: black !important; margin: 6px 0 4px 0 !important;"><b style="color: black !important; font-weight: bold !important;">Informasi Karyawan</b></p>
                        <table class="invoice-table table-style8">
                            <tbody>
                                <tr>
                                    <th style="text-align: left;">NAMA LENGKAP</th>
                                    <td colspan="2" style="height: 30px; border-bottom: 1px solid #333;">:</td>
                                </tr>
                                <tr>
                                    <th style="text-align: left;">NO TLP</th>
                                    <td colspan="2" style="height: 30px; border-bottom: 1px solid #333;">:</td>
                                </tr>
                                <tr>
                                    <th style="text-align: left;">TANGGAL LAHIR</th>
                                    <td colspan="2" style="height: 30px; border-bottom: 1px solid #333;">:</td>
                                </tr>
                                <tr>
                                    <th style="text-align: left;">JENIS KELAMIN</th>
                                    <td colspan="2" style="height: 30px; border-bottom: 1px solid #333;">:   ☐
                                        Laki-laki &nbsp;&nbsp;&nbsp; ☐ Perempuan</td>
                                </tr>
                                <tr>
                                    <th style="text-align: left;">ALAMAT LENGKAP</th>
                                    <td colspan="2" style="height: 30px; border-bottom: 1px solid #333;">:</td>
                                </tr>
                                <tr>
                                    <th style="text-align: left;">EMAIL</th>
                                    <td colspan="2" style="height: 30px; border-bottom: 1px solid #333;">:</td>
                                </tr>
                                <tr>
                                    <th style="text-align: left;">STATUS PERNIKAHAN</th>
                                    <td colspan="2" style="height: 30px; ">: ☐ Belum
                                        &nbsp;&nbsp; ☐ Menikah &nbsp;&nbsp; ☐ Duda/Janda</td>
                                </tr>
                                <tr>
                                    <th style="text-align: left;">JUMLAH ANAK</th>
                                    <td colspan="2" style="height: 30px; ">:</td>
                                </tr>
                            </tbody>
                        </table>
                        <p class="table-title" style="display: block !important; visibility: visible !important; color: black !important; margin: 6px 0 4px 0 !important;"><b style="color: black !important; font-weight: bold !important;">Data Pekerjaan</b></p>
                        <table class="invoice-table table-style8">
                            <tbody>
                                <tr>
                                    <th style="text-align: left;">TANGGAL MULAI KERJA</th>
                                    <td colspan="2" style="height: 30px; ">:</td>
                                </tr>
                                <tr>
                                    <th style="text-align: left;">DEPARTEMEN</th>
                                    <td colspan="2" style="height: 30px; border-bottom: 1px solid #333;">:   ☐ Bisnis
                                        &nbsp;&nbsp;&nbsp; ☐ Operasional</td>
                                </tr>
                                <tr>
                                    <th style="text-align: left;">ROLE/HAK AKSES</th>
                                    <td colspan="2" style="height: 30px; ">:</td>
                                </tr>
                                <tr>
                                    <th style="text-align: left;">STATUS JABATAN</th>
                                    <td colspan="3" style="height: 30px; ">:</td>
                                </tr>
                                <tr>
                                    <th style="text-align: left;">STATUS AKUN</th>
                                    <td colspan="2" style="height: 30px; ">:   ☐ Aktif
                                        &nbsp;&nbsp;&nbsp; ☐ Nonaktif</td>
                                </tr>
                            </tbody>
                        </table>
                        <br>
                        <br>
                        <p class="table-title"><b>Tanda Tangan & Persetujuan :</b></p>
                        <table class="invoice-table table-style8">
                            <tbody>
                                <tr>
                                    <th style="text-align: center;">Calon / Karyawan</th>
                                    <th style="text-align: center;">HRD</th>
                                    <th style="text-align: center;">Manager</th>
                                </tr>
                                <tr>
                                    <td style="height: 80px; text-align: center; vertical-align: bottom;">
                                        <div style=" margin-bottom: 5px; height: 60px;">
                                        </div>
                                        <small>Tanggal: ___________</small>
                                    </td>
                                    <td style="height: 80px; text-align: center; vertical-align: bottom;">
                                        <div style=" margin-bottom: 5px; height: 60px;">
                                        </div>
                                        <small>Tanggal: ___________</small>
                                    </td>
                                    <td style="height: 80px; text-align: center; vertical-align: bottom;">
                                        <div style=" margin-bottom: 5px; height: 60px;">
                                        </div>
                                        <small>Tanggal: ___________</small>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p class="invoice-note mt-3 text-xs">
                            Formulir ini dibuat secara otomatis oleh sistem dan data terakhir <br>diperbarui pada
                            {{ now()->format('d F Y H:i') }} WIB.
                        </p>
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
        // Print and Download functionality for registration form
        document.addEventListener('DOMContentLoaded', function() {

            // Print Button Functionality
            const printBtn = document.querySelector('.print_btn');
            if (printBtn) {
                printBtn.addEventListener('click', function() {
                    console.log('Print button clicked');
                    window.print();
                });
            }

            // Download Button Functionality  
            const downloadBtn = document.getElementById('download_btn');
            if (downloadBtn) {
                downloadBtn.addEventListener('click', function() {
                    var downloadSection = document.getElementById('download_section');

                    if (!downloadSection) {
                        alert('Download section not found!');
                        return;
                    }

                    var formTitle = 'Formulir_Pendataan_Karyawan';
                    var finalFilename = formTitle + '_' + '{{ now()->format('Y-m-d') }}' + '.pdf';

                    // Check if required libraries are available
                    if (typeof html2canvas === 'undefined' || typeof jsPDF === 'undefined') {
                        console.error('Required libraries not loaded:', {
                            html2canvas: typeof html2canvas,
                            jsPDF: typeof jsPDF
                        });

                        // Try to use html2pdf from main.js or create it
                        if (typeof html2pdf === 'undefined') {
                            // Fallback: use print dialog
                            console.log('html2pdf not available, using print dialog as fallback');
                            window.print();
                            return;
                        }
                    }

                    var opt = {
                        margin: [20, 15, 20, 15],
                        filename: finalFilename,
                        image: {
                            type: 'png',
                            quality: 1.0
                        },
                        html2canvas: {
                            scale: 2,
                            useCORS: true,
                            scrollY: 0,
                            windowWidth: document.body.scrollWidth,
                            letterRendering: true,
                            allowTaint: false,
                            backgroundColor: '#ffffff'
                        },
                        pagebreak: {
                            mode: ['avoid-all', 'css', 'legacy']
                        },
                        jsPDF: {
                            unit: 'pt',
                            format: 'a4',
                            orientation: 'portrait'
                        }
                    };

                    // Hide buttons before generating PDF
                    const invoiceButtons = document.querySelector('.invoice-buttons');
                    if (invoiceButtons) {
                        invoiceButtons.style.display = 'none';
                    }

                    html2pdf().set(opt).from(downloadSection).save()
                        .then(function() {
                            if (invoiceButtons) {
                                invoiceButtons.style.display = 'block';
                            }
                            console.log('PDF generated successfully!');
                        })
                        .catch(function(error) {
                            if (invoiceButtons) {
                                invoiceButtons.style.display = 'block';
                            }
                            console.error('Error generating PDF:', error);
                            alert('Error generating PDF: ' + error.message);
                        });
                });
            }
        });
    </script>

    <!-- Floating Print Button -->
    <button class="floating-print-btn" id="floating-print-btn" title="Print Document">
        <i class="fas fa-print"></i>
    </button>

    <script>
        // Floating Print Button Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const floatingPrintBtn = document.getElementById('floating-print-btn');
            
            if (floatingPrintBtn) {
                floatingPrintBtn.addEventListener('click', function() {
                    // Hide the floating button before printing
                    floatingPrintBtn.style.display = 'none';
                    
                    // Trigger print
                    window.print();
                    
                    // Show the floating button again after print dialog closes
                    setTimeout(function() {
                        floatingPrintBtn.style.display = 'flex';
                    }, 1000);
                });
            }
        });
    </script>

</body>

</html>
