<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $record->name }}
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ $record->bank_name }} • {{ $record->no_rekening }}
                    @if ($record->is_cash)
                        <span
                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 ml-2">
                            💰 Kas Tunai
                        </span>
                    @else
                        <span
                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 ml-2">
                            🏦 Rekening Bank
                        </span>
                    @endif
                </p>
            </div>

            <!-- Current Balance -->
            <div class="text-right">
                <p class="text-sm text-gray-500 dark:text-gray-400">Saldo Saat Ini</p>
                <p
                    class="text-3xl font-bold {{ $record->saldo >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                    Rp {{ number_format($record->saldo, 0, ',', '.') }}
                </p>
                @if ($record->perubahan_saldo != 0)
                    <p
                        class="text-sm {{ $record->perubahan_saldo >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ $record->perubahan_saldo >= 0 ? '+' : '' }}Rp
                        {{ number_format($record->perubahan_saldo, 0, ',', '.') }} dari saldo awal
                    </p>
                @endif
            </div>
        </div>

        <!-- Form with Tabs -->
        {{ $this->form }}
    </div>
</x-filament-panels::page>
