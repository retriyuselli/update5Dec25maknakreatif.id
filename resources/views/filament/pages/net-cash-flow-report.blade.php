<x-filament-panels::page>
    <style>
        @media print {
            .no-print {
                display: none;
            }

            body {
                font-size: 12px;
            }

            .container {
                max-width: 100%;
                margin: 0;
                padding: 0;
            }

            table {
                width: 100%;
            }

            /* Hide Filament sidebar and header when printing */
            aside,
            header,
            .fi-topbar,
            .fi-sidebar {
                display: none !important;
            }

            .fi-main {
                margin: 0 !important;
                padding: 0 !important;
            }
        }
    </style>

    <div class="bg-white rounded-lg shadow-lg p-8 max-w-7xl mx-auto w-full">
        <div class="flex justify-between items-center mb-8 border-b pb-4">
            <div class="flex items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">{{ $pageTitle }}</h1>
                    <p class="text-gray-500 text-sm">Digenerate pada:
                        {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY, HH:mm') }}</p>
                </div>
            </div>
            <div class="flex gap-2 no-print">
                <a href="{{ route('reports.net-cash-flow.pdf.stream', ['status' => $status]) }}" target="_blank"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded inline-flex items-center transition cursor-pointer">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                        </path>
                    </svg>
                    Preview PDF
                </a>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-green-50 rounded-lg p-6 border border-green-200">
                <h3 class="text-green-800 text-sm font-semibold uppercase tracking-wider mb-2">Total Pembayaran Masuk
                </h3>
                <p class="text-3xl font-bold text-green-700"> {{ number_format($totalPaymentsAll, 0, ',', '.') }}</p>
            </div>
            <div class="bg-red-50 rounded-lg p-6 border border-red-200">
                <h3 class="text-red-800 text-sm font-semibold uppercase tracking-wider mb-2">Total Pengeluaran Project
                </h3>
                <p class="text-3xl font-bold text-red-700"> {{ number_format($totalExpensesAll, 0, ',', '.') }}</p>
            </div>
            <div class="bg-blue-50 rounded-lg p-6 border border-blue-200">
                <h3 class="text-blue-800 text-sm font-semibold uppercase tracking-wider mb-2">Net Cash Flow (Sisa)</h3>
                <p class="text-3xl font-bold text-blue-700"> {{ number_format($totalNetCashFlowAll, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-gray-300 text-sm">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 p-3 text-left font-semibold text-gray-700 w-10">No</th>
                        <th class="border border-gray-300 p-3 text-left font-semibold text-gray-700">Nama Project /
                            Order</th>
                        <th class="border border-gray-300 p-3 text-left font-semibold text-gray-700">Account Manager
                        </th>
                        <th class="border border-gray-300 p-3 text-left font-semibold text-gray-700">Event Manager</th>
                        <th class="border border-gray-300 p-3 text-right font-semibold text-gray-700">Pembayaran Masuk
                        </th>
                        <th class="border border-gray-300 p-3 text-right font-semibold text-gray-700">Pengeluaran</th>
                        <th class="border border-gray-300 p-3 text-right font-semibold text-gray-700">Net Cash Flow</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $index => $order)
                        <tr class="hover:bg-gray-50 {{ $loop->even ? 'bg-gray-50' : '' }}">
                            <td class="border border-gray-300 p-3 text-center">{{ $loop->iteration }}</td>
                            <td class="border border-gray-300 p-3">
                                <div class="font-medium text-gray-900">{{ $order->name }}</div>
                                <div class="text-gray-500 text-xs">
                                    {{ ucwords(strtolower($order->items->first()?->product?->parent?->name ?? ($order->items->first()?->product?->name ?? '-'))) }}
                                </div>
                                <div class="text-gray-900 text-xs mt-1">
                                    Total Paket : {{ number_format($order->grand_total, 0, ',', '.') }}
                                </div>
                            </td>
                            <td class="border border-gray-300 p-3">
                                {{ $order->user->name ?? '-' }}
                                <div class="text-gray-400 text-xs mt-1">
                                    Closing {{ \Carbon\Carbon::parse($order->created_at)->format('d M Y') }}</div>
                            </td>
                            <td class="border border-gray-300 p-3">{{ $order->employee->name ?? '-' }}</td>
                            <td class="border border-gray-300 p-3 text-right font-medium text-gray-900">
                                {{ number_format($order->total_payments_received, 0, ',', '.') }}
                            </td>
                            <td class="border border-gray-300 p-3 text-right font-medium text-gray-900">
                                {{ number_format($order->total_expenses_incurred, 0, ',', '.') }}
                            </td>
                            <td
                                class="border border-gray-300 p-3 text-right font-bold {{ $order->net_cash_flow >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                                {{ number_format($order->net_cash_flow, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="border border-gray-300 p-8 text-center text-gray-500">
                                Tidak ada data order dengan status ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-gray-200 font-bold">
                        <td colspan="4" class="border border-gray-300 p-3 text-right">TOTAL</td>
                        <td class="border border-gray-300 p-3 text-right text-green-700">
                            {{ number_format($totalPaymentsAll, 0, ',', '.') }}</td>
                        <td class="border border-gray-300 p-3 text-right text-red-700">
                            {{ number_format($totalExpensesAll, 0, ',', '.') }}</td>
                        <td class="border border-gray-300 p-3 text-right text-blue-700">
                            {{ number_format($totalNetCashFlowAll, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</x-filament-panels::page>
