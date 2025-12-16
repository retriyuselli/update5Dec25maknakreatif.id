@extends('layouts.app')

@section('title', 'WOFINS - Portal Internal WO')

@section('content')
    <div class="min-h-screen bg-white">
        <!-- Navigation Header -->
        @include('front.header')

        <!-- Hero Section -->
        <section class="pt-16 pb-20 sm:pt-20 sm:pb-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="lg:grid lg:grid-cols-12 lg:gap-8 items-center">
                    <!-- Hero Text -->
                    <div class="lg:col-span-6">
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 leading-tight">
                            Platform operasional <span class="text-blue-600">terintegrasi</span> untuk wedding organizer
                            modern
                        </h1>
                        <p class="mt-6 text-lg text-gray-600 leading-relaxed">
                            Sederhanakan approval, kelola vendor & keuangan, dan pantau kinerja tim dalam satu tempat.
                        </p>

                        <!-- CTA Buttons -->
                        <div class="mt-8 flex flex-col sm:flex-row gap-4">
                            <a href="{{ route('pendaftaran') }}"
                                class="inline-flex items-center justify-center px-6 py-3 text-base font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 transition-colors">
                                Mulai Gratis
                                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </a>
                            <a href="#demo"
                                class="inline-flex items-center justify-center px-6 py-3 text-base font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                Lihat Demo
                            </a>
                        </div>

                        <!-- Trust Indicators -->
                        <div class="mt-8 flex items-center space-x-6 text-sm text-black">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span>Setup 5 menit</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span>Tidak perlu kartu kredit</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span>Support 24/7</span>
                            </div>
                        </div>
                    </div>

                    <!-- Hero Visual/Dashboard Mockup -->
                    <div class="mt-12 lg:mt-0 lg:col-span-6">
                        <div class="relative">
                            <!-- Main Dashboard Card -->
                            <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden">
                                <!-- Dashboard Header -->
                                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                                    <div class="flex items-center justify-between">
                                        <div class="text-black">
                                            <h3 class="font-semibold text-black">Dashboard Operasional</h3>
                                            <p class="text-black text-sm">Real-time overview</p>
                                        </div>
                                        <div class="flex space-x-1">
                                            <div class="w-3 h-3 bg-red-400 rounded-full"></div>
                                            <div class="w-3 h-3 bg-yellow-400 rounded-full"></div>
                                            <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dashboard Content -->
                                <div class="p-6">
                                    <!-- Stats Cards -->
                                    <div class="grid grid-cols-2 gap-4 mb-6">
                                        <div class="bg-green-50 p-4 rounded-lg">
                                            <div class="text-2xl font-bold text-green-600">Rp 325M</div>
                                            <div class="text-sm text-green-700">Budget Tersisa</div>
                                            <div class="mt-2 w-full bg-green-200 rounded-full h-2">
                                                <div class="bg-green-500 h-2 rounded-full" style="width: 75%"></div>
                                            </div>
                                        </div>
                                        <div class="bg-blue-50 p-4 rounded-lg">
                                            <div class="text-2xl font-bold text-blue-600">18</div>
                                            <div class="text-sm text-blue-700">Event Aktif</div>
                                            <div class="mt-2 flex items-center text-xs text-blue-600">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                +12% dari bulan lalu
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pending Approvals -->
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <h4 class="font-semibold text-gray-900 mb-3">Menunggu Approval</h4>
                                        <div class="space-y-3">
                                            <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                                                <div>
                                                    <div class="font-medium text-gray-900">Dokumentasi Wedding</div>
                                                    <div class="text-sm text-gray-600">Vendor: Foto Studio ABC</div>
                                                </div>
                                                <div class="text-right">
                                                    <div class="font-semibold text-gray-900">Rp 7.5M</div>
                                                    <span
                                                        class="inline-flex px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                                        Pending
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                                <div>
                                                    <div class="font-medium text-gray-900">Dekorasi Event</div>
                                                    <div class="text-sm text-gray-600">Vendor: Dekor Nusantara</div>
                                                </div>
                                                <div class="text-right">
                                                    <div class="font-semibold text-gray-900">Rp 12M</div>
                                                    <span
                                                        class="inline-flex px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                                        Approved
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Floating Notification -->
                            <div
                                class="absolute -top-20 md:-top-10 -right-4 bg-blue-600 text-white p-3 rounded-lg shadow-lg max-w-xs z-20">
                                <div class="flex items-start space-x-2">
                                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 2L3 7v11a1 1 0 001 1h3v-7h6v7h3a1 1 0 001-1V7l-7-5z" />
                                    </svg>
                                    <div>
                                        <div class="font-medium text-sm">Approval Baru</div>
                                        <div class="text-xs text-blue-100">3 nota dinas memerlukan review</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Logo Band / Trusted By -->
        <section class="py-12 bg-gray-50 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-8">
                    <p class="text-sm font-medium text-black uppercase tracking-wide">
                        PT. Makna Kreatif Indonesia telah banyak melakukan kerjasama dengan berbagai perusahaan <br>
                        industri pernikahan di Sumatera Selatan dan sekitarnya, antara lain:
                    </p>
                </div>

                <!-- Single Row - Moving Left -->
                <div class="relative overflow-hidden">
                    <!-- Container with width that shows exactly 6 logos -->
                    <div class="w-full max-w-5xl mx-auto">
                        <div class="animate-scroll-left flex space-x-12">
                            <!-- First set of all logos -->
                            <div class="flex space-x-12 flex-shrink-0">
                                @if (isset($topRowLogos) && $topRowLogos->count() > 0)
                                    @foreach ($topRowLogos as $logo)
                                        <img src="{{ $logo->logo_url }}" alt="{{ $logo->alt_text ?: $logo->company_name }}"
                                            title="{{ $logo->company_name }}"
                                            class="h-16 w-auto opacity-60 hover:opacity-100 transition-opacity flex-shrink-0" />
                                    @endforeach
                                    @if (isset($bottomRowLogos) && $bottomRowLogos->count() > 0)
                                        @foreach ($bottomRowLogos as $logo)
                                            <img src="{{ $logo->logo_url }}"
                                                alt="{{ $logo->alt_text ?: $logo->company_name }}"
                                                title="{{ $logo->company_name }}"
                                                class="h-16 w-auto opacity-60 hover:opacity-100 transition-opacity flex-shrink-0" />
                                        @endforeach
                                    @endif
                                @else
                                    <!-- Fallback logos if no data - All 12 logos -->
                                    <img src="https://logo.clearbit.com/google.com" alt="Google"
                                        class="h-16 w-auto opacity-60 hover:opacity-100 transition-opacity flex-shrink-0" />
                                    <img src="https://logo.clearbit.com/microsoft.com" alt="Microsoft"
                                        class="h-16 w-auto opacity-60 hover:opacity-100 transition-opacity flex-shrink-0" />
                                    <img src="https://logo.clearbit.com/apple.com" alt="Apple"
                                        class="h-16 w-auto opacity-60 hover:opacity-100 transition-opacity flex-shrink-0" />
                                    <img src="https://logo.clearbit.com/amazon.com" alt="Amazon"
                                        class="h-16 w-auto opacity-60 hover:opacity-100 transition-opacity flex-shrink-0" />
                                    <img src="https://logo.clearbit.com/netflix.com" alt="Netflix"
                                        class="h-16 w-auto opacity-60 hover:opacity-100 transition-opacity flex-shrink-0" />
                                    <img src="https://logo.clearbit.com/spotify.com" alt="Spotify"
                                        class="h-16 w-auto opacity-60 hover:opacity-100 transition-opacity flex-shrink-0" />
                                    <img src="https://logo.clearbit.com/tesla.com" alt="Tesla"
                                        class="h-16 w-auto opacity-60 hover:opacity-100 transition-opacity flex-shrink-0" />
                                    <img src="https://logo.clearbit.com/meta.com" alt="Meta"
                                        class="h-16 w-auto opacity-60 hover:opacity-100 transition-opacity flex-shrink-0" />
                                    <img src="https://logo.clearbit.com/adobe.com" alt="Adobe"
                                        class="h-16 w-auto opacity-60 hover:opacity-100 transition-opacity flex-shrink-0" />
                                    <img src="https://logo.clearbit.com/slack.com" alt="Slack"
                                        class="h-16 w-auto opacity-60 hover:opacity-100 transition-opacity flex-shrink-0" />
                                    <img src="https://logo.clearbit.com/zoom.us" alt="Zoom"
                                        class="h-16 w-auto opacity-60 hover:opacity-100 transition-opacity flex-shrink-0" />
                                    <img src="https://logo.clearbit.com/shopify.com" alt="Shopify"
                                        class="h-16 w-auto opacity-60 hover:opacity-100 transition-opacity flex-shrink-0" />
                                @endif
                            </div>
                            <!-- Duplicate set for seamless loop -->
                            <div class="flex space-x-12 flex-shrink-0">
                                @if (isset($topRowLogos) && $topRowLogos->count() > 0)
                                    @foreach ($topRowLogos as $logo)
                                        <img src="{{ $logo->logo_url }}"
                                            alt="{{ $logo->alt_text ?: $logo->company_name }}"
                                            title="{{ $logo->company_name }}"
                                            class="h-16 w-auto opacity-60 hover:opacity-100 transition-opacity flex-shrink-0" />
                                    @endforeach
                                    @if (isset($bottomRowLogos) && $bottomRowLogos->count() > 0)
                                        @foreach ($bottomRowLogos as $logo)
                                            <img src="{{ $logo->logo_url }}"
                                                alt="{{ $logo->alt_text ?: $logo->company_name }}"
                                                title="{{ $logo->company_name }}"
                                                class="h-16 w-auto opacity-60 hover:opacity-100 transition-opacity flex-shrink-0" />
                                        @endforeach
                                    @endif
                                @else
                                    <!-- Fallback logos if no data - All 12 logos duplicate -->
                                    <img src="https://logo.clearbit.com/google.com" alt="Google"
                                        class="h-16 w-auto opacity-60 hover:opacity-100 transition-opacity flex-shrink-0" />
                                    <img src="https://logo.clearbit.com/microsoft.com" alt="Microsoft"
                                        class="h-16 w-auto opacity-60 hover:opacity-100 transition-opacity flex-shrink-0" />
                                    <img src="https://logo.clearbit.com/apple.com" alt="Apple"
                                        class="h-16 w-auto opacity-60 hover:opacity-100 transition-opacity flex-shrink-0" />
                                    <img src="https://logo.clearbit.com/amazon.com" alt="Amazon"
                                        class="h-16 w-auto opacity-60 hover:opacity-100 transition-opacity flex-shrink-0" />
                                    <img src="https://logo.clearbit.com/netflix.com" alt="Netflix"
                                        class="h-16 w-auto opacity-60 hover:opacity-100 transition-opacity flex-shrink-0" />
                                    <img src="https://logo.clearbit.com/spotify.com" alt="Spotify"
                                        class="h-16 w-auto opacity-60 hover:opacity-100 transition-opacity flex-shrink-0" />
                                    <img src="https://logo.clearbit.com/tesla.com" alt="Tesla"
                                        class="h-16 w-auto opacity-60 hover:opacity-100 transition-opacity flex-shrink-0" />
                                    <img src="https://logo.clearbit.com/meta.com" alt="Meta"
                                        class="h-16 w-auto opacity-60 hover:opacity-100 transition-opacity flex-shrink-0" />
                                    <img src="https://logo.clearbit.com/adobe.com" alt="Adobe"
                                        class="h-16 w-auto opacity-60 hover:opacity-100 transition-opacity flex-shrink-0" />
                                    <img src="https://logo.clearbit.com/slack.com" alt="Slack"
                                        class="h-16 w-auto opacity-60 hover:opacity-100 transition-opacity flex-shrink-0" />
                                    <img src="https://logo.clearbit.com/zoom.us" alt="Zoom"
                                        class="h-16 w-auto opacity-60 hover:opacity-100 transition-opacity flex-shrink-0" />
                                    <img src="https://logo.clearbit.com/shopify.com" alt="Shopify"
                                        class="h-16 w-auto opacity-60 hover:opacity-100 transition-opacity flex-shrink-0" />
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </section>

        <!-- Feature Tabs Section -->
        <section id="features" class="py-20" x-data="{ activeTab: 'finance' }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900">
                        Lebih praktis dengan database online yang terintegrasi
                    </h2>
                    <p class="mt-4 text-lg text-gray-600">
                        Kelola semua aspek operasional wedding organizer dalam satu platform yang mudah digunakan.
                    </p>
                </div>

                <!-- Tab Navigation -->
                <div class="flex flex-wrap justify-center gap-4 mb-12">
                    <button @click="activeTab = 'finance'"
                        :class="activeTab === 'finance' ? 'bg-blue-600 text-white' :
                            'bg-white text-gray-700 border border-gray-200'"
                        class="px-6 py-3 rounded-lg font-medium transition-colors hover:bg-blue-50">
                        Manajemen Keuangan
                    </button>
                    <button @click="activeTab = 'vendor'"
                        :class="activeTab === 'vendor' ? 'bg-blue-600 text-white' :
                            'bg-white text-gray-700 border border-gray-200'"
                        class="px-6 py-3 rounded-lg font-medium transition-colors hover:bg-blue-50">
                        Kelola Vendor
                    </button>
                    <button @click="activeTab = 'team'"
                        :class="activeTab === 'team' ? 'bg-blue-600 text-white' :
                            'bg-white text-gray-700 border border-gray-200'"
                        class="px-6 py-3 rounded-lg font-medium transition-colors hover:bg-blue-50">
                        Manajemen Tim
                    </button>
                    <button @click="activeTab = 'reports'"
                        :class="activeTab === 'reports' ? 'bg-blue-600 text-white' :
                            'bg-white text-gray-700 border border-gray-200'"
                        class="px-6 py-3 rounded-lg font-medium transition-colors hover:bg-blue-50">
                        Laporan & Analytics
                    </button>
                </div>

                <!-- Tab Content -->
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <!-- Content -->
                    <div>
                        <!-- Finance Tab -->
                        <div x-show="activeTab === 'finance'" x-cloak>
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">
                                Mengelola data keuangan untuk sebuah project
                            </h3>
                            <p class="text-gray-600 mb-6">
                                Kontrol penuh atas arus kas dengan sistem approval berjenjang, tracking budget real-time,
                                dan otomasi pembayaran vendor.
                            </p>
                            <ul class="space-y-4">
                                <li class="flex items-start space-x-3">
                                    <svg class="w-6 h-6 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <div>
                                        <div class="font-medium text-gray-900">Nota Dinas Digital</div>
                                        <div class="text-gray-600">Pengajuan dana terstruktur dengan approval otomatis
                                        </div>
                                    </div>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <svg class="w-6 h-6 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <div>
                                        <div class="font-medium text-gray-900">Budget Tracking</div>
                                        <div class="text-gray-600">Monitor penggunaan budget per event secara real-time
                                        </div>
                                    </div>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <svg class="w-6 h-6 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <div>
                                        <div class="font-medium text-gray-900">Payment Schedule</div>
                                        <div class="text-gray-600">Jadwal pembayaran otomatis ke vendor</div>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <!-- Vendor Tab -->
                        <div x-show="activeTab === 'vendor'" x-cloak>
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">
                                Direktori vendor yang terorganisir
                            </h3>
                            <p class="text-gray-600 mb-6">
                                Database lengkap vendor dengan rating, portofolio, dan riwayat kerjasama untuk decision
                                making yang lebih baik.
                            </p>
                            <ul class="space-y-4">
                                <li class="flex items-start space-x-3">
                                    <svg class="w-6 h-6 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <div>
                                        <div class="font-medium text-gray-900">Vendor Profiling</div>
                                        <div class="text-gray-600">Profil lengkap dengan portofolio dan rating</div>
                                    </div>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <svg class="w-6 h-6 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <div>
                                        <div class="font-medium text-gray-900">Performance Tracking</div>
                                        <div class="text-gray-600">Evaluasi kinerja dan riwayat kerjasama</div>
                                    </div>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <svg class="w-6 h-6 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <div>
                                        <div class="font-medium text-gray-900">Contract Management</div>
                                        <div class="text-gray-600">Kelola kontrak dan dokumen vendor</div>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <!-- Team Tab -->
                        <div x-show="activeTab === 'team'" x-cloak>
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">
                                Koordinasi tim yang lebih efektif
                            </h3>
                            <p class="text-gray-600 mb-6">
                                Manajemen role, task assignment, dan real-time collaboration untuk tim yang lebih produktif.
                            </p>
                            <ul class="space-y-4">
                                <li class="flex items-start space-x-3">
                                    <svg class="w-6 h-6 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <div>
                                        <div class="font-medium text-gray-900">Role & Permission</div>
                                        <div class="text-gray-600">Akses kontrol sesuai tanggung jawab</div>
                                    </div>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <svg class="w-6 h-6 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <div>
                                        <div class="font-medium text-gray-900">Task Management</div>
                                        <div class="text-gray-600">Assignment dan tracking progress task</div>
                                    </div>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <svg class="w-6 h-6 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <div>
                                        <div class="font-medium text-gray-900">Communication Hub</div>
                                        <div class="text-gray-600">Chat dan notifikasi real-time</div>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <!-- Reports Tab -->
                        <div x-show="activeTab === 'reports'" x-cloak>
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">
                                Insights dan analytics mendalam
                            </h3>
                            <p class="text-gray-600 mb-6">
                                Dashboard analytics dengan visualisasi data untuk decision making berbasis data.
                            </p>
                            <ul class="space-y-4">
                                <li class="flex items-start space-x-3">
                                    <svg class="w-6 h-6 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <div>
                                        <div class="font-medium text-gray-900">Financial Reports</div>
                                        <div class="text-gray-600">Laporan keuangan per event dan periode</div>
                                    </div>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <svg class="w-6 h-6 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <div>
                                        <div class="font-medium text-gray-900">Performance Analytics</div>
                                        <div class="text-gray-600">Analisis performa tim dan vendor</div>
                                    </div>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <svg class="w-6 h-6 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <div>
                                        <div class="font-medium text-gray-900">Export & Integration</div>
                                        <div class="text-gray-600">Export ke Excel dan integrasi sistem akuntansi</div>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <div class="mt-8">
                            <a href="/register"
                                class="inline-flex items-center px-6 py-3 text-base font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 transition-colors">
                                Coba Fitur Ini Gratis
                                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Visual -->
                    <div>
                        <div
                            class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-2xl p-8 h-96 flex items-center justify-center">
                            <div class="text-center text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <p class="text-lg font-medium">Interactive Feature Demo</p>
                                <p class="text-sm">Visualisasi fitur akan ditampilkan di sini</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- HR Solution Section -->
        <section class="py-20 bg-gray-50 relative overflow-hidden">
            <!-- Background Decorative Elements -->
            <div class="absolute inset-0">
                <!-- Floating icons background -->
                <div class="absolute top-20 right-20 text-pink-200 opacity-50">
                    <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z" />
                        <path d="M10.05 17.3L7.75 15L9.17 13.59L10.05 14.47L14.83 9.69L16.24 11.11L10.05 17.3Z" />
                    </svg>
                </div>
                <div class="absolute top-40 right-80 text-red-300 opacity-40">
                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm0-14c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6zm0 10c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm0-6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z" />
                    </svg>
                </div>
                <div
                    class="absolute bottom-20 right-40 w-8 h-8 bg-gradient-to-br from-red-300 to-red-400 rounded transform rotate-45 opacity-50">
                </div>
                <div
                    class="absolute bottom-40 right-20 w-6 h-6 bg-gradient-to-br from-blue-300 to-blue-400 rounded-full opacity-40">
                </div>
                <div class="absolute top-60 right-60 text-blue-300 opacity-30">
                    <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M5,5H10V10H5V5M14,5H19V10H14V5M14,14H19V19H14V14M5,14H10V19H5V14Z" />
                    </svg>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
                <!-- Main Container with rounded background -->
                <div class="bg-white rounded-3xl shadow-lg p-8 lg:p-12">
                    <div class="lg:grid lg:grid-cols-12 lg:gap-12 items-center">
                        <!-- Content -->
                        <div class="lg:col-span-6">
                            <h2 class="text-4xl sm:text-5xl font-bold text-gray-900 leading-tight mb-6">
                                Satu solusi untuk semua kebutuhan
                                <span class="text-gray-700">HR Anda</span>
                            </h2>
                            <p class="text-lg text-gray-600 leading-relaxed mb-8">
                                Optimalkan pengelolaan operasi HR Anda dengan bantuan solusi terintegrasi dari Mekari
                                Talenta.
                            </p>

                            <!-- CTA Buttons -->
                            <div class="flex flex-col sm:flex-row gap-4">
                                <a href="https://wa.me/6281234567890?text=Halo,%20saya%20ingin%20tahu%20lebih%20lanjut%20tentang%20solusi%20HR%20Mekari%20Talenta"
                                    target="_blank"
                                    class="inline-flex items-center justify-center px-6 py-3 text-base font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-lg">
                                    <svg class="mr-2 w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.087" />
                                    </svg>
                                    WhatsApp sales
                                </a>
                                <a href="/register"
                                    class="inline-flex items-center justify-center px-6 py-3 text-base font-medium text-blue-600 bg-white border-2 border-blue-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 transition-all duration-200 shadow-sm">
                                    Coba gratis
                                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <!-- Visual/Illustration -->
                        <div class="mt-12 lg:mt-0 lg:col-span-6">
                            <div class="relative">
                                <!-- Main image -->
                                <div class="flex justify-center">
                                    <img src="/images/excited-asian-colleagues-looking-laptop-screen-together-office.png"
                                        alt="Excited Asian Colleagues Looking at Laptop Screen Together in Office"
                                        class="w-full max-w-lg h-auto hover:scale-105 transition-transform duration-300"
                                        loading="lazy">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="py-20 bg-gray-50" id="faq">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-12">
                    <!-- Left Column - FAQ Title and Description -->
                    <div>
                        <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-6">
                            Apa itu software WOFINS?
                        </h2>
                        <div class="prose prose-gray">
                            <p class="text-gray-600 mb-6">
                                Aplikasi atau software WOFINS adalah platform pengelolaan operasional wedding organizer
                                berbasis informasi untuk membantu perusahaan event mengelola nota dinas, vendor management,
                                keuangan, tim koordinasi, hingga proses perencanaan dan eksekusi event, serta seluruh
                                operasional wedding organizer lainnya sehari-hari.
                            </p>
                            <p class="text-gray-600 mb-6">
                                WOFINS sendiri merupakan akronim dari Wedding Organizer Financial Information System. Pada
                                umumnya WOFINS berbasis komputasi cloud dan dapat diakses secara online dan offline. Dengan
                                menggunakan software WOFINS, wedding organizer dapat lebih fokus pada proses kreatif dan
                                pertumbuhan bisnis event.
                            </p>
                            <p class="text-gray-600 mb-8">
                                Salah satu contoh aplikasi WOFINS adalah https://demo-wofins.com yang dikembangkan berbasis
                                komputasi cloud. Aplikasi demo wofins menawarkan fleksibilitas tinggi dengan WOFINS online
                                yang dapat diakses dari mana saja, kapan saja, dengan perangkat apa saja, baik itu desktop,
                                mobile, iOS, atau Android.
                            </p>
                            <p class="text-gray-600">
                                Baca pertanyaan dan jawaban lengkap seputar penggunaan aplikasi WOFINS online di sini.
                            </p>
                        </div>

                        <!-- WhatsApp Sales Button -->
                        {{-- <div class="mt-8">
                        <a href="https://wa.me/6281234567890" target="_blank" class="inline-flex items-center px-6 py-3 bg-green-500 text-white font-medium rounded-lg hover:bg-green-600 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                            </svg>
                            WhatsApp Sales
                        </a>
                    </div> --}}
                    </div>

                    <!-- Right Column - FAQ Accordion -->
                    <div x-data="{ openFaq: null }">
                        <div class="space-y-4">
                            <!-- FAQ Item 1 -->
                            <div class="border border-gray-200 rounded-lg">
                                <button @click="openFaq = openFaq === 1 ? null : 1"
                                    class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                    <span class="font-medium text-gray-900">Apakah aplikasi WOFINS berbasis online?</span>
                                    <svg :class="openFaq === 1 ? 'transform rotate-180' : ''"
                                        class="w-5 h-5 text-gray-500 transition-transform">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="openFaq === 1" x-collapse class="px-6 pb-4">
                                    <p class="text-gray-600 text-xs">
                                        Ya, Anda dapat mengelola segala macam operasional wedding organizer secara online.
                                        Aplikasi atau software WOFINS menggunakan sistem berbasis cloud yang dapat diakses
                                        melalui desktop maupun mobile, melalui web atau aplikasi di perangkat iOS serta
                                        Android.
                                    </p>
                                </div>
                            </div>

                            <!-- FAQ Item 2 -->
                            <div class="border border-gray-200 rounded-lg">
                                <button @click="openFaq = openFaq === 2 ? null : 2"
                                    class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                    <span class="font-medium text-gray-900">Apa saja fitur software WOFINS online ?</span>
                                    <svg :class="openFaq === 2 ? 'transform rotate-180' : ''"
                                        class="w-5 h-5 text-gray-500 transition-transform">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="openFaq === 2" x-collapse class="px-6 pb-4">
                                    <p class="text-gray-600 text-xs">
                                        WOFINS menyediakan berbagai fitur lengkap untuk manajemen wedding organizer,
                                        termasuk nota dinas digital, vendor management, budget tracking, timeline event,
                                        team coordination, client management, dan laporan keuangan yang komprehensif.
                                    </p>
                                </div>
                            </div>

                            <!-- FAQ Item 3 -->
                            <div class="border border-gray-200 rounded-lg">
                                <button @click="openFaq = openFaq === 3 ? null : 3"
                                    class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                    <span class="font-medium text-gray-900">Apakah software WOFINS gratis?</span>
                                    <svg :class="openFaq === 3 ? 'transform rotate-180' : ''"
                                        class="w-5 h-5 text-gray-500 transition-transform">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="openFaq === 3" x-collapse class="px-6 pb-4">
                                    <p class="text-gray-600 text-xs">
                                        WOFINS menawarkan free trial selama 7 hari tanpa komitmen. Setelah masa trial,
                                        tersedia berbagai paket berlangganan yang disesuaikan dengan kebutuhan wedding
                                        organizer Anda, mulai dari freelancer hingga agency besar.
                                    </p>
                                </div>
                            </div>

                            <!-- FAQ Item 4 -->
                            <div class="border border-gray-200 rounded-lg">
                                <button @click="openFaq = openFaq === 4 ? null : 4"
                                    class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                    <span class="font-medium text-gray-900">Bagaimana jika saya ingin berlangganan software
                                        WOFINS ?</span>
                                    <svg :class="openFaq === 4 ? 'transform rotate-180' : ''"
                                        class="w-5 h-5 text-gray-500 transition-transform">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="openFaq === 4" x-collapse class="px-6 pb-4">
                                    <p class="text-gray-600 text-xs">
                                        Anda dapat memulai dengan menggunakan free trial selama 7 hari. Setelah itu, tim
                                        sales kami akan membantu Anda memilih paket yang paling sesuai dengan kebutuhan
                                        operasional dan budget wedding organizer Anda. Hubungi kami melalui WhatsApp atau
                                        form kontak.
                                    </p>
                                </div>
                            </div>

                            <!-- FAQ Item 5 -->
                            <div class="border border-gray-200 rounded-lg">
                                <button @click="openFaq = openFaq === 5 ? null : 5"
                                    class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                    <span class="font-medium text-gray-900">Kenapa wedding organizer dapat mempercayakan
                                        sistem pada WOFINS online ?</span>
                                    <svg :class="openFaq === 5 ? 'transform rotate-180' : ''"
                                        class="w-5 h-5 text-gray-500 transition-transform">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="openFaq === 5" x-collapse class="px-6 pb-4">
                                    <p class="text-gray-600 text-xs">
                                        WOFINS telah dipercaya oleh para owner wedding organizer di Indonesia dengan
                                        sertifikasi keamanan yang tinggi, uptime 99.9%, backup data otomatis, dan dukungan
                                        customer service 24/7. Selain itu, sistem kami dibuat khusus untuk workflow wedding
                                        organizer dengan fitur yang relevan dan mudah digunakan.
                                    </p>
                                </div>
                            </div>

                            <!-- FAQ Item 6 -->
                            <div class="border border-gray-200 rounded-lg">
                                <button @click="openFaq = openFaq === 6 ? null : 6"
                                    class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                    <span class="font-medium text-gray-900">Apa saja aplikasi WOFINS terbaik bagi wedding
                                        organizer?</span>
                                    <svg :class="openFaq === 6 ? 'transform rotate-180' : ''"
                                        class="w-5 h-5 text-gray-500 transition-transform">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="openFaq === 6" x-collapse class="px-6 pb-4">
                                    <p class="text-gray-600 text-xs">
                                        Aplikasi Wedding Organizer terbaik adalah yang dapat memenuhi kebutuhan spesifik
                                        wedding organizer Anda. WOFINS menawarkan solusi yang komprehensif, mudah digunakan,
                                        terintegrasi dengan vendor dan supplier, memiliki support yang responsif, dan harga
                                        yang kompetitif untuk semua skala wedding organizer.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="bg-blue-600 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl sm:text-4xl font-bold text-white">
                    Siap meningkatkan efisiensi operasional?
                </h2>
                <p class="mt-4 text-xl text-blue-100 max-w-2xl mx-auto">
                    Bergabung dengan 200+ wedding organizer yang sudah merasakan manfaatnya.
                </p>
                <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('pendaftaran') }}"
                        class="inline-flex items-center justify-center px-8 py-3 text-base font-medium text-blue-600 bg-white border border-transparent rounded-lg hover:bg-gray-50 transition-colors">
                        Mulai Gratis Sekarang
                    </a>
                    <a href="/admin"
                        class="inline-flex items-center justify-center px-8 py-3 text-base font-medium text-white bg-blue-700 border border-transparent rounded-lg hover:bg-blue-800 transition-colors">
                        Login ke Dashboard
                    </a>
                </div>
            </div>
        </section>

        <!-- Footer -->
        @include('front.footer')

    </div>
@endsection
