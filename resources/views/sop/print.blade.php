<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $sop->title }} - SOP Print</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts - Noto Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    
    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'noto-sans': ['Noto Sans', 'sans-serif'],
                        'sans': ['Noto Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <style>
        /* Override font for all elements */
        * {
            font-family: 'Noto Sans', sans-serif !important;
        }
        
        body {
            font-family: 'Noto Sans', sans-serif !important;
        }
        
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-before: always;
            }
            .avoid-break {
                page-break-inside: avoid;
            }
        }
        
        .print-header {
            border-bottom: 3px solid #3b82f6;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .step-container {
            margin-bottom: 25px;
            break-inside: avoid;
        }
        
        .step-number {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
            font-family: 'Poppins', sans-serif;
            flex-shrink: 0;
        }
        
        /* Notes content styling with yellow color */
        .notes-content {
            color: #a16207 !important; /* yellow-700 */
            font-family: 'Poppins', sans-serif !important;
        }
        
        .notes-content *,
        .notes-content ol,
        .notes-content ul,
        .notes-content li,
        .notes-content p {
            color: #a16207 !important; /* yellow-700 */
            font-family: 'Poppins', sans-serif !important;
        }
        
        /* Ensure proper list styling for notes */
        .notes-content ol {
            list-style-type: decimal !important;
            padding-left: 1.5rem !important;
            margin: 0.5rem 0 !important;
        }
        
        .notes-content ul {
            list-style-type: disc !important;
            padding-left: 1.5rem !important;
            margin: 0.5rem 0 !important;
        }
        
        .notes-content li {
            display: list-item !important;
            margin: 0.25rem 0 !important;
        }
        
        /* Step description styling */
        .step-description {
            font-family: 'Poppins', sans-serif !important;
        }
        
        .step-description ol {
            list-style-type: decimal !important;
            padding-left: 1.5rem !important;
            margin: 0.5rem 0 !important;
        }
        
        .step-description ul {
            list-style-type: disc !important;
            padding-left: 1.5rem !important;
            margin: 0.5rem 0 !important;
        }
        
        .step-description li {
            display: list-item !important;
            margin: 0.25rem 0 !important;
        }
        
        .step-description p {
            margin: 0.5rem 0 !important;
        }
        
        /* Additional custom utilities */
        .text-yellow-700 {
            color: #a16207 !important;
        }
        
        .bg-yellow-50 {
            background-color: #fefce8 !important;
        }
        
        .border-yellow-400 {
            border-color: #facc15 !important;
        }
        
        .text-yellow-800 {
            color: #92400e !important;
        }
    </style>
    </style>
</head>
<body class="bg-white text-gray-900 print:bg-white font-poppins" style="font-family: 'Poppins', sans-serif !important;">
    <div class="max-w-4xl mx-auto p-8">
        <!-- Print Controls (hidden in print) -->
        <div class="no-print mb-6 flex justify-between items-center bg-gray-100 p-4 rounded-lg">
            <div>
                <h2 class="text-lg font-semibold">Preview Mode</h2>
                <p class="text-sm text-gray-600">Siap untuk dicetak atau disimpan sebagai PDF</p>
            </div>
            <div class="space-x-3">
                <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    🖨️ Print
                </button>
                <button onclick="goBack()" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                    ← Kembali
                </button>
            </div>
        </div>

        <!-- Header -->
        <header class="print-header">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="px-4 py-2 rounded-full text-sm font-medium border-2" 
                         style="border-color: {{ $sop->category->color }}; color: {{ $sop->category->color }};">
                        📁 {{ $sop->category->name }}
                    </div>
                </div>
                
                <div class="text-right">
                    <div class="text-sm text-gray-600 mb-1">Version {{ $sop->formatted_version }}</div>
                    @if($sop->is_active)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                            ✓ Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-800">
                            ✗ Tidak Aktif
                        </span>
                    @endif
                </div>
            </div>

            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $sop->title }}</h1>
            
            @if($sop->description)
                <p class="text-gray-700 text-sm leading-relaxed mb-6">{{ $sop->description }}</p>
            @endif

            <!-- Meta Information -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 bg-gray-50 p-4 rounded-lg">
                <div>
                    <span class="text-gray-500 text-sm block mb-1">Dibuat oleh:</span>
                    <div class="font-medium">{{ $sop->creator->name }}</div>
                </div>
                
                <div>
                    <span class="text-gray-500 text-sm block mb-1">Berlaku sejak:</span>
                    <div class="font-medium">{{ $sop->effective_date?->format('d M Y') ?? '-' }}</div>
                </div>
                
                <div>
                    <span class="text-gray-500 text-sm block mb-1">Terakhir diperbarui:</span>
                    <div class="font-medium">{{ $sop->updated_at->format('d M Y') }}</div>
                </div>
                
                <div>
                    <span class="text-gray-500 text-sm block mb-1">Review berikutnya:</span>
                    <div class="font-medium">{{ $sop->next_review_date?->format('d M Y') ?? '-' }}</div>
                </div>
            </div>

            <!-- Keywords -->
            @if($sop->keywords)
                <div class="mt-4">
                    <span class="text-sm font-medium text-gray-700 block mb-2">Kata Kunci:</span>
                    <div class="flex flex-wrap gap-2">
                        @foreach(explode(',', $sop->keywords) as $keyword)
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-300">
                                {{ trim($keyword) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        </header>

        <!-- Steps Section -->
        <section class="mb-8">
            <div class="flex items-center justify-between mb-6 pb-3 border-b-2 border-gray-200">
                <h2 class="text-2xl font-semibold text-gray-900 flex items-center">
                    📋 Langkah-langkah Prosedur
                </h2>
                <span class="text-sm font-medium px-3 py-1 rounded-full bg-blue-100 text-blue-800 border border-blue-300">
                    {{ $sop->steps_count }} Langkah
                </span>
            </div>
            
            @if($sop->steps && count($sop->steps) > 0)
                <div class="space-y-6">
                    @foreach($sop->steps as $index => $step)
                        <div class="step-container avoid-break">
                            <div class="flex">
                                <!-- Step Number -->
                                <div class="mr-6">
                                    <div class="step-number">
                                        {{ $step['step_number'] ?? $index + 1 }}
                                    </div>
                                    @if(!$loop->last)
                                        <div class="w-px h-6 bg-gray-300 mx-auto mt-2"></div>
                                    @endif
                                </div>
                                
                                <!-- Step Content -->
                                <div class="flex-1">
                                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-3">{{ $step['title'] }}</h3>
                                        <div class="text-gray-700 text-sm leading-relaxed step-description mb-3">
                                            {!! $step['description'] !!}
                                        </div>
                                        
                                        @if(!empty($step['notes']))
                                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg mt-4">
                                                <div class="flex items-start">
                                                    <div class="w-4 h-4 mr-3 mt-0.5 flex-shrink-0 text-yellow-600">💡</div>
                                                    <div class="flex-1">
                                                        <p class="text-sm font-medium text-yellow-800 mb-2">Catatan Penting:</p>
                                                        <div class="text-sm leading-relaxed notes-content">
                                                            {!! $step['notes'] !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 text-gray-500">
                    <p class="text-lg">Belum ada langkah prosedur yang ditambahkan.</p>
                </div>
            @endif
        </section>

        <!-- History Section (if applicable) -->
        @if($sop->revisions && $sop->revisions->count() > 0)
            <section class="page-break">
                <div class="mb-6 pb-3 border-b-2 border-gray-200">
                    <h2 class="text-2xl font-semibold text-gray-900">📅 Riwayat Revisi</h2>
                </div>
                
                <div class="space-y-4">
                    @foreach($sop->revisions->take(10) as $revision)
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <span class="font-medium">Version {{ $revision->version }}</span>
                                    <span class="text-gray-500 ml-2">{{ $revision->created_at->format('d M Y, H:i') }}</span>
                                </div>
                                <span class="text-sm text-gray-600">{{ $revision->revisor->name }}</span>
                            </div>
                            @if($revision->revision_notes)
                                <p class="text-sm text-gray-700">{{ $revision->revision_notes }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Footer -->
        <footer class="mt-12 pt-6 border-t border-gray-200 text-center text-sm text-gray-500">
            <p>Dokumen SOP ini dicetak pada {{ now()->format('d M Y, H:i') }} WIB</p>
            <p class="mt-1">© {{ config('app.name') }} - Standard Operating Procedure</p>
        </footer>
    </div>

    <script>
        // Function to handle back button with fallback
        function goBack() {
            // Check if there's history to go back to and if the previous page is from the same domain
            if (window.history.length > 1 && document.referrer && document.referrer.indexOf(window.location.hostname) !== -1) {
                window.history.back();
            } else {
                // Fallback: redirect to Filament SOP resource or admin dashboard
                window.location.href = '/admin/sops';
            }
        }
        
        // Auto print if print parameter is present
        @if($isPrint ?? false)
            window.onload = function() {
                window.print();
            }
        @endif
    </script>
</body>
</html>
