<x-filament-panels::page>

    {{-- Memuat file CSS khusus untuk halaman detail uang keluar --}}
    <link rel="stylesheet" href="{{ asset('assets/payment/paymentmethod.css') }}">

    <div class="bg-white shadow-m border border-gray-200 rounded-xl p-4 sm:p-6 lg:p-8 ring-gray-100">
        <!-- Header -->
        <div class="flex justify-between items-center border-b pb-4">
            <div>
                <h1 class="font-bold text-gray-800 text-sm sm:text-base">DETAIL UANG KELUAR</h1>
                <p class="text-gray-600 text-sm sm:text-base">Rekening: {{ $record->name }}</p>
            </div>
            <div>
                <div class="text-right">
                    <p class="text-lg font-bold text-red-600">
                        Rp {{ number_format($totalUangKeluar, 0, ',', '.') }}
                    </p>
                    <p class="text-sm text-gray-500">
                        {{ $expenses->count() + $expenseOps->count() + $pengeluaranLain->count() }} transaksi
                    </p>
                </div>
            </div>
        </div>

        <!-- Ringkasan Informasi -->
        <div class="billing-info text-sm sm:text-base mt-8 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-gray-700 font-bold mb-2">- Detail Rekening:</h2>
                    <p class="text-gray-600">- Nama Rekening: {{ $record->name }}</p>
                    <p class="text-gray-600">- Total Pengeluaran: Rp {{ number_format($totalUangKeluar, 0, ',', '.') }}
                    </p>
                    <p class="text-gray-600">- Jumlah Transaksi:
                        {{ $expenses->count() + $expenseOps->count() + $pengeluaranLain->count() }}</p>
                </div>
                <div>
                    <h2 class="text-sm font-semibold mb-2">Distribusi Pengeluaran:</h2>
                    <p class="text-gray-600">- Wedding: {{ $expenses->count() }} transaksi</p>
                    <p class="text-gray-600">- Operasional: {{ $expenseOps->count() }} transaksi</p>
                    <p class="text-gray-600">- Lainnya: {{ $pengeluaranLain->count() }} transaksi</p>
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
                                Ringkasan Pengeluaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="px-4 py-2 border-b border-gray-200">Total Wedding Expense</td>
                            <td class="text-right px-4 py-2 border-b border-gray-200">Rp
                                {{ number_format($expenses->sum('amount'), 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 border-b border-gray-200">Total Operational Expense</td>
                            <td class="text-right px-4 py-2 border-b border-gray-200">Rp
                                {{ number_format($expenseOps->sum('amount'), 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 border-b border-gray-200">Total Pengeluaran Lain</td>
                            <td class="text-right px-4 py-2 border-b border-gray-200">Rp
                                {{ number_format($pengeluaranLain->sum('amount'), 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr class="total">
                            <td class="font-semibold px-4 py-2 border-b border-gray-200">TOTAL UANG KELUAR</td>
                            <td class="text-right font-semibold px-4 py-2 border-b border-gray-200">
                                <strong>Rp {{ number_format($totalUangKeluar, 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Detail Wedding Expense -->
        <div class="mt-12 pt-8 mb-12">
            <h3 class="section-header mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="section-header-icon" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <div class="section-header-content">
                    <span class="section-header-title">Detail Wedding Expense</span>
                    <p class="section-description">Rincian pengeluaran untuk acara pernikahan.</p>
                </div>
            </h3>
            <div class="overflow-x-auto mt-6">
                <table class="item-pengurangan-table w-full text-sm sm:text-base">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                No. ND</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Project</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Vendor</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Keterangan</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($expenses as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->date_expense ? \Carbon\Carbon::parse($item->date_expense)->format('d M Y') : 'Tanggal tidak tersedia' }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-900">
                                    {{ $item->no_nd ?? 'Tidak ada nomor ND' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ ucwords(strtolower($item->order->prospect->name_event ?? 'Tidak ada project')) }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ ucwords(strtolower($item->vendor->name ?? 'Tidak ada vendor')) }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ ucwords(strtolower($item->note ?? 'Wedding Expense')) }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-red-600 text-right font-medium">
                                    -Rp {{ number_format($item->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    🏢 Belum ada transaksi wedding expense
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($expenses->count() > 0)
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-sm font-medium text-gray-900">
                                    Total Wedding Expense
                                </td>
                                <td class="px-4 py-3 text-sm font-bold text-red-600 text-right">
                                    -Rp {{ number_format($expenses->sum('amount'), 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        <!-- Detail Operational Expense -->
        <div class="mt-12 pt-8 mb-12">
            <h3 class="section-header mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="section-header-icon" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <div class="section-header-content">
                    <span class="section-header-title">Detail Operational Expense</span>
                    <p class="section-description">Rincian pengeluaran operasional harian.</p>
                </div>
            </h3>
            <div class="overflow-x-auto mt-6">
                <table class="item-pengurangan-table w-full text-sm sm:text-base">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                No. ND</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nama</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Keterangan</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($expenseOps as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->date_expense ? \Carbon\Carbon::parse($item->date_expense)->format('d M Y') : 'Tanggal tidak tersedia' }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-900">
                                    {{ $item->no_nd ?? 'Tidak ada nomor ND' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ $item->name ?? 'Operational Expense' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ $item->note ?? 'Operational Expense' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-orange-600 text-right font-medium">
                                    -Rp {{ number_format($item->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    🏢 Belum ada transaksi operational expense
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($expenseOps->count() > 0)
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-sm font-medium text-gray-900">
                                    Total Operational Expense
                                </td>
                                <td class="px-4 py-3 text-sm font-bold text-orange-600 text-right">
                                    -Rp {{ number_format($expenseOps->sum('amount'), 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        <!-- Detail Pengeluaran Lain -->
        <div class="mt-12 pt-8 mb-12">
            <h3 class="section-header mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="section-header-icon" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <div class="section-header-content">
                    <span class="section-header-title">Detail Pengeluaran Lain</span>
                    <p class="section-description">Rincian pengeluaran lain-lain.</p>
                </div>
            </h3>
            <div class="overflow-x-auto mt-6">
                <table class="item-pengurangan-table w-full text-sm sm:text-base">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                No. ND</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Keterangan</th>
                            <th
                                class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($pengeluaranLain as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->date_expense ? \Carbon\Carbon::parse($item->date_expense)->format('d M Y') : 'Tanggal tidak tersedia' }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-900">
                                    {{ $item->no_nd ?? 'Tidak ada nomor ND' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ $item->note ?? 'Pengeluaran Lain' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-purple-600 text-right font-medium">
                                    -Rp {{ number_format($item->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                    🏢 Belum ada transaksi pengeluaran lain
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($pengeluaranLain->count() > 0)
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-sm font-medium text-gray-900">
                                    Total Pengeluaran Lain
                                </td>
                                <td class="px-4 py-3 text-sm font-bold text-purple-600 text-right">
                                    -Rp {{ number_format($pengeluaranLain->sum('amount'), 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

</x-filament-panels::page>
