<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            {{ $record->user->name }}
        </h3>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ Carbon\Carbon::create()->month($record->month)->format('F') }} {{ $record->year }}
        </p>
    </div>

    <!-- Target Information -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
            <h4 class="font-medium text-blue-900 dark:text-blue-100 mb-2">Target Amount</h4>
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                IDR {{ number_format($record->target_amount, 0, ',', '.') }}
            </p>
        </div>

        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
            <h4 class="font-medium text-green-900 dark:text-green-100 mb-2">Achieved Amount</h4>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                IDR {{ number_format($achieved, 0, ',', '.') }}
            </p>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="space-y-2">
        <div class="flex justify-between text-sm">
            <span class="font-medium text-gray-700 dark:text-gray-300">Progress</span>
            <span class="font-medium text-gray-700 dark:text-gray-300">{{ $percentage }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-4 dark:bg-gray-700">
            <div class="h-4 rounded-full transition-all duration-300 {{ 
                $percentage > 100 ? 'bg-green-600' : 
                ($percentage >= 100 ? 'bg-green-500' : 
                ($percentage >= 50 ? 'bg-yellow-500' : 'bg-red-500'))
            }}" style="width: {{ min($percentage, 100) }}%"></div>
        </div>
    </div>

    <!-- Status Badge -->
    <div class="flex items-center justify-center">
        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium {{
            $status === 'Overachieved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' :
            ($status === 'Achieved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' :
            ($status === 'Partially Achieved' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' :
            'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'))
        }}">
            @if($status === 'Overachieved')
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
            @elseif($status === 'Achieved')
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
            @elseif($status === 'Partially Achieved')
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
            @else
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
            @endif
            {{ $status }}
        </span>
    </div>

    <!-- Additional Information -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 space-y-2">
        <h4 class="font-medium text-gray-900 dark:text-white mb-3">Summary</h4>
        
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-600 dark:text-gray-400">Remaining:</span>
                <span class="font-medium text-gray-900 dark:text-white ml-2">
                    IDR {{ number_format(max(0, $record->target_amount - $achieved), 0, ',', '.') }}
                </span>
            </div>
            
            <div>
                <span class="text-gray-600 dark:text-gray-400">Performance:</span>
                <span class="font-medium text-gray-900 dark:text-white ml-2">
                    @if($percentage > 100)
                        +{{ number_format($percentage - 100, 2) }}% above target
                    @elseif($percentage < 100)
                        {{ number_format(100 - $percentage, 2) }}% below target
                    @else
                        Target achieved!
                    @endif
                </span>
            </div>
        </div>
    </div>
</div>
