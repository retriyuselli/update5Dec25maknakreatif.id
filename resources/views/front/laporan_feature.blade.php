@extends('layouts.app')

@section('title', 'Fitur Laporan Bisnis')

@section('content')
    <div class="min-h-screen bg-white font-sans">
        @include('front.header')

        <!-- Hero Section -->
        <section class="pt-24 pb-16 md:pt-32 md:pb-24 overflow-hidden">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-4xl mx-auto mb-12">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 leading-tight mb-6">
                        Buat & Analisis Lebih Dari <span class="text-blue-600">50 Laporan</span> Keuangan & Bisnis Lebih
                        Mudah
                    </h1>
                    <p class="text-lg text-gray-600 mb-8 max-w-2xl mx-auto">
                        Jadikan keputusan bisnis lebih baik dengan informasi yang akurat tentang performa bisnismu dengan
                        cara lebih mudah menggunakan software akuntansi Wofins.
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
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            Jadwalkan Demo
                        </a>
                    </div>
                </div>

                <!-- Hero Image (Dashboard Charts) -->
                <div class="relative max-w-6xl mx-auto mt-12">
                    <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Chart 1: Rincian Pendapatan (Area Chart) -->
                            <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                                <h3 class="font-bold text-gray-800 text-sm mb-4">RINCIAN PENDAPATAN</h3>
                                <div class="h-40 relative flex items-end justify-between px-2 gap-1">
                                    <!-- Simulated Area Chart -->
                                    <div
                                        class="w-full h-full bg-gradient-to-t from-blue-50 to-white relative overflow-hidden rounded-b-lg">
                                        <svg viewBox="0 0 100 50" class="w-full h-full" preserveAspectRatio="none">
                                            <path d="M0 50 L0 30 C20 40, 40 10, 60 25 C80 35, 100 15, 100 5 L100 50 Z"
                                                fill="#eff6ff" stroke="#3b82f6" stroke-width="0.5" />
                                            <path d="M0 50 L0 40 C30 45, 50 35, 70 40 C90 45, 100 30, 100 35 L100 50 Z"
                                                fill="#fff7ed" stroke="#f97316" stroke-width="0.5" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex justify-between text-[10px] text-gray-400 mt-2">
                                    <span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span><span>Mei</span><span>Jun</span>
                                </div>
                            </div>

                            <!-- Chart 2: Biaya Per Kategori (Donut Chart) -->
                            <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                                <h3 class="font-bold text-gray-800 text-sm mb-4">BIAYA PER KATEGORI</h3>
                                <div class="flex items-center justify-center h-40">
                                    <div
                                        class="w-32 h-32 rounded-full border-[12px] border-pink-400 border-l-yellow-400 border-t-blue-400 transform rotate-45 relative">
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <div class="text-center">
                                                <div class="text-xs text-gray-500">Total</div>
                                                <div class="font-bold text-gray-800">125jt</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Chart 3: Tren Neraca (Bar Chart) -->
                            <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                                <h3 class="font-bold text-gray-800 text-sm mb-4">TREN NERACA</h3>
                                <div class="h-40 flex items-end justify-between gap-2 px-2">
                                    <div class="w-1/5 bg-gray-100 h-[40%] rounded-t relative group">
                                        <div class="absolute bottom-0 w-full bg-blue-400 h-[60%] rounded-t"></div>
                                    </div>
                                    <div class="w-1/5 bg-gray-100 h-[60%] rounded-t relative group">
                                        <div class="absolute bottom-0 w-full bg-blue-400 h-[70%] rounded-t"></div>
                                    </div>
                                    <div class="w-1/5 bg-gray-100 h-[50%] rounded-t relative group">
                                        <div class="absolute bottom-0 w-full bg-blue-400 h-[50%] rounded-t"></div>
                                    </div>
                                    <div class="w-1/5 bg-gray-100 h-[80%] rounded-t relative group">
                                        <div class="absolute bottom-0 w-full bg-blue-400 h-[80%] rounded-t"></div>
                                    </div>
                                    <div class="w-1/5 bg-gray-100 h-[70%] rounded-t relative group">
                                        <div class="absolute bottom-0 w-full bg-blue-400 h-[60%] rounded-t"></div>
                                    </div>
                                </div>
                                <div class="flex justify-between text-[10px] text-gray-400 mt-2">
                                    <span>Aset</span><span>Kewajiban</span><span>Modal</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Feature 1: Dashboard Summary -->
        <section id="features" class="py-20 bg-white">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
                    <div class="lg:w-1/2">
                        <div
                            class="inline-block px-4 py-1 bg-blue-100 text-blue-600 rounded-full text-sm font-semibold mb-4">
                            Ringkasan Performa</div>
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">Dapatkan Ringkasan Performa dari
                            Dashboard</h2>
                        <p class="text-lg text-gray-600 mb-6 leading-relaxed">
                            Lihat pengeluaran usahamu, produk paling laris, laba rugi, dan saldo bank dalam bentuk grafik
                            cantik dari dashboard. Pantau kesehatan bisnis dalam sekilas pandang.
                        </p>
                    </div>
                    <div class="lg:w-1/2">
                        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-2">
                            <img src="https://placehold.co/600x400/e2e8f0/475569?text=Dashboard+Preview"
                                alt="Dashboard Preview" class="rounded-xl w-full h-auto">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Feature 2: Mobile Access -->
        <section class="py-20 bg-gray-50">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
                    <div class="lg:w-1/2 order-2 lg:order-1">
                        <div class="relative">
                            <!-- Phone Mockup -->
                            <div class="bg-gray-800 rounded-[2.5rem] p-3 shadow-2xl w-64 mx-auto border-8 border-gray-800">
                                <div class="bg-white rounded-[2rem] overflow-hidden h-96 relative">
                                    <div
                                        class="absolute top-0 w-full h-6 bg-gray-100 flex justify-center items-center gap-2">
                                        <div class="w-12 h-3 bg-black rounded-b-lg"></div>
                                    </div>
                                    <!-- App Content -->
                                    <div class="pt-8 px-4">
                                        <div class="flex justify-between items-center mb-4">
                                            <div class="w-8 h-8 rounded-full bg-gray-200"></div>
                                            <div class="w-4 h-4 rounded-full bg-red-500"></div>
                                        </div>
                                        <div class="h-24 bg-blue-500 rounded-xl mb-4 p-4 text-white">
                                            <div class="text-xs opacity-80">Saldo Kas</div>
                                            <div class="text-xl font-bold">Rp 125.000.000</div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div class="h-20 bg-gray-100 rounded-lg"></div>
                                            <div class="h-20 bg-gray-100 rounded-lg"></div>
                                            <div class="h-20 bg-gray-100 rounded-lg"></div>
                                            <div class="h-20 bg-gray-100 rounded-lg"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="lg:w-1/2 order-1 lg:order-2">
                        <div
                            class="inline-block px-4 py-1 bg-purple-100 text-purple-600 rounded-full text-sm font-semibold mb-4">
                            Pantau Kapan Saja</div>
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">Lihat Laporan Bisnis dari Manapun</h2>
                        <p class="text-lg text-gray-600 mb-6 leading-relaxed">
                            Sedang jalan-jalan ataupun sedang perjalanan dinas, kamu bisa memantau kesehatan bisnismu dari
                            manapun melalui handphone, tablet ataupun laptop.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Feature 3: Data Driven Decision -->
        <section class="py-20 bg-white">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
                    <div class="lg:w-1/2">
                        <div
                            class="inline-block px-4 py-1 bg-orange-100 text-orange-600 rounded-full text-sm font-semibold mb-4">
                            Keputusan Cerdas</div>
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">Buat Keputusan Bisnis Berdasar Data
                            Aktual</h2>
                        <p class="text-lg text-gray-600 mb-6 leading-relaxed">
                            Jangan menunggu hingga akhir bulan untuk mendapatkan laporan bisnismu. Lihat laporan laba rugi,
                            neraca, arus kas bahkan executive summary kapanpun kamu membutuhkan. Dapatkan laporan mendalam
                            untuk merencanakan bisnismu secara tepat.
                        </p>
                    </div>
                    <div class="lg:w-1/2">
                        <div
                            class="bg-white rounded-2xl shadow-xl border border-gray-100 p-2 transform rotate-2 hover:rotate-0 transition duration-500">
                            <img src="https://placehold.co/600x400/e2e8f0/475569?text=Tablet+Data+View"
                                alt="Data Analysis" class="rounded-xl w-full h-auto">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Feature 4: Profitability -->
        <section class="py-20 bg-blue-50">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="max-w-3xl mx-auto mb-12">
                    <div class="inline-block px-4 py-1 bg-blue-100 text-blue-600 rounded-full text-sm font-semibold mb-4">
                        Profitabilitas</div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">Laporan Profitabilitas</h2>
                    <p class="text-lg text-gray-600">
                        Kini Anda dapat melihat laporan profitabilitas per tagihan, per pelanggan, bahkan per produk dengan
                        mudah. Dengan begini, Anda bisa membuat keputusan bisnis yang lebih baik berdasarkan histori data
                        yang pasti.
                    </p>
                </div>

                <div class="flex flex-col md:flex-row justify-center gap-8 items-end">
                    <div
                        class="bg-white rounded-t-2xl shadow-xl border-t border-x border-gray-200 p-4 w-full max-w-2xl h-64 overflow-hidden relative">
                        <div class="absolute top-0 left-0 w-full h-8 bg-gray-50 border-b flex items-center px-4 gap-2">
                            <div class="w-3 h-3 rounded-full bg-red-400"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                            <div class="w-3 h-3 rounded-full bg-green-400"></div>
                        </div>
                        <div class="mt-8 space-y-3">
                            <div class="flex justify-between border-b pb-2 text-sm font-bold text-gray-700">
                                <span>Item</span><span>Profit</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Produk A</span><span class="text-green-600">+ Rp 5.000.000</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Jasa Konsultasi</span><span class="text-green-600">+ Rp 12.000.000</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Produk B</span><span class="text-red-500">- Rp 1.200.000</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Maintenance</span><span class="text-green-600">+ Rp 3.500.000</span>
                            </div>
                        </div>
                    </div>
                    <div
                        class="bg-gray-900 rounded-[2rem] p-3 shadow-xl w-64 border-4 border-gray-900 transform translate-y-12">
                        <div class="bg-white rounded-[1.5rem] overflow-hidden h-80 relative p-4">
                            <div class="text-center font-bold mb-4 text-sm">Profit per Project</div>
                            <div class="space-y-3">
                                <div class="bg-blue-50 p-2 rounded">
                                    <div class="text-xs text-gray-500">Project Alpha</div>
                                    <div class="font-bold text-blue-600">35% Margin</div>
                                </div>
                                <div class="bg-green-50 p-2 rounded">
                                    <div class="text-xs text-gray-500">Project Beta</div>
                                    <div class="font-bold text-green-600">42% Margin</div>
                                </div>
                                <div class="bg-yellow-50 p-2 rounded">
                                    <div class="text-xs text-gray-500">Project Gamma</div>
                                    <div class="font-bold text-yellow-600">18% Margin</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- List of Reports -->
        <section class="py-20 bg-white">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="border-l-4 border-blue-600 pl-6 mb-12">
                    <h2 class="text-3xl font-bold text-gray-900">Laporan Apa Saja yang Disediakan Wofins?</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Finansial</h3>
                        <div class="space-y-8">
                            <div class="flex gap-4">
                                <div class="w-full">
                                    <h4 class="font-bold text-gray-800 flex items-center gap-2">
                                        Laporan Neraca
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                            </path>
                                        </svg>
                                    </h4>
                                    <p class="text-sm text-gray-600 mt-2">Gambaran riil terkait aset, kewajiban, dan modal
                                        pada bisnis yang Kamu miliki dalam periode akuntansi berjalan.</p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div class="w-full">
                                    <h4 class="font-bold text-gray-800 flex items-center gap-2">
                                        Laporan Laba Rugi
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                            </path>
                                        </svg>
                                    </h4>
                                    <p class="text-sm text-gray-600 mt-2">Laporan keuntungan atau kerugian yang dihasilkan
                                        bisnis yang kamu miliki selama periode waktu tertentu.</p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div class="w-full">
                                    <h4 class="font-bold text-gray-800 flex items-center gap-2">
                                        Ringkasan Eksekutif
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                            </path>
                                        </svg>
                                    </h4>
                                    <p class="text-sm text-gray-600 mt-2">Rangkuman performa bisnis Anda secara keseluruhan
                                        dalam satu tampilan ringkas.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-6">&nbsp;</h3> <!-- Spacer -->
                        <div class="space-y-8">
                            <div class="flex gap-4">
                                <div class="w-full">
                                    <h4 class="font-bold text-gray-800 flex items-center gap-2">
                                        Laporan Arus Kas
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                            </path>
                                        </svg>
                                    </h4>
                                    <p class="text-sm text-gray-600 mt-2">Memuat informasi pemasukan dan pendapatan bisnis
                                        yang Kamu miliki selama periode waktu tertentu.</p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div class="w-full">
                                    <h4 class="font-bold text-gray-800 flex items-center gap-2">
                                        Perubahan Modal
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                            </path>
                                        </svg>
                                    </h4>
                                    <p class="text-sm text-gray-600 mt-2">Laporan ini memberikan gambaran tentang bagaimana
                                        modal awal berubah akibat adanya penambahan atau pengurangan dari pengembalian
                                        pribadi oleh pemilik (prive) serta kerugian perusahaan.</p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div class="w-full">
                                    <h4 class="font-bold text-gray-800 flex items-center gap-2">
                                        Hutang Piutang per Kontak
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                            </path>
                                        </svg>
                                    </h4>
                                    <p class="text-sm text-gray-600 mt-2">Detail lengkap hutang dan piutang untuk setiap
                                        vendor dan pelanggan Anda.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @include('front.footer')
    </div>
@endsection
