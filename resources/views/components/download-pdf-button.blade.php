@props([
    'route' => '#', // Default route jika tidak diberikan
    'label' => 'Download PDF' // Default label tombol
])

<div>
    <a href="{{ $route }}"
    class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md text-xs font-medium shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 print:hidden">
        {{-- Ganti ikon jika perlu --}}
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
        </svg>
        {{ $label }}
    </a>
</div>

