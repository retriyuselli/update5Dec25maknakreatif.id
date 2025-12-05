<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Rekonsiliasi Perbandingan - {{ $record->paymentMethod->bank_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .fi-page { background: #f9fafb; min-height: 100vh; }
        .fi-header { background: white; border-bottom: 1px solid #e5e7eb; padding: 1rem 0; }
        .fi-main { padding: 1rem; margin: 0 auto; max-width: 80rem; }
        @media (min-width: 640px) { .fi-main { padding: 1.25rem; } }
        @media (min-width: 1024px) { .fi-main { padding: 2rem; } }
    </style>
    </head>
<body class="fi-page">
    <div class="fi-header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Rekonsiliasi Perbandingan - {{ $record->paymentMethod->bank_name }} {{ $record->paymentMethod->no_rekening }}</h1>
                    <p class="text-xs sm:text-sm text-gray-600 mt-1">Periode: {{ $record->period_start->format('d F Y') }} - {{ $record->period_end->format('d F Y') }}</p>
                </div>
                <div class="flex flex-wrap gap-2 sm:gap-3">
                    <a href="{{ url('/admin/bank-statements/' . $record->id) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-medium">← Kembali</a>
                    <a href="{{ url('/admin/reconciliation/export?' . http_build_query(['payment_method_id' => $record->payment_method_id,'start_date' => $record->period_start->format('Y-m-d'),'end_date' => $record->period_end->format('Y-m-d'),])) }}" target="_blank" class="bg-green-600 hover:bg-green-700 text-white px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-medium">📊 Export Excel</a>
                    <button onclick="autoMatchHighConfidence()" class="bg-blue-600 hover:bg-blue-700 text-white px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-medium">⚡ Auto Match (85%+)</button>
                </div>
            </div>
        </div>
    </div>

    <div class="fi-main">
        <div class="space-y-6">
            <div class="bg-white shadow rounded-lg border border-gray-200 p-4">
                <form method="GET" class="flex flex-wrap items-center gap-2">
                    <input type="date" name="filter_start" value="{{ request()->query('filter_start', $record->period_start->format('Y-m-d')) }}" class="border border-gray-300 rounded px-2 py-1 text-sm w-full sm:w-auto" />
                    <input type="date" name="filter_end" value="{{ request()->query('filter_end', $record->period_end->format('Y-m-d')) }}" class="border border-gray-300 rounded px-2 py-1 text-sm w-full sm:w-auto" />
                    @php $ft = request()->query('filter_type', 'all'); @endphp
                    <select name="filter_type" class="border border-gray-300 rounded px-2 py-1 text-sm w-full sm:w-auto">
                        <option value="all" {{ $ft === 'all' ? 'selected' : '' }}>Semua</option>
                        <option value="debit" {{ $ft === 'debit' ? 'selected' : '' }}>Debit</option>
                        <option value="credit" {{ $ft === 'credit' ? 'selected' : '' }}>Kredit</option>
                    </select>
                    @php $gp = (int) request()->query('per_page', 10); @endphp
                    <select name="per_page" class="border border-gray-300 rounded px-2 py-1 text-sm w-full sm:w-auto">
                        @foreach([10,50,100] as $opt)
                            <option value="{{ $opt }}" {{ $gp === $opt ? 'selected' : '' }}>{{ $opt }}/hal</option>
                        @endforeach
                    </select>
                    <button type="submit" class="bg-gray-800 hover:bg-black text-white px-3 py-1 rounded text-sm w-full sm:w-auto">Terapkan</button>
                    <a href="{{ request()->url() }}" class="text-gray-600 hover:text-gray-800 text-sm w-full sm:w-auto">Reset</a>
                </form>
            </div>

            @php
                $matchRateApp = $statistics['total_app_transactions'] > 0 ? round(($statistics['matched_count'] / $statistics['total_app_transactions']) * 100, 1) : 0;
                $matchRateBank = $statistics['total_bank_items'] > 0 ? round(($statistics['matched_count'] / $statistics['total_bank_items']) * 100, 1) : 0;
                $globalPerPage = (int) request()->query('per_page', 10);
                $filterStart = request()->query('filter_start');
                $filterEnd = request()->query('filter_end');
                $filterType = request()->query('filter_type', 'all');
                $fsC = $filterStart ? \Carbon\Carbon::parse($filterStart) : null;
                $feC = $filterEnd ? \Carbon\Carbon::parse($filterEnd) : null;
                $filterType = in_array($filterType, ['debit','credit']) ? $filterType : 'all';
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <div class="bg-white p-4 sm:p-6 rounded-lg shadow border border-gray-200">
                    <div class="text-center">
                        <div class="text-2xl sm:text-3xl font-bold text-green-600">{{ $statistics['matched_count'] }}</div>
                        <div class="text-xs sm:text-sm text-gray-500 mt-1">Transaksi Cocok</div>
                        <div class="text-[10px] sm:text-xs text-gray-400 mt-2">{{ $statistics['matched_count'] > 0 ? round(($statistics['matched_count'] / max($statistics['total_app_transactions'], 1)) * 100, 1) : 0 }}% dari total app</div>
                    </div>
                </div>
                <div class="bg-white p-4 sm:p-6 rounded-lg shadow border border-gray-200">
                    <div class="text-center">
                        <div class="text-2xl sm:text-3xl font-bold text-orange-600">{{ $statistics['unmatched_app_count'] }}</div>
                        <div class="text-xs sm:text-sm text-gray-500 mt-1">Transaksi App Belum Cocok</div>
                        <div class="text-[10px] sm:text-xs text-gray-400 mt-2">Dari {{ $statistics['total_app_transactions'] }} total transaksi</div>
                    </div>
                </div>
                <div class="bg-white p-4 sm:p-6 rounded-lg shadow border border-gray-200">
                    <div class="text-center">
                        <div class="text-2xl sm:text-3xl font-bold text-red-600">{{ $statistics['unmatched_bank_count'] }}</div>
                        <div class="text-xs sm:text-sm text-gray-500 mt-1">Mutasi Bank Belum Cocok</div>
                        <div class="text-[10px] sm:text-xs text-gray-400 mt-2">Dari {{ $statistics['total_bank_items'] }} total mutasi</div>
                    </div>
                </div>
                <div class="bg-white p-4 sm:p-6 rounded-lg shadow border border-gray-200">
                    <div class="text-center">
                        <div class="text-xl sm:text-3xl font-bold text-blue-600">App {{ $matchRateApp }}% • Bank {{ $matchRateBank }}%</div>
                        <div class="text-xs sm:text-sm text-gray-500 mt-1">Tingkat Kecocokan</div>
                        <div class="text-[10px] sm:text-xs text-gray-400 mt-2">Per App • Per Bank</div>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg border border-gray-200">
                @php
                    $itemsAll = $record->reconciliationItems->sortBy('date');
                    if ($fsC || $feC || $filterType !== 'all') {
                        $itemsAll = $itemsAll->filter(function($item) use ($fsC,$feC,$filterType) {
                            $date = \Carbon\Carbon::parse($item->date);
                            $isDebit = (($item->debit ?? 0) > 0);
                            if ($fsC && $date->lt($fsC)) return false;
                            if ($feC && $date->gt($feC)) return false;
                            if ($filterType === 'debit' && !$isDebit) return false;
                            if ($filterType === 'credit' && $isDebit) return false;
                            return true;
                        });
                    }
                    $bsPerPage = (int) request()->query('bs_per_page', 10);
                    $bsPage = max(1, (int) request()->query('bs_page', 1));
                    $bsTotal = $itemsAll->count();
                    $startIndex = ($bsPage - 1) * $bsPerPage;
                    $slice = $itemsAll->slice($startIndex, $bsPerPage);
                    $priorSum = $itemsAll->slice(0, $startIndex)->reduce(function($carry,$it){
                        return $carry + ((($it->credit ?? 0) - ($it->debit ?? 0)));
                    }, 0);
                    $runningBalance = (float) ($record->opening_balance ?? 0) + (float) $priorSum;
                    $bsTotalPages = max(1, (int) ceil($bsTotal / $bsPerPage));
                @endphp
                <div class="px-4 sm:px-6 py-3 sm:py-4 bg-blue-50 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"></path></svg>
                            <h4 class="text-base sm:text-lg font-semibold text-blue-800">Data Bank Statement ({{ $bsTotal }})</h4>
                        </div>
                        <form method="GET" class="flex flex-wrap items-center gap-2">
                            <input type="hidden" name="filter_start" value="{{ request()->query('filter_start') }}" />
                            <input type="hidden" name="filter_end" value="{{ request()->query('filter_end') }}" />
                            <input type="hidden" name="filter_type" value="{{ request()->query('filter_type') }}" />
                            <input type="hidden" name="per_page" value="{{ request()->query('per_page', 10) }}" />
                            @php $bsPerPageSel = (int) request()->query('bs_per_page', 10); @endphp
                            <select name="bs_per_page" class="border border-gray-300 rounded px-2 py-1 text-sm w-full sm:w-auto">
                                <option value="10" {{ $bsPerPageSel === 10 ? 'selected' : '' }}>10/hal</option>
                                <option value="50" {{ $bsPerPageSel === 50 ? 'selected' : '' }}>50/hal</option>
                                <option value="100" {{ $bsPerPageSel === 100 ? 'selected' : '' }}>100/hal</option>
                            </select>
                            <button type="submit" class="bg-gray-800 hover:bg-black text-white px-3 py-1 rounded text-sm w-full sm:w-auto">Terapkan</button>
                        </form>
                    </div>
                </div>
                <div class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs sm:text-sm">
                            <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-3 py-2 sm:px-6 sm:py-3 text-left font-medium text-gray-700">Tanggal</th>
                                <th class="px-3 py-2 sm:px-6 sm:py-3 text-left font-medium text-gray-700">Keterangan</th>
                                <th class="px-3 py-2 sm:px-6 sm:py-3 text-center font-medium text-gray-700">Jenis</th>
                                <th class="px-3 py-2 sm:px-6 sm:py-3 text-right font-medium text-gray-700">Jumlah</th>
                                <th class="px-3 py-2 sm:px-6 sm:py-3 text-right font-medium text-gray-700">Saldo</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                            @foreach($slice as $item)
                                @php $runningBalance += (($item->credit ?? 0) - ($item->debit ?? 0)); @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2 sm:px-6 sm:py-4 text-gray-900">{{ \Carbon\Carbon::parse($item->date)->format('d/m/Y') }}</td>
                                    <td class="px-3 py-2 sm:px-6 sm:py-4 text-gray-900"><div class="max-w-xs truncate" title="{{ $item->description }}">{{ $item->description }}</div></td>
                                    <td class="px-3 py-2 sm:px-6 sm:py-4 text-center">
                                        @php $isDebit = ($item->debit ?? 0) > 0; $amount = $isDebit ? ($item->debit ?? 0) : ($item->credit ?? 0); @endphp
                                        @if($amount > 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $isDebit ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">{{ $isDebit ? '↗️ Keluar' : '↙️ Masuk' }}</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 sm:px-6 sm:py-4 text-right text-gray-900">
                                        @if($amount > 0)
                                            <span class="font-medium {{ $isDebit ? 'text-red-600' : 'text-green-600' }}">{{ $isDebit ? '- Rp' : '+ Rp' }} {{ number_format($amount, 0, ',', '.') }}</span>
                                            <div class="text-xs text-gray-500 mt-1">Bank: {{ $isDebit ? 'Debit' : 'Credit' }}</div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 sm:px-6 sm:py-4 text-right text-gray-900 font-medium"><span class="{{ $runningBalance >= 0 ? 'text-green-600' : 'text-red-600' }}">Rp {{ number_format($runningBalance, 0, ',', '.') }}</span></td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50 border-t border-gray-200">
                                <tr>
                                    <td class="px-3 py-2 sm:px-6 sm:py-3 text-left text-sm text-gray-600" colspan="5">
                                        Menampilkan 
                                        {{ min(($bsPage - 1) * $bsPerPage + 1, $bsTotal) }}–
                                        {{ min($bsPage * $bsPerPage, $bsTotal) }} 
                                        dari {{ $bsTotal }}
                                        <span class="ml-4">
                                            @if($bsPage > 1)
                                                <a class="text-blue-600 hover:text-blue-800" href="{{ request()->fullUrlWithQuery(['bs_page' => $bsPage - 1, 'bs_per_page' => $bsPerPage]) }}">Prev</a>
                                            @else
                                                <span class="text-gray-400">Prev</span>
                                            @endif
                                            <span class="mx-2">|</span>
                                            @if($bsPage < $bsTotalPages)
                                                <a class="text-blue-600 hover:text-blue-800" href="{{ request()->fullUrlWithQuery(['bs_page' => $bsPage + 1, 'bs_per_page' => $bsPerPage]) }}">Next</a>
                                            @else
                                                <span class="text-gray-400">Next</span>
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            @if($statistics['matched_count'] > 0)
            <div class="bg-white shadow rounded-lg border border-gray-200">
                <div class="px-4 sm:px-6 py-3 sm:py-4 bg-green-50 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            <h4 class="text-base sm:text-lg font-semibold text-green-800">Transaksi yang Cocok ({{ $statistics['matched_count'] }})</h4>
                        </div>
                        <form method="GET" class="flex flex-wrap items-center gap-2">
                            <input type="hidden" name="filter_start" value="{{ request()->query('filter_start') }}" />
                            <input type="hidden" name="filter_end" value="{{ request()->query('filter_end') }}" />
                            <input type="hidden" name="filter_type" value="{{ request()->query('filter_type') }}" />
                            <input type="hidden" name="per_page" value="{{ request()->query('per_page', 10) }}" />
                            <input type="hidden" name="matched_page" value="1" />
                            @php $matchedPerPageSel = (int) request()->query('matched_per_page', 10); @endphp
                            <select name="matched_per_page" class="border border-gray-300 rounded px-2 py-1 text-sm w-full sm:w-auto">
                                @foreach([10,50,100] as $opt)
                                    <option value="{{ $opt }}" {{ $matchedPerPageSel === $opt ? 'selected' : '' }}>{{ $opt }}/hal</option>
                                @endforeach
                            </select>
                            <button type="submit" class="bg-gray-800 hover:bg-black text-white px-3 py-1 rounded text-sm w-full sm:w-auto">Terapkan</button>
                        </form>
                    </div>
                </div>
                <div class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs sm:text-sm">
                            <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-3 py-2 sm:px-6 sm:py-3 text-left font-medium text-gray-700">Tanggal</th>
                                <th class="px-3 py-2 sm:px-6 sm:py-3 text-left font-medium text-gray-700">Keterangan App</th>
                                <th class="px-3 py-2 sm:px-6 sm:py-3 text-left font-medium text-gray-700">Keterangan Bank</th>
                                <th class="px-3 py-2 sm:px-6 sm:py-3 text-right font-medium text-gray-700">Nominal</th>
                                <th class="px-3 py-2 sm:px-6 sm:py-3 text-center font-medium text-gray-700">Confidence</th>
                                <th class="px-3 py-2 sm:px-6 sm:py-3 text-center font-medium text-gray-700">Aksi</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                            @php
                                $matched = $reconciliationResults['matched'];
                                if ($matched instanceof \Illuminate\Support\Collection) { $matched = $matched->all(); }
                                if ($fsC || $feC || $filterType !== 'all') {
                                    $matched = array_values(array_filter($matched, function($m) use ($fsC,$feC,$filterType) {
                                        $bankItem = $m['bank_item'];
                                        $date = \Carbon\Carbon::parse($bankItem->date);
                                        $isDebit = (($bankItem->debit ?? 0) > 0);
                                        if ($fsC && $date->lt($fsC)) return false;
                                        if ($feC && $date->gt($feC)) return false;
                                        if ($filterType === 'debit' && !$isDebit) return false;
                                        if ($filterType === 'credit' && $isDebit) return false;
                                        return true;
                                    }));
                                }
                                $matchedTotal = count($matched);
                                $matchedPerPage = (int) request()->query('matched_per_page', $globalPerPage);
                                $matchedPage = max(1, (int) request()->query('matched_page', 1));
                                $matchedSlice = array_slice($matched, ($matchedPage - 1) * $matchedPerPage, $matchedPerPage);
                                $matchedTotalPages = max(1, (int) ceil($matchedTotal / $matchedPerPage));
                            @endphp
                            @foreach($matchedSlice as $match)
                                @php
                                    $appTransaction = $match['app_transaction'];
                                    $bankItem = $match['bank_item'];
                                    $appDebit = $appTransaction->debit_amount ?? 0;
                                    $appCredit = $appTransaction->credit_amount ?? 0;
                                    $appAmount = $appDebit > 0 ? $appDebit : $appCredit;
                                    $appIsDebit = $appDebit > 0;
                                    $bankDebit = $bankItem->debit ?? 0;
                                    $bankCredit = $bankItem->credit ?? 0;
                                    $bankAmount = $bankDebit > 0 ? $bankDebit : $bankCredit;
                                    $bankIsDebit = $bankDebit > 0;
                                    $appAmount = (float) $appAmount;
                                    $bankAmount = (float) $bankAmount;
                                    $actualDiff = abs((float)$appAmount - (float)$bankAmount);
                                    $isMatchCondition = ($actualDiff < 0.01) || (round($appAmount, 2) == round($bankAmount, 2));
                                    $hasZeroIssue = ($bankDebit == 0 && $bankCredit == 0) || $bankAmount == 0;
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2 sm:px-6 sm:py-4 text-gray-900">{{ \Carbon\Carbon::parse($appTransaction->transaction_date)->format('d M Y') }}</td>
                                    <td class="px-3 py-2 sm:px-6 sm:py-4 text-gray-900 max-w-xs"><div class="truncate" title="{{ $appTransaction->description }}">{{ Str::limit($appTransaction->description, 50) }}</div><div class="text-xs text-gray-500 mt-1">{{ $appTransaction->source_table }}</div></td>
                                    <td class="px-3 py-2 sm:px-6 sm:py-4 text-gray-900 max-w-xs">
                                        <div class="truncate" title="{{ $bankItem->description }}">{{ Str::limit($bankItem->description, 50) }}</div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            @if(app()->environment('local') && $hasZeroIssue)
                                                <span class="text-red-600 font-bold">Bank amount issue</span>
                                            @else
                                                Bank: {{ $bankIsDebit ? 'Debit' : 'Credit' }} {{ $bankIsDebit ? '-' : '+' }}Rp {{ number_format($bankAmount, 0, ',', '.') }} @if(!$isMatchCondition)<span class="text-orange-600 font-medium">Diff</span>@endif
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 sm:px-6 sm:py-4 text-right">
                                        <div class="space-y-1">
                                            <div><span class="text-xs text-gray-500">App:</span> <span class="font-medium {{ $appIsDebit ? 'text-red-600' : 'text-green-600' }}">{{ $appIsDebit ? '-' : '+' }}Rp {{ number_format($appAmount, 0, ',', '.') }}</span></div>
                                            <div><span class="text-xs text-gray-500">Bank:</span> <span class="font-medium {{ $bankIsDebit ? 'text-red-600' : 'text-green-600' }}">{{ $bankIsDebit ? '-' : '+' }}Rp {{ number_format($bankAmount, 0, ',', '.') }}</span></div>
                                            @if($isMatchCondition)
                                                <div class="text-xs text-green-600">Match</div>
                                            @else
                                                <div class="text-xs text-orange-600">Diff: Rp {{ number_format($actualDiff, 0, ',', '.') }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 sm:px-6 sm:py-4 text-center"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $match['confidence'] >= 90 ? 'bg-green-100 text-green-800' : ($match['confidence'] >= 75 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">{{ $match['confidence'] }}%</span></td>
                                    <td class="px-3 py-2 sm:px-6 sm:py-4 text-center"><button type="button" onclick="unmarkAsMatched('{{ $appTransaction->source_id }}', '{{ $appTransaction->source_table }}', '{{ $bankItem->id }}')" class="text-red-600 hover:text-red-800 text-sm font-medium">Unmark</button></td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50 border-t border-gray-200"><tr><td class="px-3 py-2 sm:px-6 sm:py-3 text-left text-sm text-gray-600" colspan="6">Menampilkan {{ min(($matchedPage - 1) * $matchedPerPage + 1, $matchedTotal) }}–{{ min($matchedPage * $matchedPerPage, $matchedTotal) }} dari {{ $matchedTotal }} <span class="ml-4">@if($matchedPage > 1)<a class="text-blue-600 hover:text-blue-800" href="{{ request()->fullUrlWithQuery(['matched_page' => $matchedPage - 1, 'matched_per_page' => $matchedPerPage]) }}">Prev</a>@else<span class="text-gray-400">Prev</span>@endif<span class="mx-2">|</span>@if($matchedPage < $matchedTotalPages)<a class="text-blue-600 hover:text-blue-800" href="{{ request()->fullUrlWithQuery(['matched_page' => $matchedPage + 1, 'matched_per_page' => $matchedPerPage]) }}">Next</a>@else<span class="text-gray-400">Next</span>@endif</span></td></tr></tfoot>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            @if($statistics['unmatched_app_count'] > 0)
            <div class="bg-white shadow rounded-lg border border-gray-200">
                <div class="px-4 sm:px-6 py-3 sm:py-4 bg-orange-50 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-orange-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                            <h4 class="text-base sm:text-lg font-semibold text-orange-800">Transaksi Aplikasi Belum Cocok ({{ $statistics['unmatched_app_count'] }})</h4>
                        </div>
                        <form method="GET" class="flex flex-wrap items-center gap-2">
                            <input type="hidden" name="filter_start" value="{{ request()->query('filter_start') }}" />
                            <input type="hidden" name="filter_end" value="{{ request()->query('filter_end') }}" />
                            <input type="hidden" name="filter_type" value="{{ request()->query('filter_type') }}" />
                            <input type="hidden" name="per_page" value="{{ request()->query('per_page', 10) }}" />
                            <input type="hidden" name="ua_page" value="1" />
                            @php $uaPerPageSel = (int) request()->query('ua_per_page', 10); @endphp
                            <select name="ua_per_page" class="border border-gray-300 rounded px-2 py-1 text-sm w-full sm:w-auto">
                                @foreach([10,50,100] as $opt)
                                    <option value="{{ $opt }}" {{ $uaPerPageSel === $opt ? 'selected' : '' }}>{{ $opt }}/hal</option>
                                @endforeach
                            </select>
                            <button type="submit" class="bg-gray-800 hover:bg-black text-white px-3 py-1 rounded text-sm w-full sm:w-auto">Terapkan</button>
                        </form>
                    </div>
                </div>
                <div class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs sm:text-sm">
                            <thead class="bg-gray-50 border-b border-gray-200"><tr><th class="px-3 py-2 sm:px-6 sm:py-3 text-left font-medium text-gray-700">Tanggal</th><th class="px-3 py-2 sm:px-6 sm:py-3 text-left font-medium text-gray-700">Keterangan</th><th class="px-3 py-2 sm:px-6 sm:py-3 text-right font-medium text-gray-700">Nominal</th><th class="px-3 py-2 sm:px-6 sm:py-3 text-left font-medium text-gray-700">Tipe</th><th class="px-3 py-2 sm:px-6 sm:py-3 text-center font-medium text-gray-700">Aksi</th></tr></thead>
                            <tbody class="divide-y divide-gray-200">
                            @php
                                $ua = $reconciliationResults['unmatched_app'];
                                if ($ua instanceof \Illuminate\Support\Collection) { $ua = $ua->all(); }
                                if ($fsC || $feC || $filterType !== 'all') {
                                    $ua = array_values(array_filter($ua, function($t) use ($fsC,$feC,$filterType) {
                                        $date = \Carbon\Carbon::parse($t->transaction_date);
                                        $isDebit = (($t->debit_amount ?? 0) > 0);
                                        if ($fsC && $date->lt($fsC)) return false;
                                        if ($feC && $date->gt($feC)) return false;
                                        if ($filterType === 'debit' && !$isDebit) return false;
                                        if ($filterType === 'credit' && $isDebit) return false;
                                        return true;
                                    }));
                                }
                                $uaTotal = count($ua);
                                $uaPerPage = (int) request()->query('ua_per_page', $globalPerPage);
                                $uaPage = max(1, (int) request()->query('ua_page', 1));
                                $uaSlice = array_slice($ua, ($uaPage - 1) * $uaPerPage, $uaPerPage);
                                $uaTotalPages = max(1, (int) ceil($uaTotal / $uaPerPage));
                            @endphp
                            @foreach($uaSlice as $transaction)
                                @php $amount = $transaction->debit_amount ?: $transaction->credit_amount; $isDebit = (bool) $transaction->debit_amount; @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2 sm:px-6 sm:py-4 text-gray-900">{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y') }}</td>
                                    <td class="px-3 py-2 sm:px-6 sm:py-4 text-gray-900 max-w-md"><div class="truncate" title="{{ $transaction->description }}">{{ Str::limit($transaction->description, 80) }}</div></td>
                                    <td class="px-3 py-2 sm:px-6 sm:py-4 text-right"><span class="font-medium {{ $isDebit ? 'text-red-600' : 'text-green-600' }}">{{ $isDebit ? '-' : '+' }}Rp {{ number_format($amount, 0, ',', '.') }}</span></td>
                                    <td class="px-3 py-2 sm:px-6 sm:py-4 text-gray-600"><span class="inline-flex items-center text-xs font-medium text-gray-800">{{ $transaction->source_table }}</span></td>
                                    <td class="px-3 py-2 sm:px-6 sm:py-4 text-center"><button type="button" onclick="findManualMatch('{{ $transaction->source_id }}', '{{ $transaction->source_table }}')" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Manual Match</button></td>
                                </tr>
                            @endforeach
                            </tbody>
                            @if($reconciliationResults['unmatched_app'])
                            <tfoot class="bg-gray-50 border-t border-gray-200">
                                <tr>
                                    <td class="px-3 py-2 sm:px-6 sm:py-3 text-left font-semibold text-gray-900" colspan="2">Total</td>
                                    <td class="px-3 py-2 sm:px-6 sm:py-3 text-right font-semibold text-gray-900">
                                        @php $totalUnmatchedApp = 0; foreach($ua as $t) { $totalUnmatchedApp += ($t->debit_amount ?: $t->credit_amount); } @endphp
                                        <span class="text-orange-600">Rp {{ number_format($totalUnmatchedApp, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="px-3 py-2 sm:px-6 sm:py-3" colspan="2">
                                        <div class="text-sm text-gray-600">Menampilkan {{ min(($uaPage - 1) * $uaPerPage + 1, $uaTotal) }}–{{ min($uaPage * $uaPerPage, $uaTotal) }} dari {{ $uaTotal }} <span class="ml-4">@if($uaPage > 1)<a class="text-blue-600 hover:text-blue-800" href="{{ request()->fullUrlWithQuery(['ua_page' => $uaPage - 1, 'ua_per_page' => $uaPerPage]) }}">Prev</a>@else<span class="text-gray-400">Prev</span>@endif<span class="mx-2">|</span>@if($uaPage < $uaTotalPages)<a class="text-blue-600 hover:text-blue-800" href="{{ request()->fullUrlWithQuery(['ua_page' => $uaPage + 1, 'ua_per_page' => $uaPerPage]) }}">Next</a>@else<span class="text-gray-400">Next</span>@endif</span></div>
                                    </td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
            @endif

            @if($statistics['unmatched_bank_count'] > 0)
            <div class="bg-white shadow rounded-lg border border-gray-200">
                <div class="px-4 sm:px-6 py-3 sm:py-4 bg-red-50 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                        <h4 class="text-base sm:text-lg font-semibold text-red-800">Mutasi Bank Belum Cocok ({{ $statistics['unmatched_bank_count'] }})</h4>
                        </div>
                        <form method="GET" class="flex flex-wrap items-center gap-2">
                            <input type="hidden" name="filter_start" value="{{ request()->query('filter_start') }}" />
                            <input type="hidden" name="filter_end" value="{{ request()->query('filter_end') }}" />
                            <input type="hidden" name="filter_type" value="{{ request()->query('filter_type') }}" />
                            <input type="hidden" name="per_page" value="{{ request()->query('per_page', 10) }}" />
                            <input type="hidden" name="ub_page" value="1" />
                            @php $ubPerPageSel = (int) request()->query('ub_per_page', 10); @endphp
                            <select name="ub_per_page" class="border border-gray-300 rounded px-2 py-1 text-sm w-full sm:w-auto">
                                @foreach([10,50,100] as $opt)
                                    <option value="{{ $opt }}" {{ $ubPerPageSel === $opt ? 'selected' : '' }}>{{ $opt }}/hal</option>
                                @endforeach
                            </select>
                            <button type="submit" class="bg-gray-800 hover:bg-black text-white px-3 py-1 rounded text-sm w-full sm:w-auto">Terapkan</button>
                        </form>
                    </div>
                </div>
                <div class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs sm:text-sm">
                            <thead class="bg-gray-50 border-b border-gray-200"><tr><th class="px-3 py-2 sm:px-6 sm:py-3 text-left font-medium text-gray-700">Tanggal</th><th class="px-3 py-2 sm:px-6 sm:py-3 text-left font-medium text-gray-700">Keterangan</th><th class="px-3 py-2 sm:px-6 sm:py-3 text-right font-medium text-gray-700">Nominal</th><th class="px-3 py-2 sm:px-6 sm:py-3 text-center font-medium text-gray-700">Aksi</th></tr></thead>
                            <tbody class="divide-y divide-gray-200">
                            @php
                                $ub = $reconciliationResults['unmatched_bank'];
                                if ($ub instanceof \Illuminate\Support\Collection) { $ub = $ub->all(); }
                                if ($fsC || $feC || $filterType !== 'all') {
                                    $ub = array_values(array_filter($ub, function($item) use ($fsC,$feC,$filterType) {
                                        $date = \Carbon\Carbon::parse($item->date);
                                        $isDebit = (($item->debit ?? 0) > 0);
                                        if ($fsC && $date->lt($fsC)) return false;
                                        if ($feC && $date->gt($feC)) return false;
                                        if ($filterType === 'debit' && !$isDebit) return false;
                                        if ($filterType === 'credit' && $isDebit) return false;
                                        return true;
                                    }));
                                }
                                $ubTotal = count($ub);
                                $ubPerPage = (int) request()->query('ub_per_page', 10);
                                $ubPage = max(1, (int) request()->query('ub_page', 1));
                                $ubSlice = array_slice($ub, ($ubPage - 1) * $ubPerPage, $ubPerPage);
                                $ubTotalPages = max(1, (int) ceil($ubTotal / $ubPerPage));
                            @endphp
                            @foreach($ubSlice as $item)
                                @php $amount = $item->debit ?: $item->credit; $isDebit = (bool) $item->debit; @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2 sm:px-6 sm:py-4 text-gray-900">{{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}</td>
                                    <td class="px-3 py-2 sm:px-6 sm:py-4 text-gray-900 max-w-md"><div class="truncate" title="{{ $item->description }}">{{ Str::limit($item->description, 80) }}</div></td>
                                    <td class="px-3 py-2 sm:px-6 sm:py-4 text-right"><span class="font-medium {{ $isDebit ? 'text-red-600' : 'text-green-600' }}">{{ $isDebit ? '-' : '+' }}Rp {{ number_format($amount, 0, ',', '.') }}</span></td>
                                    <td class="px-3 py-2 sm:px-6 sm:py-4 text-center"><button type="button" onclick="findManualMatchBank('{{ $item->id }}')" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Manual Match</button></td>
                                </tr>
                            @endforeach
                            </tbody>
                            @if($reconciliationResults['unmatched_bank'])
                            <tfoot class="bg-gray-50 border-t border-gray-200">
                                <tr>
                                    <td class="px-3 py-2 sm:px-6 sm:py-3 text-left font-semibold text-gray-900" colspan="2">Total</td>
                                    <td class="px-3 py-2 sm:px-6 sm:py-3 text-right font-semibold text-gray-900">
                                        @php $totalUnmatchedBank = 0; foreach($ub as $i) { $totalUnmatchedBank += ($i->debit ?: $i->credit); } @endphp
                                        <span class="text-red-600">Rp {{ number_format($totalUnmatchedBank, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="px-3 py-2 sm:px-6 sm:py-3">
                                        <div class="text-sm text-gray-600">Menampilkan {{ min(($ubPage - 1) * $ubPerPage + 1, $ubTotal) }}–{{ min($ubPage * $ubPerPage, $ubTotal) }} dari {{ $ubTotal }} <span class="ml-4">@if($ubPage > 1)<a class="text-blue-600 hover:text-blue-800" href="{{ request()->fullUrlWithQuery(['ub_page' => $ubPage - 1, 'ub_per_page' => $ubPerPage]) }}">Prev</a>@else<span class="text-gray-400">Prev</span>@endif<span class="mx-2">|</span>@if($ubPage < $ubTotalPages)<a class="text-blue-600 hover:text-blue-800" href="{{ request()->fullUrlWithQuery(['ub_page' => $ubPage + 1, 'ub_per_page' => $ubPerPage]) }}">Next</a>@else<span class="text-gray-400">Next</span>@endif</span></div>
                                    </td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
            @endif

            @if($statistics['total_app_transactions'] === 0 && $statistics['total_bank_items'] === 0)
            <div class="bg-white shadow rounded-lg border border-gray-200 p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Data Untuk Direkonsiliasi</h3>
                <p class="text-gray-500">Tidak ada transaksi aplikasi atau data mutasi bank pada periode ini.</p>
            </div>
            @endif
        </div>
    </div>

    <script>
    function autoMatchHighConfidence() {
        if (!confirm('Apakah Anda yakin ingin menandai semua kecocokan dengan confidence 85%+ sebagai cocok?')) { return; }
        fetch('/admin/reconciliation/auto-match', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json','X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
            body: JSON.stringify({ payment_method_id: {{ $record->payment_method_id }}, start_date: '{{ $record->period_start->format('Y-m-d') }}', end_date: '{{ $record->period_end->format('Y-m-d') }}' })
        })
        .then(response => response.json())
        .then(data => { if (data.success) { alert(`${data.matched_count} transaksi berhasil di-match otomatis!`); location.reload(); } })
        .catch(error => { console.error('Error:', error); });
    }
    function unmarkAsMatched(sourceId, sourceTable, bankItemId) {
        if (!confirm('Apakah Anda yakin ingin membatalkan match ini?')) { return; }
        fetch('/admin/reconciliation/unmark-matched', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json','X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
            body: JSON.stringify({ source_id: sourceId, source_table: sourceTable, bank_item_id: bankItemId })
        })
        .then(response => { if (!response.ok) { throw new Error(`HTTP error! status: ${response.status}`); } return response.json(); })
        .then(data => { if (data.success) { alert('Match berhasil dibatalkan!'); location.reload(); } else { alert('Error: ' + (data.message || 'Unknown error')); } })
        .catch(error => { console.error('Error:', error); alert('Terjadi error: ' + error.message); });
    }
    function findManualMatch(sourceId, sourceTable) { alert('Fitur manual match untuk transaksi aplikasi akan segera tersedia.'); }
    function findManualMatchBank(bankItemId) { alert('Fitur manual match untuk mutasi bank akan segera tersedia.'); }
    </script>
    </body>
</html>