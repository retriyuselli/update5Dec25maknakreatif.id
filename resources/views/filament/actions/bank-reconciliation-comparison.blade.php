@php
    use App\Models\UnifiedTransaction;
    use App\Services\ReconciliationService;
    
    // Safety check - $bankStatement should be passed from the action
    if (!isset($bankStatement) || !$bankStatement || !$bankStatement->paymentMethod) {
        $hasError = true;
    } else {
        $hasError = false;
        $paymentMethodId = $bankStatement->payment_method_id;
        $startDate = $bankStatement->period_start->format('Y-m-d');
        $endDate = $bankStatement->period_end->format('Y-m-d');
        
        // Get reconciliation results
        $reconciliationService = new ReconciliationService();
        $results = $reconciliationService->reconcile($paymentMethodId, $startDate, $endDate);
        $stats = $results['statistics'];
        
        // Calculate match percentage
        $totalItems = $stats['total_app_transactions'] + $stats['total_bank_items'];
        $matchPercent = $totalItems > 0 ? round(($stats['matched_count'] * 2 / $totalItems) * 100, 1) : 0;
    }
@endphp

@if ($hasError)
    <div class="text-center py-12">
        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L2.236 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Data Tidak Tersedia</h3>
        <p class="text-gray-500">Bank Statement atau Payment Method tidak ditemukan.</p>
    </div>
@else

<div class="space-y-6">
    {{-- Header Info --}}
    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
        <div class="flex items-center space-x-3">
            <div class="p-2 bg-blue-100 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-blue-900">Rekonsiliasi Perbandingan</h3>
                <p class="text-sm text-blue-700">
                    Periode: {{ $bankStatement->period_start->format('d M Y') }} - {{ $bankStatement->period_end->format('d M Y') }}
                    @if($bankStatement->paymentMethod)
                        | {{ $bankStatement->paymentMethod->bank_name }} - {{ $bankStatement->paymentMethod->no_rekening }}
                    @endif
                </p>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-lg border border-gray-200 text-center">
            <div class="text-3xl font-bold text-green-600">{{ $stats['matched_count'] }}</div>
            <div class="text-sm text-gray-500">Cocok</div>
        </div>
        <div class="bg-white p-4 rounded-lg border border-gray-200 text-center">
            <div class="text-3xl font-bold text-orange-600">{{ $stats['unmatched_app_count'] }}</div>
            <div class="text-sm text-gray-500">Transaksi App</div>
        </div>
        <div class="bg-white p-4 rounded-lg border border-gray-200 text-center">
            <div class="text-3xl font-bold text-red-600">{{ $stats['unmatched_bank_count'] }}</div>
            <div class="text-sm text-gray-500">Mutasi Bank</div>
        </div>
        <div class="bg-white p-4 rounded-lg border border-gray-200 text-center">
            <div class="text-3xl font-bold text-blue-600">{{ $matchPercent }}%</div>
            <div class="text-sm text-gray-500">Tingkat Kecocokan</div>
        </div>
    </div>

    @if($stats['matched_count'] > 0)
        {{-- Matched Transactions Section --}}
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="px-4 py-3 bg-green-50 border-b border-gray-200">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <h4 class="text-lg font-semibold text-green-800">Transaksi yang Cocok ({{ $stats['matched_count'] }})</h4>
                </div>
            </div>
            
            <div class="overflow-x-auto max-h-96">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-700">Tanggal</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-700">Keterangan App</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-700">Keterangan Bank</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-700">Nominal</th>
                            <th class="px-4 py-3 text-center font-medium text-gray-700">Confidence</th>
                            <th class="px-4 py-3 text-center font-medium text-gray-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach(collect($results['matched'])->take(10) as $match)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    {{ $match['app_transaction']->transaction_date->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="max-w-48 truncate" title="{{ $match['app_transaction']->description }}">
                                        {{ $match['app_transaction']->description }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ ucfirst(str_replace('_', ' ', $match['app_transaction']->source_table)) }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="max-w-48 truncate" title="{{ $match['bank_item']->description }}">
                                        {{ $match['bank_item']->description }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if($match['app_transaction']->is_income)
                                        <span class="text-green-600 font-medium">
                                            +{{ number_format($match['app_transaction']->credit_amount, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-red-600 font-medium">
                                            -{{ number_format($match['app_transaction']->debit_amount, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $confidence = $match['confidence'];
                                        $color = $confidence >= 90 ? 'green' : ($confidence >= 70 ? 'yellow' : 'red');
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-{{ $color }}-100 text-{{ $color }}-800">
                                        {{ round($confidence) }}%
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button" 
                                            onclick="markAsMatched({{ $match['app_transaction']->source_id }}, '{{ $match['app_transaction']->source_table }}', {{ $match['bank_item']->id }}, {{ $match['confidence'] }})"
                                            class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                                        Konfirmasi
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                
                @if(count($results['matched']) > 10)
                    <div class="px-4 py-3 bg-gray-50 text-center text-sm text-gray-600">
                        Menampilkan 10 dari {{ count($results['matched']) }} transaksi yang cocok
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if($stats['unmatched_app_count'] > 0)
        {{-- Unmatched App Transactions Section --}}
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="px-4 py-3 bg-orange-50 border-b border-gray-200">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <h4 class="text-lg font-semibold text-orange-800">Transaksi App Tidak Cocok ({{ $stats['unmatched_app_count'] }})</h4>
                </div>
            </div>
            
            <div class="overflow-x-auto max-h-64">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-700">Tanggal</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-700">Keterangan</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-700">Nominal</th>
                            <th class="px-4 py-3 text-center font-medium text-gray-700">Sumber</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach(collect($results['unmatched_app'])->take(5) as $transaction)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">
                                    <div class="max-w-64 truncate" title="{{ $transaction->description }}">
                                        {{ $transaction->description }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if($transaction->is_income)
                                        <span class="text-green-600 font-medium">
                                            +{{ number_format($transaction->credit_amount, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-red-600 font-medium">
                                            -{{ number_format($transaction->debit_amount, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                        {{ ucfirst(str_replace('_', ' ', $transaction->source_table)) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                
                @if(count($results['unmatched_app']) > 5)
                    <div class="px-4 py-3 bg-gray-50 text-center text-sm text-gray-600">
                        Menampilkan 5 dari {{ count($results['unmatched_app']) }} transaksi tidak cocok
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if($stats['unmatched_bank_count'] > 0)
        {{-- Unmatched Bank Items Section --}}
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="px-4 py-3 bg-red-50 border-b border-gray-200">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <h4 class="text-lg font-semibold text-red-800">Mutasi Bank Tidak Cocok ({{ $stats['unmatched_bank_count'] }})</h4>
                </div>
            </div>
            
            <div class="overflow-x-auto max-h-64">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-700">Tanggal</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-700">Keterangan</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-700">Debit</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-700">Kredit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach(collect($results['unmatched_bank'])->take(5) as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $item->date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">
                                    <div class="max-w-64 truncate" title="{{ $item->description }}">
                                        {{ $item->description }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if($item->debit > 0)
                                        <span class="text-red-600 font-medium">
                                            {{ number_format($item->debit, 0, ',', '.') }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if($item->credit > 0)
                                        <span class="text-green-600 font-medium">
                                            {{ number_format($item->credit, 0, ',', '.') }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                
                @if(count($results['unmatched_bank']) > 5)
                    <div class="px-4 py-3 bg-gray-50 text-center text-sm text-gray-600">
                        Menampilkan 5 dari {{ count($results['unmatched_bank']) }} mutasi tidak cocok
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if($stats['matched_count'] == 0 && $stats['unmatched_app_count'] == 0 && $stats['unmatched_bank_count'] == 0)
        {{-- Empty State --}}
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Data Untuk Direkonsiliasi</h3>
            <p class="text-gray-500">Tidak ada transaksi aplikasi atau data mutasi bank pada periode ini.</p>
        </div>
    @endif

    {{-- Action Buttons --}}
    @if($stats['matched_count'] > 0)
        <div class="flex justify-between items-center pt-4 border-t border-gray-200">
            <div class="text-sm text-gray-600">
                Ditemukan {{ $stats['matched_count'] }} kecocokan dengan tingkat confidence tinggi
            </div>
            <div class="space-x-3">
                <button type="button" 
                        onclick="autoMatchHighConfidence()"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                    Auto Match (85%+)
                </button>
                <button type="button" 
                        onclick="exportReconciliationResults()"
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-sm font-medium">
                    Export Excel
                </button>
            </div>
        </div>
    @endif
</div>

<script>
function markAsMatched(sourceId, sourceTable, bankItemId, confidence) {
    // Implementation for marking individual matches
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
            // Show success notification
            window.dispatchEvent(new CustomEvent('notify', {
                detail: { message: 'Transaksi berhasil ditandai sebagai cocok!', type: 'success' }
            }));
            
            // Refresh the modal or update UI
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        window.dispatchEvent(new CustomEvent('notify', {
            detail: { message: 'Gagal menandai transaksi', type: 'error' }
        }));
    });
}

function autoMatchHighConfidence() {
    if (!confirm('Apakah Anda yakin ingin menandai semua kecocokan dengan confidence 85%+ sebagai cocok?')) {
        return;
    }
    
    // Implementation for auto matching high confidence items
    fetch('/admin/reconciliation/auto-match', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            payment_method_id: {{ $paymentMethodId }},
            start_date: '{{ $startDate }}',
            end_date: '{{ $endDate }}'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.dispatchEvent(new CustomEvent('notify', {
                detail: { message: `${data.matched_count} transaksi berhasil di-match otomatis!`, type: 'success' }
            }));
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function exportReconciliationResults() {
    try {
        // Create export URL with current reconciliation parameters
        const exportUrl = '/admin/reconciliation/export?' + new URLSearchParams({
            payment_method_id: {{ $paymentMethodId }},
            start_date: '{{ $startDate }}',
            end_date: '{{ $endDate }}'
        });
        
        // Show loading notification
        window.dispatchEvent(new CustomEvent('notify', {
            detail: { message: 'Memproses export data...', type: 'info' }
        }));
        
        // Open export in new window to trigger download
        window.open(exportUrl, '_blank');
        
        // Success notification after short delay
        setTimeout(() => {
            window.dispatchEvent(new CustomEvent('notify', {
                detail: { message: 'File Excel berhasil didownload!', type: 'success' }
            }));
        }, 1000);
        
    } catch (error) {
        console.error('Export error:', error);
        window.dispatchEvent(new CustomEvent('notify', {
            detail: { message: 'Gagal export data: ' + error.message, type: 'error' }
        }));
    }
}
</script>
@endif