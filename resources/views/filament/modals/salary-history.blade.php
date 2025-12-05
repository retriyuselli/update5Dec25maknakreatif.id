<div class="space-y-4">
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
        <div class="flex items-center space-x-2 mb-2 gap-3">
            <x-heroicon-o-chart-bar class="w-5 h-5 text-blue-500" />
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Riwayat Gaji - {{ $user->name }}
            </h3>
        </div>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Total {{ $payrolls->count() }} record gaji ditemukan
        </p>
    </div>

    <div class="space-y-3 max-h-96 overflow-y-auto">
        @foreach($payrolls as $index => $payroll)
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-white dark:bg-gray-900">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex items-center space-x-2">
                        @if($index === 0)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 gap-3">
                                {{-- <x-heroicon-s-star class="w-3 h-3 mr-1" /> --}}
                                Terbaru
                            </span>
                        @endif
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $payroll->created_at->format('d/m/Y H:i') }}
                        </span>
                    </div>
                    <div class="text-right">
                        @if($payroll->pay_period)
                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                Periode: {{ $payroll->pay_period }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3">
                        <div class="flex items-center space-x-2 mb-1 gap-3">
                            <x-heroicon-o-banknotes class="w-4 h-4 text-blue-500" />
                            <span class="text-sm font-medium text-blue-700 dark:text-blue-300">Gaji Bulanan</span>
                        </div>
                        <div class="text-lg font-bold text-blue-900 dark:text-blue-100">
                            {{ 'Rp ' . (int) ($payroll->monthly_salary ?? 0) }}
                        </div>
                    </div>

                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3">
                        <div class="flex items-center space-x-2 mb-1 gap-3">
                            <x-heroicon-o-gift class="w-4 h-4 text-green-500" />
                            <span class="text-sm font-medium text-green-700 dark:text-green-300">Bonus</span>
                        </div>
                        <div class="text-lg font-bold text-green-900 dark:text-green-100">
                            {{ 'Rp ' . (int) ($payroll->bonus ?? 0) }}
                        </div>
                    </div>

                    <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-3">
                        <div class="flex items-center space-x-2 mb-1 gap-3">
                            <x-heroicon-o-calculator class="w-4 h-4 text-purple-500" />
                            <span class="text-sm font-medium text-purple-700 dark:text-purple-300">Total Tahunan</span>
                        </div>
                        <div class="text-lg font-bold text-purple-900 dark:text-purple-100">
                            {{ 'Rp ' . (int) ($payroll->calculated_annual_salary ?? 0) }}
                        </div>
                    </div>
                </div>

                @if($payroll->review_date || $payroll->next_review_date)
                    <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            @if($payroll->review_date)
                                <div class="flex items-center space-x-2">
                                    {{-- <x-heroicon-o-calendar class="w-4 h-4 text-gray-400" /> --}}
                                    <span class="text-gray-600 dark:text-gray-400">Review:</span>
                                    <span class="font-medium">{{ \Carbon\Carbon::parse($payroll->review_date)->format('d/m/Y') }}</span>
                                </div>
                            @endif
                            @if($payroll->next_review_date)
                                <div class="flex items-center space-x-2 gap-3">
                                    {{-- <x-heroicon-o-calendar-days class="w-4 h-4 text-gray-400" /> --}}
                                    <span class="text-gray-600 dark:text-gray-400">Review Berikutnya:</span>
                                    <span class="font-medium">{{ \Carbon\Carbon::parse($payroll->next_review_date)->format('d/m/Y') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if($payroll->notes)
                    <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-start space-x-2 gap-3">
                            {{-- <x-heroicon-o-document-text class="w-4 h-4 text-gray-400 mt-0.5" /> --}}
                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Catatan:</span>
                                <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $payroll->notes }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4 mt-4">
        <div class="flex items-start space-x-2">
            {{-- <x-heroicon-o-information-circle class="w-5 h-5 text-yellow-500 mt-0.5" /> --}}
            <div>
                <h4 class="text-sm font-medium text-yellow-800 dark:text-yellow-300">Informasi</h4>
                <p class="text-sm text-yellow-700 dark:text-yellow-400 mt-1">
                    Data diurutkan dari yang terbaru. Untuk mengelola gaji aktif, gunakan tombol "Kelola Gaji" di menu aksi.
                </p>
            </div>
        </div>
    </div>
</div>
