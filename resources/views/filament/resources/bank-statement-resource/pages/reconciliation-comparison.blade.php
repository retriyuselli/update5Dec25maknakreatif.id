<x-filament-panels::page>
    @if (!$dataLoaded)
        <div class="bg-red-50 p-4 rounded-lg border border-red-200">
            <div class="flex items-center space-x-3">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-red-800 font-medium">Data tidak dapat dimuat:
                    {{ $errorReason ?? 'Terjadi kesalahan yang tidak diketahui.' }}</span>
            </div>
        </div>
    @else
        <div class="space-y-6">
            {{-- Header Info Card --}}
            <div class="bg-white shadow rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-blue-900">Informasi Rekonsiliasi</h3>
                            <p class="text-sm text-blue-700">
                                Bank: {{ $record->paymentMethod->bank_name }} -
                                {{ $record->paymentMethod->no_rekening }}
                                @if ($record->paymentMethod->cabang)
                                    | Cabang: {{ $record->paymentMethod->cabang }}
                                @endif
                            </p>
                            <p class="text-sm text-blue-600 mt-1">
                                Periode: {{ $record->period_start->format('d F Y') }} -
                                {{ $record->period_end->format('d F Y') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Statistics Widget --}}
            @livewire(\App\Filament\Resources\BankStatements\Widgets\ReconciliationStats::class, ['record' => $record])

            {{-- Matched Transactions Widget --}}
            @livewire(\App\Filament\Resources\BankStatements\Widgets\MatchedTable::class, ['record' => $record])

            {{-- Unmatched Transactions Widgets --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @livewire(\App\Filament\Resources\BankStatements\Widgets\UnmatchedAppTable::class, ['record' => $record])
                @livewire(\App\Filament\Resources\BankStatements\Widgets\UnmatchedBankTable::class, ['record' => $record])
            </div>
        </div>
    @endif
</x-filament-panels::page>
