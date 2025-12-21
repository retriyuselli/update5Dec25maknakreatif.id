@extends('layouts.app')

@section('title', 'Fitur Manajemen Vendor')

@section('content')
    <div class="min-h-screen bg-white font-sans">
        @include('front.header')

        <!-- Hero Section -->
        <section class="pt-24 pb-16 md:pt-32 md:pb-24 overflow-hidden">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-4xl mx-auto mb-12">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 leading-tight mb-6">
                        Kelola <span class="text-blue-600">Database Vendor</span> dan Pengadaan Lebih Efektif
                    </h1>
                    <p class="text-lg text-gray-600 mb-8 max-w-2xl mx-auto">
                        Satu platform untuk semua kebutuhan manajemen vendor Anda. Simpan data, pantau kinerja, dan kelola pembayaran vendor dengan mudah.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="{{ route('front.register') }}" class="w-full sm:w-auto px-8 py-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 text-center">
                            Mulai Kelola Vendor
                        </a>
                        <a href="#features" class="w-full sm:w-auto px-8 py-4 bg-white text-blue-600 font-semibold rounded-lg border border-blue-200 hover:bg-blue-50 transition duration-300 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Pelajari Fitur
                        </a>
                    </div>
                </div>
                
                <!-- Hero Image Placeholder (Vendor Dashboard) -->
                <div class="relative max-w-6xl mx-auto mt-12">
                    <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden p-4">
                        <!-- Top Stats Cards -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                                    <span class="text-xs text-gray-500 font-medium">Total Vendor</span>
                                </div>
                                <div class="text-lg font-bold text-gray-900">42 Vendor</div>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-xl border border-purple-100">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-2 h-2 rounded-full bg-purple-500"></div>
                                    <span class="text-xs text-gray-500 font-medium">Tagihan Aktif</span>
                                </div>
                                <div class="text-lg font-bold text-gray-900">15 Invoice</div>
                            </div>
                            <div class="bg-orange-50 p-4 rounded-xl border border-orange-100">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-2 h-2 rounded-full bg-orange-500"></div>
                                    <span class="text-xs text-gray-500 font-medium">Jatuh Tempo</span>
                                </div>
                                <div class="text-lg font-bold text-gray-900">Rp 12.500.000</div>
                            </div>
                            <div class="bg-green-50 p-4 rounded-xl border border-green-100">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                    <span class="text-xs text-gray-500 font-medium">Pembelian Bulan Ini</span>
                                </div>
                                <div class="text-lg font-bold text-gray-900">Rp 45.200.000</div>
                            </div>
                        </div>

                        <!-- Vendor List Preview -->
                        <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-gray-50 text-gray-600 font-medium border-b">
                                    <tr>
                                        <th class="px-6 py-4">Nama Vendor</th>
                                        <th class="px-6 py-4">Kategori</th>
                                        <th class="px-6 py-4">Kontak</th>
                                        <th class="px-6 py-4 text-right">Total Pembelian</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 font-medium text-gray-900">PT Maju Mundur</td>
                                        <td class="px-6 py-4"><span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs">Elektronik</span></td>
                                        <td class="px-6 py-4 text-gray-500">Budi Santoso</td>
                                        <td class="px-6 py-4 text-right font-medium">Rp 125.000.000</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 font-medium text-gray-900">CV Kreatif Digital</td>
                                        <td class="px-6 py-4"><span class="bg-purple-100 text-purple-700 px-2 py-1 rounded-full text-xs">Jasa Design</span></td>
                                        <td class="px-6 py-4 text-gray-500">Siti Aminah</td>
                                        <td class="px-6 py-4 text-right font-medium">Rp 45.500.000</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 font-medium text-gray-900">UD Sumber Makmur</td>
                                        <td class="px-6 py-4"><span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs">ATK</span></td>
                                        <td class="px-6 py-4 text-gray-500">Joko Widodo</td>
                                        <td class="px-6 py-4 text-right font-medium">Rp 8.200.000</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Feature 1: Centralized Data -->
        <section id="features" class="py-20 bg-white">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
                    <div class="lg:w-1/2">
                        <div class="inline-block px-4 py-1 bg-blue-100 text-blue-600 rounded-full text-sm font-semibold mb-4">Database Terpusat</div>
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">Simpan Data Vendor dalam Satu Tempat Aman</h2>
                        <p class="text-lg text-gray-600 mb-6 leading-relaxed">
                            Lupakan catatan manual yang berantakan. Simpan semua informasi vendor mulai dari kontak, alamat, hingga detail rekening bank dalam satu database yang aman dan mudah diakses.
                        </p>
                    </div>
                    <div class="lg:w-1/2">
                        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 transform rotate-1 hover:rotate-0 transition duration-500">
                            <div class="flex items-center gap-4 mb-6 border-b pb-4">
                                <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center text-gray-400">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-xl text-gray-900">PT Teknologi Masa Depan</h3>
                                    <p class="text-gray-500">Vendor IT & Software</p>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-xs text-gray-500 uppercase tracking-wider">Email</label>
                                        <div class="text-gray-900 font-medium">contact@techfuture.id</div>
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-500 uppercase tracking-wider">Telepon</label>
                                        <div class="text-gray-900 font-medium">+62 21 555 1234</div>
                                    </div>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 uppercase tracking-wider">Alamat</label>
                                    <div class="text-gray-900 font-medium">Jl. Sudirman Kav 52-53, Jakarta Selatan</div>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 uppercase tracking-wider">Rekening Bank</label>
                                    <div class="text-gray-900 font-medium">BCA 1234567890 a.n PT Teknologi Masa Depan</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Feature 2: Transaction History -->
        <section class="py-20 bg-gray-50">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
                    <div class="lg:w-1/2 order-2 lg:order-1">
                        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 transform -rotate-1 hover:rotate-0 transition duration-500">
                            <h3 class="font-bold text-lg mb-4 text-gray-900">Riwayat Transaksi</h3>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">Pembelian Server</div>
                                            <div class="text-xs text-gray-500">12 Okt 2023 • INV/2023/10/001</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-gray-900">Rp 15.000.000</div>
                                        <span class="text-xs text-green-600 font-bold">Lunas</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">Maintenance Bulanan</div>
                                            <div class="text-xs text-gray-500">01 Nov 2023 • INV/2023/11/005</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-gray-900">Rp 2.500.000</div>
                                        <span class="text-xs text-orange-600 font-bold">Pending</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="lg:w-1/2 order-1 lg:order-2">
                        <div class="inline-block px-4 py-1 bg-purple-100 text-purple-600 rounded-full text-sm font-semibold mb-4">Riwayat Lengkap</div>
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">Pantau Setiap Transaksi dengan Vendor</h2>
                        <p class="text-lg text-gray-600 mb-6 leading-relaxed">
                            Lihat kembali riwayat pembelian, status pembayaran, dan total pengeluaran untuk setiap vendor. Analisis vendor mana yang paling sering Anda gunakan.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 bg-blue-600">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-8">
                    Siap Mengelola Vendor Anda dengan Lebih Baik?
                </h2>
                <p class="text-xl text-blue-100 mb-10 max-w-2xl mx-auto">
                    Bergabunglah sekarang dan rasakan kemudahan manajemen vendor dan pengadaan barang dalam satu aplikasi.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="{{ route('front.register') }}" class="px-8 py-4 bg-white text-blue-600 font-bold rounded-lg shadow-lg hover:bg-gray-100 transition duration-300">
                        Daftar Sekarang Gratis
                    </a>
                    <a href="{{ route('front.kontak') }}" class="px-8 py-4 bg-blue-700 text-white font-bold rounded-lg border border-blue-500 hover:bg-blue-800 transition duration-300">
                        Hubungi Tim Sales
                    </a>
                </div>
            </div>
        </section>

        @include('front.footer')
    </div>
@endsection
