@extends('layouts.app')

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
                                <span class="block xl:inline">Kelola Payroll dan</span>
                                <span class="block text-blue-600 xl:inline">Akuntansi Terintegrasi dalam Satu Platform</span>
                            </h1>
                            <p
                                class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                                Dengan Makna Kreatif, Anda bisa mengintegrasikan proses akuntansi dengan fitur penggajian,
                                tunjangan, dan penghitungan pajak penghasilan karyawan.
                            </p>
                            <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                                <div class="rounded-md shadow">
                                    <a href="{{ route('front.register') }}"
                                        class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 md:py-4 md:text-lg">
                                        Coba Gratis Sekarang
                                    </a>
                                </div>
                                <div class="mt-3 sm:mt-0 sm:ml-3">
                                    <a href="https://wa.me/6281234567890"
                                        class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 md:py-4 md:text-lg">
                                        Jadwalkan Demo
                                    </a>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
            <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
                <img class="ml-10 h-56 w-full object-cover sm:h-72 md:h-96 lg:w-full lg:h-full"
                    src="https://images.unsplash.com/photo-1554224155-6726b3ff858f?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1951&q=80"
                    alt="Payroll dashboard">
            </div>
        </div>

        <!-- Feature 1: Catat dan Kelola Payroll -->
        <div class="py-12 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="lg:text-center">
                    <h2 class="text-base text-blue-600 font-semibold tracking-wide uppercase">Gaji Mudah, Laporan Otomatis
                    </h2>
                    <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                        Catat dan Kelola Payroll Lebih Mudah
                    </p>
                    <p class="mt-4 max-w-2xl text-xl text-gray-500 lg:mx-auto">
                        Lakukan proses pencatatan data penggajian lebih cepat dan rapi. Nantinya pencatatan penggajian
                        karyawan tersebut akan otomatis masuk di laporan jurnal.
                    </p>
                </div>

                <div class="mt-10">
                    <div class="grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-2">
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Payroll Dashboard</h3>
                                <div class="mt-2 max-w-xl text-sm text-gray-500">
                                    <p>Kelola komponen gaji seperti gaji pokok, tunjangan, dan potongan dengan mudah dalam
                                        satu tampilan.</p>
                                </div>
                                <div class="mt-4 bg-gray-100 h-48 rounded flex items-center justify-center text-gray-400">
                                    <!-- Placeholder for Payroll UI image -->
                                    <img src="{{ asset('images/payroll/payroll.png') }}" alt="Payroll Dashboard"
                                        class="h-full w-full object-cover rounded">
                                </div>
                            </div>
                        </div>
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Jurnal Umum Otomatis</h3>
                                <div class="mt-2 max-w-xl text-sm text-gray-500">
                                    <p>Setiap transaksi penggajian akan tercatat secara otomatis ke dalam jurnal umum
                                        akuntansi Anda.</p>
                                </div>
                                <div class="mt-4 bg-gray-100 h-48 rounded flex items-center justify-center text-gray-400">
                                    <!-- Placeholder for Journal UI image -->
                                    <img src="{{ asset('images/payroll/payroll1.png') }}" alt="Jurnal Umum Otomatis"
                                        class="h-full w-full object-cover rounded">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feature 2: PPh & BPJS -->
        <div class="py-12 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-12 lg:grid-cols-2 items-center">
                    <div>
                        <span class="h-12 w-12 rounded-md flex items-center justify-center bg-blue-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </span>
                        <h2 class="text-3xl font-extrabold tracking-tight text-gray-900">
                            Slip Gaji Tercatat Otomatis
                        </h2>
                        <p class="mt-4 text-lg text-gray-500">
                            Selain gaji, komponen penting yang otomatis tercatat di laporan jurnal adalah PPh atau pajak
                            penghasilan karyawan. Kini pajak penjualan dan pajak penghasilan bisa Anda pantau dalam satu
                            platform terintegrasi.
                        </p>
                    </div>
                    <div class="relative">
                        <img class="w-full rounded-xl shadow-xl ring-1 ring-black ring-opacity-5"
                            src="{{ asset('images/payroll/slipgaji.png') }}" alt="PPh Karyawan">
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="bg-blue-700">
            <div class="max-w-2xl mx-auto text-center py-16 px-4 sm:py-20 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-extrabold text-white sm:text-4xl">
                    <span class="block">Kelola Keuangan Bisnismu Lebih Mudah!</span>
                </h2>
                <p class="mt-4 text-lg leading-6 text-blue-200">
                    Bergabunglah dengan ribuan bisnis lainnya yang telah menggunakan platform kami untuk efisiensi
                    operasional.
                </p>
                <div class="mt-8 flex justify-center">
                    <div class="inline-flex rounded-md shadow">
                        <a href="{{ route('front.register') }}"
                            class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-blue-600 bg-white hover:bg-blue-50">
                            Coba Gratis Sekarang
                        </a>
                    </div>
                    <div class="ml-3 inline-flex">
                        <a href="https://wa.me/6281234567890"
                            class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-800 hover:bg-blue-900">
                            Jadwalkan Demo
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @include('front.footer')
    </div>
@endsection
