<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <!-- Category Badge & Version -->
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="flex items-center px-3 py-1 rounded-full text-sm font-medium gap-3" 
                         style="background-color: {{ $record->category->color }}20; color: {{ $record->category->color }}">
                        <x-heroicon-o-folder class="w-4 h-4" />
                        {{ $record->category->name }}
                    </div>
                </div>
                
                <div class="flex items-center space-x-3">
                    <span class="text-sm text-gray-500 bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-lg">
                        Version {{ $record->formatted_version }}
                    </span>
                    @if($record->needsReview())
                        <span class="text-sm text-red-600 bg-red-100 px-3 py-1 rounded-lg">
                            <x-heroicon-o-exclamation-triangle class="w-4 h-4 inline mr-1" />
                            Perlu Review
                        </span>
                    @endif
                    @if($record->is_active)
                        <span class="text-sm text-green-600 bg-green-100 px-3 py-1 rounded-lg">
                            <x-heroicon-o-check-circle class="w-4 h-4 inline mr-1" />
                            Aktif
                        </span>
                    @else
                        <span class="text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-lg">
                            <x-heroicon-o-x-circle class="w-4 h-4 inline mr-1" />
                            Tidak Aktif
                        </span>
                    @endif
                </div>
            </div>

            <!-- Title & Description -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">{{ $record->title }}</h1>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-5 leading-relaxed">{{ $record->description }}</p>
            </div>

            <!-- Meta Information Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="flex text-sm">
                    {{-- <x-heroicon-o-user class="w-4 h-4 text-gray-400 flex-shrink-0" /> --}}
                    <div class="ml-8">
                        <span class="text-gray-500 dark:text-gray-400 block">Dibuat oleh:</span>
                        <div class="font-medium text-gray-900 dark:text-white">{{ $record->creator->name }}</div>
                    </div>
                </div>
                
                <div class="flex text-sm">
                    {{-- <x-heroicon-o-calendar class="w-4 h-4 text-gray-400 flex-shrink-0" /> --}}
                    <div class="ml-8">
                        <span class="text-gray-500 dark:text-gray-400 block">Berlaku sejak:</span>
                        <div class="font-medium text-gray-900 dark:text-white">{{ $record->effective_date?->format('d M Y') ?? '-' }}</div>
                    </div>
                </div>
                
                <div class="flex text-sm">
                    {{-- <x-heroicon-o-clock class="w-4 h-4 text-gray-400 flex-shrink-0" /> --}}
                    <div class="ml-8">
                        <span class="text-gray-500 dark:text-gray-400 block">Terakhir diupdate:</span>
                        <div class="font-medium text-gray-900 dark:text-white">{{ $record->updated_at->format('d M Y H:i') }}</div>
                    </div>
                </div>
                
                <div class="flex text-sm">
                    {{-- <x-heroicon-o-arrow-path class="w-4 h-4 text-gray-400 flex-shrink-0" /> --}}
                    <div class="ml-8">
                        <span class="text-gray-500 dark:text-gray-400 block">Review date:</span>
                        <div class="font-medium text-gray-900 dark:text-white {{ $record->needsReview() ? 'text-red-600' : '' }}">
                            {{ $record->review_date?->format('d M Y') ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Keywords -->
            @if($record->keywords)
                <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <div class="flex items-start">
                        {{-- <x-heroicon-o-tag class="w-4 h-4 mr-2 text-blue-600 mt-0.5" /> --}}
                        <div>
                            <span class="text-sm font-medium text-blue-800 dark:text-blue-200 block mb-2">Kata Kunci:</span>
                            <div class="flex flex-wrap gap-2">
                                @foreach(explode(',', $record->keywords) as $keyword)
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-medium" 
                                          style="background-color: #dbeafe; color: #1e40af; border: 1px solid #3b82f6;">
                                        {{ trim($keyword) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Steps Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center gap-3">
                    <x-heroicon-o-list-bullet class="w-5 h-5 text-blue-600" />
                    <span>Langkah-langkah Prosedur</span>
                    <span class="ml-auto text-xs font-medium px-3 py-1 rounded-full" 
                          style="background-color: #dbeafe; color: #1e40af; border: 1px solid #3b82f6;">
                        {{ $record->steps_count }} Langkah
                    </span>
                </h2>
            </div>
            
            <div class="p-6">
                @if($record->steps && count($record->steps) > 0)
                    <div class="space-y-6">
                        @foreach($record->steps as $index => $step)
                            <div class="flex group">
                                <!-- Step Number -->
                                <div class="flex-shrink-0 p-4">
                                    <div class="w-8 h-10 rounded-full mr-5 flex items-center justify-center text-sm font-bold shadow-lg" 
                                         style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white;">
                                        {{ $step['step_number'] ?? $index + 1 }}
                                    </div>
                                    @if(!$loop->last)
                                        <div class="w-px h-8 mx-auto mt-2" style="background: linear-gradient(180deg, #93c5fd 0%, transparent 100%);"></div>
                                    @endif
                                </div>
                                
                                <!-- Step Content -->
                                <div class="flex-1 mr-5 mb-0">
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 group-hover:bg-gray-100 dark:group-hover:bg-gray-600 transition-colors">
                                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2 text-base">{{ $step['title'] }}</h3>
                                        <div class="text-gray-700 text-sm dark:text-gray-300 mb-3 leading-relaxed step-description">
                                            {!! $step['description'] !!}
                                        </div>
                                        
                                        @if(!empty($step['notes']))
                                            <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-3 rounded-r-lg mt-3">
                                                <div class="flex items-start gap-3">
                                                    <x-heroicon-o-light-bulb class="w-4 h-4 text-yellow-600 flex-shrink-0" />
                                                    <div>
                                                        <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200 mb-1">Catatan Penting:</p>
                                                        <div class="text-sm dark:text-yellow-300 notes-content">
                                                            {!! $step['notes'] !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                        <x-heroicon-o-exclamation-circle class="w-12 h-12 mx-auto mb-4" />
                        <p class="text-lg">Belum ada langkah-langkah yang didefinisikan untuk SOP ini.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Supporting Documents Section -->
        @if($record->supporting_documents && count($record->supporting_documents) > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center gap-3">
                        <x-heroicon-o-paper-clip class="w-5 h-5 text-green-600" />
                        <span>Dokumen Pendukung</span>
                        <span class="ml-auto text-sm bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-3 py-1 rounded-full">
                            {{ count($record->supporting_documents) }} File
                        </span>
                    </h2>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($record->supporting_documents as $document)
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:shadow-md transition-shadow hover:border-blue-300 dark:hover:border-blue-500">
                                <div class="flex items-center gap-3">
                                    @php
                                        $extension = pathinfo($document, PATHINFO_EXTENSION);
                                        $iconClass = match(strtolower($extension)) {
                                            'pdf' => 'text-red-600',
                                            'doc', 'docx' => 'text-blue-600',
                                            'xls', 'xlsx' => 'text-green-600',
                                            'jpg', 'jpeg', 'png', 'gif' => 'text-purple-600',
                                            default => 'text-gray-600'
                                        };
                                        $iconName = match(strtolower($extension)) {
                                            'pdf' => 'document-text',
                                            'doc', 'docx' => 'document-text',
                                            'xls', 'xlsx' => 'table-cells',
                                            'jpg', 'jpeg', 'png', 'gif' => 'photo',
                                            default => 'document'
                                        };
                                    @endphp
                                    
                                    <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center mr-3">
                                        @svg('heroicon-o-' . $iconName, 'w-5 h-5 ' . $iconClass)
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ basename($document) }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">
                                            {{ strtoupper($extension) }} File
                                        </p>
                                    </div>
                                    <div class="ml-2">
                                        <a href="{{ Storage::url($document) }}" 
                                           target="_blank"
                                           class="inline-flex items-center px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition-colors">
                                            <x-heroicon-o-arrow-down-tray class="w-3 h-3 mr-1" />
                                            Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Revision History Section -->
        @if($record->revisions && $record->revisions->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center gap-3">
                        <x-heroicon-o-clock class="w-5 h-5 text-orange-600" />
                        <span>Riwayat Revisi</span>
                        <span class="ml-auto text-sm bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200 px-3 py-1 rounded-full">
                            {{ $record->revisions->count() }} Revisi
                        </span>
                    </h2>
                </div>
                
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($record->revisions->take(5) as $revision)
                            <div class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg gap-3">
                                <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900 rounded-full flex items-center justify-center">
                                    <x-heroicon-o-pencil class="w-4 h-4 text-orange-600" />
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            Version {{ $revision->formatted_version }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $revision->revision_date->format('d M Y H:i') }}
                                        </p>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">
                                        Direvisi oleh: {{ $revision->revisor->name }}
                                    </p>
                                    @if($revision->revision_notes)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ $revision->revision_notes }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
