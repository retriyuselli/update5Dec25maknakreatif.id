<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd"></path>
                </svg>
                <span class="text-lg font-semibold text-red-800">Mutasi Bank Belum Cocok ({{ count($items) }})</span>
            </div>
        </x-slot>

        <div class="overflow-x-auto">
            <table class="w-full text-sm divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-700">Tanggal</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-700">Keterangan</th>
                        <th class="px-6 py-3 text-right font-medium text-gray-700">Nominal</th>
                        <th class="px-6 py-3 text-center font-medium text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse ($items as $item)
                        @php
                            // Check if 'debit' exists, otherwise fallback or assume structure
                            // BankReconciliationItem usually has 'debit' and 'credit' columns
                            $debit = $item->debit ?? 0;
                            $credit = $item->credit ?? 0;
                            $amount = $debit > 0 ? $debit : $credit;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-gray-900">
                                {{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-gray-900 max-w-md">
                                <div class="truncate" title="{{ $item->description }}">
                                    {{ \Illuminate\Support\Str::limit($item->description, 80) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if ($debit > 0)
                                    <span class="font-medium text-red-600">- Rp
                                        {{ number_format($debit, 0, ',', '.') }}</span>
                                @else
                                    <span class="font-medium text-green-600">+ Rp
                                        {{ number_format($credit, 0, ',', '.') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <x-filament::button size="sm" color="gray"
                                    onclick="alert('Fitur manual match untuk mutasi bank ini sedang dalam pengembangan.')">
                                    Manual Match
                                </x-filament::button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                Tidak ada mutasi bank yang belum cocok.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
