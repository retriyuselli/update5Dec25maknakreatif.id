<x-filament-panels::page>

    {{-- Memuat file CSS khusus untuk halaman detail uang masuk --}}
    <link rel="stylesheet" href="{{ asset('assets/payment/paymentmethod.css') }}">

    <div class="bg-white shadow-m border border-gray-200 rounded-xl p-4 sm:p-6 lg:p-8 ring-gray-100">
        <!-- Header -->
        <div class="flex justify-between items-center border-b pb-4">
            <div>
                <h1 class="font-bold text-gray-800 text-sm sm:text-base">DETAIL UANG MASUK</h1>
                <p class="text-gray-600 text-sm sm:text-base">Rekening: {{ $record->name }}</p>
            </div>
            <div>
                <div class="text-right">
                    <p class="text-lg font-bold text-green-600">
                        Rp {{ number_format($totalUangMasuk, 0, ',', '.') }}
                    </p>
                    <p class="text-sm text-gray-500">
                        {{ $pendapatanLain->count() + $dataPembayaran->count() }} transaksi masuk
                    </p>
                    <p class="text-xs text-gray-400 italic">
                        (Transaksi sejak
                        {{ $record->opening_balance_date ? \Carbon\Carbon::parse($record->opening_balance_date)->format('d M Y') : 'pembukaan rekening' }})
                    </p>
                </div>
            </div>
        </div>

        <!-- Ringkasan Informasi -->
        <div class="billing-info text-sm sm:text-base mt-8 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-gray-700 font-bold mb-2">Detail Rekening:</h2>
                    <p class="text-gray-600">- Nama Rekening: {{ $record->name }}</p>
                    <p class="text-gray-600">- Saldo Awal: Rp
                        {{ number_format($record->opening_balance, 0, ',', '.') }}</p>
                    <p class="text-gray-600">- Total Transaksi Masuk: Rp
                        {{ number_format($totalUangMasuk, 0, ',', '.') }}</p>
                    <p class="text-gray-600">- Jumlah Transaksi:
                        {{ $pendapatanLain->count() + $dataPembayaran->count() }}
                    </p>
                </div>
                <div>
                    <h2 class="text-sm font-semibold mb-2">Distribusi Pemasukan:</h2>
                    <p class="text-gray-600">- Pendapatan Lain: {{ $pendapatanLain->count() }}
                        transaksi</p>
                    <p class="text-gray-600">- Pembayaran Customer: {{ $dataPembayaran->count() }}
                        transaksi</p>
                    <p class="text-gray-500 text-xs mt-2 italic">
                        * Catatan: Total di atas adalah transaksi masuk aktual,
                        tidak termasuk saldo awal (opening balance).
                    </p>
                </div>
            </div>
        </div>

        <!-- Tabel Ringkasan -->
        <div class="mt-10 mb-12">
            <div class="col-12 overflow-x-auto">
                <table class="detail-tagihan-table w-full text-sm sm:text-base">
                    <thead>
                        <tr>
                            <th colspan="2" class="bg-gray-100 text-left px-4 py-2 font-semibold text-gray-700">
                                Ringkasan Pemasukan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="px-4 py-2 border-b border-gray-200">Total Pendapatan Lain</td>
                            <td class="text-right px-4 py-2 border-b border-gray-200">Rp
                                {{ number_format($pendapatanLain->sum('nominal'), 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 border-b border-gray-200">Total Pembayaran Customer</td>
                            <td class="text-right px-4 py-2 border-b border-gray-200">Rp
                                {{ number_format($dataPembayaran->sum('nominal'), 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr class="total">
                            <td class="font-semibold px-4 py-2 border-b border-gray-200">TOTAL UANG MASUK</td>
                            <td class="text-right font-semibold px-4 py-2 border-b border-gray-200">
                                <strong>Rp {{ number_format($totalUangMasuk, 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Detail Pendapatan Lain -->
        <div class="mt-12 pt-8 mb-12">
            <h3 class="section-header mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="section-header-icon" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <div class="section-header-content">
                    <span class="section-header-title">Detail Pendapatan Lain</span>
                    <p class="section-description">Rincian pendapatan di luar pembayaran customer.</p>
                </div>
            </h3>
            <div class="overflow-x-auto mt-6">
                <table class="item-pengurangan-table w-full text-sm sm:text-base">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Keterangan</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($pendapatanLain as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->tgl_bayar ? \Carbon\Carbon::parse($item->tgl_bayar)->format('d M Y') : 'Tanggal tidak tersedia' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ $item->keterangan ?? 'Pendapatan Lain' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-green-600 text-right font-medium">
                                    +Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                                    📝 Belum ada transaksi pendapatan lain
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($pendapatanLain->count() > 0)
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="2" class="px-4 py-3 text-sm font-medium text-gray-900">
                                    Total Pendapatan Lain
                                </td>
                                <td class="px-4 py-3 text-sm font-bold text-green-600 text-right">
                                    +Rp {{ number_format($pendapatanLain->sum('nominal'), 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        <!-- Detail Pembayaran Customer -->
        <div class="mt-12 pt-8 mb-12">
            <h3 class="section-header mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="section-header-icon" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <div class="section-header-content">
                    <span class="section-header-title">Detail Pembayaran Customer</span>
                    <p class="section-description">Rincian pembayaran yang diterima dari customer.</p>
                </div>
            </h3>
            <div class="overflow-x-auto mt-6">
                <table class="item-pengurangan-table w-full text-sm sm:text-base">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Customer/Perusahaan</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($dataPembayaran as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->tgl_bayar ? \Carbon\Carbon::parse($item->tgl_bayar)->format('d M Y') : 'Tanggal tidak tersedia' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ $item->order->prospect->name_event ?? 'Pembayaran Customer' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-blue-600 text-right font-medium">
                                    +Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                                    🏢 Belum ada transaksi pembayaran customer
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($dataPembayaran->count() > 0)
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="2" class="px-4 py-3 text-sm font-medium text-gray-900">
                                    Total Pembayaran Customer
                                </td>
                                <td class="px-4 py-3 text-sm font-bold text-blue-600 text-right">
                                    +Rp {{ number_format($dataPembayaran->sum('nominal'), 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

</x-filament-panels::page>
