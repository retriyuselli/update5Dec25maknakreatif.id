@extends('layouts.app')

@section('title', 'Fitur Invoice')

@section('content')
    <div class="min-h-screen bg-white font-sans">
        @include('front.header')

        <!-- Hero Section -->
        <section class="pt-24 pb-16 md:pt-32 md:pb-24 overflow-hidden">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-4xl mx-auto mb-12">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 leading-tight mb-6">
                        Buat <span class="text-blue-600">Invoice dan Faktur</span> Lebih Efisien dengan Software Kami
                    </h1>
                    <p class="text-lg text-gray-600 mb-8 max-w-2xl mx-auto">
                        Tinggalkan cara manual. Beralih ke sistem invoice digital yang terintegrasi, cepat, dan profesional
                        untuk bisnis Anda.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="{{ route('front.register') }}"
                            class="w-full sm:w-auto px-8 py-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 text-center">
                            Coba Gratis Sekarang
                        </a>
                        <a href="#features"
                            class="w-full sm:w-auto px-8 py-4 bg-white text-blue-600 font-semibold rounded-lg border border-blue-200 hover:bg-blue-50 transition duration-300 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Pelajari Fitur
                        </a>
                    </div>
                </div>

                <!-- Hero Image Placeholder -->
                <div class="relative max-w-5xl mx-auto mt-12">
                    <div
                        class="bg-gray-100 rounded-2xl shadow-2xl border border-gray-200 overflow-hidden aspect-[16/9] flex items-center justify-center group relative">
                        <!-- Decorative Elements -->
                        <div class="absolute top-0 left-0 w-full h-8 bg-gray-200 flex items-center px-4 gap-2">
                            <div class="w-3 h-3 rounded-full bg-red-400"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                            <div class="w-3 h-3 rounded-full bg-green-400"></div>
                        </div>
                        <img src="{{ asset('images/invoice/invoice_1.png') }}" alt="Dashboard Invoice Preview"
                            class="w-full h-full object-cover object-top pt-8">

                        <!-- Floating Cards (Decorative) -->
                        <div class="absolute -left-4 top-20 bg-white p-4 rounded-xl shadow-lg border border-gray-100 animate-bounce"
                            style="animation-duration: 3s;">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center text-green-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-500">Invoice Paid</div>
                                    <div class="text-sm font-bold text-gray-900">Rp 2.500.000</div>
                                </div>
                            </div>
                        </div>

                        <div class="absolute -right-4 bottom-20 bg-white p-4 rounded-xl shadow-lg border border-gray-100 animate-bounce"
                            style="animation-duration: 4s;">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-500">Reminder Sent</div>
                                    <div class="text-sm font-bold text-gray-900">Success</div>
                                </div>
                            </div>
                        </div>
                        </img>
                    </div>
                </div>
        </section>

        <!-- Feature 1: Design Elegan -->
        <section id="features" class="py-20 bg-gray-50">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
                    <div class="lg:w-1/2 order-2 lg:order-1">
                        <div
                            class="bg-white rounded-2xl shadow-xl border border-gray-100 p-2 transform rotate-2 hover:rotate-0 transition duration-500">
                            <img src="{{ asset('images/invoice/inv_portrait.png') }}" alt="Invoice Template Preview"
                                class="w-full h-auto rounded-xl shadow-sm">
                        </div>
                    </div>
                    <div class="lg:w-1/2 order-1 lg:order-2">
                        <div
                            class="inline-block px-4 py-1 bg-blue-100 text-blue-600 rounded-full text-sm font-semibold mb-4">
                            Professional Design</div>
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">Buat Invoice dengan Desain Elegan dan
                            Profesional</h2>
                        <p class="text-lg text-gray-600 mb-6 leading-relaxed">
                            Kesan pertama itu penting. Buat invoice yang mencerminkan profesionalitas brand Anda dengan
                            template yang dapat disesuaikan. Tambahkan logo, ubah warna, dan atur layout sesuai kebutuhan.
                        </p>
                        <ul class="space-y-4">
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-green-500 mt-1 mr-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-gray-700">Template siap pakai yang profesional</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-green-500 mt-1 mr-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-gray-700">Kustomisasi logo dan warna brand</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Feature 2: Laporan Detail -->
        <section class="py-20 bg-white">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
                    <div class="lg:w-1/2">
                        <div
                            class="inline-block px-4 py-1 bg-purple-100 text-purple-600 rounded-full text-sm font-semibold mb-4">
                            Analytics</div>
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">Laporan Pendapatan Detail dan
                            Real-time</h2>
                        <p class="text-lg text-gray-600 mb-6 leading-relaxed">
                            Pantau kesehatan keuangan bisnis Anda kapan saja. Dapatkan wawasan mendalam tentang arus kas,
                            pendapatan, dan piutang tak tertagih melalui dashboard yang intuitif.
                        </p>
                        <ul class="space-y-4">
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-green-500 mt-1 mr-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-gray-700">Grafik pendapatan visual</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-green-500 mt-1 mr-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-gray-700">Monitoring status pembayaran real-time</span>
                            </li>
                        </ul>
                    </div>
                    <div class="lg:w-1/2">
                        <div
                            class="bg-white rounded-2xl shadow-xl border border-gray-100 p-2 transform -rotate-2 hover:rotate-0 transition duration-500">
                            <img src="{{ asset('images/invoice/keuntungan.png') }}" alt="Laporan Keuntungan"
                                class="w-full h-auto rounded-xl shadow-sm">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Feature 3: Pajak Otomatis -->
        {{-- <section class="py-20 bg-gray-50">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
                    <div class="lg:w-1/2 order-2 lg:order-1">
                        <div
                            class="bg-white rounded-2xl shadow-xl border border-gray-100 p-2 transform rotate-1 hover:rotate-0 transition duration-500">
                            <div class="bg-gray-100 rounded-xl h-64 md:h-80 flex items-center justify-center">
                                <span class="text-gray-400">Tax Calculation UI</span>
                            </div>
                        </div>
                    </div>
                    <div class="lg:w-1/2 order-1 lg:order-2">
                        <div
                            class="inline-block px-4 py-1 bg-orange-100 text-orange-600 rounded-full text-sm font-semibold mb-4">
                            Tax Automation</div>
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">Perhitungan Pajak Otomatis</h2>
                        <p class="text-lg text-gray-600 mb-6 leading-relaxed">
                            Tidak perlu pusing dengan perhitungan pajak manual. Sistem kami secara otomatis menghitung PPN
                            dan pajak lainnya sesuai dengan aturan yang berlaku, memastikan kepatuhan pajak bisnis Anda.
                        </p>
                    </div>
                </div>
            </div>
        </section> --}}

        <!-- Feature 4: Reminder & WhatsApp -->
        <section class="py-20 bg-white">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Fitur Pintar Lainnya</h2>
                    <p class="text-gray-600">Maksimalkan efisiensi penagihan dengan fitur-fitur modern kami</p>
                </div>

                <div class="grid md:grid-cols-2 gap-8 lg:gap-12">
                    <!-- Invoice Reminder -->
                    <div class="bg-gray-50 rounded-2xl p-8 hover:shadow-lg transition duration-300 border border-gray-100">
                        <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 mb-6">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Invoice Reminder</h3>
                        <p class="text-gray-600 leading-relaxed mb-6">
                            Kirim pengingat pembayaran otomatis kepada klien sebelum atau setelah jatuh tempo. Kurangi
                            risiko telat bayar secara signifikan.
                        </p>
                        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-2 h-2 rounded-full bg-yellow-400"></div>
                                <div class="text-xs font-semibold text-gray-500">Reminder - H-3 Jatuh Tempo</div>
                            </div>
                            <div class="text-sm text-gray-800">"Halo, mengingatkan invoice #INV-001 akan jatuh tempo dalam
                                3 hari..."</div>
                        </div>
                    </div>

                    <!-- WhatsApp Billing -->
                    <div class="bg-gray-50 rounded-2xl p-8 hover:shadow-lg transition duration-300 border border-gray-100">
                        <div
                            class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center text-green-600 mb-6">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Kirim Tagihan via WhatsApp</h3>
                        <p class="text-gray-600 leading-relaxed mb-6">
                            Jangkau klien di platform yang paling sering mereka gunakan. Kirim link invoice langsung via
                            WhatsApp dan percepat proses pembayaran.
                        </p>
                        <button
                            class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition duration-300 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z">
                                </path>
                            </svg>
                            Share Invoice
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 bg-blue-600 relative overflow-hidden">
            <!-- Decorative Background -->
            <div class="absolute inset-0 opacity-10">
                <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <path d="M0 100 C 20 0 50 0 100 100 Z" fill="white" />
                </svg>
            </div>

            <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">Kelola Keuangan Bisnismu Lebih Mudah!</h2>
                <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
                    Bergabunglah dengan ribuan pebisnis yang telah beralih ke sistem invoice modern kami.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('front.register') }}"
                        class="w-full sm:w-auto px-8 py-4 bg-white text-blue-600 font-bold rounded-lg hover:bg-gray-100 transition duration-300 shadow-lg">
                        Mulai Sekarang Gratis
                    </a>
                    <a href="{{ route('kontak') }}"
                        class="w-full sm:w-auto px-8 py-4 bg-transparent border-2 border-white text-white font-bold rounded-lg hover:bg-white/10 transition duration-300">
                        Hubungi Sales
                    </a>
                </div>
            </div>
        </section>

        @include('front.footer')
    </div>
@endsection
