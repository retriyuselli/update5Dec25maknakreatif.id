@extends('layouts.app')

@section('title', $blog->meta_title ?? $blog->title . ' - WOFINS')
@section('meta_description', $blog->meta_description ?? $blog->excerpt)

@push('styles')
    <style>
        .prose h1 {
            font-size: 1.8em;
            margin-top: 0;
            margin-bottom: 0.8em;
            line-height: 1.2;
            font-weight: 800;
        }

        .prose h2 {
            font-size: 1.5em;
            margin-top: 1.4em;
            margin-bottom: 0.8em;
            line-height: 1.35;
            font-weight: 700;
        }

        .prose h3 {
            font-size: 1.25em;
            margin-top: 1.3em;
            margin-bottom: 0.6em;
            line-height: 1.5;
            font-weight: 600;
        }

        .prose h4 {
            font-size: 1.1em;
            margin-top: 1.2em;
            margin-bottom: 0.5em;
            line-height: 1.5;
            font-weight: 600;
        }

        .prose p {
            margin-bottom: 1.1em;
            line-height: 1.7;
            color: #374151;
            font-size: 0.95em;
        }

        .prose ul {
            list-style-type: disc;
            padding-left: 1.625em;
            margin-bottom: 1.25em;
            margin-top: 1.25em;
        }

        .prose ol {
            list-style-type: decimal;
            padding-left: 1.625em;
            margin-bottom: 1.25em;
            margin-top: 1.25em;
        }

        .prose li {
            margin-bottom: 0.5em;
            padding-left: 0.375em;
        }

        .prose li p {
            margin-bottom: 0.75em;
        }

        .prose strong {
            font-weight: 600;
            color: #111827;
        }

        .prose a {
            color: #2563eb;
            text-decoration: underline;
            font-weight: 500;
        }

        .prose blockquote {
            font-weight: 500;
            font-style: italic;
            color: #111827;
            border-left-width: 0.25rem;
            border-left-color: #e5e7eb;
            margin-top: 1.6em;
            margin-bottom: 1.6em;
            padding-left: 1em;
        }

        .prose img {
            margin-top: 2em;
            margin-bottom: 2em;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .prose hr {
            border-color: #e5e7eb;
            margin-top: 3em;
            margin-bottom: 3em;
        }
    </style>
@endpush

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
                            <div class="prose max-w-none text-gray-700">
                                {!! $blog->content !!}
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
                                <h3 class="text-xl font-bold text-gray-900">{{ $blog->author_name }}</h3>
                                <p class="text-blue-600 font-medium mb-4">{{ $blog->author_title }}</p>
                                <p class="text-gray-600 text-sm leading-relaxed">
                                    {{ $blog->author_bio ?? 'Penulis profesional yang berpengalaman di bidang manajemen pernikahan dan keuangan.' }}
                                </p>
                            </div>
                        </div>

                        <!-- Newsletter -->
                        <div class="bg-blue-600 rounded-2xl shadow-lg p-6 text-white">
                            <h3 class="text-xl font-bold mb-4">Berlangganan Newsletter</h3>
                            <p class="text-blue-100 text-sm mb-6">Dapatkan tips dan artikel terbaru langsung di inbox Anda.
                            </p>
                            <form class="space-y-4">
                                <input type="email" placeholder="Alamat email Anda"
                                    class="w-full px-4 py-2 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-400 focus:outline-none">
                                <button type="submit"
                                    class="w-full bg-white text-blue-600 font-bold py-2 rounded-lg hover:bg-blue-50 transition-colors">
                                    Berlangganan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('front.footer')
    </div>
@endsection
