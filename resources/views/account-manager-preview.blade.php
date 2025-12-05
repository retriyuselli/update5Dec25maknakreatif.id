<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Report Account Manager</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'poppins': ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        'theme': '#2D7CFE',
                        'title': '#111111',
                        'body': '#6E6E6E',
                        'smoke': '#f3f3f3',
                        'smoke-dark': '#E1ECFF',
                        'light': '#72849B',
                        'border': '#E3E3E3',
                    }
                }
            }
        }
    </script>
</head>
<body class="font-poppins m-0 p-5 bg-gradient-to-br from-theme to-black text-body text-sm leading-relaxed">
    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-2xl overflow-hidden">
        <!-- Header -->
        <div class="bg-black text-white p-8 text-center">
            <h1 class="text-3xl font-bold mb-2">Preview Report Account Manager</h1>
            <p class="text-base opacity-90">Review laporan sebelum mendownload file HTML</p>
        </div>
        
        <!-- Content -->
        <div class="p-0">
            {!! $previewContent !!}
        </div>
        
        <!-- Actions -->
        <div class="bg-smoke p-6 border-t border-border flex flex-wrap justify-center gap-4">
            <a href="{{ route('account-manager.report.html', ['userId' => request('userId'), 'month' => request('month'), 'year' => request('year')]) }}" 
               class="inline-flex items-center gap-2 px-6 py-3 bg-theme text-white rounded-lg font-semibold transition-all duration-200 hover:bg-blue-600 hover:-translate-y-0.5 shadow-lg hover:shadow-xl" 
               target="_blank">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                Preview Report
            </a>
            <a href="{{ route('account-manager.report.pdf.download', ['userId' => request('userId'), 'month' => request('month'), 'year' => request('year')]) }}" 
               class="inline-flex items-center gap-2 px-6 py-3 bg-title text-white rounded-lg font-semibold transition-all duration-200 hover:bg-gray-800 hover:-translate-y-0.5 shadow-lg hover:shadow-xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Download PDF
            </a>
            <button onclick="window.close()" 
                    class="inline-flex items-center gap-2 px-6 py-3 bg-light text-white rounded-lg font-semibold transition-all duration-200 hover:bg-gray-600 hover:-translate-y-0.5 shadow-lg hover:shadow-xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Tutup
            </button>
        </div>
    </div>

    <script>
        // Auto focus pada halaman ketika dibuka
        window.focus();
        
        // Handle Preview Report button click (use bg-theme class selector)
        document.querySelector('a[class*="bg-theme"]').addEventListener('click', function() {
            // Optional: Show loading state
            this.style.opacity = '0.7';
            this.innerHTML = '<span class="flex items-center gap-2"><svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Loading Preview...</span>';
            
            // Reset after a short delay (the HTML report will open in new tab)
            setTimeout(() => {
                this.style.opacity = '1';
                this.innerHTML = `
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Preview Report
                `;
            }, 1500);
        });

        // Handle PDF download button click (use bg-title class selector)
        document.querySelector('a[class*="bg-title"]').addEventListener('click', function() {
            // Optional: Show loading state
            this.style.opacity = '0.7';
            this.innerHTML = '<span class="flex items-center gap-2"><svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Downloading...</span>';
            
            setTimeout(() => {
                this.style.opacity = '1';
                this.innerHTML = `
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download PDF
                `;
            }, 2000);
        });
    </script>
</body>
</html>