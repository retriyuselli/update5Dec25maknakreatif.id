@extends('layouts.app')

@section('title', 'Fitur Manajemen Biaya')

@section('content')
    <div class="min-h-screen bg-white font-sans">
        @include('front.header')

        <!-- Hero Section -->
        <section class="pt-24 pb-16 md:pt-32 md:pb-24 overflow-hidden">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-4xl mx-auto mb-12">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 leading-tight mb-6">
                        Catat dan Analisis <span class="text-blue-600">Setiap Biaya</span> dalam Bisnis Lebih Mudah dan
                        Praktis
                    </h1>
                    <p class="text-lg text-gray-600 mb-8 max-w-2xl mx-auto">
                        Catat biaya, pantau pos pengeluaran, dan dapatkan laporan pengeluaran dalam hitungan detik.
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
                            Jadwalkan Demo
                        </a>
                    </div>
                </div>

                <!-- Hero Image Placeholder (Dashboard Expenses) -->
                <div class="relative max-w-6xl mx-auto mt-12">
                    <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden p-4">
                        <!-- Top Stats Cards -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-yellow-50 p-4 rounded-xl border border-yellow-100">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-2 h-2 rounded-full bg-yellow-500"></div>
                                    <span class="text-xs text-gray-500 font-medium">Bulan Ini</span>
                                </div>
                                <div class="text-lg font-bold text-gray-900">Rp 521.562</div>
                            </div>
                            <div class="bg-red-50 p-4 rounded-xl border border-red-100">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-2 h-2 rounded-full bg-red-500"></div>
                                    <span class="text-xs text-gray-500 font-medium">30 Hari Lalu</span>
                                </div>
                                <div class="text-lg font-bold text-gray-900">Rp 1.025.815</div>
                            </div>
                            <div class="bg-orange-50 p-4 rounded-xl border border-orange-100">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-2 h-2 rounded-full bg-orange-500"></div>
                                    <span class="text-xs text-gray-500 font-medium">Belum Dibayar</span>
                                </div>
                                <div class="text-lg font-bold text-gray-900">Rp 2.573.000</div>
                            </div>
                            <div class="bg-green-50 p-4 rounded-xl border border-green-100">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                    <span class="text-xs text-gray-500 font-medium">Jatuh Tempo</span>
                                </div>
                                <div class="text-lg font-bold text-gray-900">Rp 2.575.000</div>
                            </div>
                        </div>

                        <!-- Charts Area -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div
                                class="md:col-span-2 bg-white border border-gray-100 rounded-xl p-6 h-64 flex items-end justify-between gap-4">
                                <!-- Bar Chart Simulation -->
                                <div class="w-full bg-gray-50 rounded-t-lg h-[40%] relative group">
                                    <div class="absolute bottom-0 w-full bg-teal-400 rounded-t-lg h-[60%]"></div>
                                    <div class="absolute bottom-[60%] w-full bg-pink-300 rounded-t-lg h-[20%]"></div>
                                </div>
                                <div class="w-full bg-gray-50 rounded-t-lg h-[60%] relative group">
                                    <div class="absolute bottom-0 w-full bg-teal-400 rounded-t-lg h-[70%]"></div>
                                    <div class="absolute bottom-[70%] w-full bg-pink-300 rounded-t-lg h-[10%]"></div>
                                </div>
                                <div class="w-full bg-gray-50 rounded-t-lg h-[50%] relative group">
                                    <div class="absolute bottom-0 w-full bg-blue-400 rounded-t-lg h-[80%]"></div>
                                </div>
                                <div class="w-full bg-gray-50 rounded-t-lg h-[45%] relative group">
                                    <div class="absolute bottom-0 w-full bg-teal-400 rounded-t-lg h-[50%]"></div>
                                    <div class="absolute bottom-[50%] w-full bg-pink-300 rounded-t-lg h-[30%]"></div>
                                </div>
                                <div class="w-full bg-gray-50 rounded-t-lg h-[70%] relative group">
                                    <div class="absolute bottom-0 w-full bg-pink-400 rounded-t-lg h-full"></div>
                                </div>
                            </div>
                            <div
                                class="bg-white border border-gray-100 rounded-xl p-6 h-64 flex items-center justify-center relative">
                                <!-- Donut Chart Simulation -->
                                <div
                                    class="w-40 h-40 rounded-full border-[16px] border-pink-400 border-l-blue-400 border-t-teal-400 transform rotate-45">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Feature 1: Paperless -->
        <section id="features" class="py-20 bg-white">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
                    <div class="lg:w-1/2">
                        <div
                            class="inline-block px-4 py-1 bg-blue-100 text-blue-600 rounded-full text-sm font-semibold mb-4">
                            Simpan Bukti Pembayaran</div>
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">Saatnya Paperless, Simpan Bukti
                            Pembayaran di Sistem</h2>
                        <p class="text-lg text-gray-600 mb-6 leading-relaxed">
                            Pindahkan penyimpanan bukti kwitansi dan nota kantormu ke sistem digital. Meja jadi rapi, bukti
                            transaksi jadi mudah ditemukan kapan saja.
                        </p>
                    </div>
                    <div class="lg:w-1/2">
                        <div
                            class="bg-white rounded-2xl shadow-xl border border-gray-100 p-4 transform rotate-1 hover:rotate-0 transition duration-500">
                            <div class="bg-gray-50 rounded-xl p-6">
                                <div class="flex items-center justify-between mb-4 border-b pb-4">
                                    <div>
                                        <div class="text-xs text-gray-500">Biaya - Lunas</div>
                                        <div class="font-bold text-gray-900">INV-2023-001</div>
                                    </div>
                                    <div class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-xs font-bold">LUNAS
                                    </div>
                                </div>
                                <div class="space-y-3">
                                    <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                                    <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                                    <div
                                        class="h-32 bg-gray-100 rounded border-2 border-dashed border-gray-300 flex items-center justify-center text-gray-400 text-sm mt-4">
                                        Lampiran Bukti (Image/PDF)
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Feature 2: Custom Categories -->
        <section class="py-20 bg-gray-50">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
                    <div class="lg:w-1/2 order-2 lg:order-1">
                        <div
                            class="bg-white rounded-2xl shadow-xl border border-gray-100 p-4 transform -rotate-1 hover:rotate-0 transition duration-500">
                            <div class="bg-white rounded-xl border border-gray-200 p-4">
                                <div class="text-sm font-bold text-gray-700 mb-2">Kategori Biaya</div>
                                <div class="space-y-2">
                                    <div class="flex items-center p-2 hover:bg-blue-50 rounded cursor-pointer">
                                        <div class="w-8 h-8 rounded bg-blue-100 mr-3"></div>
                                        <div class="flex-1">
                                            <div class="h-3 bg-gray-800 rounded w-24 mb-1"></div>
                                            <div class="h-2 bg-gray-400 rounded w-16"></div>
                                        </div>
                                    </div>
                                    <div
                                        class="flex items-center p-2 bg-blue-50 rounded cursor-pointer border-l-4 border-blue-500">
                                        <div class="w-8 h-8 rounded bg-blue-100 mr-3"></div>
                                        <div class="flex-1">
                                            <div class="h-3 bg-gray-800 rounded w-32 mb-1"></div>
                                            <div class="h-2 bg-gray-400 rounded w-20"></div>
                                        </div>
                                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <div class="flex items-center p-2 hover:bg-blue-50 rounded cursor-pointer">
                                        <div class="w-8 h-8 rounded bg-blue-100 mr-3"></div>
                                        <div class="flex-1">
                                            <div class="h-3 bg-gray-800 rounded w-20 mb-1"></div>
                                            <div class="h-2 bg-gray-400 rounded w-12"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="lg:w-1/2 order-1 lg:order-2">
                        <div
                            class="inline-block px-4 py-1 bg-purple-100 text-purple-600 rounded-full text-sm font-semibold mb-4">
                            Kustomisasi Kategori</div>
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">Kustomisasi dan Kategorikan Biaya
                            Sesuai Kebutuhan</h2>
                        <p class="text-lg text-gray-600 mb-6 leading-relaxed">
                            Sesuaikan kategori biaya dengan kultur bisnismu, semua bisa diatur dan dikategorikan dengan
                            rapi. Analisa pos pengeluaran mana yang paling besar.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Feature 3: Easy Recording -->
        <section class="py-20 bg-white">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Catat Pengeluaran dengan Mudah</h2>
                    <p class="text-gray-600">Tak perlu lagi menumpuk kertas pengeluaran, catat semuanya dengan mudah di
                        sistem. Review cash flow bisnismu akan otomatis terupdate.</p>
                </div>

                <div class="flex flex-col md:flex-row gap-8 items-center justify-center">
                    <div class="w-full md:w-5/12">
                        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                            <h3 class="font-bold text-lg mb-4">Biaya - Lunas</h3>
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="text-xs text-gray-500 block mb-1">Tanggal</label>
                                    <div class="h-8 bg-gray-50 rounded border border-gray-200 w-full"></div>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 block mb-1">Nomor</label>
                                    <div class="h-8 bg-gray-50 rounded border border-gray-200 w-full"></div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="text-xs text-gray-500 block mb-1">Vendor / Penerima</label>
                                <div class="h-8 bg-gray-50 rounded border border-gray-200 w-full"></div>
                            </div>
                            <div class="mb-4">
                                <label class="text-xs text-gray-500 block mb-1">Total Biaya</label>
                                <div
                                    class="h-10 bg-gray-50 rounded border border-gray-200 w-full flex items-center px-3 font-bold text-gray-700">
                                    Rp 0</div>
                            </div>
                        </div>
                    </div>
                    <div class="hidden md:flex w-2/12 items-center justify-center">
                        <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="w-full md:w-5/12">
                        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                            <h3 class="font-bold text-lg mb-4">Tambah Biaya</h3>
                            <div class="space-y-4">
                                <div>
                                    <div class="h-4 bg-gray-200 rounded w-1/3 mb-2"></div>
                                    <div class="h-8 bg-gray-100 rounded w-full border border-dashed border-gray-300"></div>
                                </div>
                                <div>
                                    <div class="h-4 bg-gray-200 rounded w-1/4 mb-2"></div>
                                    <div class="h-8 bg-gray-100 rounded w-full border border-dashed border-gray-300"></div>
                                </div>
                                <div class="flex justify-end pt-4">
                                    <button class="px-4 py-2 bg-blue-600 text-white rounded text-sm font-bold">Simpan
                                        Biaya</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Feature 4: Payment Status Tracking -->
        <section class="py-20 bg-gray-50">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
                    <div class="lg:w-1/2 order-2 lg:order-1">
                        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-gray-50 text-gray-600 font-medium border-b">
                                        <tr>
                                            <th class="px-6 py-3">Tanggal</th>
                                            <th class="px-6 py-3">Nomor</th>
                                            <th class="px-6 py-3">Status</th>
                                            <th class="px-6 py-3 text-right">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4">12 Nov 2023</td>
                                            <td class="px-6 py-4 font-medium">EXP-001</td>
                                            <td class="px-6 py-4"><span
                                                    class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-bold">Lunas</span>
                                            </td>
                                            <td class="px-6 py-4 text-right">Rp 1.500.000</td>
                                        </tr>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4">15 Nov 2023</td>
                                            <td class="px-6 py-4 font-medium">EXP-002</td>
                                            <td class="px-6 py-4"><span
                                                    class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full text-xs font-bold">Sebagian</span>
                                            </td>
                                            <td class="px-6 py-4 text-right">Rp 750.000</td>
                                        </tr>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4">18 Nov 2023</td>
                                            <td class="px-6 py-4 font-medium">EXP-003</td>
                                            <td class="px-6 py-4"><span
                                                    class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs font-bold">Belum
                                                    Bayar</span></td>
                                            <td class="px-6 py-4 text-right">Rp 2.100.000</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="lg:w-1/2 order-1 lg:order-2">
                        <div
                            class="inline-block px-4 py-1 bg-green-100 text-green-600 rounded-full text-sm font-semibold mb-4">
                            Lacak Transaksi</div>
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">Lacak Status Pembayaran Tiap
                            Transaksi</h2>
                        <p class="text-lg text-gray-600 mb-6 leading-relaxed">
                            Tak ada lagi telat bayar biaya pengeluaran. Kamu bisa melacak biaya mana saja yang sudah dibayar
                            atau belum, atau bahkan yang baru dibayar sebagian.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Feature 5: Excel Import -->
        <section class="py-20 bg-white">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
                    <div class="lg:w-1/2">
                        <div
                            class="inline-block px-4 py-1 bg-orange-100 text-orange-600 rounded-full text-sm font-semibold mb-4">
                            Import Excel</div>
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">Impor Semuanya dengan Excel</h2>
                        <p class="text-lg text-gray-600 mb-6 leading-relaxed">
                            Masukkan ratusan pencatatan biaya sekaligus ke dalam sistem hanya dengan sekali klik. Fitur
                            Import biaya akan menghemat berjam-jam waktumu yang sangat berharga.
                        </p>
                    </div>
                    <div class="lg:w-1/2">
                        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-2">
                            <div
                                class="bg-gray-50 rounded-xl p-6 border-2 border-dashed border-gray-300 flex flex-col items-center justify-center text-center h-64">
                                <div
                                    class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center text-green-600 mb-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                </div>
                                <h4 class="font-bold text-gray-800 mb-2">Upload File Excel</h4>
                                <p class="text-sm text-gray-500 mb-4">Drag & drop file here or click to browse</p>
                                <button
                                    class="px-4 py-2 bg-white border border-gray-300 rounded text-sm font-medium hover:bg-gray-50">Browse
                                    Files</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Bottom CTA / Realtime Monitoring -->
        <section class="py-20 bg-white">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="inline-block px-4 py-1 bg-blue-100 text-blue-600 rounded-full text-sm font-semibold mb-6">
                    Mobile Ready</div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">Pantau Pengeluaran Bisnismu Secara <span
                        class="italic text-blue-600">Realtime</span></h2>
                <p class="text-lg text-gray-600 mb-12 max-w-3xl mx-auto">
                    Tidak ada bisnis yang gratis, pengeluaran bertambah saat bisnis berkembang. Kami membantumu melacak
                    semua pos pengeluaran dari alat kantor hingga gaji karyawan. Analisa dan monitor pengeluaran bulanan,
                    maupun tahunan agar keputusan yang tepat dengan informasi yang akurat.
                </p>

                <div class="relative max-w-4xl mx-auto mt-8 flex justify-center">
                    <img src="{{ asset('images/invoice/inv_phone.png') }}" alt="Mobile Monitoring"
                        class="w-full h-auto object-contain">
                </div>
            </div>
        </section>

        @include('front.footer')
    </div>
@endsection
