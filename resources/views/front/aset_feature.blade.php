@extends('layouts.app')

@section('title', 'Fitur Aset Bisnis')

@section('content')
    <div class="min-h-screen bg-white font-sans">
        @include('front.header')

        <!-- Hero Section -->
        <div class="relative bg-white overflow-hidden">
            <div class="max-w-7xl mx-auto">
                <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
                    <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                        <div class="sm:text-center lg:text-left">
                            <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                                <span class="block xl:inline">Hitung Setiap Penyusutan</span>
                                <span class="block text-blue-600 xl:inline">Aset Tetap Bisnis Secara Cepat dan
                                    Otomatis</span>
                            </h1>
                            <p
                                class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                                Software akuntansi MaknaPro membuatmu mudah melacak aset tetap, mendepresiasi aset secara
                                otomatis dan mendapatkan laporan aset secara realtime.
                            </p>
                            <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                                <div class="rounded-md shadow">
                                    <a href="#"
                                        class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 md:py-4 md:text-lg md:px-10">
                                        Coba MaknaPro Sekarang
                                    </a>
                                </div>
                                <div class="mt-3 sm:mt-0 sm:ml-3">
                                    <a href="#"
                                        class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 md:py-4 md:text-lg md:px-10">
                                        Jadwalkan Demo
                                    </a>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
            <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
                <img class="h-56 w-full object-cover sm:h-72 md:h-96 lg:w-full lg:h-full"
                    src="https://images.unsplash.com/photo-1554224155-6726b3ff858f?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1950&q=80"
                    alt="Manajemen Aset">
            </div>
        </div>

        <!-- Dashboard Preview Section (Image from user reference) -->
        <div class="py-12 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <!-- Using a placeholder or generic dashboard image since we don't have the exact user image asset -->
                    <img src="{{ asset('images/aset-dashboard-preview.png') }}" alt="Dashboard Aset"
                        class="rounded-lg shadow-xl mx-auto"
                        onerror="this.src='https://placehold.co/1200x600/e2e8f0/1e293b?text=Dashboard+Aset+Tetap'">
                </div>
            </div>
        </div>

        <!-- Realtime Report Section -->
        <div class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="lg:grid lg:grid-cols-2 lg:gap-8 items-center">
                    <div>
                        <span class="text-blue-600 font-semibold text-sm tracking-wide uppercase">Laporan Aset Terkini,
                            Keputusan Tepat</span>
                        <h2 class="mt-2 text-3xl font-extrabold text-gray-900 sm:text-4xl">
                            Laporan Aset Realtime
                        </h2>
                        <p class="mt-4 text-lg text-gray-500">
                            Dapatkan laporan aset bisnis mu secara realtime dan mendalam.
                        </p>
                    </div>
                    <div class="mt-10 lg:mt-0 relative">
                        <img class="rounded-lg shadow-lg" src="{{ asset('images/aset-report-preview.png') }}"
                            alt="Laporan Aset"
                            onerror="this.src='https://placehold.co/600x400/e2e8f0/1e293b?text=Laporan+Aset+Realtime'">
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Grid Section -->
        <div class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="lg:grid lg:grid-cols-2 lg:gap-16">
                    <!-- Feature 1 -->
                    <div class="mb-12 lg:mb-0">
                        <span class="text-blue-600 font-semibold text-sm tracking-wide uppercase">Purchasing Langsung Catat
                            Aset</span>
                        <h3 class="mt-2 text-2xl font-bold text-gray-900">
                            Terhubung dengan Fitur Purchasing
                        </h3>
                        <p class="mt-4 text-gray-500">
                            Saat melakukan purchasing, aset tetap yang dibeli otomatis akan masuk ke dalam daftar aset
                            tetap. Tak perlu lagi input dua kali.
                        </p>
                        <div class="mt-6">
                            <img class="rounded-lg shadow-md w-full"
                                src="{{ asset('images/aset-purchasing-integration.png') }}" alt="Integrasi Purchasing"
                                onerror="this.src='https://placehold.co/600x350/e2e8f0/1e293b?text=Integrasi+Purchasing'">
                        </div>
                    </div>

                    <!-- Feature 2 -->
                    <div>
                        <span class="text-blue-600 font-semibold text-sm tracking-wide uppercase">Kelola Aset Sekali
                            Klik</span>
                        <h3 class="mt-2 text-2xl font-bold text-gray-900">
                            Atur Pelepasan dan Penjualan Aset dengan Sekali Klik
                        </h3>
                        <p class="mt-4 text-gray-500">
                            Pelepasan dan penjualan aset dapat dicatat dengan sekali klik, dan akan otomatis terupdate
                            dilaporan keuangan bismismu.
                        </p>
                        <div class="mt-6">
                            <img class="rounded-lg shadow-md w-full" src="{{ asset('images/aset-disposal.png') }}"
                                alt="Pelepasan Aset"
                                onerror="this.src='https://placehold.co/600x350/e2e8f0/1e293b?text=Pelepasan+Aset'">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Depreciation Section -->
        <div class="py-16 bg-blue-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <span class="text-blue-600 font-semibold text-sm tracking-wide uppercase">Depresiasi Otomatis, Tanpa
                        Ribet</span>
                    <h2 class="mt-2 text-3xl font-extrabold text-gray-900 sm:text-4xl">
                        Perhitungan Depresiasi Otomatis
                    </h2>
                    <p class="mt-4 max-w-2xl text-xl text-gray-500 mx-auto">
                        MaknaPro akan menghitungkan depresiasi setiap aset secara otomatis setiap akhir bulan. Tak perlu
                        lagi repot dengan rumus-rumus depresiasi yang ribet itu.
                    </p>
                </div>
                <div class="bg-white rounded-xl shadow-2xl overflow-hidden p-4">
                    <!-- Form Placeholder matching image -->
                    <img src="{{ asset('images/aset-depreciation-calc.png') }}" alt="Kalkulasi Depresiasi" class="w-full"
                        onerror="this.src='https://placehold.co/1200x500/e2e8f0/1e293b?text=Perhitungan+Depresiasi+Otomatis'">
                </div>
            </div>
        </div>

        <!-- Mobile Feature Section -->
        <div class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="lg:grid lg:grid-cols-2 lg:gap-8 items-center">
                    <div class="order-2 lg:order-1">
                        <span class="text-blue-600 font-semibold text-sm tracking-wide uppercase">Kelola Aset Bisnis
                            Sederhana</span>
                        <h2 class="mt-2 text-3xl font-extrabold text-gray-900 sm:text-4xl">
                            Catat dan Lacak Aset dengan Mudah
                        </h2>
                        <p class="mt-4 text-lg text-gray-500">
                            Aset bisnis seperti bangunan, mobil, mesin, dan alat kantor bisa dicatat dengan sekali klik.
                            Tentukan perhitungan depresiasi, dan MaknaPro akan melakukan sisanya.
                        </p>
                    </div>
                    <div class="order-1 lg:order-2 relative mt-10 lg:mt-0">
                        <img class="mx-auto rounded-lg shadow-lg w-64 md:w-80"
                            src="{{ asset('images/aset-mobile-app.png') }}" alt="Mobile App Aset"
                            onerror="this.src='https://placehold.co/350x700/e2e8f0/1e293b?text=Mobile+App'">
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="bg-white border-t border-gray-200">
            <div class="max-w-4xl mx-auto text-center py-16 px-4 sm:py-20 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-extrabold text-blue-900 sm:text-4xl">
                    Kelola Keuangan Bisnismu Lebih Mudah!
                </h2>
                <div class="mt-8 flex justify-center space-x-4">
                    <a href="#"
                        class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Coba MaknaPro Sekarang
                    </a>
                    <a href="#"
                        class="inline-flex items-center justify-center px-5 py-3 border border-blue-600 text-base font-medium rounded-md text-blue-600 bg-white hover:bg-blue-50">
                        Jadwalkan Demo
                    </a>
                </div>
            </div>
        </div>

        @include('front.footer')
    </div>
@endsection
