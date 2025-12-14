<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details PDF: {{ $product->name }}</title>
    <style>
        /* Import Noto Sans font dari Google Fonts untuk PDF */
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,400;0,700;1,400;1,700&display=swap');

        @page {
            /* margin: 2cm; */
            margin-top: 2cm;
            margin-bottom: 2cm;
            margin-left: 1cm;
            margin-right: 1cm;
            /* Margin atas dan bawah bisa disesuaikan lebih lanjut jika header/footer membutuhkan ruang spesifik */
            /* Contoh: margin-top: 1.5cm; margin-bottom: 1.5cm; */
        }

        body {
            font-family: 'Noto Sans', sans-serif;
            font-size: 10pt;
            /* Ukuran font standar untuk PDF */
            background-color: #ffffff;
            margin: 0;
            /* Body margin is 0, page margins are handled by @page */
            padding: 0;
            line-height: 1.2;
            /* Sedikit lebih longgar dari 1 untuk keterbacaan dan potensi kalkulasi break yang lebih baik */
            color: #333;
        }

        .pdf-container {
            width: 100%;
            margin: 0 auto;
            background: #ffffff;
            padding: 0;
            /* Padding utama diatur oleh @page margin */
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .header img {
            max-height: 40px;
            /* Kembalikan ukuran logo yang lebih wajar */
            margin-bottom: 10px;
            margin-top: 0px;
        }

        .header h1 {
            margin: 0;
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header p {
            margin: 2px 0;
            font-size: 8pt;
            color: #555;
        }

        .details-table {
            /* Tabel info dokumen */
            width: 100%;
            margin-top: 15px;
            border-collapse: collapse;
            font-size: 8pt;
            /* Ukuran font lebih kecil untuk tabel */
        }

        .vendor-description {
            margin-left: 10px;
            font-size: 8pt;
            margin-top: 3px;
            margin-bottom: 3px;
            padding-left: 20px;
            /* list-style-position: inside; */
        }

        .items-table,
        .total-table {
            /* Tabel komponen, kalkulasi harga */
            width: 100%;
            margin-top: 10px;
            /* Margin atas dari judul section atau elemen sebelumnya */
            border-collapse: collapse;
            font-size: 8pt;
        }

        .details-table tr,
        .items-table tr,
        .total-table tr {
            page-break-inside: auto;
            /* Izinkan baris tabel terpotong jika perlu untuk mengisi halaman */
        }

        .details-table td,
        .items-table td,
        .items-table th,
        .total-table td {
            padding: 6px 8px;
            /* Padding lebih kecil */
            border: 1px solid #ddd;
            /* vertical-align: top; Jaga konsistensi alignment */
        }

        .items-table th {
            background: #f8f8f8;
            text-align: left;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8pt;
            page-break-inside: auto;
            /* Izinkan header terpotong jika perlu */
        }

        .items-table thead,
        .details-table thead,
        .total-table thead {
            display: table-header-group;
            /* Agar header tabel berulang jika tabel multi-halaman */
        }

        .items-table .text-right,
        .total-table .text-right {
            text-align: right;
            text-transform: capitalize;
            font-size: 8pt;
        }

        .description-html-content {
            /* Kelas baru untuk styling HTML dari deskripsi */
            font-size: 8pt;
            color: #555;
            margin-top: 3px;
            text-transform: capitalize;
            line-height: 1.3;
            padding-left: 1px;
            list-style-position: inside;
            margin-bottom: 3px;

        }

        .description-html-content p,
        .description-html-content ul,
        .description-html-content ol {
            margin-top: 3px;
            margin-bottom: 3px;
        }

        .description-html-content ul,
        .description-html-content ol {
            padding-left: 5px;
            /* Indentasi untuk list */
        }

        .description-html-content li {
            margin-bottom: 2px;
        }

        .total-table td {
            text-align: right;
        }

        .total-table td:first-child {
            text-align: right;
            font-weight: bold;
            width: 80%;
        }

        .package-details-box {
            margin-top: 20px;
            border: 1px solid #eee;
            padding: 15px;
            /* Padding sedikit lebih besar */
            background: #fdfdfd;
            page-break-before: auto;
            /* Izinkan box ini terpotong */
        }

        h3.section-title {
            /* Kelas untuk judul bagian */
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 9pt;
            font-weight: bold;
            color: #333;
            page-break-after: auto;
            /* Izinkan page break setelah judul section */
        }

        .signature-table {
            width: 100%;
            margin-top: 30px;
            page-break-inside: auto;
            /* Izinkan tabel tanda tangan terpotong */
            font-size: 9pt;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin: 0 20px;
            /* Margin kiri kanan untuk garis */
        }

        .footer {
            text-align: center;
            margin-top: 15px;
            /* Jarak dari konten terakhir */
            padding-top: 5px;
            font-size: 6pt;
            color: #777;
            position: fixed;
            bottom: 0.5cm;
            /* Jarak dari bawah halaman */
            left: 1cm;
            right: 1cm;
            /* width: auto; atau biarkan browser menghitung berdasarkan left/right */
        }

        strong {
            font-weight: bold;
        }

        /* Pastikan bold bekerja */
    </style>
</head>

<body>
    <div class="pdf-container">
        {{-- Header Section --}}
        <div class="header">
            @php
                $logoPath = public_path('images/logomki.png');
                $logoSrc = '';
                if (file_exists($logoPath)) {
                    try {
                        $logoData = base64_encode(file_get_contents($logoPath));
                        $mimeType = mime_content_type($logoPath);
                        if ($mimeType) {
                            // Pastikan mime type valid
                            $logoSrc = 'data:' . $mimeType . ';base64,' . $logoData;
                        }
                    } catch (\Exception $e) {
                        // Handle error jika file tidak bisa dibaca atau base64 gagal
                        $logoSrc = ''; // Kosongkan jika error
                    }
                }
            @endphp
            @if ($logoSrc)
                <img src="{{ $logoSrc }}" alt="Makna Kreatif Indonesia">
            @endif
            {{-- <h1>{{ $product->name }}</h1> --}}
            <p class="mt-1">Jl. Sintraman Jaya I No. 2148, 20 Ilir D II, Kec. Kemuning, Kota Palembang, Sumatera
                Selatan 30137</p>
            <p>PT. Makna Kreatif Indonesia | maknawedding@gmail.com | 0813 7318 3794</p>
        </div>

        {{-- Simulation Information --}}
        <table class="details-table">
            <tr>
                <td style="width: 50%;">
                    <strong>WEDDING PACKAGE SIMULATION</strong><br>
                    Product Name : {{ $product->name }}<br>
                    Category : {{ $product->category->name ?? 'N/A' }}<br>
                    Capacity : {{ $product->pax }} Pax
                </td>
                <td style="width: 50%;">
                    <strong>Document Details</strong><br>
                    Reference : PROD-{{ str_pad($product->id, 6, '0', STR_PAD_LEFT) }}<br>
                    Date : {{ now()->format('d F Y H:i:s') }}<br>
                    Printed By : <strong>{{ auth()->user()->name ?? 'System' }}</strong>
                </td>
            </tr>
        </table>

        {{-- Package Details --}}
        <div class="package-details-box">
            <h3 class="section-title">Package Components & Services</h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th>Description</th>
                        <th style="width: 15%; text-align: right;">Vendor</th>
                        <th style="width: 15%; text-align: right;">Public</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalVendorPrice = 0;
                        $totalPublicPrice = 0;
                    @endphp
                    @forelse($product->items ?? [] as $item)
                        <tr>
                            <td style="text-align: center; vertical-align: top;">{{ $loop->iteration }}</td>
                            <td>
                                <div>
                                    {{ $item->vendor->name ?? 'Vendor Tidak Diketahui' }}
                                </div>
                                @isset($item->description)
                                    <ol class="vendor-description">
                                        {!! strip_tags($item->description, '<li>') !!}
                                    </ol>
                                @endisset
                            </td>
                            <td style="text-align: right; vertical-align: top;">
                                {{ number_format($item->harga_vendor ?? 0, 0, ',', '.') }}</td>
                            <td style="text-align: right; vertical-align: top;">
                                {{ number_format($item->price_public ?? ($item->harga_publish ?? 0), 0, ',', '.') }}
                            </td>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 10px;">Tidak ada item spesifik yang
                                terdaftar untuk produk ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Addition Details --}}
        @if ($product->penambahanHarga && $product->penambahanHarga->count() > 0)
            <div class="package-details-box">
                <h3 class="section-title">Additional Price Details</h3>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width: 5%; vertical-align: top;">No.</th>
                            <th style="vertical-align: top;">Description</th>
                            <th style="width: 15%; text-align: right; vertical-align: top;">Vendor</th>
                            <th style="width: 15%; text-align: right; vertical-align: top;">Publish</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($product->penambahanHarga as $addition)
                            <tr>
                                <td style="text-align: center; vertical-align: top;">{{ $loop->iteration }}</td>
                                <td>
                                    <div style="font-weight: bold; margin-bottom: 2px;">
                                        {{ $addition->vendor->name ?? 'N/A' }}
                                    </div>
                                    @isset($addition->description)
                                        <ol class="vendor-description">
                                            {!! strip_tags($addition->description, '<li>') !!}
                                        </ol>
                                    @endisset
                                </td>
                                <td style="text-align: right; vertical-align: top;">
                                    {{ number_format($addition->harga_vendor ?? 0, 0, ',', '.') }}
                                </td>
                                <td style="text-align: right; vertical-align: top;">
                                    {{ number_format($addition->harga_publish ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Reduction Details --}}
        <div class="package-details-box"> {{-- Gunakan box yang sudah ada stylenya --}}
            <h3 class="section-title">Reduction Details</h3> {{-- Gunakan kelas judul --}}
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 5%; vertical-align: top;">No.</th> {{-- Ganti Vendor Name menjadi No. --}}
                        <th style="vertical-align: top;">Description</th>
                        <th style="width: 15%; text-align: right; vertical-align: top;">Value</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($product->pengurangans ?? [] as $discount)
                        <tr>
                            <td style="text-align: center; vertical-align: top;">{{ $loop->iteration }}</td>
                            {{-- Tambahkan nomor urut --}}
                            <td>
                                <div style="font-weight: bold; margin-bottom: 2px;">
                                    {{ $discount->description ?? 'N/A' }}</div> {{-- Nama Vendor --}}
                                @isset($discount->notes)
                                    <ol class="vendor-description">
                                        {!! strip_tags($discount->notes, '<li>') !!}
                                    </ol>
                                @endisset
                            </td>
                            <td style="text-align: right; vertical-align: top;">
                                {{ number_format($discount->amount ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 10px;">No reductions listed for this
                                product.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>


        {{-- Price Calculation --}}
        <div class="package-details-box">
            @php
                // Hitung total berdasarkan jumlah harga publik item
                $totalPublicPrice = ($product->items ?? collect())->sum(function ($item) {
                    return ($item->harga_publish ?? 0) * ($item->quantity ?? 1);
                });

                // Hitung total berdasarkan jumlah harga vendor item
                $totalVendorPrice = ($product->items ?? collect())->sum(function ($item) {
                    return ($item->harga_vendor ?? 0) * ($item->quantity ?? 1);
                });

                // Hitung total jumlah diskon
                $totalDiscountAmount = ($product->pengurangans ?? collect())->sum('amount');

                // Hitung total jumlah penambahan harga
                $totalAdditionAmount = ($product->penambahanHarga ?? collect())->sum('harga_publish');
                $totalAdditionVendorAmount = ($product->penambahanHarga ?? collect())->sum('harga_vendor');

                // Hitung harga final setelah diskon dan penambahan
                $finalPriceAfterDiscounts = $totalPublicPrice - $totalDiscountAmount + $totalAdditionAmount;
                $finalVendorPriceAfterDiscounts = $totalVendorPrice - $totalDiscountAmount + $totalAdditionVendorAmount;

                // Hitung Profit & Loss
                $profitAndLoss = $finalPriceAfterDiscounts - $finalVendorPriceAfterDiscounts;
            @endphp

            <h3 class="section-title">Price Calculation</h3> {{-- Gunakan kelas judul --}}
            <table class="total-table">
                <tr>
                    <td><strong>Total Publish Price</strong></td>
                    <td style="text-align: right; font-weight: bold;">
                        {{ number_format($totalPublicPrice, 0, ',', '.') }}</td> {{-- Gunakan style inline untuk bold --}}
                </tr>
                <tr>
                    <td>Total Vendor Price</td>
                    <td style="text-align: right;">{{ number_format($totalVendorPrice, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td><strong>Total Reduction</strong></td>
                    <td style="text-align: right; font-weight: bold; color: red;"> -
                        {{ number_format($totalDiscountAmount, 0, ',', '.') }}</td>
                </tr>
                @if ($totalAdditionAmount > 0 || $totalAdditionVendorAmount > 0)
                    <tr>
                        <td><strong>Total Addition Publish</strong></td>
                        <td style="text-align: right; font-weight: bold; color: green;"> +
                            {{ number_format($totalAdditionAmount, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Total Addition Vendor</strong></td>
                        <td style="text-align: right; font-weight: bold; color: green;"> +
                            {{ number_format($totalAdditionVendorAmount, 0, ',', '.') }}</td>
                    </tr>
                @endif
                <tr>
                    <td><strong>Total Paket Publish</strong></td>
                    <td style="text-align: right; font-weight: bold;">
                        {{ number_format($finalPriceAfterDiscounts, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td><strong>Total Paket Vendor</strong></td>
                    <td style="text-align: right; font-weight: bold;">
                        {{ number_format($finalVendorPriceAfterDiscounts, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td><strong>Profit & Loss</strong></td>
                    {{-- Tambahkan style kondisional untuk warna merah jika profit < 30jt --}}
                    <td
                        style="text-align: right; font-weight: bold; color: {{ $profitAndLoss < 30000000 ? 'red' : '#333' }};">
                        {{ number_format($profitAndLoss, 0, ',', '.') }}
                    </td>
                </tr>
            </table>
        </div>

        {{-- Signatures --}}
        <table class="signature-table" style="width: 100%;">
            <tr>
                {{-- Kolom Kiri: Approval By --}}
                <td style="width: 48%; text-align: center; vertical-align: top; padding: 0;">
                    <p style="margin-bottom: 50px;"><strong>Approval By:</strong></p>
                    <br>
                    <div style="border-top: 1px solid #000; margin: 0 20px;"></div>
                    <p style="margin-top: 5px; font-size: 8pt;">Rama Dhona Utama</p>
                </td>

                {{-- Spasi antara kolom --}}
                <td style="width: 4%; padding: 0;"></td>

                {{-- Kolom Kanan: Prepared By --}}
                <td style="width: 48%; text-align: center; vertical-align: top; padding: 0;">
                    <p style="margin-bottom: 50px;"><strong>Prepared By:</strong></p>
                    <br>
                    <div style="border-top: 1px solid #000; margin: 0 20px;"></div>
                    <p style="margin-top: 5px; font-size: 8pt;">Account Manager</p>
                </td>
            </tr>
        </table>

        {{-- Footer (jika diperlukan di setiap halaman) --}}
        <div class="footer">
            PT. Makna Kreatif Indonesia | {{ now()->format('d F Y H:i:s') }} {{-- Placeholder untuk nomor halaman jika library mendukung --}}
        </div>
    </div>
</body>

</html>
