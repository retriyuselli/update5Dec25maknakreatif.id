<div class="space-y-4">
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h4 class="text-sm font-semibold text-blue-800 mb-2">📊 Ringkasan Order</h4>
        <div class="grid grid-cols-3 gap-4 text-sm">
            <div>
                <span class="text-blue-600 font-medium">Total Order:</span>
                <span class="ml-2 font-semibold">{{ $orders->count() }}</span>
            </div>
            <div>
                <span class="text-blue-600 font-medium">Total Value:</span>
                <span class="ml-2 font-semibold">Rp {{ number_format($orders->sum('grand_total'), 0, ',', '.') }}</span>
            </div>
            <div>
                <span class="text-blue-600 font-medium">Rata-rata:</span>
                <span class="ml-2 font-semibold">Rp {{ number_format($orders->avg('grand_total'), 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-4 py-3">No. Order</th>
                    <th scope="col" class="px-4 py-3">Event</th>
                    <th scope="col" class="px-4 py-3">Closing Date</th>
                    <th scope="col" class="px-4 py-3">Status</th>
                    <th scope="col" class="px-4 py-3 text-right">Grand Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                            {{ $order->number }}
                        </td>
                        <td class="px-4 py-3">
                            {{ $order->prospect?->name_event ?? $order->name }}
                        </td>
                        <td class="px-4 py-3">
                            {{ $order->closing_date ? \Carbon\Carbon::parse($order->closing_date)->format('d M Y') : '-' }}
                        </td>
                        <td class="px-4 py-3">
                            <span @class([
                                'inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium',
                                'bg-green-100 text-green-800' => $order->status === 'completed',
                                'bg-blue-100 text-blue-800' => $order->status === 'confirmed',
                                'bg-yellow-100 text-yellow-800' => $order->status === 'pending',
                                'bg-red-100 text-red-800' => $order->status === 'cancelled',
                                'bg-gray-100 text-gray-800' => !in_array($order->status, ['completed', 'confirmed', 'pending', 'cancelled'])
                            ])>
                                {{ $order->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold">
                            Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
