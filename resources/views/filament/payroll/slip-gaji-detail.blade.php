@php
    $totalKompensasi = $record->total_compensation;
    $gajiPerHari = $record->monthly_salary / 30;
    $bonusAmount = $record->bonus ?? 0;
@endphp

<div class="space-y-6">
    {{-- Header Information --}}
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 p-6 rounded-lg border border-gray-200 dark:border-gray-600">
        <div class="flex items-center space-x-4">
            <div class="flex-shrink-0">
                @if($user->avatar_url)
                    <img class="h-16 w-16 rounded-full object-cover" src="{{ Storage::url($user->avatar_url) }}" alt="{{ $user->name }}">
                @else
                    @php
                        $initials = collect(explode(' ', $user->name))
                            ->map(fn($word) => strtoupper(substr($word, 0, 1)))
                            ->take(2)
                            ->implode('');
                    @endphp
                    <div class="h-16 w-16 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-lg">
                        {{ $initials }}
                    </div>
                @endif
            </div>
            <div class="flex-1 gap-3">
                <h3 class="text-xl ml-5 font-bold text-gray-900 dark:text-white">{{ $user->name }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-300">{{ $user->email }}</p>
                <div class="flex items-center space-x-4 mt-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                        {{ $user->status->status_name ?? 'No Status' }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                        {{ ucfirst($user->department ?? 'No Department') }}
                    </span>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500 dark:text-gray-400">Periode</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white">{{ now()->format('F Y') }}</p>
            </div>
        </div>
    </div>

    {{-- Salary Breakdown --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Pendapatan --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-600">
            <h4 class="text-lg font-semibold text-green-700 dark:text-green-400 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
                Pendapatan
            </h4>
            <div class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-700 dark:text-gray-300">Gaji Pokok</span>
                    <span class="font-semibold text-gray-900 dark:text-white">Rp {{ number_format($record->monthly_salary, 0, ',', '.') }}</span>
                </div>
                @if($bonusAmount > 0)
                <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-700 dark:text-gray-300">Bonus</span>
                    <span class="font-semibold text-green-600 dark:text-green-400">+ Rp {{ number_format($bonusAmount, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="flex justify-between items-center py-2 pt-3 border-t-2 border-green-200 dark:border-green-700">
                    <span class="text-lg font-bold text-green-700 dark:text-green-400">Total Pendapatan</span>
                    <span class="text-lg font-bold text-green-700 dark:text-green-400">Rp {{ number_format($totalKompensasi, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Informasi Tambahan --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-600">
            <h4 class="text-lg font-semibold text-blue-700 dark:text-blue-400 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Informasi Payroll
            </h4>
            <div class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-700 dark:text-gray-300">Gaji Tahunan</span>
                    <span class="font-medium text-gray-900 dark:text-white">Rp {{ number_format($record->annual_salary, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-700 dark:text-gray-300">Gaji Per Hari</span>
                    <span class="font-medium text-gray-900 dark:text-white">Rp {{ number_format($gajiPerHari, 0, ',', '.') }}</span>
                </div>
                @if($record->last_review_date)
                <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-700 dark:text-gray-300">Review Terakhir</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($record->last_review_date)->format('d/m/Y') }}</span>
                </div>
                @endif
                @if($record->next_review_date)
                <div class="flex justify-between items-center py-2">
                    <span class="text-gray-700 dark:text-gray-300">Review Berikutnya</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($record->next_review_date)->format('d/m/Y') }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Notes Section --}}
    @if($record->notes)
    <div class="bg-yellow-50 dark:bg-yellow-900/20 p-6 rounded-lg border border-yellow-200 dark:border-yellow-800">
        <h4 class="text-lg font-semibold text-yellow-800 dark:text-yellow-400 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Riwayat Catatan
        </h4>
        
        @php
            // Split notes by double newlines to separate different entries
            $noteEntries = collect(explode("\n\n", $record->notes))
                ->filter(fn($entry) => !empty(trim($entry)))
                ->map(function($entry, $index) {
                    $lines = explode("\n", trim($entry));
                    $firstLine = $lines[0] ?? '';
                    
                    // Check if first line contains date pattern [dd/mm/yyyy]
                    if (preg_match('/\[(\d{2}\/\d{2}\/\d{4})\]/', $firstLine, $matches)) {
                        $date = $matches[1];
                        $content = str_replace($matches[0], '', $firstLine);
                        $content = trim($content);
                        
                        // Add remaining lines if any
                        if (count($lines) > 1) {
                            $content .= "\n" . implode("\n", array_slice($lines, 1));
                        }
                        
                        return [
                            'date' => $date,
                            'content' => trim($content),
                            'has_date' => true
                        ];
                    } else {
                        return [
                            'date' => null,
                            'content' => $entry,
                            'has_date' => false
                        ];
                    }
                })
                ->reverse(); // Show newest first
        @endphp
        
        <div class="space-y-4">
            @foreach($noteEntries as $index => $entry)
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-yellow-200 dark:border-yellow-700 shadow-sm">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                {{ $noteEntries->count() - $index }}
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            @if($entry['has_date'])
                                <div class="flex items-center space-x-2 mb-2">
                                    <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-yellow-700 dark:text-yellow-300">{{ $entry['date'] }}</span>
                                </div>
                            @else
                                <div class="flex items-center space-x-2 mb-2">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Catatan Umum</span>
                                </div>
                            @endif
                            <div class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line leading-relaxed">
                                {{ $entry['content'] }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            
            @if($noteEntries->isEmpty())
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="mt-2 text-sm text-yellow-600 dark:text-yellow-400">Belum ada catatan</p>
                </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Footer --}}
    <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-600 text-center">
        <p class="text-xs text-gray-500 dark:text-gray-400">
            Slip gaji ini dibuat pada {{ now()->format('d F Y \p\u\k\u\l H:i') }} WIB
        </p>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
            Data payroll terakhir diperbarui: {{ $record->updated_at->format('d F Y H:i') }} WIB
        </p>
    </div>
</div>
