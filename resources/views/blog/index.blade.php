@extends('layouts.app')

@section('title', 'Blog - WOFINS')

@section('content')
    @include('front.header')
    <div class="min-h-screen bg-gray-50">
        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-16"
            style="background: linear-gradient(to right, #2563eb, #4338ca);">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h1 class="text-4xl md:text-5xl font-bold mb-4">
                        Blog WOFINS
                    </h1>
                    <p class="text-xl md:text-2xl text-blue-100 max-w-3xl mx-auto mb-8">
                        Tips, tutorial, dan insight terbaru seputar manajemen wedding organizer
                    </p>
                    <!-- CTA Buttons -->
                    <div class="flex flex-wrap justify-center gap-4">
                        <a href="{{ route('blog.category', 'tutorial') }}"
                            class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                </path>
                            </svg>
                            Tutorial WOFINS
                        </a>
                        <a href="{{ route('blog.category', 'tips') }}"
                            class="inline-flex items-center px-6 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors font-medium">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z">
                                </path>
                            </svg>
                            Tips & Strategi
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Category Navigation -->
            <div class="mb-8">
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="{{ route('blog') }}"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Semua Artikel
                    </a>
                    @foreach ($categories as $category)
                        @php
                            $categoryColors = [
                                'Tutorial' => 'bg-green-600 hover:bg-green-700',
                                'Business' => 'bg-purple-600 hover:bg-purple-700',
                                'Tips' => 'bg-orange-600 hover:bg-orange-700',
                                'Keuangan' => 'bg-red-600 hover:bg-red-700',
                                'Featured' => 'bg-blue-600 hover:bg-blue-700',
                            ];
                            $colorClass = $categoryColors[$category] ?? 'bg-gray-600 hover:bg-gray-700';
                        @endphp
                        <a href="{{ route('blog.category', strtolower($category)) }}"
                            class="px-4 py-2 {{ $colorClass }} text-white rounded-lg transition-colors">
                            {{ $category }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="lg:grid lg:grid-cols-4 lg:gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-3">
                    @if ($featuredPosts->isNotEmpty())
                        <!-- Featured Article -->
                        <div class="mb-12">
                            @php $featured = $featuredPosts->first() @endphp
                            <div
                                class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300">
                                <div class="relative">
                                    <img src="{{ $featured->featured_image }}" alt="{{ $featured->title }}"
                                        class="w-full h-64 md:h-80 object-cover">
                                    <div class="absolute top-4 left-4">
                                        <span
                                            class="bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-medium">{{ $featured->category }}</span>
                                    </div>
                                </div>
                                <div class="p-6 md:p-8">
                                    <div class="flex items-center text-gray-500 text-sm mb-3">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        <span>{{ $featured->published_at->format('d M Y') }}</span>
                                        <span class="mx-2">•</span>
                                        <span>{{ $featured->read_time }} min read</span>
                                    </div>
                                    <h2
                                        class="text-2xl md:text-3xl font-bold text-gray-900 mb-4 hover:text-blue-600 transition-colors">
                                        <a href="{{ route('blog.detail', $featured->slug) }}"
                                            class="block">{{ $featured->title }}</a>
                                    </h2>
                                    <p class="text-gray-600 text-lg leading-relaxed mb-6">
                                        {{ $featured->excerpt }}
                                    </p>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($featured->author_name) }}&color=ffffff&background=3b82f6&size=40"
                                                alt="{{ $featured->author_name }}" class="w-10 h-10 rounded-full mr-3">
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $featured->author_name }}</p>
                                                <p class="text-gray-500 text-sm">{{ $featured->author_title }}</p>
                                            </div>
                                        </div>
                                        <a href="{{ route('blog.detail', $featured->slug) }}"
                                            class="text-blue-600 hover:text-blue-700 font-medium inline-flex items-center">
                                            Baca Selengkapnya
                                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Articles Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                        @foreach ($recentPosts->skip($featuredPosts->isNotEmpty() ? 1 : 0) as $post)
                            <article
                                class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 group">
                                <div class="relative overflow-hidden">
                                    <img src="{{ $post->featured_image }}" alt="{{ $post->title }}"
                                        class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                                    <div class="absolute top-3 left-3">
                                        @php
                                            $categoryColors = [
                                                'Tutorial' => 'bg-green-500',
                                                'Business' => 'bg-purple-500',
                                                'Tips' => 'bg-orange-500',
                                                'Keuangan' => 'bg-red-500',
                                                'Featured' => 'bg-blue-500',
                                            ];
                                            $colorClass = $categoryColors[$post->category] ?? 'bg-gray-500';
                                        @endphp
                                        <span
                                            class="{{ $colorClass }} text-white px-2 py-1 rounded-full text-xs font-medium">{{ $post->category }}</span>
                                    </div>
                                </div>
                                <div class="p-6">
                                    <div class="flex items-center text-gray-500 text-sm mb-3">
                                        <span>{{ $post->published_at->format('d M Y') }}</span>
                                        <span class="mx-2">•</span>
                                        <span>{{ $post->read_time }} min read</span>
                                    </div>
                                    <h3
                                        class="text-xl font-bold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors">
                                        <a href="{{ route('blog.detail', $post->slug) }}"
                                            class="block">{{ $post->title }}</a>
                                    </h3>
                                    <p class="text-gray-600 mb-4 line-clamp-3">
                                        {{ $post->excerpt }}
                                    </p>
                                    <a href="{{ route('blog.detail', $post->slug) }}"
                                        class="text-blue-600 hover:text-blue-700 font-medium text-sm inline-flex items-center">
                                        Baca Artikel
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <!-- Load More Button -->
                    @if ($recentPosts->count() >= 6)
                        <div class="text-center">
                            <a href="#"
                                class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                Lihat Artikel Lainnya
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1 mt-12 lg:mt-0">
                    <div class="sticky top-24 space-y-8">
                        <!-- Search -->
                        <div class="bg-white rounded-2xl shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Cari Artikel</h3>
                            <form action="{{ route('blog.search') }}" method="GET" class="relative">
                                <input type="text" name="q" placeholder="Cari artikel..."
                                    value="{{ request('q') }}"
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <button type="submit" class="sr-only">Search</button>
                            </form>
                        </div>

                        <!-- Categories -->
                        <div class="bg-white rounded-2xl shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Kategori</h3>
                            <div class="space-y-3">
                                @foreach ($categories as $category)
                                    <a href="{{ route('blog.category', strtolower($category)) }}"
                                        class="flex items-center justify-between text-gray-700 hover:text-blue-600 transition-colors">
                                        <span>{{ $category }}</span>
                                        <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs">
                                            {{ App\Models\Blog::where('category', $category)->where('is_published', true)->count() }}
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <!-- Popular Posts -->
                        <div class="bg-white rounded-2xl shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Artikel Popular</h3>
                            <div class="space-y-4">
                                @php
                                    $popularPosts = App\Models\Blog::where('is_published', true)
                                        ->orderBy('views_count', 'desc')
                                        ->take(3)
                                        ->get();
                                @endphp
                                @foreach ($popularPosts as $popularPost)
                                    <article class="flex space-x-3">
                                        <img src="{{ $popularPost->featured_image }}" alt="{{ $popularPost->title }}"
                                            class="w-16 h-16 object-cover rounded-lg flex-shrink-0">
                                        <div>
                                            <h4
                                                class="text-sm font-medium text-gray-900 hover:text-blue-600 transition-colors">
                                                <a href="{{ route('blog.detail', $popularPost->slug) }}"
                                                    class="block">{{ Str::limit($popularPost->title, 50) }}</a>
                                            </h4>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $popularPost->published_at->format('d M Y') }}</p>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>

                        <!-- Newsletter -->
                        {{-- <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl shadow-lg p-6 text-white">
                            <h3 class="text-lg font-semibold mb-3">Newsletter WOFINS</h3>
                            <p class="text-blue-100 text-sm mb-4">
                                Dapatkan tips terbaru seputar manajemen wedding organizer langsung di inbox Anda.
                            </p>
                            <form class="space-y-3">
                                <input type="email" placeholder="Email Anda"
                                    class="w-full px-4 py-3 rounded-lg text-gray-900 placeholder-gray-500 focus:ring-2 focus:ring-blue-300 focus:outline-none">
                                <button type="submit"
                                    class="w-full bg-white text-blue-600 py-3 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                                    Berlangganan
                                </button>
                            </form>
                        </div> --}}

                        <!-- Tags -->
                        <div class="bg-white rounded-2xl shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tags Popular</h3>
                            <div class="flex flex-wrap gap-2">
                                @php
                                    $popularTags = [
                                        'wedding',
                                        'finance',
                                        'business',
                                        'tutorial',
                                        'tips',
                                        'budget',
                                        'vendor',
                                        'keuangan',
                                    ];
                                @endphp
                                @foreach ($popularTags as $tag)
                                    <a href="{{ route('blog.search', ['q' => $tag]) }}"
                                        class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm hover:bg-blue-100 hover:text-blue-600 cursor-pointer transition-colors">
                                        {{ ucfirst($tag) }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
