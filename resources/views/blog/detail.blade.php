@extends('layouts.app')

@section('title', $blog->meta_title ?? $blog->title . ' - WOFINS')
@section('meta_description', $blog->meta_description ?? $blog->excerpt)

@section('content')
    @include('front.header')
    <div class="min-h-screen bg-gray-50">
        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-16"
            style="background: linear-gradient(to right, #2563eb, #4338ca);">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <div class="flex items-center justify-center mb-4">
                        @php
                            $categoryColors = [
                                'Tutorial' => 'bg-green-500',
                                'Business' => 'bg-purple-500',
                                'Tips' => 'bg-orange-500',
                                'Keuangan' => 'bg-red-500',
                                'Featured' => 'bg-blue-500',
                            ];
                            $colorClass = $categoryColors[$blog->category] ?? 'bg-gray-500';
                        @endphp
                        <span
                            class="{{ $colorClass }} text-white px-3 py-1 rounded-full text-sm font-medium mr-4">{{ $blog->category }}</span>
                        <span class="text-blue-200">{{ $blog->published_at->format('d M Y') }} • {{ $blog->read_time }} min
                            read</span>
                    </div>
                    <h1 class="text-4xl md:text-5xl font-bold mb-6">
                        {{ $blog->title }}
                    </h1>
                    <p class="text-xl text-blue-100 mb-8">
                        {{ $blog->excerpt }}
                    </p>
                    <div class="flex items-center justify-center">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($blog->author_name) }}&color=ffffff&background=3b82f6&size=50"
                            alt="{{ $blog->author_name }}" class="w-12 h-12 rounded-full mr-4">
                        <div class="text-left">
                            <p class="font-medium">{{ $blog->author_name }}</p>
                            <p class="text-blue-200 text-sm">{{ $blog->author_title }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <!-- Article Content -->
                    <article class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <img src="{{ $blog->featured_image }}" alt="{{ $blog->title }}"
                            class="w-full h-64 md:h-96 object-cover">

                        <div class="p-8 md:p-12">
                            <div class="prose prose-lg max-w-none">
                                {!! $blog->content !!}

                                {{-- <div class="bg-blue-50 border-l-4 border-blue-500 p-6 my-8">
                        <h3 class="text-lg font-semibold text-blue-900 mb-2">Kesimpulan</h3>
                        <p class="text-blue-800">
                            Mengelola budget wedding organizer membutuhkan disiplin, sistem yang baik, dan tools yang tepat. Dengan menerapkan 10 tips di atas, Anda dapat meningkatkan profitabilitas bisnis sambil menjaga kualitas layanan yang prima.
                        </p>
                    </div> --}}
                            </div>
                    </article>

                    <!-- Related Articles -->
                    @if ($relatedPosts->isNotEmpty())
                        <div class="mt-16">
                            <h2 class="text-2xl font-bold text-gray-900 mb-8">Artikel Terkait</h2>
                            <div class="grid grid-cols-1 gap-6">
                                @foreach ($relatedPosts->take(2) as $relatedPost)
                                    <article
                                        class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300">
                                        <div class="flex">
                                            <img src="{{ $relatedPost->featured_image }}" alt="{{ $relatedPost->title }}"
                                                class="w-24 h-24 object-cover">
                                            <div class="p-4 flex-1">
                                                <div class="flex items-center text-gray-500 text-xs mb-2">
                                                    <span>{{ $relatedPost->published_at->format('d M Y') }}</span>
                                                    <span class="mx-1">•</span>
                                                    <span>{{ $relatedPost->read_time }} min</span>
                                                </div>
                                                <h3
                                                    class="text-sm font-bold text-gray-900 mb-2 hover:text-blue-600 transition-colors line-clamp-2">
                                                    <a
                                                        href="{{ route('blog.detail', $relatedPost->slug) }}">{{ $relatedPost->title }}</a>
                                                </h3>
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="sticky top-8 space-y-8">
                        <!-- Author Info -->
                        <div class="bg-white rounded-2xl shadow-lg p-6">
                            <div class="text-center">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($blog->author_name) }}&color=ffffff&background=3b82f6&size=80"
                                    alt="{{ $blog->author_name }}" class="w-20 h-20 rounded-full mx-auto mb-4">
                                <h3 class="text-lg font-bold text-gray-900">{{ $blog->author_name }}</h3>
                                <p class="text-blue-600 text-sm mb-4">{{ $blog->author_title }}</p>
                                <p class="text-gray-600 text-sm">
                                    Expert dalam bidang wedding organizer dan manajemen keuangan. Berbagi tips dan strategi
                                    untuk mengembangkan bisnis wedding organizer yang sukses.
                                </p>
                            </div>
                        </div>

                        <!-- Popular Articles -->
                        @php
                            $popularPosts = App\Models\Blog::where('is_published', true)
                                ->where('id', '!=', $blog->id)
                                ->orderBy('views_count', 'desc')
                                ->take(5)
                                ->get();
                        @endphp

                        @if ($popularPosts->isNotEmpty())
                            <div class="bg-white rounded-2xl shadow-lg p-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Artikel Populer</h3>
                                <div class="space-y-4">
                                    @foreach ($popularPosts as $popularPost)
                                        <div class="flex space-x-3">
                                            <img src="{{ $popularPost->featured_image }}" alt="{{ $popularPost->title }}"
                                                class="w-16 h-16 rounded-lg object-cover">
                                            <div class="flex-1">
                                                <h4
                                                    class="text-sm font-medium text-gray-900 hover:text-blue-600 transition-colors line-clamp-2">
                                                    <a
                                                        href="{{ route('blog.detail', $popularPost->slug) }}">{{ $popularPost->title }}</a>
                                                </h4>
                                                <div class="flex items-center text-gray-500 text-xs mt-1">
                                                    <span>{{ $popularPost->views_count }} views</span>
                                                    <span class="mx-1">•</span>
                                                    <span>{{ $popularPost->read_time }} min</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Categories -->
                        @php
                            $categories = App\Models\Blog::where('is_published', true)
                                ->select('category')
                                ->distinct()
                                ->get()
                                ->pluck('category');
                        @endphp

                        <div class="bg-white rounded-2xl shadow-lg p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Kategori</h3>
                            <div class="space-y-2">
                                @foreach ($categories as $category)
                                    @php
                                        $categoryCount = App\Models\Blog::where('category', $category)
                                            ->where('is_published', true)
                                            ->count();
                                        $categoryColors = [
                                            'Tutorial' => 'bg-green-100 text-green-800 border-green-200',
                                            'Business' => 'bg-purple-100 text-purple-800 border-purple-200',
                                            'Tips' => 'bg-orange-100 text-orange-800 border-orange-200',
                                            'Keuangan' => 'bg-red-100 text-red-800 border-red-200',
                                            'Featured' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        ];
                                        $colorClass =
                                            $categoryColors[$category] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                                    @endphp
                                    <a href="{{ route('blog.category', strtolower($category)) }}"
                                        class="flex items-center justify-between p-2 rounded-lg border {{ $colorClass }} hover:shadow-md transition-all duration-200">
                                        <span class="font-medium">{{ $category }}</span>
                                        <span class="text-xs">{{ $categoryCount }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <!-- Newsletter Signup -->
                        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl shadow-lg p-6 text-white">
                            <h3 class="text-lg font-bold mb-2">Newsletter WOFINS</h3>
                            <p class="text-blue-100 text-sm mb-4">
                                Dapatkan tips terbaru tentang wedding organizer dan manajemen keuangan langsung di inbox
                                Anda.
                            </p>
                            <form class="space-y-3">
                                <input type="email" placeholder="Email Anda"
                                    class="w-full px-3 py-2 rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                                <button type="submit"
                                    class="w-full bg-white text-blue-600 py-2 rounded-lg text-sm font-medium hover:bg-blue-50 transition-colors">
                                    Berlangganan
                                </button>
                            </form>
                        </div>

                        <!-- Share Buttons -->
                        <div class="bg-white rounded-2xl shadow-lg p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Bagikan Artikel</h3>
                            <div class="grid grid-cols-1 gap-2">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                                    target="_blank"
                                    class="flex items-center justify-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M20 10c0-5.523-4.477-10-10-10S0 4.477 0 10c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V10h2.54V7.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V10h2.773l-.443 2.89h-2.33v6.988C16.343 19.128 20 14.991 20 10z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Facebook
                                </a>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($blog->title) }}"
                                    target="_blank"
                                    class="flex items-center justify-center bg-blue-400 text-white px-4 py-2 rounded-lg hover:bg-blue-500 transition-colors text-sm">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M6.29 18.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0020 3.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.073 4.073 0 01.8 7.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 010 16.407a11.616 11.616 0 006.29 1.84">
                                        </path>
                                    </svg>
                                    Twitter
                                </a>
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->url()) }}"
                                    target="_blank"
                                    class="flex items-center justify-center bg-blue-700 text-white px-4 py-2 rounded-lg hover:bg-blue-800 transition-colors text-sm">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.338 16.338H13.67V12.16c0-.995-.017-2.277-1.387-2.277-1.39 0-1.601 1.086-1.601 2.207v4.248H8.014v-8.59h2.559v1.174h.037c.356-.675 1.227-1.387 2.526-1.387 2.703 0 3.203 1.778 3.203 4.092v4.711zM5.005 6.575a1.548 1.548 0 11-.003-3.096 1.548 1.548 0 01.003 3.096zm-1.337 9.763H6.34v-8.59H3.667v8.59zM17.668 1H2.328C1.595 1 1 1.581 1 2.298v15.403C1 18.418 1.595 19 2.328 19h15.34c.734 0 1.332-.582 1.332-1.299V2.298C19 1.581 18.402 1 17.668 1z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    LinkedIn
                                </a>
                                <button
                                    onclick="navigator.share ? navigator.share({title: '{{ $blog->title }}', url: '{{ request()->url() }}'}) : navigator.clipboard.writeText('{{ request()->url() }}')"
                                    class="flex items-center justify-center bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors text-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z">
                                        </path>
                                    </svg>
                                    Share
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Back to Blog -->
            <div class="mt-12 text-center">
                <a href="{{ route('blog') }}"
                    class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Blog
                </a>
            </div>
        </div>
    </div>
    @include('front.footer')
@endsection
