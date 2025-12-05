@extends('layouts.app')

@section('title', 'Tutorial - Blog WOFINS')

@section('content')
<script>
    // Redirect to dynamic tutorial category page
    window.location.href = "{{ route('blog.category', 'tutorial') }}";
</script>

<div class="min-h-screen bg-gray-50 flex items-center justify-center">
    <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <p class="text-gray-600">Mengalihkan ke halaman Tutorial...</p>
        <p class="text-sm text-gray-500 mt-2">
            Jika tidak teralihkan otomatis, 
            <a href="{{ route('blog.category', 'tutorial') }}" class="text-blue-600 hover:underline">klik di sini</a>
        </p>
    </div>
</div>
@endsection
            <div class="lg:col-span-3">
                <!-- Tutorial Categories -->
                <div class="mb-8">
                    <div class="flex flex-wrap gap-3">
                        <button class="bg-green-600 text-white px-4 py-2 rounded-full text-sm font-medium">Semua Tutorial</button>
                        <button class="bg-gray-100 text-gray-700 hover:bg-green-100 hover:text-green-600 px-4 py-2 rounded-full text-sm font-medium transition-colors">Setup Awal</button>
                        <button class="bg-gray-100 text-gray-700 hover:bg-green-100 hover:text-green-600 px-4 py-2 rounded-full text-sm font-medium transition-colors">Fitur Lanjutan</button>
                        <button class="bg-gray-100 text-gray-700 hover:bg-green-100 hover:text-green-600 px-4 py-2 rounded-full text-sm font-medium transition-colors">Troubleshooting</button>
                    </div>
                </div>

                <!-- Tutorial Articles -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                    <!-- Tutorial 1 -->
                    <article class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 group">
                        <div class="relative overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1606800052052-a08af7148866?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                                 alt="Setup Tutorial" 
                                 class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute top-3 left-3">
                                <span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs font-medium">Setup Awal</span>
                            </div>
                            <div class="absolute top-3 right-3 bg-black bg-opacity-50 text-white px-2 py-1 rounded text-xs">
                                5 min
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center text-gray-500 text-sm mb-3">
                                <span>29 Agustus 2025</span>
                                <span class="mx-2">•</span>
                                <span>Pemula</span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-green-600 transition-colors">
                                <a href="#" class="block">Cara Setup System WOFINS untuk Pemula</a>
                            </h3>
                            <p class="text-gray-600 mb-4">
                                Panduan lengkap step-by-step untuk memulai menggunakan WOFINS dari awal. Mulai dari registrasi hingga konfigurasi dasar.
                            </p>
                            <div class="flex items-center justify-between">
                                <a href="#" class="text-green-600 hover:text-green-700 font-medium text-sm inline-flex items-center">
                                    Mulai Tutorial
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                                <div class="flex items-center text-gray-500 text-xs">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    1.2k views
                                </div>
                            </div>
                        </div>
                    </article>

                    <!-- Tutorial 2 -->
                    <article class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 group">
                        <div class="relative overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                                 alt="Dashboard Tutorial" 
                                 class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute top-3 left-3">
                                <span class="bg-blue-500 text-white px-2 py-1 rounded-full text-xs font-medium">Dashboard</span>
                            </div>
                            <div class="absolute top-3 right-3 bg-black bg-opacity-50 text-white px-2 py-1 rounded text-xs">
                                8 min
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center text-gray-500 text-sm mb-3">
                                <span>27 Agustus 2025</span>
                                <span class="mx-2">•</span>
                                <span>Pemula</span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-green-600 transition-colors">
                                <a href="#" class="block">Memahami Dashboard dan Fitur Utama</a>
                            </h3>
                            <p class="text-gray-600 mb-4">
                                Tour lengkap dashboard WOFINS dan penjelasan setiap fitur utama yang tersedia untuk mengelola bisnis wedding organizer.
                            </p>
                            <div class="flex items-center justify-between">
                                <a href="#" class="text-green-600 hover:text-green-700 font-medium text-sm inline-flex items-center">
                                    Mulai Tutorial
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                                <div class="flex items-center text-gray-500 text-xs">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    856 views
                                </div>
                            </div>
                        </div>
                    </article>

                    <!-- Tutorial 3 -->
                    <article class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 group">
                        <div class="relative overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1554224155-6726b3ff858f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                                 alt="Project Management" 
                                 class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute top-3 left-3">
                                <span class="bg-purple-500 text-white px-2 py-1 rounded-full text-xs font-medium">Project</span>
                            </div>
                            <div class="absolute top-3 right-3 bg-black bg-opacity-50 text-white px-2 py-1 rounded text-xs">
                                12 min
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center text-gray-500 text-sm mb-3">
                                <span>25 Agustus 2025</span>
                                <span class="mx-2">•</span>
                                <span>Menengah</span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-green-600 transition-colors">
                                <a href="#" class="block">Mengelola Project Wedding dari A-Z</a>
                            </h3>
                            <p class="text-gray-600 mb-4">
                                Tutorial komprehensif untuk mengelola project wedding menggunakan WOFINS. Mulai dari input klien hingga closing project.
                            </p>
                            <div class="flex items-center justify-between">
                                <a href="#" class="text-green-600 hover:text-green-700 font-medium text-sm inline-flex items-center">
                                    Mulai Tutorial
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                                <div class="flex items-center text-gray-500 text-xs">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    2.1k views
                                </div>
                            </div>
                        </div>
                    </article>

                    <!-- Tutorial 4 -->
                    <article class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 group">
                        <div class="relative overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1554224154-26032fced8bd?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                                 alt="Financial Reports" 
                                 class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute top-3 left-3">
                                <span class="bg-red-500 text-white px-2 py-1 rounded-full text-xs font-medium">Keuangan</span>
                            </div>
                            <div class="absolute top-3 right-3 bg-black bg-opacity-50 text-white px-2 py-1 rounded text-xs">
                                15 min
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center text-gray-500 text-sm mb-3">
                                <span>23 Agustus 2025</span>
                                <span class="mx-2">•</span>
                                <span>Lanjutan</span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-green-600 transition-colors">
                                <a href="#" class="block">Membuat Laporan Keuangan yang Akurat</a>
                            </h3>
                            <p class="text-gray-600 mb-4">
                                Panduan detail untuk menggunakan fitur laporan keuangan WOFINS dan menginterpretasi data untuk business insight.
                            </p>
                            <div class="flex items-center justify-between">
                                <a href="#" class="text-green-600 hover:text-green-700 font-medium text-sm inline-flex items-center">
                                    Mulai Tutorial
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                                <div class="flex items-center text-gray-500 text-xs">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 616 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    674 views
                                </div>
                            </div>
                        </div>
                    </article>
                </div>

                <!-- Pagination -->
                <div class="flex justify-center">
                    <nav class="flex items-center space-x-2">
                        <button class="px-3 py-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <button class="px-4 py-2 bg-green-600 text-white rounded-lg font-medium">1</button>
                        <button class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">2</button>
                        <button class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">3</button>
                        <button class="px-3 py-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 mt-12 lg:mt-0">
                <div class="sticky top-24 space-y-8">
                    <!-- Tutorial Progress -->
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Progress Tutorial</h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Setup Awal</span>
                                <span class="text-xs text-green-600 font-medium">Selesai</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: 100%"></div>
                            </div>
                            
                            <div class="flex items-center justify-between mt-4">
                                <span class="text-sm text-gray-600">Fitur Dasar</span>
                                <span class="text-xs text-blue-600 font-medium">75%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: 75%"></div>
                            </div>
                            
                            <div class="flex items-center justify-between mt-4">
                                <span class="text-sm text-gray-600">Fitur Lanjutan</span>
                                <span class="text-xs text-orange-600 font-medium">25%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-orange-600 h-2 rounded-full" style="width: 25%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Start</h3>
                        <div class="space-y-3">
                            <a href="#" class="flex items-center p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                                <div class="flex-shrink-0 w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center">
                                    <span class="text-white text-sm font-bold">1</span>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">Registrasi Akun</p>
                                    <p class="text-xs text-gray-500">2 menit</p>
                                </div>
                            </a>
                            
                            <a href="#" class="flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                                    <span class="text-white text-sm font-bold">2</span>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">Setup Profil</p>
                                    <p class="text-xs text-gray-500">5 menit</p>
                                </div>
                            </a>
                            
                            <a href="#" class="flex items-center p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                                <div class="flex-shrink-0 w-8 h-8 bg-purple-600 rounded-lg flex items-center justify-center">
                                    <span class="text-white text-sm font-bold">3</span>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">Project Pertama</p>
                                    <p class="text-xs text-gray-500">10 menit</p>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Help & Support -->
                    <div class="bg-gradient-to-br from-green-600 to-teal-700 rounded-2xl shadow-lg p-6 text-white">
                        <h3 class="text-lg font-semibold mb-3">Butuh Bantuan?</h3>
                        <p class="text-green-100 text-sm mb-4">
                            Tim support kami siap membantu Anda 24/7 melalui live chat atau email.
                        </p>
                        <button class="w-full bg-white text-green-600 py-3 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                            Hubungi Support
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
