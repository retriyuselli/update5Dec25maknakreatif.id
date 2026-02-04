<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd"></path>
                </svg>
                <span class="text-lg font-semibold text-orange-800">Transaksi Aplikasi Belum Cocok ({{ count($items) }})</span>
            </div>
        </x-slot>

        <div class="overflow-x-auto">
            <table class="w-full text-sm divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-700">Tanggal</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-700">Keterangan</th>
                        <th class="px-6 py-3 text-right font-medium text-gray-700">Nominal</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-700">Tipe</th>
                        <th class="px-6 py-3 text-center font-medium text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach ($items as $transaction)
                        @php
                            $amount = $transaction->debit_amount ?: $transaction->credit_amount;
                            $isDebit = (bool) $transaction->debit_amount;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-gray-900">
                                {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-gray-900 max-w-md">
                                <div class="truncate" title="{{ $transaction->description }}">
                                    {{ Str::limit($transaction->description, 80) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-medium {{ $isDebit ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $isDebit ? '-' : '+' }}
                                    {{ number_format($amount, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $transaction->source_table }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <x-filament::button
                                    size="sm"
                                    color="gray"
                                    wire:click="findManualMatch('{{ $transaction->source_id }}', '{{ $transaction->source_table }}')"
                                >
                                    Manual Match
                                </x-filament::button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
