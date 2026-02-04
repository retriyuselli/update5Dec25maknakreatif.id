<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"></path>
                </svg>
                <span class="text-lg font-semibold text-green-800">Transaksi yang Cocok ({{ count($matches) }})</span>
            </div>
        </x-slot>

        <div class="overflow-x-auto">
            <table class="w-full text-sm divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-700">Tanggal</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-700">Keterangan App</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-700">Keterangan Bank</th>
                        <th class="px-6 py-3 text-right font-medium text-gray-700">Nominal</th>
                        <th class="px-6 py-3 text-center font-medium text-gray-700">Confidence</th>
                        <th class="px-6 py-3 text-center font-medium text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach ($matches as $match)
                        @php
                            $appTransaction = $match['app_transaction'];
                            $bankItem = $match['bank_item'];
                            $amount = $appTransaction->debit_amount ?: $appTransaction->credit_amount;
                            $isDebit = (bool) $appTransaction->debit_amount;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-gray-900">
                                {{ \Carbon\Carbon::parse($appTransaction->transaction_date)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-gray-900 max-w-xs">
                                <div class="truncate" title="{{ $appTransaction->description }}">
                                    {{ Str::limit($appTransaction->description, 50) }}
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $appTransaction->source_table }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-900 max-w-xs">
                                <div class="truncate" title="{{ $bankItem->description }}">
                                    {{ Str::limit($bankItem->description, 50) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-medium {{ $isDebit ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $isDebit ? '-' : '+' }}
                                    {{ number_format($amount, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $match['confidence'] >= 90
                                        ? 'bg-green-100 text-green-800'
                                        : ($match['confidence'] >= 75
                                            ? 'bg-yellow-100 text-yellow-800'
                                            : 'bg-red-100 text-red-800') }}">
                                    {{ $match['confidence'] }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <x-filament::button
                                    size="sm"
                                    color="danger"
                                    wire:click="unmarkAsMatched('{{ $appTransaction->source_id }}', '{{ $appTransaction->source_table }}', '{{ $bankItem->id }}')"
                                    wire:confirm="Apakah Anda yakin ingin membatalkan match ini?"
                                >
                                    Unmark
                                </x-filament::button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
