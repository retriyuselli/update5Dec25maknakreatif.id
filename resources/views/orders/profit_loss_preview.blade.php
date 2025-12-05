<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Laba Rugi - Order #{{ $order->number }}</title>
    {{-- Menggunakan Poppins dari Google Fonts untuk tampilan web --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    {{-- Sertakan Tailwind CSS dari CDN untuk kemudahan --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            color: #000000;
            font-size: 14px;
            line-height: 1.4;
            max-width: 100%;
        }
        
        .header {
            border-bottom: 2px solid #ddd;
            margin-bottom: 20px;
            padding-bottom: 15px;
        }
        
        .invoice-title h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
            text-transform: uppercase;
            color: #1f2937;
        }
        
        .invoice-title h4 {
            font-size: 16px;
            font-weight: normal;
            margin-top: 5px;
            color: #6b7280;
        }
        
        table {
            border-collapse: collapse;
            margin-bottom: 15px;
            width: 100%;
        }
        
        th, td {
            padding: 10px;
            text-align: left;
            vertical-align: top;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: 600;
            border: 1px solid #ddd;
        }
        
        .bordered th, .bordered td {
            border: 1px solid #ddd;
        }
        
        .profit-positive {
            background-color: #f0fdf4;
            border: 2px solid #16a34a;
            color: #15803d;
        }
        
        .profit-negative {
            background-color: #fef2f2;
            border: 2px solid #dc2626;
            color: #b91c1c;
        }
        
        .section-header {
            background-color: #374151;
            color: white;
            font-weight: 600;
            text-align: center;
            padding: 12px;
            margin: 20px 0 10px 0;
        }
        
        .highlight-row {
            background-color: #f3f4f6;
            font-weight: 600;
        }
        
        .download-btn {
            background-color: #2563eb;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            transition: background-color 0.2s;
        }
        
        .download-btn:hover {
            background-color: #1d4ed8;
        }

        /* Print Styles */
        @media print {
            body {
                background: white !important;
                padding: 0 !important;
            }
            
            .max-w-4xl {
                max-width: none !important;
                margin: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }
            
            /* Hide action buttons when printing */
            .print-hide {
                display: none !important;
            }
            
            /* Adjust colors for print */
            .bg-green-100 {
                background-color: #f0f9ff !important;
            }
            
            .bg-red-100 {
                background-color: #fef2f2 !important;
            }
            
            /* Ensure proper page breaks */
            .section-break {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body class="bg-gray-50 p-6">
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-lg">
        <!-- Header Section -->
        <div class="header text-center">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <img src="{{ asset(config('invoice.logo', 'images/logo.png')) }}" alt="Company Logo" 
                         class="h-12 w-auto">
                </div>
                <div class="text-right">
                    <h2 class="text-lg font-semibold text-gray-800">PT. Makna Kreatif Indonesia</h2>
                    <p class="text-sm text-gray-600">Jl. Contoh No. 123, Jakarta</p>
                    <p class="text-sm text-gray-600">Tel: (021) 123-4567</p>
                </div>
            </div>
            
            <div class="invoice-title">
                <h1>LAPORAN LABA RUGI</h1>
                <h4>Order #{{ $order->number }} - {{ $order->prospect?->name_event ?? 'N/A' }}</h4>
                <p class="text-sm text-gray-600 mt-2">Tanggal Generate: {{ $generatedDate }}</p>
            </div>
            
            <!-- Action Buttons -->
            <div class="mt-6 flex flex-wrap gap-3 print-hide">
                <a href="{{ route('orders.profit_loss.download', $order) }}" 
                   class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-green-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Download PDF
                </a>
                
                </a>
                
            </div>                <button onclick="window.print()" 
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zM5 10a1 1 0 011-1h1a1 1 0 110 2H6a1 1 0 01-1-1zm7 1h1a1 1 0 100-2h-1a1 1 0 100 2z" clip-rule="evenodd" />
                    </svg>
                    Print
                </button>
                <a href="javascript:history.back()" 
                   class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        @php
            // Kalkulasi untuk ringkasan dan progress bar
            $totalPembayaranDiterima = $order->dataPembayaran->sum('nominal');
            $grandTotal = $order->grand_total ?? 0;
            $sisaPembayaran = $grandTotal - $totalPembayaranDiterima;
            $paymentProgress = $grandTotal > 0 ? ($totalPembayaranDiterima / $grandTotal) * 100 : 0;
            $paymentProgress = min($paymentProgress, 100); // Batasi maksimal 100%
        @endphp

        <!-- Invoice Information Section -->
        <div class="grid grid-cols-2 gap-8 mb-8">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-300 pb-2">Detail Event</h3>
                <div class="space-y-2 text-sm">
                    <p><span class="font-medium text-gray-600">Event:</span> {{ $order->prospect?->name_event ?? 'N/A' }}</p>
                    <p><span class="font-medium text-gray-600">CPP:</span> {{ $order->prospect?->name_cpp ?? 'N/A' }}</p>
                    <p><span class="font-medium text-gray-600">CPW:</span> {{ $order->prospect?->name_cpw ?? 'N/A' }}</p>
                    <p><span class="font-medium text-gray-600">Venue:</span> {{ $order->prospect?->venue ?? 'N/A' }}</p>
                    <p><span class="font-medium text-gray-600">Pax:</span> {{ $order->pax ?? 'N/A' }} orang</p>
                    <p><span class="font-medium text-gray-600">Account Manager:</span> {{ $order->employee?->name ?? 'N/A' }}</p>
                </div>
            </div>
            
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-300 pb-2">Informasi Laporan</h3>
                <div class="space-y-2 text-sm">
                    <p><span class="font-medium text-gray-600">Tanggal Laporan:</span> {{ now()->format('d F Y') }}</p>
                    <p><span class="font-medium text-gray-600">Order Date:</span> {{ $order->created_at->format('d F Y') }}</p>
                    <p><span class="font-medium text-gray-600">Tgl Lamaran:</span> 
                        {{ $order->prospect->date_lamaran ? \Carbon\Carbon::parse($order->prospect->date_lamaran)->format('d F Y') : '-' }}
                    </p>
                    <p><span class="font-medium text-gray-600">Tgl Akad:</span> 
                        {{ $order->prospect->date_akad ? \Carbon\Carbon::parse($order->prospect->date_akad)->format('d F Y') : '-' }}
                    </p>
                    <p><span class="font-medium text-gray-600">Tgl Resepsi:</span> 
                        {{ $order->prospect->date_resepsi ? \Carbon\Carbon::parse($order->prospect->date_resepsi)->format('d F Y') : '-' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Financial Summary Section -->
        <div class="section-header section-break">
            RINGKASAN KEUANGAN
        </div>

        <!-- Progress Payment Bar -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <div class="flex justify-between mb-2 text-sm">
                <span class="font-medium text-gray-700">Progress Pembayaran</span>
                <span class="font-semibold text-blue-600">{{ number_format($paymentProgress, 1) }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-blue-500 h-3 rounded-full transition-all duration-300" 
                     style="width: {{ $paymentProgress }}%"></div>
            </div>
        </div>

        <!-- Financial Details Table -->
        <table class="bordered w-full mb-6">
            <thead>
                <tr>
                    <th class="w-2/3">Keterangan</th>
                    <th class="w-1/3 text-right">Nilai (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="font-medium">Total Paket Awal</td>
                    <td class="text-right font-mono">{{ number_format($order->total_price ?? 0, 0, ',', '.') }}</td>
                </tr>
                @if(($order->penambahan ?? 0) > 0)
                <tr>
                    <td class="font-medium text-green-600">Total Penambahan</td>
                    <td class="text-right font-mono text-green-600">+ {{ number_format($order->penambahan, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if(($order->promo ?? 0) > 0)
                <tr>
                    <td class="font-medium text-red-600">Total Promo/Diskon</td>
                    <td class="text-right font-mono text-red-600">- {{ number_format($order->promo, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if(($order->pengurangan ?? 0) > 0)
                <tr>
                    <td class="font-medium text-red-600">Total Pengurangan</td>
                    <td class="text-right font-mono text-red-600">- {{ number_format($order->pengurangan, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr class="highlight-row">
                    <td class="font-bold text-lg">GRAND TOTAL (Nilai Proyek)</td>
                    <td class="text-right font-bold text-lg font-mono">{{ number_format($grandTotal, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="font-medium">Total Pembayaran Diterima</td>
                    <td class="text-right font-mono text-green-600">{{ number_format($totalPembayaranDiterima, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="font-medium">Sisa Pembayaran Klien</td>
                    <td class="text-right font-mono {{ $sisaPembayaran > 0 ? 'text-red-600' : 'text-green-600' }}">
                        {{ number_format($sisaPembayaran, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td class="font-medium">Total Pengeluaran</td>
                    <td class="text-right font-mono text-red-600">{{ number_format($order->tot_pengeluaran ?? 0, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Profit/Loss Result -->
        @php $profitLoss = $order->laba_kotor ?? 0; @endphp
        <div class="p-6 rounded-lg {{ $profitLoss >= 0 ? 'profit-positive' : 'profit-negative' }} mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-bold">LABA / RUGI KOTOR</h3>
                    <p class="text-sm mt-1">Grand Total - Total Pengeluaran</p>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-bold font-mono">
                        Rp {{ number_format($profitLoss, 0, ',', '.') }}
                    </p>
                    <p class="text-sm mt-1">
                        {{ $profitLoss >= 0 ? 'PROFIT' : 'LOSS' }}
                    </p>
                </div>
            </div>
        </div>
                            </div>

        <!-- Detail Pembayaran Section -->
        @if ($order->dataPembayaran->count() > 0)
        <div class="section-header">
            DETAIL PEMBAYARAN DITERIMA
        </div>
        
        <table class="bordered w-full mb-8">
            <thead>
                <tr>
                    <th class="text-left">Tanggal Bayar</th>
                    <th class="text-left">Metode Pembayaran</th>
                    <th class="text-left">Keterangan</th>
                    <th class="text-right">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->dataPembayaran as $pembayaran)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($pembayaran->tgl_bayar)->format('d M Y') }}</td>
                    <td>{{ $pembayaran->paymentMethod?->name ?? 'N/A' }}</td>
                    <td>{{ $pembayaran->keterangan ?? '-' }}</td>
                    <td class="text-right font-mono">{{ number_format($pembayaran->nominal ?? 0, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr class="highlight-row">
                    <td colspan="3" class="font-bold">TOTAL PEMBAYARAN DITERIMA</td>
                    <td class="text-right font-bold font-mono">{{ number_format($totalPembayaranDiterima, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
        @endif

        <!-- Detail Pengeluaran Section -->
        @if ($order->expenses->count() > 0)
        <div class="section-header">
            DETAIL PENGELUARAN VENDOR
        </div>
        
        <table class="bordered w-full mb-8">
            <thead>
                <tr>
                    <th class="text-left">Tanggal</th>
                    <th class="text-left">Vendor</th>
                    <th class="text-left">No. ND</th>
                    <th class="text-left">Keterangan</th>
                    <th class="text-right">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->expenses()->latest('date_expense')->get() as $expense)
                <tr>
                    <td>{{ $expense->date_expense ? \Carbon\Carbon::parse($expense->date_expense)->format('d M Y') : '-' }}</td>
                    <td>{{ $expense->vendor->name ?? 'N/A' }}</td>
                    <td>{{ $expense->no_nd ? 'ND-0' . $expense->no_nd : '-' }}</td>
                    <td>{{ $expense->description ?? '-' }}</td>
                    <td class="text-right font-mono">{{ number_format($expense->amount ?? 0, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr class="highlight-row">
                    <td colspan="4" class="font-bold">TOTAL PENGELUARAN</td>
                    <td class="text-right font-bold font-mono">{{ number_format($order->tot_pengeluaran ?? 0, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
        @endif

        <!-- Summary Conclusion -->
        <div class="mt-8 p-6 bg-gray-50 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Kesimpulan Analisis</h3>
            <div class="grid grid-cols-2 gap-6 text-sm">
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">Status Pembayaran:</h4>
                    <p class="mb-1">Progress: <span class="font-semibold">{{ number_format($paymentProgress, 1) }}%</span></p>
                    <p class="mb-1">Sudah Diterima: <span class="text-green-600 font-semibold">Rp {{ number_format($totalPembayaranDiterima, 0, ',', '.') }}</span></p>
                    <p>Sisa Tagihan: <span class="{{ $sisaPembayaran > 0 ? 'text-red-600' : 'text-green-600' }} font-semibold">Rp {{ number_format($sisaPembayaran, 0, ',', '.') }}</span></p>
                </div>
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">Analisis Profitabilitas:</h4>
                    <p class="mb-1">Margin: <span class="font-semibold">{{ $grandTotal > 0 ? number_format(($profitLoss / $grandTotal) * 100, 1) : 0 }}%</span></p>
                    <p class="mb-1">Status: <span class="{{ $profitLoss >= 0 ? 'text-green-600' : 'text-red-600' }} font-semibold">{{ $profitLoss >= 0 ? 'PROFITABLE' : 'LOSS MAKING' }}</span></p>
                    <p>Efisiensi: <span class="font-semibold">{{ $grandTotal > 0 ? number_format((($grandTotal - ($order->tot_pengeluaran ?? 0)) / $grandTotal) * 100, 1) : 0 }}%</span></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
            </div>

            {{-- IMPROVEMENT: Menonjolkan hasil Laba/Rugi dalam kartu terpisah --}}
            @php $profitLoss = $order->laba_kotor ?? 0; @endphp
            <div class="mt-6 p-6 rounded-lg border"
                style="background-color: {{ $profitLoss >= 0 ? '#f0fdf4' : '#fef2f2' }}; border-color: {{ $profitLoss >= 0 ? '#bbf7d0' : '#fecaca' }}">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-base font-semibold"
                            style="color: {{ $profitLoss >= 0 ? '#166534' : '#991b1b' }}">
                            Laba / Rugi Kotor</p>
                        <p class="text-xs"
                            style="color: {{ $profitLoss >= 0 ? '#16a34a' : '#dc2626' }}">Grand Total -
                            Total Pengeluaran</p>
                    </div>
                    <p class="text-2xl font-bold whitespace-nowrap"
                        style="color: {{ $profitLoss >= 0 ? '#15803d' : '#b91c1c' }}">
                        Rp {{ number_format($profitLoss, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </section>
    </div>
</body>

</html>
