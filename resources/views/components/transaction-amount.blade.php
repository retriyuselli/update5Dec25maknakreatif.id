{{-- 
    Transaction Amount Display Component
    
    Props:
    - $debit: debit amount (bank terminology)  
    - $credit: credit amount (bank terminology)
    - $showBankTerm: whether to show bank terminology (optional)
    - $size: 'sm', 'md', 'lg' (optional, default: 'md')
--}}

@php
    $isDebit = $debit > 0;
    $amount = $isDebit ? $debit : $credit;
    $direction = $isDebit ? 'keluar' : 'masuk';
    $directionLabel = $isDebit ? 'Uang Keluar' : 'Uang Masuk';
    $bankTerm = $isDebit ? 'Debit' : 'Credit';
    $colorClass = $isDebit ? 'text-red-600' : 'text-green-600';
    $bgClass = $isDebit ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800';
    $icon = $isDebit ? '↗️' : '↙️';
    $prefix = $isDebit ? '-' : '+';
    
    $size = $size ?? 'md';
    $showBankTerm = $showBankTerm ?? false;
    
    $sizeClasses = [
        'sm' => ['text' => 'text-sm', 'badge' => 'text-xs px-2 py-0.5', 'subtext' => 'text-xs'],
        'md' => ['text' => 'text-base', 'badge' => 'text-xs px-2.5 py-0.5', 'subtext' => 'text-xs'],
        'lg' => ['text' => 'text-lg', 'badge' => 'text-sm px-3 py-1', 'subtext' => 'text-sm'],
    ];
    
    $classes = $sizeClasses[$size];
@endphp

<div class="flex flex-col items-end">
    {{-- Main amount display --}}
    <div class="flex items-center gap-2">
        <span class="inline-flex items-center {{ $classes['badge'] }} rounded-full font-medium {{ $bgClass }}">
            {{ $icon }} {{ ucfirst($direction) }}
        </span>
        <span class="font-medium {{ $colorClass }} {{ $classes['text'] }}" 
              title="{{ $directionLabel }} - Bank: {{ $bankTerm }} Rp {{ number_format($amount, 0, ',', '.') }}">
            {{ $prefix }} Rp {{ number_format($amount, 0, ',', '.') }}
        </span>
    </div>
    
    {{-- Optional bank terminology --}}
    @if($showBankTerm && $amount > 0)
        <div class="{{ $classes['subtext'] }} text-gray-500 mt-1">
            Bank: {{ $bankTerm }}
        </div>
    @endif
</div>