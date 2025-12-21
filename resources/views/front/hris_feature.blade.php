@extends('layouts.app')

@section('title', 'Fitur HRIS & Payroll')

@section('content')
    <div class="min-h-screen bg-white font-sans">
        @include('front.header')

        <!-- Hero Section -->
        <div class="relative bg-white overflow-hidden">
            <div class="max-w-7xl mx-auto">
                <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
                    <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                        <div class="sm:text-center lg:text-left">
                            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 leading-tight">
                                <span class="block xl:inline">Sistem HRIS Modern untuk</span>
                                <span class="block text-blue-600 xl:inline">Pengelolaan Karyawan yang Lebih Efisien</span>
                            </h1>
                            <p class="mt-6 text-lg text-gray-600 leading-relaxed sm:max-w-xl sm:mx-auto lg:mx-0">
                                Tinggalkan cara manual. Kelola data karyawan, absensi, hingga performa tim dalam satu
                                aplikasi terintegrasi yang mudah digunakan.
                            </p>
                            <div class="mt-8 flex flex-col sm:flex-row gap-4 sm:justify-center lg:justify-start">
                                <a href="#"
                                    class="inline-flex items-center justify-center px-6 py-3 text-base font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 transition-colors">
                                    Coba MaknaPro Sekarang
                                </a>
                                <a href="#"
                                    class="inline-flex items-center justify-center px-6 py-3 text-base font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                    Jadwalkan Demo
                                </a>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
            <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
                <img class="ml-10 h-56 w-full object-cover sm:h-72 md:h-96 lg:w-full lg:h-full"
                    src="https://images.unsplash.com/photo-1551434678-e076c223a692?q=80&w=2070&auto=format&fit=crop"
                    alt="HRIS Dashboard">
                alt="HRIS Dashboard">
            </div>
        </div>

        <!-- Employee Integration Section -->
        <div class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="lg:grid lg:grid-cols-2 lg:gap-8 items-center">
                    <div class="mb-10 lg:mb-0">
                        <span
                            class="text-blue-600 font-semibold text-sm tracking-wide uppercase bg-blue-50 px-3 py-1 rounded-full">Database
                            Karyawan Digital</span>
                        <h2 class="mt-4 text-3xl font-bold text-gray-900 sm:text-4xl">
                            Kelola Data Tim Secara Terpusat
                        </h2>
                        <p class="mt-4 text-lg text-gray-600 leading-relaxed">
                            Simpan dan kelola seluruh informasi karyawan mulai dari biodata, kontrak kerja, hingga riwayat
                            karir dalam satu database aman yang dapat diakses kapan saja. Tidak ada lagi dokumen yang
                            tercecer.
                        </p>
                    </div>
                    <div class="relative">
                        <img class="rounded-xl shadow-xl ring-1 ring-black ring-opacity-5"
                            src="{{ asset('images/hris-employee-data.png') }}" alt="Data Karyawan"
                            onerror="this.src='https://placehold.co/800x600/e2e8f0/1e293b?text=Database+Karyawan+Digital'">
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Grid Section (Kasbon & Reimbursement) -->
        <div class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="lg:grid lg:grid-cols-2 lg:gap-16">
                    <!-- Feature 1: Kasbon -->
                    <div class="flex flex-col mb-12 lg:mb-0">
                        <div>
                            <span
                                class="text-blue-600 font-semibold text-sm tracking-wide uppercase bg-blue-50 px-3 py-1 rounded-full">Manajemen
                                Kasbon Transparan</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">
                                Kelola Pinjaman Karyawan Lebih Rapi
                            </h3>
                            <p class="mt-4 text-gray-600 leading-relaxed flex-grow">
                                Pantau pengajuan dan sisa pinjaman karyawan dengan mudah. Sistem otomatis memotong gaji
                                sesuai tenor yang disepakati, meminimalisir kesalahan hitung dan menjaga arus kas
                                perusahaan.
                            </p>
                        </div>
                        <div class="mt-6">
                            <img class="rounded-lg shadow-md w-full" src="{{ asset('images/hris-kasbon.png') }}"
                                alt="Sistem Kasbon"
                                onerror="this.src='https://placehold.co/600x400/e2e8f0/1e293b?text=Manajemen+Pinjaman+Karyawan'">
                        </div>
                    </div>

                    <!-- Feature 2: Reimbursement -->
                    <div class="flex flex-col">
                        <div>
                            <span
                                class="text-blue-600 font-semibold text-sm tracking-wide uppercase bg-blue-50 px-3 py-1 rounded-full">Klaim
                                Reimbursement Paperless</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">
                                Proses Klaim Mudah & Cepat
                            </h3>
                            <p class="mt-4 text-gray-600 leading-relaxed flex-grow">
                                Karyawan dapat mengajukan klaim biaya operasional langsung dari aplikasi dengan melampirkan
                                foto bukti. Approval berjenjang memudahkan kontrol pengeluaran perusahaan tanpa tumpukan
                                kertas.
                            </p>
                        </div>
                        <div class="mt-6">
                            <img class="rounded-lg shadow-md w-full" src="{{ asset('images/hris-reimbursement.png') }}"
                                alt="Reimbursement"
                                onerror="this.src='https://placehold.co/600x400/e2e8f0/1e293b?text=Klaim+Reimbursement+Online'">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Presence Integration Section -->
        <div class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="lg:grid lg:grid-cols-2 lg:gap-12 items-center">
                    <div class="order-2 lg:order-1">
                        <span
                            class="text-blue-600 font-semibold text-sm tracking-wide uppercase bg-blue-50 px-3 py-1 rounded-full">Absensi
                            Online & Real-time</span>
                        <h2 class="mt-4 text-3xl font-bold text-gray-900 sm:text-4xl">
                            Monitor Kehadiran dari Mana Saja
                        </h2>
                        <p class="mt-4 text-lg text-gray-600 leading-relaxed">
                            Pantau kedisiplinan tim dengan fitur absensi berbasis GPS dan selfie. Rekapitulasi kehadiran,
                            lembur, dan cuti terhitung otomatis dan terhubung langsung ke sistem payroll untuk penggajian
                            yang akurat.
                        </p>
                    </div>
                    <div class="order-1 lg:order-2 relative mb-10 lg:mb-0">
                        <img class="rounded-xl shadow-xl ring-1 ring-black ring-opacity-5"
                            src="{{ asset('images/hris-presence.png') }}" alt="Presensi Terintegrasi"
                            onerror="this.src='https://placehold.co/800x600/e2e8f0/1e293b?text=Absensi+Mobile+Real-time'">
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="bg-blue-600">
            <div class="max-w-4xl mx-auto text-center py-16 px-4 sm:py-20 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-extrabold text-white sm:text-4xl">
                    Tingkatkan Produktivitas Tim HR Anda
                </h2>
                <p class="mt-4 text-xl text-blue-100">
                    Bergabunglah dengan perusahaan modern lainnya yang telah beralih ke sistem HRIS digital MaknaPro.
                </p>
                <div class="mt-8 flex justify-center space-x-4">
                    <a href="#"
                        class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-blue-600 bg-white hover:bg-gray-50">
                        Coba Gratis Sekarang
                    </a>
                    <a href="#"
                        class="inline-flex items-center justify-center px-5 py-3 border border-white text-base font-medium rounded-md text-white hover:bg-blue-700">
                        Hubungi Sales
                    </a>
                </div>
            </div>
        </div>

        @include('front.footer')
    </div>
@endsection
