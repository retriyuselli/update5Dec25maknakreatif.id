<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('assets/invoice/invoice.css') }}">

    <div class="bg-white shadow-sm border border-gray-200 rounded-xl p-4 sm:p-6 lg:p-8">
        
        <!-- Header -->
        <h1 class="font-bold text-gray-800 text-xl">SURAT PERSETUJUAN PEMBAYARAN</h1>
        <h2 class="font-semibold text-gray-700 text-lg mt-2">{{ $notaDinas->no_nd }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-gray-600 text-sm">Tgl: {{ $notaDinas->created_at->format('d F Y') }}</p>
                <p class="text-sm text-gray-800">Diajukan oleh: {{ $notaDinas->pengirim->name ?? 'N/A' }}</p>
                <p class="text-sm text-gray-800">Status:
                    <span
                        class="text-gray-600 text-sm
                            {{ $notaDinas->status === 'disetujui'
                                ? 'bg-green-100 text-green-800'
                                : ($notaDinas->status === 'diajukan'
                                    ? 'bg-yellow-100 text-yellow-800'
                                    : ($notaDinas->status === 'ditolak'
                                        ? 'bg-red-100 text-red-800'
                                        : 'bg-gray-100 text-gray-800')) }}">
                        {{ ucfirst($notaDinas->status) }}
                    </span>
                </p>
            </div>
            <div>
                <p class="text-gray-600 text-sm"><strong>Sifat:</strong> {{ $notaDinas->sifat }}</p>
                <p class="text-gray-600 text-sm"><strong>Hal:</strong> {{ $notaDinas->hal }}</p>
                @if($notaDinas->nd_upload)
                    <p class="text-gray-600 text-sm mt-2">
                        <strong>File Lampiran:</strong> 
                        <a href="{{ asset('storage/' . $notaDinas->nd_upload) }}" 
                           target="_blank" 
                           class="text-blue-600 hover:text-blue-800 underline ml-1">
                            📎 {{ basename($notaDinas->nd_upload) }}
                        </a>
                    </p>
                @endif
            </div>
        </div>

        <!-- Detail Pengeluaran -->
        <div class="mb-6">
            <h3 class="font-semibold text-gray-800 text-lg mb-2" style="margin-top: 15px;">Detail Pengeluaran</h3>
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Vendor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Keperluan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Event</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Invoice</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jumlah (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($details as $detail)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $detail->vendor->name ?? 'N/A' }}
                                    </div>
                                    @if ($detail->order && $detail->order->prospect)
                                        <div class="text-sm text-gray-500">{{ $detail->order->prospect->name ?? '' }}
                                        </div>
                                    @endif
                                    @if ($detail->payment_stage)
                                        <div class="text-xs text-blue-600 font-medium">{{ $detail->payment_stage }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $detail->keperluan }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $detail->order && $detail->order->prospect ? $detail->order->prospect->name_event : $detail->event ?? '-' }}
                                    @if ($detail->jenis_pengeluaran)
                                        <div class="text-xs text-green-600 font-medium">{{ ucfirst($detail->jenis_pengeluaran) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $detail->invoice_number ?? '-' }}
                                    @if ($detail->invoice_file)
                                        <div class="text-xs text-green-600 font-medium">📎 File Ada</div>
                                    @else
                                        <div class="text-xs text-red-500">📄 Tidak Ada File</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-medium">
                                     {{ number_format($detail->jumlah_transfer, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-sm font-medium text-gray-900 text-right">Total:
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right">
                                 {{ number_format($totalJumlahTransfer, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Detail Transfer Bank -->
        <div class="mb-6">
            <h3 class="font-semibold text-gray-800 text-lg mb-4" style="margin-top: 15px;">Detail Transfer Bank</h3>
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Vendor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Bank</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                No. Rekening</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Atas Nama</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jumlah (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $bankGroups = $details
                                ->whereNotNull('bank_name')
                                ->groupBy(function ($item) {
                                    return $item->bank_name . '|' . $item->bank_account;
                                });
                        @endphp
                        @foreach ($bankGroups as $bankGroup)
                            @php $firstDetail = $bankGroup->first(); @endphp
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $firstDetail->vendor->name ?? 'N/A' }}</div>
                                    @if($bankGroup->count() > 1)
                                        <div class="text-xs text-gray-500">+ {{ $bankGroup->count() - 1 }} vendor lainnya</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $firstDetail->bank_name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $firstDetail->bank_account }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $firstDetail->account_holder }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-medium">
                                     {{ number_format($bankGroup->sum('jumlah_transfer'), 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-sm font-medium text-gray-900 text-right">Total Transfer:</td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right">
                                @php
                                    $totalBankTransfer = $details->whereNotNull('bank_name')->sum('jumlah_transfer');
                                @endphp
                                 {{ number_format($totalBankTransfer, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer mt-8 pt-6 border-t border-gray-300 text-center">
        <p class="text-sm text-gray-600">Catatan Penting!!!</p>
        <p class="text-sm text-gray-600">Pastikan semua dokumen pendukung telah dilampirkan sebelum mengajukan nota dinas. Jangan sampai ada kesalahan sebelum mengirimkan nota dinas.</p>
    </div>

    <!-- Print Styles -->
    <style>
        @media print {

            /* Hide Filament UI elements */
            .fi-topbar,
            .fi-sidebar,
            .fi-header,
            .fi-btn,
            button,
            .fi-breadcrumbs,
            .fi-page-header,
            .fi-page-actions,
            .filament-page-actions {
                display: none !important;
            }

            /* Reset main layout for print */
            .fi-main,
            .fi-page,
            .fi-page-content {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }

            body {
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
            }

            /* Reset container styles */
            .bg-white {
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
            }

            /* Optimize spacing for print */
            .mb-6 {
                margin-bottom: 15px !important;
            }

            .mb-4 {
                margin-bottom: 10px !important;
            }

            .p-4,
            .p-6,
            .p-8 {
                padding: 10px !important;
            }

            /* Table styling for print */
            table {
                break-inside: avoid;
                font-size: 10px !important;
            }

            th,
            td {
                padding: 4px !important;
                border: 1px solid #333 !important;
            }

            th {
                background-color: #f5f5f5 !important;
                font-weight: bold !important;
            }

            /* Grid layouts for print */
            .grid {
                break-inside: avoid;
                display: block !important;
            }

            .grid>div {
                display: inline-block !important;
                width: 48% !important;
                vertical-align: top !important;
                margin-right: 2% !important;
            }

            .grid>div:last-child {
                margin-right: 0 !important;
            }

            /* Font sizes for print */
            .text-xl {
                font-size: 16px !important;
            }

            .text-lg {
                font-size: 14px !important;
            }

            .text-sm {
                font-size: 10px !important;
            }

            .text-xs {
                font-size: 9px !important;
            }

            /* Colors for print */
            .text-gray-800 {
                color: #000 !important;
            }

            .text-gray-600 {
                color: #333 !important;
            }

            .text-blue-600 {
                color: #000 !important;
                font-weight: bold !important;
            }

            /* Background colors */
            .bg-gray-50 {
                background-color: #f8f8f8 !important;
            }

            .bg-blue-50 {
                background-color: #f0f0f0 !important;
            }
        }

        /* Print button functionality */
        @media screen {
            .print-btn {
                position: fixed;
                bottom: 20px;
                right: 20px;
                z-index: 1000;
            }
        }
    </style>

    <!-- Print Script -->
    <script>
        // Add print shortcut
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });

        // Optimize print layout
        window.addEventListener('beforeprint', function() {
            document.body.classList.add('printing');
        });

        window.addEventListener('afterprint', function() {
            document.body.classList.remove('printing');
        });
    </script>
</x-filament-panels::page>
