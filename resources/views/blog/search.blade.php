@extends('layouts.app')

@section('title', 'Pencarian: ' . $query . ' - Blog WOFINS')

@section('content')
@include('front.header')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">
                    Hasil Pencarian
                </h1>
                <p class="text-xl text-blue-100">
                    {{ $posts->total() }} artikel ditemukan untuk "<strong>{{ $query }}</strong>"
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Breadcrumb -->
        <nav class="mb-8">
            <ol class="flex items-center space-x-2 text-sm text-gray-500">
                <li><a href="/" class="hover:text-blue-600">Home</a></li>
                <li><span class="mx-2">/</span></li>
                <li><a href="{{ route('blog') }}" class="hover:text-blue-600">Blog</a></li>
                <li><span class="mx-2">/</span></li>
                <li class="text-gray-900">Pencarian</li>
            </ol>
        </nav>

        <!-- Search Bar -->
        <div class="mb-8">
            <form action="{{ route('blog.search') }}" method="GET" class="max-w-md mx-auto">
                <div class="relative">
                    <input type="text" 
                           name="q"
                           placeholder="Cari artikel..." 
                           value="{{ $query }}"
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <button type="submit" class="absolute right-2 top-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Cari
                    </button>
                </div>
            </form>
        </div>

        @if($posts->isNotEmpty())
        <!-- Search Results -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            @foreach($posts as $post)
            <article class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 group">
                <div class="relative overflow-hidden">
                    <img src="{{ $post->featured_image }}" 
                         alt="{{ $post->title }}" 
                         class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                    <div class="absolute top-3 left-3">
                        @php
                        $categoryColors = [
                            'Tutorial' => 'bg-green-500',
                            'Business' => 'bg-purple-500', 
                            'Tips' => 'bg-orange-500',
                            'Keuangan' => 'bg-red-500',
                            'Featured' => 'bg-blue-500'
                        ];
                        $colorClass = $categoryColors[$post->category] ?? 'bg-gray-500';
                        @endphp
                        <span class="{{ $colorClass }} text-white px-2 py-1 rounded-full text-xs font-medium">{{ $post->category }}</span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex items-center text-gray-500 text-sm mb-3">
                        <span>{{ $post->published_at->format('d M Y') }}</span>
                        <span class="mx-2">•</span>
                        <span>{{ $post->read_time }} min read</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors">
                        <a href="{{ route('blog.detail', $post->slug) }}" class="block">{{ $post->title }}</a>
                    </h3>
                    <p class="text-gray-600 mb-4 line-clamp-3">
                        {{ $post->excerpt }}
                    </p>
                    <a href="{{ route('blog.detail', $post->slug) }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm inline-flex items-center">
                        Baca Artikel
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </article>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="flex justify-center">
            {{ $posts->appends(['q' => $query])->links() }}
        </div>
        @else
        <!-- No Results -->
        <div class="text-center py-16">
            <div class="max-w-md mx-auto">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada hasil</h3>
                <p class="text-gray-500 mb-6">Tidak ditemukan artikel yang sesuai dengan pencarian "<strong>{{ $query }}</strong>".</p>
                <div class="space-y-4">
                    <p class="text-sm text-gray-600">Saran:</p>
                    <ul class="text-sm text-gray-500 space-y-1">
                        <li>• Coba kata kunci yang lebih umum</li>
                        <li>• Periksa ejaan kata kunci</li>
                        <li>• Gunakan kata kunci yang berbeda</li>
                    </ul>
                    <a href="{{ route('blog') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Kembali ke Blog
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@include('front.footer')
@endsection
