<x-filament-panels::page>
    @php
        // Get data directly from page properties
        try {
            $reconciliationService = new \App\Services\ReconciliationService();
            $reconciliationResults = $reconciliationService->reconcile(
                $record->payment_method_id,
                $record->period_start->format('Y-m-d'),
                $record->period_end->format('Y-m-d')
            );
            $statistics = $reconciliationResults['statistics'];
            
            // Calculate match percentage
            $totalItems = $statistics['total_app_transactions'] + $statistics['total_bank_items'];
            $matchPercent = $totalItems > 0 ? round(($statistics['matched_count'] * 2 / $totalItems) * 100, 1) : 0;
        } catch (\Exception $e) {
            // Fallback data in case of error
            $reconciliationResults = [
                'matched' => [],
                'unmatched_app' => [],
                'unmatched_bank' => [],
                'statistics' => [
                    'total_app_transactions' => 0,
                    'total_bank_items' => 0,
                    'matched_count' => 0,
                    'unmatched_app_count' => 0,
                    'unmatched_bank_count' => 0,
                ]
            ];
            $statistics = $reconciliationResults['statistics'];
            $matchPercent = 0;
        }
    @endphp

    <div class="space-y-6">
        {{-- Header Info Card --}}
        <div class="bg-white shadow rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-blue-900">Informasi Rekonsiliasi</h3>
                        <p class="text-sm text-blue-700">
                            Bank: {{ $record->paymentMethod->bank_name }} - {{ $record->paymentMethod->no_rekening }}
                            @if($record->paymentMethod->cabang)
                                | Cabang: {{ $record->paymentMethod->cabang }}
                            @endif
                        </p>
                        <p class="text-sm text-blue-600 mt-1">
                            Periode: {{ $record->period_start->format('d F Y') }} - {{ $record->period_end->format('d F Y') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600">{{ $statistics['matched_count'] }}</div>
                    <div class="text-sm text-gray-500 mt-1">Transaksi Cocok</div>
                    <div class="text-xs text-gray-400 mt-2">
                        {{ $statistics['matched_count'] > 0 ? 
                           round(($statistics['matched_count'] / max($statistics['total_app_transactions'], 1)) * 100, 1) : 0 }}% dari total app
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
                <div class="text-center">
                    <div class="text-3xl font-bold text-orange-600">{{ $statistics['unmatched_app_count'] }}</div>
                    <div class="text-sm text-gray-500 mt-1">Transaksi App Belum Cocok</div>
                    <div class="text-xs text-gray-400 mt-2">
                        Dari {{ $statistics['total_app_transactions'] }} total transaksi
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
                <div class="text-center">
                    <div class="text-3xl font-bold text-red-600">{{ $statistics['unmatched_bank_count'] }}</div>
                    <div class="text-sm text-gray-500 mt-1">Mutasi Bank Belum Cocok</div>
                    <div class="text-xs text-gray-400 mt-2">
                        Dari {{ $statistics['total_bank_items'] }} total mutasi
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
                <div class="text-center">
                    @php
                        $totalItems = $statistics['total_app_transactions'] + $statistics['total_bank_items'];
                        $matchPercent = $totalItems > 0 ? round(($statistics['matched_count'] * 2 / $totalItems) * 100, 1) : 0;
                    @endphp
                    <div class="text-3xl font-bold text-blue-600">{{ $matchPercent }}%</div>
                    <div class="text-sm text-gray-500 mt-1">Tingkat Kecocokan</div>
                    <div class="text-xs text-gray-400 mt-2">
                        Match Rate Overall
                    </div>
                </div>
            </div>
        </div>

        @if($statistics['matched_count'] > 0)
            {{-- Matched Transactions Section --}}
            <div class="bg-white shadow rounded-lg border border-gray-200">
                <div class="px-6 py-4 bg-green-50 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <h4 class="text-lg font-semibold text-green-800">Transaksi yang Cocok ({{ $statistics['matched_count'] }})</h4>
                        </div>
                    </div>
                </div>
                
                <div class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left font-medium text-gray-700">Tanggal</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-700">Keterangan App</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-700">Keterangan Bank</th>
                                    <th class="px-6 py-3 text-right font-medium text-gray-700">Nominal</th>
                                    <th class="px-6 py-3 text-center font-medium text-gray-700">Confidence</th>
                                    <th class="px-6 py-3 text-center font-medium text-gray-700">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($reconciliationResults['matched'] as $match)
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
                                                {{ $isDebit ? '-' : '+' }}Rp {{ number_format($amount, 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $match['confidence'] >= 90 ? 'bg-green-100 text-green-800' : 
                                                   ($match['confidence'] >= 75 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                {{ $match['confidence'] }}%
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <button type="button" 
                                                    onclick="unmarkAsMatched('{{ $appTransaction->source_id }}', '{{ $appTransaction->source_table }}', '{{ $bankItem->id }}')"
                                                    class="text-red-600 hover:text-red-800 text-sm font-medium">
                                                Unmark
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        @if($statistics['unmatched_app_count'] > 0)
            {{-- Unmatched App Transactions Section --}}
            <div class="bg-white shadow rounded-lg border border-gray-200">
                <div class="px-6 py-4 bg-orange-50 border-b border-gray-200">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <h4 class="text-lg font-semibold text-orange-800">Transaksi Aplikasi Belum Cocok ({{ $statistics['unmatched_app_count'] }})</h4>
                    </div>
                </div>
                
                <div class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left font-medium text-gray-700">Tanggal</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-700">Keterangan</th>
                                    <th class="px-6 py-3 text-right font-medium text-gray-700">Nominal</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-700">Tipe</th>
                                    <th class="px-6 py-3 text-center font-medium text-gray-700">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($reconciliationResults['unmatched_app'] as $transaction)
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
                                                {{ $isDebit ? '-' : '+' }}Rp {{ number_format($amount, 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $transaction->source_table }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <button type="button" 
                                                    onclick="findManualMatch('{{ $transaction->source_id }}', '{{ $transaction->source_table }}')"
                                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                Manual Match
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        @if($statistics['unmatched_bank_count'] > 0)
            {{-- Unmatched Bank Items Section --}}
            <div class="bg-white shadow rounded-lg border border-gray-200">
                <div class="px-6 py-4 bg-red-50 border-b border-gray-200">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <h4 class="text-lg font-semibold text-red-800">Mutasi Bank Belum Cocok ({{ $statistics['unmatched_bank_count'] }})</h4>
                    </div>
                </div>
                
                <div class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left font-medium text-gray-700">Tanggal</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-700">Keterangan</th>
                                    <th class="px-6 py-3 text-right font-medium text-gray-700">Nominal</th>
                                    <th class="px-6 py-3 text-center font-medium text-gray-700">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($reconciliationResults['unmatched_bank'] as $item)
                                    @php
                                        $amount = $item->debit_amount ?: $item->credit_amount;
                                        $isDebit = (bool) $item->debit_amount;
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-gray-900">
                                            {{ \Carbon\Carbon::parse($item->transaction_date)->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-gray-900 max-w-md">
                                            <div class="truncate" title="{{ $item->description }}">
                                                {{ Str::limit($item->description, 80) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="font-medium {{ $isDebit ? 'text-red-600' : 'text-green-600' }}">
                                                {{ $isDebit ? '-' : '+' }}Rp {{ number_format($amount, 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <button type="button" 
                                                    onclick="findManualMatchBank('{{ $item->id }}')"
                                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                Manual Match
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        @if($statistics['total_app_transactions'] === 0 && $statistics['total_bank_items'] === 0)
            {{-- No Data State --}}
            <div class="bg-white shadow rounded-lg border border-gray-200 p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Data Untuk Direkonsiliasi</h3>
                <p class="text-gray-500">Tidak ada transaksi aplikasi atau data mutasi bank pada periode ini.</p>
            </div>
        @endif
    </div>

    {{-- JavaScript untuk fungsi rekonsiliasi --}}
    <script>
    function markAsMatched(sourceId, sourceTable, bankItemId, confidence) {
        fetch('/admin/reconciliation/mark-matched', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                source_id: sourceId,
                source_table: sourceTable,
                bank_item_id: bankItemId,
                confidence: confidence
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    function unmarkAsMatched(sourceId, sourceTable, bankItemId) {
        if (!confirm('Apakah Anda yakin ingin membatalkan match ini?')) {
            return;
        }
        
        // Implementation for unmarking matches (you may need to create this endpoint)
        fetch('/admin/reconciliation/unmark-matched', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                source_id: sourceId,
                source_table: sourceTable,
                bank_item_id: bankItemId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    function autoMatchHighConfidence() {
        if (!confirm('Apakah Anda yakin ingin menandai semua kecocokan dengan confidence 85%+ sebagai cocok?')) {
            return;
        }
        
        fetch('/admin/reconciliation/auto-match', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                payment_method_id: {{ $record->payment_method_id }},
                start_date: '{{ $record->period_start->format('Y-m-d') }}',
                end_date: '{{ $record->period_end->format('Y-m-d') }}'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`${data.matched_count} transaksi berhasil di-match otomatis!`);
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    function findManualMatch(sourceId, sourceTable) {
        alert('Fitur manual match akan diimplementasikan');
        // Implementation for manual matching
    }

    function findManualMatchBank(bankItemId) {
        alert('Fitur manual match bank akan diimplementasikan');
        // Implementation for manual matching bank items
    }
    </script>
</x-filament-panels::page>