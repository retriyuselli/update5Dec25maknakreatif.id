@php
    $company = null;
    if (\Illuminate\Support\Facades\Schema::hasTable('companies')) {
        $company = \App\Models\Company::query()->first();
    }
    $logoSrc =
        $company && $company->logo_url
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($company->logo_url)
            : asset(config('invoice.logo', 'images/logo.png'));
@endphp

<x-filament-panels::page>

    <link rel="stylesheet" href="{{ asset('assets/invoice/invoice.css') }}">

    <div
        class="bg-white dark:bg-gray-800 shadow-m border border-gray-200 dark:border-gray-700 rounded-xl p-4 sm:p-6 lg:p-8 ring-gray-100 dark:ring-gray-700">
        <!-- Invoice Header -->
        <div
            class="flex justify-between items-center border-b border-gray-200 dark:border-gray-600 dark:border-gray-600 pb-4">
            <div>
                <h1 class="font-bold text-gray-800 dark:text-white text-sm sm:text-base">DETAILS #{{ $order->id }}
                </h1>
                <p class="text-gray-600 dark:text-white text-sm sm:text-base">Date:
                    {{ $order->created_at->format('d M Y') }}
                </p>
            </div>
            <div>
                <img src="{{ $logoSrc }}" alt="Company Logo" class="h-10 w-auto mr-4">
            </div>
        </div>

        <!-- Download Buttons -->
        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('invoice.download', ['order' => $order]) }}" target="_blank"
                class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                Download Invoice
            </a>
        </div>

        @php
            $grandTotal = $order->grand_total ?? 0;
            $totalPaid = $order->bayar ?? 0;
            $paymentProgress = $grandTotal > 0 ? ($totalPaid / $grandTotal) * 100 : 0;
            $paymentProgress = min($paymentProgress, 100);

            // Hitung total berdasarkan jumlah harga publik item dari semua produk dalam order
            $totalPublicPrice = 0;
            $totalVendorPrice = 0;
            $totalAdditionAmount = 0;
            $totalAdditionVendorAmount = 0;
            $totalDiscountAmount = 0;

            // Loop melalui semua item order untuk menghitung total
            foreach ($order->items as $orderItem) {
                $product = $orderItem->product;
                if ($product) {
                    // Hitung berdasarkan quantity dari order item
                    $quantity = $orderItem->quantity ?? 1;

                    // Total harga publish dan vendor dari product items
                    $productPublicPrice = ($product->items ?? collect())->sum(function ($item) {
                        return ($item->harga_publish ?? 0) * ($item->quantity ?? 1);
                    });

                    $productVendorPrice = ($product->items ?? collect())->sum(function ($item) {
                        return ($item->harga_vendor ?? 0) * ($item->quantity ?? 1);
                    });

                    // Akumulasi berdasarkan quantity order
                    $totalPublicPrice += $productPublicPrice * $quantity;
                    $totalVendorPrice += $productVendorPrice * $quantity;

                    // Total penambahan dari product
                    $productAdditionPublish = ($product->penambahanHarga ?? collect())->sum('harga_publish');
                    $productAdditionVendor = ($product->penambahanHarga ?? collect())->sum('harga_vendor');

                    $totalAdditionAmount += $productAdditionPublish * $quantity;
                    $totalAdditionVendorAmount += $productAdditionVendor * $quantity;

                    // Total pengurangan dari product
                    $productDiscount = ($product->pengurangans ?? collect())->sum('amount');
                    $totalDiscountAmount += $productDiscount * $quantity;
                }
            }

            // Harga dasar paket adalah total harga publik
            $basePackagePrice = $totalPublicPrice;

            // Hitung harga final setelah diskon dan penambahan
            $finalPriceAfterDiscounts = $basePackagePrice - $totalDiscountAmount + $totalAdditionAmount;
            $finalVendorPriceAfterDiscounts = $totalVendorPrice - $totalDiscountAmount + $totalAdditionVendorAmount;

            // Hitung Profit & Loss dari perhitungan detail
            $calculatedProfitLoss = $finalPriceAfterDiscounts - $finalVendorPriceAfterDiscounts;
        @endphp

        <!-- Billing Information -->
        <div class="billing-info text-sm sm:text-base">
            {{-- <div class="mt-6 grid grid-cols-2 gap-4 text-sm"> --}}
            <div>
                <h2 class="text-gray-700 dark:text-white font-bold mb-2">Billed To :</h2>
                <p class="text-gray-600 dark:text-white">Event :
                    {{ $order->prospect->name_event ?? 'N/A' }}</p>
                <p class="text-gray-600 dark:text-white">Name Nama : CPP_{{ $order->prospect->name_cpp }} &
                    CPW_{{ $order->prospect->name_cpw }}</p>
                <p class="text-gray-600 dark:text-white">Alamat :
                    {{ ucwords(strtolower($order->prospect->address ?? 'N/A')) }}
                </p>
                <p class="text-gray-600 dark:text-white">No Tlp :
                    +62{{ $order->prospect->phone ?? 'N/A' }}</p>
                <p class="text-gray-600 dark:text-white">Venue :
                    {{ $order->prospect->venue ?? 'N/A' }} /
                    {{ $order->pax ?? 'N/A' }}
                    Pax</p>
                <p class="text-gray-600 dark:text-white">Account Manager :
                    {{ $order->user->name ?? 'N/A' }}</p>
                <p class="text-gray-600 dark:text-white">Event Manager :
                    {{ $order->employee->name ?? 'N/A' }}</p>
            </div>
            <div>
                <h2 class="text-sm font-semibold mb-2 text-gray-900 dark:text-white">Invoice Information :</h2>
                <p class="text-gray-600 dark:text-white">Invoice Date : {{ now()->format('d F Y') }}</p>
                <p class="text-gray-600 dark:text-white">Due Date :
                    {{ now()->addDays(30)->format('d F Y') }}</p>
                <p class="status-bayar text-gray-900 dark:text-white">Status Pembayaran :
                    @if ($order->is_paid)
                        <span class="text-green-600 font-semibold">Paid</span>
                    @else
                        <span class="text-red-600 font-semibold">Unpaid</span>
                    @endif
                </p>
                <p class="text-gray-600 dark:text-white">Tgl Lamaran :
                    {{ $order->prospect->date_lamaran ? \Carbon\Carbon::parse($order->prospect->date_lamaran)->format('d F Y') : '-' }}
                </p>
                <p class="text-gray-600 dark:text-white">Tgl Akad :
                    {{ $order->prospect->date_akad ? \Carbon\Carbon::parse($order->prospect->date_akad)->format('d F Y') : '-' }}
                </p>
                <p class="text-gray-600 dark:text-white">Tgl Resepsi:
                    {{ $order->prospect->date_resepsi ? \Carbon\Carbon::parse($order->prospect->date_resepsi)->format('d F Y') : '-' }}
                </p>
            </div>
        </div>

        <!-- Separator Line -->
        <hr class="border-t-2 border-gray-200 dark:border-gray-600 py-1.5">

        <!-- Rincian Perhitungan Pada Product -->
        <div class="mt-8 pt-10 mb-10">
            <h3 class="section-header">
                <svg xmlns="http://www.w3.org/2000/svg" class="section-header-icon" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="section-header-content">
                    <span class="section-header-title">Rincian Perhitungan Pada Product</span>
                    <p class="section-description">Menampilkan rincian item yang menjadi faktor pengurang dari total
                        harga paket produk.</p>
                </div>
            </h3>
            <div class="overflow-x-auto">
                <table class="item-pengurangan-table w-full text-sm sm:text-base">
                    <thead>
                        <tr>
                            <th colspan="2"
                                class="bg-gray-100 dark:bg-gray-700 text-left px-4 py-2 font-semibold text-gray-700 dark:text-white">
                                Price Calculation Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td
                                class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                Total Publish Price</td>
                            <td
                                class="text-right px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                Rp
                                {{ number_format($basePackagePrice, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <td
                                class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                Total Vendor Price</td>
                            <td
                                class="text-right px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                Rp
                                {{ number_format($totalVendorPrice, 0, ',', '.') }}
                            </td>
                        </tr>

                        @if ($totalAdditionAmount > 0)
                            <tr>
                                <td
                                    class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                    Total Addition Publish (Penambahan)</td>
                                <td
                                    class="text-right px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-green-600">
                                    + Rp
                                    {{ number_format($totalAdditionAmount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif

                        @if ($totalAdditionVendorAmount > 0)
                            <tr>
                                <td
                                    class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                    Total Addition Vendor (Penambahan)</td>
                                <td
                                    class="text-right px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-green-600">
                                    + Rp
                                    {{ number_format($totalAdditionVendorAmount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif

                        @if ($totalDiscountAmount > 0)
                            <tr>
                                <td
                                    class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                    Total Reduction (Pengurangan)</td>
                                <td
                                    class="text-right px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-red-600">
                                    - Rp
                                    {{ number_format($totalDiscountAmount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif

                        <tr>
                            <td
                                class="font-semibold px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                Total Paket Publish</td>
                            <td
                                class="text-right font-semibold px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                Rp
                                {{ number_format($finalPriceAfterDiscounts, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <td
                                class="font-semibold px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                Total Paket Vendor</td>
                            <td
                                class="text-right font-semibold px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                Rp
                                {{ number_format($finalVendorPriceAfterDiscounts, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <td
                                class="font-semibold px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                Calculated Profit & Loss</td>
                            <td
                                class="text-right font-semibold px-4 py-2 border-b border-gray-200 dark:border-gray-600 {{ $calculatedProfitLoss < 25000000 ? 'text-red-600' : 'text-green-600' }}">
                                <strong>Rp {{ number_format($calculatedProfitLoss, 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Rincian Perhitungan Realisasi -->
        <div class="mt-8 pt-10 mb-10">
            <h3 class="section-header">
                <svg xmlns="http://www.w3.org/2000/svg" class="section-header-icon" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="section-header-content">
                    <span class="section-header-title">Rincian Perhitungan Realisasi</span>
                    <p class="section-description">Menampilkan rincian perhitungan yang telah di realisasikan.</p>
                </div>
            </h3>
            <div class="overflow-x-auto">
                <table class="item-pengurangan-table w-full text-sm sm:text-base">
                    <thead class="bg-gray-50">
                        <tr>
                            <th colspan="2"
                                class="bg-gray-100 dark:bg-gray-700 text-left px-4 py-2 font-semibold text-gray-700 dark:text-white">
                                Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td
                                class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                Total Paket Awal</td>
                            <td
                                class="text-right px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                Rp
                                {{ number_format($basePackagePrice, 0, ',', '.') }}
                            </td>
                        </tr>

                        @if ($order->promo > 0)
                            <tr>
                                <td
                                    class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                    Diskon</td>
                                <td
                                    class="text-right px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                    - Rp
                                    {{ number_format($order->promo, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif

                        @if ($totalAdditionAmount > 0)
                            <tr>
                                <td
                                    class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                    Penambahan</td>
                                <td
                                    class="text-right px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-green-700 font-semibold">
                                    + Rp
                                    {{ number_format($totalAdditionAmount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif

                        @if ($order->pengurangan > 0)
                            <tr>
                                <td
                                    class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                    Pengurangan</td>
                                <td
                                    class="text-right px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                    - Rp
                                    {{ number_format($order->pengurangan, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif

                        <tr>
                            <td
                                class="font-semibold px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                Grand Total</td>
                            <td
                                class="text-right font-semibold px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                Rp
                                {{ number_format($order->grand_total, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <td
                                class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                Sudah Dibayar</td>
                            <td
                                class="text-right px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                Rp
                                {{ number_format($order->bayar, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <td
                                class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                Total Pembayaran Vendor</td>
                            <td
                                class="text-right px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                Rp
                                @php
                                    $totalVendor = $order->expenses()->sum('amount');
                                @endphp
                                {{ number_format($totalVendor, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr class="total">
                            <td
                                class="font-semibold px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                Sisa Tagihan (Balance
                                Due)
                            </td>
                            <td
                                class="text-right font-semibold px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                <strong>Rp
                                    {{ number_format($order->sisa, 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                    </tbody>
                </table>

                @php $profitLoss = $order->laba_kotor ?? 0; @endphp
                <div class="profit-loss-card {{ $profitLoss >= 0 ? 'is-profit' : 'is-loss' }}">
                    <div class="profit-loss-card-content">
                        <div class="profit-loss-card-details">
                            <p class="profit-loss-card-title">Laba / Rugi Kotor</p>
                            <p class="profit-loss-card-description">Grand Total - Total Pembayaran Vendor</p>
                            @php
                                $selisihProfitLoss = $profitLoss - $calculatedProfitLoss;
                            @endphp
                            <p class="profit-loss-card-description">Selisih dengan Calculated Profit & Loss:
                                <span
                                    class="{{ $selisihProfitLoss >= 0 ? 'text-green-600' : 'text-red-600' }} font-semibold">
                                    {{ $selisihProfitLoss >= 0 ? '+' : '' }}Rp
                                    {{ number_format($selisihProfitLoss, 0, ',', '.') }}
                                </span>
                            </p>
                        </div>
                        <p class="profit-loss-card-amount">
                            Rp {{ number_format($profitLoss, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Pengurangan per Produk dalam Order -->
        @php
            // For better practice, this logic should be moved to an accessor in the Order model,
            // e.g., public function getAllProductPengurangansAttribute()
            $allProductPengurangans = collect();
            if ($order->items && $order->items->count() > 0) {
                foreach ($order->items as $orderItem) {
                    if ($orderItem->product && $orderItem->product->pengurangans->count() > 0) {
                        foreach ($orderItem->product->pengurangans as $pengurangan) {
                            // Menambahkan nama produk ke objek pengurangan untuk referensi
                            $pengurangan->product_name = $orderItem->product->name;
                            $allProductPengurangans->push($pengurangan);
                        }
                    }
                }
            }
        @endphp

        <div class="mt-8 pt-10 mb-10">
            <h3 class="section-header">
                <svg xmlns="http://www.w3.org/2000/svg" class="section-header-icon" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="section-header-content">
                    <span class="section-header-title">Rincian Item Pengurangan Produk</span>
                    <p class="section-description">Menampilkan rincian item yang menjadi faktor pengurang dari total
                        harga paket produk.</p>
                </div>
            </h3>
            <div class="overflow-x-auto">
                <table class="item-pengurangan-table w-full text-sm sm:text-base">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="bg-gray-100 dark:bg-gray-700 px-4 py-2 text-center w-10 text-gray-700 dark:text-white font-medium">
                                No</th>
                            <th
                                class="bg-gray-100 dark:bg-gray-700 px-4 py-2 text-left text-gray-700 dark:text-white font-medium">
                                Deskripsi
                                Pengurangan
                            </th>
                            <th
                                class="bg-gray-100 dark:bg-gray-700 px-4 py-2 text-right text-gray-700 dark:text-white font-medium w-2/5">
                                Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allProductPengurangans as $index => $itemPengurangan)
                            <tr>
                                <td
                                    class="text-center px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                    {{ $index + 1 }}</td>
                                <td
                                    class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                    <div>
                                        {{ ucwords(strtolower($itemPengurangan->description ?? 'N/A')) }}
                                    </div>
                                    @if ($itemPengurangan->notes)
                                        <div class="ml-7 text-gray-600 dark:text-white">
                                            {!! strip_tags($itemPengurangan->notes, '<li><strong><em><ul><br><span><div>') !!}
                                        </div>
                                    @endif
                                </td>
                                <td
                                    class="text-right px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                    Rp
                                    {{ number_format($itemPengurangan->amount ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3"
                                    class="text-center px-4 py-3 border-b border-gray-200 dark:border-gray-600 text-gray-500 dark:text-gray-100 italic">
                                    Tidak ada item pengurangan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Payment Progress Bar -->
        <div class="progress-bar-container">
            <div class="progress-bar-header">
                <span class="progress-bar-label">Progress Pembayaran</span>
                <span class="progress-bar-percentage">{{ number_format($paymentProgress, 1) }}%</span>
            </div>
            <div class="progress-bar-track">
                <div class="progress-bar-fill" style="width: {{ $paymentProgress }}%"></div>
            </div>
        </div>

        <!-- Payment History -->
        <div class="mt-8">
            <h3 class="section-header">
                <svg xmlns="http://www.w3.org/2000/svg" class="section-header-icon" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <div class="section-header-content">
                    <span class="section-header-title">Payment History</span>
                    <p class="section-description">Riwayat semua pembayaran yang telah diterima dari klien untuk
                        invoice ini.</p>
                </div>
            </h3>
            <div class="overflow-x-auto">
                <table class="payment-history-table w-full text-sm sm:text-base">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="bg-gray-100 dark:bg-gray-700 px-4 py-2 text-left text-gray-700 dark:text-white font-medium">
                                Date</th>
                            <th
                                class="bg-gray-100 dark:bg-gray-700 px-4 py-2 text-right text-gray-700 dark:text-white font-medium">
                                Amount</th>
                            <th
                                class="bg-gray-100 dark:bg-gray-700 px-4 py-2 text-left text-gray-700 dark:text-white font-medium">
                                Payment Method
                            </th>
                            <th
                                class="bg-gray-100 dark:bg-gray-700 px-4 py-2 text-left text-gray-700 dark:text-white font-medium">
                                Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($order->dataPembayaran as $payment)
                            <tr>
                                <td
                                    class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($payment->tgl_bayar)->format('d F Y') }}
                                </td>
                                <td
                                    class="text-right px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                    Rp
                                    {{ number_format($payment->nominal, 0, ',', '.') }}
                                </td>
                                <td
                                    class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                    @if ($payment->paymentMethod)
                                        <div>
                                            <span
                                                class="font-medium text-gray-900 dark:text-white">{{ $payment->paymentMethod->name }}</span>
                                            @if ($payment->paymentMethod->no_rekening)
                                                <br>
                                                <span
                                                    class="text-sm text-gray-600 dark:text-white">{{ $payment->paymentMethod->no_rekening }}</span>
                                            @endif
                                        </div>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td
                                    class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                    {{ $payment->keterangan }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4"
                                    class="text-center px-4 py-3 text-gray-500 dark:text-gray-100 italic">
                                    No payment history available.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pembayaran Vendor -->
        <div class="mt-8">
            <h3 class="section-header">
                <svg xmlns="http://www.w3.org/2000/svg" class="section-header-icon" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <div class="section-header-content">
                    <span class="section-header-title">Pembayaran Vendor</span>
                    <p class="section-description">Rincian semua pengeluaran yang telah dibayarkan kepada vendor
                        terkait proyek ini.</p>
                </div>
            </h3>
            <div class="overflow-x-auto">
                <table class="vendor-payment-table w-full text-sm sm:text-base">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="bg-gray-100 dark:bg-gray-700 px-4 py-2 text-left text-gray-700 dark:text-white font-medium">
                                Tgl</th>
                            <th
                                class="bg-gray-100 dark:bg-gray-700 px-4 py-2 text-left text-gray-700 dark:text-white font-medium">
                                Vendor</th>
                            <th
                                class="bg-gray-100 dark:bg-gray-700 px-4 py-2 text-left text-gray-700 dark:text-white font-medium">
                                Keterangan</th>
                            <th
                                class="bg-gray-100 dark:bg-gray-700 px-4 py-2 text-left text-gray-700 dark:text-white font-medium">
                                No ND</th>
                            <th
                                class="bg-gray-100 dark:bg-gray-700 px-4 py-2 text-right text-gray-700 dark:text-white font-medium">
                                Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $allExpenses = $order->expenses()->latest('date_expense')->get();
                            $visibleLimit = 5; // Jumlah item yang terlihat secara default
                        @endphp
                        @forelse($allExpenses as $expense)
                            <tr class="vendor-expense-row @if ($loop->iteration > $visibleLimit) hidden @endif">
                                <td
                                    class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                    {{ $expense->date_expense ? \Carbon\Carbon::parse($expense->date_expense)->format('d M Y') : '-' }}
                                </td>
                                <td
                                    class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                    {{ $expense->vendor->name ?? 'N/A' }}
                                </td>
                                <td
                                    class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                    {{ ucwords(strtolower($expense->note ?? 'N/A')) }}
                                </td>
                                <td
                                    class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                    {{ $expense->no_nd ? '' . $expense->no_nd : '-' }}
                                </td>
                                <td
                                    class="text-right px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                    Rp
                                    {{ number_format($expense->amount ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4"
                                    class="text-center px-4 py-3 text-gray-500 dark:text-gray-100 italic">
                                    Tidak ada data pembayaran vendor.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Tombol "Show More" --}}
            @if ($allExpenses->count() > $visibleLimit)
                <div class="flex justify-center">
                    <button id="toggle-vendor-expenses" class="show-more-button">
                        Tampilkan {{ $allExpenses->count() - $visibleLimit }} Lainnya
                    </button>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButton = document.getElementById('toggle-vendor-expenses');
            const rows = document.querySelectorAll('tr.vendor-expense-row');
            const visibleLimit = {{ $visibleLimit }};

            if (toggleButton) {
                toggleButton.addEventListener('click', function() {
                    let isShowingAll = this.dataset.showingAll === 'true';

                    rows.forEach((row, index) => {
                        if (index >= visibleLimit) {
                            row.classList.toggle('hidden');
                        }
                    });

                    // Update button text and state
                    isShowingAll = !isShowingAll;
                    this.dataset.showingAll = isShowingAll;
                    this.textContent = isShowingAll ? 'Tampilkan Lebih Sedikit' :
                        'Tampilkan {{ $allExpenses->count() - $visibleLimit }} Lainnya';
                });
            }
        });
    </script>
</x-filament-panels::page>
