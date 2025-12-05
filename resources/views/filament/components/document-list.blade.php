<div class="space-y-3">
    @if(!empty($documents))
        <p class="text-sm text-gray-600 mb-4">
            {{ count($documents) }} dokumen{{ count($documents) > 1 ? '' : '' }} telah diunggah:
        </p>
        
        @foreach($documents as $document)
            <div class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition-colors">
                <div class="flex-shrink-0 mr-3">
                    @php
                        $extension = strtolower(pathinfo($document, PATHINFO_EXTENSION));
                        $iconClass = match($extension) {
                            'pdf' => 'text-red-600',
                            'doc', 'docx' => 'text-blue-600',
                            'jpg', 'jpeg', 'png' => 'text-green-600',
                            default => 'text-gray-600'
                        };
                    @endphp
                    <svg class="w-8 h-8 {{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                </div>
                
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">
                        {{ basename($document) }}
                    </p>
                    <p class="text-xs text-gray-500 uppercase">
                        File {{ $extension }}
                    </p>
                </div>
                
                <div class="flex-shrink-0 ml-3">
                    <a href="{{ asset('storage/' . $document) }}" 
                       target="_blank" 
                       class="inline-flex items-center px-3 py-1 border border-transparent text-xs leading-4 font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                            </path>
                        </svg>
                        Buka
                    </a>
                </div>
            </div>
        @endforeach
        
        <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
            <p class="text-xs text-blue-700">
                💡 <strong>Tips:</strong> Klik "Buka" untuk melihat atau mengunduh dokumen di tab baru.
            </p>
        </div>
    @else
        <div class="text-center py-8">
            <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                </path>
            </svg>
            <p class="text-gray-500">Tidak ada dokumen yang diunggah</p>
        </div>
    @endif
</div>
