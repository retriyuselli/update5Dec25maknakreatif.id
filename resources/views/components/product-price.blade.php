<div class="flex flex-col">
    <span class="{{ $sizeClasses()['price'] }}">{{ $formattedPrice() }}</span>
    
    @if($showMargin)
        <div class="flex items-center mt-1">
            <span class="{{ $sizeClasses()['margin'] }} {{ $marginColorClass() }}">
                {{ number_format($profitMargin(), 2) }}% margin
            </span>
        </div>
    @endif
</div>