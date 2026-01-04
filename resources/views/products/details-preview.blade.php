<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details - {{ $product->name }}</title>
    {{-- Impor Font Noto Sans dari Google Fonts (untuk tampilan browser) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    {{-- Sertakan Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Konfigurasi dasar Tailwind untuk font Noto Sans
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Noto Sans', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    {{-- CSS Khusus untuk Print --}}
    <style>
        @media print {

            /* --- PENGATURAN HALAMAN DASAR --- */
            @page {
                size: A4;
                margin-top: 2cm;
                margin-bottom: 1cm;
                margin-left: 1.5cm;
                margin-right: 1cm;
            }

            /* --- PENGATURAN BODY & FONT DASAR --- */
            body {
                font-family: 'Noto Sans', Arial, sans-serif !important;
                /* Font utama, fallback ke Arial jika Noto Sans tidak termuat */
                background-color: #ffffff !important;
                /* Paksa background putih */
                color: #000000 !important;
                /* Paksa teks hitam dasar */
                font-size: 5pt !important;
                /* Ukuran font global untuk cetak. Catatan: 'pt' umumnya lebih konsisten untuk print. */
                line-height: 1.5 !important;
                /* Jarak antar baris yang sedikit lebih rapat untuk font kecil, sesuaikan */
                font-weight: normal !important;
                -webkit-print-color-adjust: exact !important;
                /* Penting jika ingin mencetak warna background (mis: pada header tabel) */
                print-color-adjust: exact !important;
                /* Standar properti untuk hal yang sama */
            }

            /* --- RESET UMUM & ELEMENT HTML --- */
            * {
                box-shadow: none !important;
                /* Hapus semua box-shadow */
                text-shadow: none !important;
                /* Hapus semua text-shadow kecuali jika diinginkan */
            }

            a {
                color: #000000 !important;
                /* Tautan menjadi hitam */
                text-decoration: none;
                /* Hapus garis bawah pada tautan, atau 'underline' jika diinginkan */
            }

            img {
                max-width: 100% !important;
                /* Pastikan gambar tidak meluap */
                height: auto;
            }

            /* --- STYLING KHUSUS UNTUK PRINT BERDASARKAN CLASS TAILWIND ATAU CUSTOM --- */

            /* Kontainer Utama: Biarkan style layar (Tailwind) yang mengatur, kecuali ada override spesifik */
            .max-w-4xl {
                /* Atau class kontainer utama Anda */
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                /* Biarkan border, shadow, background dari style layar jika diinginkan di print.
                   Contoh: jika di layar ada shadow-lg, dan ingin shadow itu hilang di print, baru tambahkan:
                   box-shadow: none !important;
                */
            }

            /* Kelas utility untuk print yang sudah ada */
            /* .print-no-shadow sudah ada di HTML, ini akan menghapus shadow khusus elemen itu */
            /* .print-no-border sudah ada di HTML */

            /* Jika ada kelas utility .print-text-* yang ingin Anda gunakan untuk override ukuran font di print */
            /*
            .print-text-sm { font-size: 8pt !important; } // Sesuaikan pt dengan kebutuhan
            .print-text-base { font-size: 9pt !important; }
            .print-text-lg { font-size: 10pt !important; }
            */

            /* Reset warna background yang mungkin tidak diinginkan saat cetak.
               Namun, jika ingin tampilan print sama dengan layar, dan layar menggunakan background ini,
               maka baris ini sebaiknya di-comment atau dihapus, dan andalkan print-color-adjust. */
            /*
            .bg-gray-50\/50, .bg-gray-100, .bg-slate-100, .odd\:bg-white, .even\:bg-slate-50\/70 {
                background-color: transparent !important; // Atau #ffffff !important;
            }
            */

            /* --- PENGATURAN TABEL --- */
            /* Biarkan style tabel dari Tailwind (layar) mendominasi.
               Hanya override jika ada perilaku default browser print yang mengganggu. */
            table {
                width: 100% !important;
                /* Umumnya diinginkan agar tabel tetap full width */
                box-shadow: none !important;
                border-collapse: collapse !important;
                /* Menggabungkan border antar sel untuk tampilan yang lebih bersih */
                margin-top: 5px !important;
                /* Jarak atas sebelum tabel, sesuaikan jika perlu */
                margin-bottom: 5px !important;
                /* Jarak bawah setelah tabel, sesuaikan jika perlu */
                font-size: inherit;
                /* Mewarisi ukuran font dari body atau elemen pembungkus. Ukuran font spesifik tabel bisa diatur di sini atau di th, td. */
                table-layout: fixed;
                /* Aktifkan baris ini jika Anda ingin lebar kolom ditentukan oleh header atau tag <col>,
                   bukan oleh konten sel. Berguna untuk tabel dengan banyak teks atau jika ingin kontrol
                   lebar kolom yang presisi. */
            }

            caption {
                /* Styling untuk elemen <caption> tabel jika Anda menggunakannya */
                padding: 8px !important;
                caption-side: bottom !important;
                /* Posisi caption (umumnya 'bottom' atau 'top') */
                text-align: left !important;
                /* font-style: italic !important; */
                /* Biarkan style layar jika ada */
                color: #555555 !important;
                /* Warna teks caption */
                font-size: 10px !important;
                /* Ukuran font caption relatif terhadap font tabel */
                margin-top: 5px !important;
                /* Jarak dari tabel */
            }

            thead,
            tbody,
            tfoot {
                /* Biasanya tidak memerlukan style khusus untuk pelebaran,
                   namun pastikan tidak ada style dari sumber lain yang membatasi lebar (misalnya, display: inline-block). */
            }

            tr {
                page-break-inside: auto !important;
                /* Berusaha mencegah baris tabel terpotong antar halaman */
            }

            th,
            td {

                border: 1px solid #ccc !important;
                padding: 4px 6px !important;
                text-align: left !important;
                vertical-align: top !important;
                /* Alignment vertikal default. Konten akan mulai dari atas sel jika sel lebih tinggi dari kontennya. */
                word-wrap: break-word !important;
                /* Memastikan teks panjang akan wrap (pindah baris) dan tidak merusak layout tabel. */
                box-sizing: border-box !important;
                /* Memastikan padding dan border termasuk dalam total width/height elemen, bukan menambahkannya. */
            }

            thead th {
                /* Header tabel */
                /* Biarkan background, color, font-weight, text-align dari style layar (Tailwind).
                   print-color-adjust: exact; pada body akan membantu mencetak background dari layar.
                background-color: #E2E8F0 !important; // Contoh jika ingin override ke warna slate-200
                font-weight: bold !important; // Tailwind biasanya sudah mengatur ini
                */
                /* Jika Anda ingin header tabel tetap terlihat saat menggulir tabel yang sangat panjang (biasanya untuk tampilan web, kurang relevan untuk PDF statis): */
                /* position: sticky; top: 0; */
            }

            /* Utility classes untuk alignment teks dalam sel. Pastikan !important digunakan untuk print jika ada konflik. */
            .text-left {
                text-align: left !important;
            }

            .text-right {
                text-align: right !important;
            }

            /* Pastikan class text-right bekerja */

            /* Styling untuk baris ganjil/genap jika diinginkan (membutuhkan print-color-adjust: exact pada body agar tercetak) */
            /*
            tbody tr:nth-child(even) {
                background-color: #f9f9f9 !important; // Warna latar untuk baris genap
            }
            tbody tr:nth-child(odd) {
                background-color: #ffffff !important; // Warna latar untuk baris ganjil (biasanya putih)
            }
            */

            /* --- KONTROL PAGE BREAK --- */
            .print-break-inside-avoid {
                break-inside: avoid;
            }

            .print-break-after-avoid {
                break-after: avoid;
            }

            .print-break-before-avoid {
                break-before: avoid;
            }

            /* Contoh penggunaan:
               <div class="print-break-inside-avoid"> Konten ini tidak akan pecah antar halaman </div>
            */

            /* --- SEMBUNYIKAN ELEMEN YANG TIDAK PERLU DICETAK --- */
            /* Tailwind sudah menyediakan 'print:hidden'. Ini contoh jika Anda butuh kelas custom. */
            .no-print,
            .print\:hidden {
                /* .print:hidden adalah dari Tailwind */
                display: none !important;
            }

            /* --- SPESIFIK UNTUK ELEMEN DI HALAMAN ANDA (SESUAIKAN JIKA PERLU) --- */
            /* Header Dokumen */
            .header-logo {
                /* Jika Anda memberi class pada logo */
                max-height: 10px !important;
                /* Sesuaikan ukuran logo untuk cetak */
            }

            /* Untuk .document-title (h1 produk), biarkan ukuran dari Tailwind (text-xl)
               Jika terlalu besar untuk print, baru override di sini. */
            /* .document-title {
                margin-bottom: 10px !important;
            }

            /* Footer (jika ada footer kustom per halaman yang tidak dihandle @page) */
            /* .custom-footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8pt; } */
        }
    </style>
</head>

<body class="font-sans bg-gray-100 p-4 md:p-8">
    <div
        class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden p-6 md:p-10 print-no-shadow print-no-border">
        {{-- Header Section --}}
        <div class="text-center mb-6 pb-4 border-b border-gray-200">
            @php
                $company = null;
                if (\Illuminate\Support\Facades\Schema::hasTable('companies')) {
                    $company = \App\Models\Company::query()->first();
                }

                $logoPath =
                    $company && $company->logo_url
                        ? \Illuminate\Support\Facades\Storage::disk('public')->path($company->logo_url)
                        : public_path('images/logomki.png');

                $logoSrc = '';
                if (file_exists($logoPath)) {
                    try {
                        $logoSrc =
                            'data:' .
                            mime_content_type($logoPath) .
                            ';base64,' .
                            base64_encode(file_get_contents($logoPath));
                    } catch (\Exception $e) {
                        // Biarkan $logoSrc kosong jika ada error
                    }
                }
            @endphp
            @if ($logoSrc)
                <img src="{{ $logoSrc }}" alt="{{ $company->company_name ?? 'Makna Kreatif Indonesia' }}"
                    class="max-h-10 mx-auto mb-4">
            @endif
            {{-- <h1 class="text-[16px] font-bold uppercase tracking-wide text-gray-800">{{ $product->name ?? 'Nama Produk Tidak Tersedia' }}</h1> --}}
            <p class="text-[12px] text-gray-600 mt-1.5">
                {{ $company->address ?? 'Jl. Sintraman Jaya I No. 2148, 20 Ilir D II, Kecamatan Kemuning, Kota Palembang, Sumatera Selatan 30137' }}
            </p>
            <p class="text-[12px] text-gray-600 mt-0">{{ $company->company_name ?? 'PT. Makna Kreatif Indonesia' }} |
                {{ $company->email ?? 'maknawedding@gmail.com' }} | {{ $company->phone ?? '+62 822-9796-2600' }}</p>
        </div>

        {{-- Simulation Information --}}
        <table class="w-full mt-4 border-collapse text-sm">
            <tr>
                <td class="border border-gray-300 p-3 w-1/2 align-top text-[13px]">
                    <strong>WEDDING PACKAGE PRODUCT</strong><br>
                    Product Name : {{ $product->name ?? 'N/A' }}<br>
                    Category : {{ $product->category->name ?? 'N/A' }}<br>
                    Capacity : {{ $product->pax ?? 'N/A' }} Pax
                </td>
                <td class="border border-gray-300 p-3 w-1/2 align-top text-[13px]">
                    <strong>Document Details</strong><br>
                    Reference : PROD-{{ str_pad($product->id ?? 0, 6, '0', STR_PAD_LEFT) }}<br>
                    Date : {{ now()->format('d F Y H:i:s') }}<br>
                    Printed By : <strong>{{ auth()->user()->name }}</strong>
                </td>
            </tr>
        </table>

        {{-- Package Details --}}
        <div class="mt-8 border border-slate-200 p-4 sm:p-5 rounded-lg bg-white shadow-sm">
            <h3 class="text-sm font-semibold mb-5">Package Components & Services</h3>
            <table class="w-full border-collapse text-sm">
                <thead>
                    <tr>
                        <th
                            class="border text-[13px] px-4 py-3 text-left bg-slate-100 font-bold uppercase tracking-wider">
                            No
                        </th>
                        <th
                            class="border text-[13px] px-4 py-3 text-left bg-slate-100 font-bold uppercase tracking-wider">
                            Description
                        </th>
                        <th class="border text-[13px] px-4 py-3 text-right bg-slate-100 font-bold uppercase tracking-wider"
                            style="width: 15%;">Vendor
                        </th>
                        <th class="border text-[13px] px-4 py-3 text-right bg-slate-100 font-bold uppercase tracking-wider"
                            style="width: 15%;">Public</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($product->items ?? [] as $item)
                        <tr
                            class="odd:bg-white even:bg-slate-50/70 hover:bg-indigo-50/70 transition-colors duration-150 ease-in-out">
                            <td class="border border-slate-300 px-4 py-3 text-center align-top">{{ $loop->iteration }}
                            </td>
                            <td class="border border-slate-300 px-4 py-3 align-top">
                                <div class="font-bold uppercase text-[13px]">
                                    {{ $item->vendor->name ?? 'Vendor Tidak Diketahui' }}</div>
                                @if ($item->description)
                                    <div class="text-sm text-slate-500 ml-10">{!! strip_tags($item->description, '<p><b><strong><em><ul><li><br><span><div>') !!}</div>
                                @endif
                            </td>
                            <td class="border border-slate-300 px-4 text-[13px] py-3 text-right align-top">
                                <!--{{ number_format($item->price_vendor ?? ($item->harga_vendor ?? 0), 0, ',', '.') }}</td>-->
                                {{ number_format($item->calculate_price_vendor, 0, ',', '.') }}
                                {{-- Menampilkan harga_publish atau harga publik yang dihitung --}}
                            <td class="border border-slate-300 px-4 py-3 text-[13px] text-right align-top">
                                {{ number_format($item->price_public ?? ($item->harga_publish ?? 0), 0, ',', '.') }}
                            </td>
                        @empty
                        <tr>
                            <td colspan="4" class="border border-slate-300 p-4 text-center text-slate-500">Tidak ada
                                item spesifik yang terdaftar untuk produk ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Detail Penambahan --}}
        @if (($product->penambahanHarga ?? collect())->count() > 0)
            <div class="mt-8 border border-slate-200 p-4 sm:p-5 rounded-lg bg-white shadow-sm">
                <h3 class="text-sm font-semibold mb-5">Penambahan</h3>
                <table class="w-full border-collapse text-sm">
                    <thead>
                        <th class="border text-[13px] px-4 py-3 text-center bg-slate-100 font-bold uppercase tracking-wider"
                            style="width: 6%;">No</th>
                        <th
                            class="border text-[13px] px-4 py-3 text-left bg-slate-100 font-bold uppercase tracking-wider">
                            Description</th>
                        <th class="border text-[13px] px-4 py-3 text-right bg-slate-100 font-bold uppercase tracking-wider"
                            style="width: 15%;">Vendor</th>
                        <th class="border text-[13px] px-4 py-3 text-right bg-slate-100 font-bold uppercase tracking-wider"
                            style="width: 15%;">Public</th>
                    </thead>
                    <tbody>
                        @foreach ($product->penambahanHarga as $index => $addition)
                            <tr
                                class="odd:bg-white even:bg-slate-50/70 hover:bg-indigo-50/70 transition-colors duration-150 ease-in-out">
                                <td class="border border-slate-300 px-4 py-3 text-center align-top text-[13px]">
                                    {{ $index + 1 }}
                                </td>
                                <td class="border border-slate-300 px-4 py-3 align-top">
                                    <div class="font-bold uppercase text-[13px]">
                                        {{ $addition->vendor->name ?? 'Item Tidak Diketahui' }}</div>
                                    @if ($addition->description)
                                        <div class="text-sm text-slate-500 ml-10">{!! strip_tags($addition->description, '<p><b><strong><em><ul><li><br><span><div>') !!}</div>
                                    @endif
                                </td>
                                <td class="border border-slate-300 px-4 py-3 text-right align-top text-[13px]"
                                    style="width: 15%">
                                    {{ number_format($addition->harga_vendor ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="border border-slate-300 px-4 py-3 text-right align-top text-[13px]"
                                    style="width: 15%">
                                    {{ number_format($addition->harga_publish ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Detail Pengurangan --}}
        @if (($product->pengurangans ?? collect())->count() > 0)
            <div class="mt-8 border border-slate-200 p-4 sm:p-5 rounded-lg bg-white shadow-sm">
                <h3 class="text-sm font-semibold mb-5">Pengurangan</h3>
                <table class="w-full border-collapse text-sm">
                    <thead>
                        <th class="border text-[13px] px-4 py-3 text-center bg-slate-100 font-bold uppercase tracking-wider"
                            style="width: 8%;">No</th>
                        <th
                            class="border text-[13px] px-4 py-3 text-left bg-slate-100 font-bold uppercase tracking-wider">
                            Description</th>
                        <th class="border text-[13px] px-4 py-3 text-right bg-slate-100 font-bold uppercase tracking-wider"
                            style="width: 15%;">Value</th>
                    </thead>
                    <tbody>
                        @foreach ($product->pengurangans as $index => $discount)
                            <tr
                                class="odd:bg-white even:bg-slate-50/70 hover:bg-indigo-50/70 transition-colors duration-150 ease-in-out">
                                <td class="border border-slate-300 px-4 py-3 text-center align-top text-[13px]">
                                    {{ $index + 1 }}
                                </td>
                                <td class="border border-slate-300 px-4 py-3 align-top">
                                    <div class="font-bold uppercase text-[13px]">
                                        {{ $discount->description ?? 'Vendor Tidak Diketahui' }}</div>
                                    @if ($discount->notes)
                                        <div class="text-sm text-slate-500 ml-10">{!! strip_tags($discount->notes, '<p><b><strong><em><ul><li><br><span><div>') !!}</div>
                                    @endif
                                </td>
                                <td class="border border-slate-300 px-4 py-3 text-right align-top text-[13px]"
                                    style="width: 18%">
                                    {{ number_format($discount->amount ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Price Calculation --}}
        @php
            // Hitung total berdasarkan jumlah harga publik item
            $totalPublicPrice = ($product->items ?? collect())->sum(function ($item) {
                return ($item->harga_publish ?? 0) * ($item->quantity ?? 1);
            });

            // Hitung total berdasarkan jumlah harga vendor item
            $totalVendorPrice = ($product->items ?? collect())->sum(function ($item) {
                return ($item->harga_vendor ?? 0) * ($item->quantity ?? 1);
            });

            // Harga dasar paket adalah total harga publik
            $basePackagePrice = $totalPublicPrice;

            // Hitung total jumlah diskon
            $totalDiscountAmount = ($product->pengurangans ?? collect())->sum('amount');

            // Hitung total jumlah penambahan
            $totalAdditionAmount = ($product->penambahanHarga ?? collect())->sum('harga_publish');
            $totalAdditionVendorAmount = ($product->penambahanHarga ?? collect())->sum('harga_vendor');

            // Hitung harga final setelah diskon dan penambahan
            $finalPriceAfterDiscounts = $basePackagePrice - $totalDiscountAmount + $totalAdditionAmount;
            $finalVendorPriceAfterDiscounts = $totalVendorPrice - $totalDiscountAmount + $totalAdditionVendorAmount;

            // Hitung Profit & Loss
            $profitAndLoss = $finalPriceAfterDiscounts - $finalVendorPriceAfterDiscounts;
        @endphp

        <div class="mt-6 border border-gray-300 p-4 rounded-md bg-gray-50/50">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">Price Calculation</h3>
            <table class="w-full border-collapse text-sm">
                <tr>
                    <td class="border border-gray-300 p-2 text-right text-xs"><strong>Total Publish Price</strong></td>
                    <td class="border border-gray-300 p-2 text-right font-semibold"><strong>
                            {{ number_format($basePackagePrice, 0, ',', '.') }}</strong></td>
                </tr>
                <tr>
                    <td class="border border-gray-300 p-2 text-right w-4/5 text-xs"><strong>Total Vendor Price</strong>
                    </td>
                    <td class="border border-gray-300 p-2 text-right font-semibold">
                        {{ number_format($totalVendorPrice, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="border border-gray-300 p-2 text-right text-xs"><strong>Total Addition Publish
                            (Penambahan)</strong></td>
                    <td class="border border-gray-300 p-2 text-right text-green-600 font-semibold"><strong>+
                            {{ number_format($totalAdditionAmount, 0, ',', '.') }}</strong></td>
                </tr>
                <tr>
                    <td class="border border-gray-300 p-2 text-right text-xs"><strong>Total Addition Vendor
                            (Penambahan)</strong></td>
                    <td class="border border-gray-300 p-2 text-right text-green-600 font-semibold"><strong>+
                            {{ number_format($totalAdditionVendorAmount, 0, ',', '.') }}</strong></td>
                </tr>
                <tr>
                    <td class="border border-gray-300 p-2 text-right text-xs"><strong>Total Reduction
                            (Pengurangan)</strong></td>
                    <td class="border border-gray-300 p-2 text-right text-red-600 font-semibold"><strong>-
                            {{ number_format($totalDiscountAmount, 0, ',', '.') }}</strong></td>
                </tr>
                <tr>
                    <td class="border border-gray-300 p-2 text-right text-xs"><strong>Total Paket Publish (Publish +
                            Addition Publish - Reduction)</strong></td>
                    <td class="border border-gray-300 p-2 text-right font-bold text-xs"><strong>
                            {{ number_format($finalPriceAfterDiscounts, 0, ',', '.') }}</strong></td>
                </tr>
                <tr>
                    <td class="border border-gray-300 p-2 text-right text-xs"><strong>Total Paket Vendor (Vendor +
                            Addition Vendor - Reduction)</strong></td>
                    <td class="border border-gray-300 p-2 text-right font-bold text-xs"><strong>
                            {{ number_format($finalVendorPriceAfterDiscounts, 0, ',', '.') }}</strong></td>
                </tr>
                <tr>
                    <td class="border border-gray-300 p-2 text-right text-xs"><strong>Profit & Loss</strong></td>
                    <td class="border border-gray-300 p-2 text-right font-semibold"><strong
                            class="{{ $profitAndLoss < 25000000 ? 'text-red-600' : 'text-green-600' }}">
                            {{ number_format($profitAndLoss, 0, ',', '.') }}</strong></td>
                </tr>
            </table>
        </div>

        {{-- Payment Schedule --}}
        <div class="mt-8 print:break-inside-avoid text-xs">
            <h3 class="text-sm font-semibold text-gray-800 mb-3 pb-2 border-b border-gray-300">Payment Schedule</h3>
            <table class="w-full border-collapse text-sm">
                @php
                    // Gunakan harga final setelah diskon yang sudah dihitung sebelumnya
                    $calculatedGrandTotal = $finalPriceAfterDiscounts;
                @endphp
                <tr>
                    <td class="border border-gray-300 p-2 text-xs"><strong>Booking Fee (Non-refundable)</strong></td>
                    <td class="border border-gray-300 p-2 text-right text-xs">
                        {{ number_format($calculatedGrandTotal * 0.1, 0, ',', '.') }}</td>
                    <td class="border border-gray-300 p-2 text-xs">Jatuh tempo saat konfirmasi</td>
                </tr>
                <tr>
                    <td class="border border-gray-300 p-2 text-xs"><strong>Down Payment (20%)</strong></td>
                    <td class="border border-gray-300 p-2 text-right text-xs">
                        {{ number_format($calculatedGrandTotal * 0.2, 0, ',', '.') }}</td>
                    <td class="border border-gray-300 p-2 text-xs">Jatuh tempo 7 hari setelah booking</td>
                </tr>
                <tr>
                    <td class="border border-gray-300 p-2 text-xs"><strong>Second Payment (40%)</strong></td>
                    <td class="border border-gray-300 p-2 text-right text-xs">
                        {{ number_format($calculatedGrandTotal * 0.4, 0, ',', '.') }}</td>
                    <td class="border border-gray-300 p-2 text-xs">Jatuh tempo 60 hari sebelum hari H</td>
                </tr>
                <tr>
                    <td class="border border-gray-300 p-2 text-xs"><strong>Final Payment (30%)</strong></td>
                    <td class="border border-gray-300 p-2 text-right text-xs">
                        {{ number_format($calculatedGrandTotal * 0.3, 0, ',', '.') }}</td>
                    <td class="border border-gray-300 p-2 text-xs">Jatuh tempo 14 hari sebelum hari H</td>
                </tr>
            </table>
        </div>

        {{-- Action Buttons (Download/Print) - Hidden on Print --}}
        <div class="flex flex-col md:flex-row items-center justify-center gap-4 mt-8 mb-6 print:hidden">
            {{-- Tombol Download PDF --}}
            <x-download-pdf-button :route="route('products.downloadPdf', $product)" label="Download PDF"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-500 active:bg-blue-700 text-white text-xs uppercase font-semibold rounded-md transition" />

            {{-- Tombol Export Excel --}}
            <x-download-pdf-button :route="route('products.exportExcelDetail', $product)" label="Export to Excel"
                class="px-4 py-2 bg-green-600 hover:bg-blue-500 active:bg-blue-700 text-white text-xs uppercase font-semibold rounded-md transition" />
        </div>
</body>

</html>
