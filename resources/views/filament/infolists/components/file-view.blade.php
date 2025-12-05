@php
    $state = $getState();
@endphp

@if ($state)
    <div class="flex items-center gap-2">
        <x-filament::icon
            icon="heroicon-m-document"
            class="h-5 w-5 text-gray-500 dark:text-gray-400"
        />
        
        <div class="flex items-center gap-2">
            <a 
                href="{{ Storage::url($state) }}"
                target="_blank"
                class="text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400"
            >
                View Document
            </a>
            
            <a 
                href="{{ Storage::url($state) }}"
                download
                class="text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400"
            >
                Download
            </a>
        </div>
    </div>
@else
    <div class="text-sm text-gray-500 dark:text-gray-400">
        No document uploaded
    </div>
@endif