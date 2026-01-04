<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Data Pribadi</title>
    {{-- Asumsikan Tailwind CSS dan font Poppins sudah di-include melalui file layout utama Anda --}}
    {{-- Jika masih menggunakan Bootstrap Icons, link ini bisa tetap ada --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    {{-- Impor Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        poppins: ['Poppins', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    {{-- Impor Google Fonts Poppins --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

</head>

@php
    $companyName = 'Makna';
    if (\Illuminate\Support\Facades\Schema::hasTable('companies')) {
        $val = \App\Models\Company::value('company_name');
        if ($val) {
            $companyName = $val;
        }
    }
@endphp

<body class="bg-gray-100 font-poppins text-sm text-gray-800 antialiased">

    @if (session('success'))
        <div id="success-alert"
            class="fixed top-5 right-5 bg-green-500 text-white py-3 px-6 rounded-lg shadow-lg z-50 flex items-center transition-opacity duration-300"
            role="alert">
            <i class="bi bi-check-circle-fill mr-2"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="ml-4 text-green-100 hover:text-white"
                onclick="document.getElementById('success-alert').style.display='none'" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white shadow-xl rounded-xl p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6 pb-4 border-b border-gray-200">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-4 sm:mb-0 self-start sm:self-center">Data
                    Crew {{ $companyName }}</h1>
                <div class="flex flex-col sm:flex-row items-center gap-3 mt-4 sm:mt-0 w-full sm:w-auto">
                    {{-- Form Pencarian --}}
                    <form action="{{ route('data-pribadi.index') }}" method="GET" class="w-full sm:w-auto">
                        <div class="relative">
                            <input type="text" name="search" placeholder="Cari Nama Lengkap..."
                                value="{{ request('search') }}"
                                class="block w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm shadow-sm"
                                aria-label="Cari Nama Lengkap">
                            <button type="submit"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-indigo-600"
                                aria-label="Cari">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>
                    <a href="{{ route('data-pribadi.create') }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-indigo-600 text-white font-medium text-sm rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                        <i class="bi bi-plus-lg mr-2"></i> Tambah Data
                    </a>
                </div>
            </div>

            @if ($dataPribadis->isEmpty())
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-md shadow-sm text-center">
                    <i class="bi bi-cloud-slash-fill text-yellow-500 text-5xl mb-3 inline-block"></i>
                    @if (request()->has('search') && !empty(request('search')))
                        <h4 class="text-lg font-semibold text-yellow-800">Tidak Ada Hasil Pencarian</h4>
                        <p class="text-yellow-700 mt-1">Tidak ada data yang cocok dengan kata kunci "<span
                                class="font-semibold">{{ request('search') }}</span>".<br>Coba gunakan kata kunci lain
                            atau <a href="{{ route('data-pribadi.index') }}"
                                class="font-bold underline hover:text-yellow-600">lihat semua data</a>.</p>
                    @else
                        <h4 class="text-lg font-semibold text-yellow-800">Belum Ada Data</h4>
                        <p class="text-yellow-700 mt-1">Saat ini belum ada data pribadi yang tersimpan. <br> Silakan <a
                                href="{{ route('data-pribadi.create') }}"
                                class="font-bold underline hover:text-yellow-600">tambahkan data baru</a> untuk memulai.
                        </p>
                    @endif
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    No</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Foto</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nama Lengkap</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    No. Telepon</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Pekerjaan</th>
                                @auth {{-- Hanya tampilkan kolom Gaji jika user login --}}
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Gaji</th>
                                @endauth
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tgl. Dibuat</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($dataPribadis as $index => $data)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $dataPribadis->firstItem() + $index }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($data->foto_url)
                                            <img src="{{ $data->foto_url }}" alt="Foto {{ $data->nama_lengkap }}"
                                                class="h-10 w-10 rounded-full object-cover shadow-sm border border-gray-200">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-indigo-500 text-white flex items-center justify-center text-sm font-semibold shadow-sm border border-gray-200"
                                                title="{{ $data->nama_lengkap }}">{{ $data->initials }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $data->nama_lengkap }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <i class="bi bi-envelope-fill text-gray-400 mr-1.5"></i>{{ $data->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        @if ($data->nomor_telepon)
                                            <i
                                                class="bi bi-telephone-fill text-gray-400 mr-1.5"></i>+62{{ $data->nomor_telepon }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <i
                                            class="bi bi-briefcase-fill text-gray-400 mr-1.5"></i>{{ $data->pekerjaan ?: '-' }}
                                    </td>
                                    @auth {{-- Hanya tampilkan data Gaji jika user login --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $data->formatted_gaji }}</td>
                                    @endauth
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $data->created_at->format('d M Y, H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($dataPribadis->hasPages())
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        {{-- Pastikan Anda telah mempublish dan menyesuaikan view paginasi Laravel untuk Tailwind CSS --}}
                        {{-- Jalankan: php artisan vendor:publish --tag=laravel-pagination dan ubah file di resources/views/vendor/pagination --}}
                        {{ $dataPribadis->appends(request()->query())->links() }}
                    </div>
                @endif
            @endif
        </div> <!-- Penutup untuk div.bg-white shadow-xl rounded-xl (L30) -->
    </div>

    <script>
        // Script untuk auto-hide notifikasi sukses
        const successAlert = document.getElementById('success-alert');
        if (successAlert) {
            setTimeout(() => {
                successAlert.style.opacity = '0';
                setTimeout(() => {
                    successAlert.style.display = 'none';
                }, 300); // Waktu untuk transisi opacity
            }, 5000); // 5 detik
        }
    </script>

    <!-- Footer -->
    @include('components.footer-simple')
</body>

</html>
