<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anggota Tim - Makna Wedding Organizer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .text-gradient {
            background: linear-gradient(135deg, #3b82f6, #fbbf24);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>

<body class="bg-white text-gray-800">
    <!-- Header Navigation -->
    @include('front.header')

    <!-- Hero Section -->
    <section class="gradient-bg text-white py-20">
        <div class="container mx-auto px-6">
            <div class="text-center max-w-4xl mx-auto">
                <h1 class="text-5xl md:text-6xl font-bold mb-6">
                    Tim <span class="text-gradient">Profesional</span> Kami
                </h1>
                <p class="text-xl md:text-2xl text-gray-300 mb-8">
                    Bertemu dengan para ahli yang akan mewujudkan pernikahan impian Anda
                </p>
                <div class="flex flex-wrap justify-center gap-4 text-sm">
                    <div class="bg-blue-600/20 backdrop-blur-sm px-4 py-2 rounded-full border border-blue-400/30">
                        <i class="fas fa-users mr-2"></i>Tim Berpengalaman
                    </div>
                    <div class="bg-yellow-600/20 backdrop-blur-sm px-4 py-2 rounded-full border border-yellow-400/30">
                        <i class="fas fa-certificate mr-2"></i>Tersertifikasi
                    </div>
                    <div class="bg-blue-600/20 backdrop-blur-sm px-4 py-2 rounded-full border border-blue-400/30">
                        <i class="fas fa-heart mr-2"></i>Berdedikasi
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Overview -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div
                        class="bg-blue-600 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-800 mb-2">{{ $totalAnggota ?? '25' }}+</h3>
                    <p class="text-gray-600">Total Anggota Tim</p>
                </div>
                <div class="text-center">
                    <div
                        class="bg-yellow-500 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-star text-2xl"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-800 mb-2">{{ $totalPengalaman ?? '5' }}+</h3>
                    <p class="text-gray-600">Tahun Pengalaman</p>
                </div>
                <div class="text-center">
                    <div
                        class="bg-blue-600 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-trophy text-2xl"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-800 mb-2">{{ $totalProyek ?? '500' }}+</h3>
                    <p class="text-gray-600">Proyek Selesai</p>
                </div>
                <div class="text-center">
                    <div
                        class="bg-yellow-500 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-smile text-2xl"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-800 mb-2">{{ $kepuasanKlien ?? '98' }}%</h3>
                    <p class="text-gray-600">Kepuasan Klien</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Filter & Search Section -->
    <section class="py-12 bg-white" x-data="{ activeFilter: 'semua', searchTerm: '' }">
        <div class="container mx-auto px-6">
            <div class="flex flex-col lg:flex-row justify-between items-center mb-8">
                <div class="mb-4 lg:mb-0">
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">Tim Kami</h2>
                    <p class="text-gray-600">Kenali para profesional di balik kesuksesan setiap acara</p>
                </div>

                <!-- Search Bar -->
                <div class="relative">
                    <input type="text" x-model="searchTerm" placeholder="Cari anggota tim..."
                        class="pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent w-80">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <!-- Filter Buttons -->
            <div class="flex flex-wrap gap-4 mb-8">
                <button @click="activeFilter = 'semua'"
                    :class="activeFilter === 'semua' ? 'bg-blue-600 text-white' :
                        'bg-gray-200 text-gray-700 hover:bg-gray-300'"
                    class="px-6 py-2 rounded-full transition-colors">
                    <i class="fas fa-users mr-2"></i>Semua Tim
                </button>
                <button @click="activeFilter = 'management'"
                    :class="activeFilter === 'management' ? 'bg-blue-600 text-white' :
                        'bg-gray-200 text-gray-700 hover:bg-gray-300'"
                    class="px-6 py-2 rounded-full transition-colors">
                    <i class="fas fa-crown mr-2"></i>Management
                </button>
                <button @click="activeFilter = 'wedding_planner'"
                    :class="activeFilter === 'wedding_planner' ? 'bg-blue-600 text-white' :
                        'bg-gray-200 text-gray-700 hover:bg-gray-300'"
                    class="px-6 py-2 rounded-full transition-colors">
                    <i class="fas fa-heart mr-2"></i>Wedding Planner
                </button>
                <button @click="activeFilter = 'koordinator'"
                    :class="activeFilter === 'koordinator' ? 'bg-blue-600 text-white' :
                        'bg-gray-200 text-gray-700 hover:bg-gray-300'"
                    class="px-6 py-2 rounded-full transition-colors">
                    <i class="fas fa-clipboard-list mr-2"></i>Koordinator
                </button>
                <button @click="activeFilter = 'crew'"
                    :class="activeFilter === 'crew' ? 'bg-blue-600 text-white' :
                        'bg-gray-200 text-gray-700 hover:bg-gray-300'"
                    class="px-6 py-2 rounded-full transition-colors">
                    <i class="fas fa-tools mr-2"></i>Crew
                </button>
            </div>
        </div>
    </section>

    <!-- Team Members Grid -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                <!-- Sample Team Member Cards - Replace with dynamic data -->
                @forelse($anggotaTim ?? [] as $anggota)
                    <div class="bg-white rounded-xl shadow-lg card-hover overflow-hidden">
                        <div class="relative">
                            <div
                                class="h-48 bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                                @if ($anggota->foto_url)
                                    <img src="{{ $anggota->foto_url }}" alt="{{ $anggota->nama_lengkap }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <div
                                        class="w-24 h-24 bg-white/20 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                                        {{ $anggota->initials }}
                                    </div>
                                @endif
                            </div>
                            <div class="absolute top-4 right-4">
                                <span class="bg-yellow-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                    {{ $anggota->pekerjaan ?? 'Staff' }}
                                </span>
                            </div>
                        </div>

                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $anggota->nama_lengkap }}</h3>
                            <p class="text-blue-600 font-semibold mb-3">{{ $anggota->pekerjaan ?? 'Staff' }}</p>

                            <div class="space-y-2 text-sm text-gray-600 mb-4">
                                <div class="flex items-center">
                                    <i class="fas fa-envelope w-4 mr-2"></i>
                                    <span>{{ $anggota->email }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-phone w-4 mr-2"></i>
                                    <span>+62{{ $anggota->nomor_telepon }}</span>
                                </div>
                                @if ($anggota->usia)
                                    <div class="flex items-center">
                                        <i class="fas fa-birthday-cake w-4 mr-2"></i>
                                        <span>{{ $anggota->usia }} tahun</span>
                                    </div>
                                @endif
                                @if ($anggota->tanggal_mulai_gabung)
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar w-4 mr-2"></i>
                                        <span>Bergabung {{ $anggota->tanggal_mulai_gabung->format('M Y') }}</span>
                                    </div>
                                @endif
                            </div>

                            @if ($anggota->motivasi_kerja)
                                <div class="bg-gray-50 p-3 rounded-lg mb-4">
                                    <p class="text-sm text-gray-700 italic">
                                        "{{ Str::limit($anggota->motivasi_kerja, 80) }}"</p>
                                </div>
                            @endif

                            <div class="flex justify-between items-center">
                                <button
                                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                    <i class="fas fa-eye mr-1"></i>Detail
                                </button>
                                <div class="flex space-x-2">
                                    <button class="text-blue-600 hover:text-blue-800 transition-colors">
                                        <i class="fas fa-envelope"></i>
                                    </button>
                                    <button class="text-blue-600 hover:text-blue-800 transition-colors">
                                        <i class="fas fa-phone"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- Static Sample Cards for Demo -->
                    <div class="bg-white rounded-xl shadow-lg card-hover overflow-hidden">
                        <div class="relative">
                            <div
                                class="h-48 bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                                <div
                                    class="w-24 h-24 bg-white/20 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                                    AS
                                </div>
                            </div>
                            <div class="absolute top-4 right-4">
                                <span class="bg-yellow-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                    CEO
                                </span>
                            </div>
                        </div>

                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Andi Setiawan</h3>
                            <p class="text-blue-600 font-semibold mb-3">Chief Executive Officer</p>

                            <div class="space-y-2 text-sm text-gray-600 mb-4">
                                <div class="flex items-center">
                                    <i class="fas fa-envelope w-4 mr-2"></i>
                                    <span>andi@maknawedding.com</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-phone w-4 mr-2"></i>
                                    <span>+62812-3456-7890</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-calendar w-4 mr-2"></i>
                                    <span>Bergabung Jan 2019</span>
                                </div>
                            </div>

                            <div class="bg-gray-50 p-3 rounded-lg mb-4">
                                <p class="text-sm text-gray-700 italic">"Memimpin dengan visi untuk menciptakan momen
                                    tak terlupakan"</p>
                            </div>

                            <div class="flex justify-between items-center">
                                <button
                                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                    <i class="fas fa-eye mr-1"></i>Detail
                                </button>
                                <div class="flex space-x-2">
                                    <button class="text-blue-600 hover:text-blue-800 transition-colors">
                                        <i class="fas fa-envelope"></i>
                                    </button>
                                    <button class="text-blue-600 hover:text-blue-800 transition-colors">
                                        <i class="fas fa-phone"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg card-hover overflow-hidden">
                        <div class="relative">
                            <div
                                class="h-48 bg-gradient-to-br from-yellow-500 to-yellow-600 flex items-center justify-center">
                                <div
                                    class="w-24 h-24 bg-white/20 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                                    SR
                                </div>
                            </div>
                            <div class="absolute top-4 right-4">
                                <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                    Wedding Planner
                                </span>
                            </div>
                        </div>

                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Sari Rahayu</h3>
                            <p class="text-blue-600 font-semibold mb-3">Senior Wedding Planner</p>

                            <div class="space-y-2 text-sm text-gray-600 mb-4">
                                <div class="flex items-center">
                                    <i class="fas fa-envelope w-4 mr-2"></i>
                                    <span>sari@maknawedding.com</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-phone w-4 mr-2"></i>
                                    <span>+62813-4567-8901</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-calendar w-4 mr-2"></i>
                                    <span>Bergabung Mar 2020</span>
                                </div>
                            </div>

                            <div class="bg-gray-50 p-3 rounded-lg mb-4">
                                <p class="text-sm text-gray-700 italic">"Setiap detail adalah kunci kesempurnaan
                                    pernikahan"</p>
                            </div>

                            <div class="flex justify-between items-center">
                                <button
                                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                    <i class="fas fa-eye mr-1"></i>Detail
                                </button>
                                <div class="flex space-x-2">
                                    <button class="text-blue-600 hover:text-blue-800 transition-colors">
                                        <i class="fas fa-envelope"></i>
                                    </button>
                                    <button class="text-blue-600 hover:text-blue-800 transition-colors">
                                        <i class="fas fa-phone"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg card-hover overflow-hidden">
                        <div class="relative">
                            <div
                                class="h-48 bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                                <div
                                    class="w-24 h-24 bg-white/20 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                                    DP
                                </div>
                            </div>
                            <div class="absolute top-4 right-4">
                                <span class="bg-yellow-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                    Koordinator
                                </span>
                            </div>
                        </div>

                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Dedi Pratama</h3>
                            <p class="text-blue-600 font-semibold mb-3">Event Coordinator</p>

                            <div class="space-y-2 text-sm text-gray-600 mb-4">
                                <div class="flex items-center">
                                    <i class="fas fa-envelope w-4 mr-2"></i>
                                    <span>dedi@maknawedding.com</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-phone w-4 mr-2"></i>
                                    <span>+62814-5678-9012</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-calendar w-4 mr-2"></i>
                                    <span>Bergabung Jun 2021</span>
                                </div>
                            </div>

                            <div class="bg-gray-50 p-3 rounded-lg mb-4">
                                <p class="text-sm text-gray-700 italic">"Koordinasi yang sempurna untuk acara yang
                                    sempurna"</p>
                            </div>

                            <div class="flex justify-between items-center">
                                <button
                                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                    <i class="fas fa-eye mr-1"></i>Detail
                                </button>
                                <div class="flex space-x-2">
                                    <button class="text-blue-600 hover:text-blue-800 transition-colors">
                                        <i class="fas fa-envelope"></i>
                                    </button>
                                    <button class="text-blue-600 hover:text-blue-800 transition-colors">
                                        <i class="fas fa-phone"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg card-hover overflow-hidden">
                        <div class="relative">
                            <div
                                class="h-48 bg-gradient-to-br from-yellow-500 to-yellow-600 flex items-center justify-center">
                                <div
                                    class="w-24 h-24 bg-white/20 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                                    LM
                                </div>
                            </div>
                            <div class="absolute top-4 right-4">
                                <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                    Crew
                                </span>
                            </div>
                        </div>

                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Lisa Maharani</h3>
                            <p class="text-blue-600 font-semibold mb-3">Decoration Crew</p>

                            <div class="space-y-2 text-sm text-gray-600 mb-4">
                                <div class="flex items-center">
                                    <i class="fas fa-envelope w-4 mr-2"></i>
                                    <span>lisa@maknawedding.com</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-phone w-4 mr-2"></i>
                                    <span>+62815-6789-0123</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-calendar w-4 mr-2"></i>
                                    <span>Bergabung Sep 2022</span>
                                </div>
                            </div>

                            <div class="bg-gray-50 p-3 rounded-lg mb-4">
                                <p class="text-sm text-gray-700 italic">"Kreativitas dalam setiap sentuhan dekorasi"
                                </p>
                            </div>

                            <div class="flex justify-between items-center">
                                <button
                                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                    <i class="fas fa-eye mr-1"></i>Detail
                                </button>
                                <div class="flex space-x-2">
                                    <button class="text-blue-600 hover:text-blue-800 transition-colors">
                                        <i class="fas fa-envelope"></i>
                                    </button>
                                    <button class="text-blue-600 hover:text-blue-800 transition-colors">
                                        <i class="fas fa-phone"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Team Performance Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Performa Tim</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Lihat pencapaian dan kontribusi setiap anggota tim dalam
                    kesuksesan perusahaan</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Top Performers -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-8">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-trophy text-yellow-500 mr-3"></i>
                        Top Performers Bulan Ini
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between bg-white p-4 rounded-lg">
                            <div class="flex items-center">
                                <div
                                    class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold mr-4">
                                    SR
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">Sari Rahayu</h4>
                                    <p class="text-sm text-gray-600">Wedding Planner</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-blue-600">15</p>
                                <p class="text-sm text-gray-600">Proyek</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between bg-white p-4 rounded-lg">
                            <div class="flex items-center">
                                <div
                                    class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center text-white font-bold mr-4">
                                    DP
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">Dedi Pratama</h4>
                                    <p class="text-sm text-gray-600">Event Coordinator</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-yellow-500">12</p>
                                <p class="text-sm text-gray-600">Proyek</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between bg-white p-4 rounded-lg">
                            <div class="flex items-center">
                                <div
                                    class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold mr-4">
                                    LM
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">Lisa Maharani</h4>
                                    <p class="text-sm text-gray-600">Decoration Crew</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-blue-500">10</p>
                                <p class="text-sm text-gray-600">Proyek</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Team Activities -->
                <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-8">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-clock text-blue-600 mr-3"></i>
                        Aktivitas Terbaru
                    </h3>
                    <div class="space-y-4">
                        <div class="bg-white p-4 rounded-lg border-l-4 border-blue-600">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-semibold text-gray-800">Proyek Wedding Andi & Sari</h4>
                                <span class="text-xs text-gray-500">2 jam lalu</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">Sari Rahayu menyelesaikan konsep dekorasi</p>
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">Selesai</span>
                        </div>

                        <div class="bg-white p-4 rounded-lg border-l-4 border-yellow-500">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-semibold text-gray-800">Proyek Wedding Budi & Rina</h4>
                                <span class="text-xs text-gray-500">4 jam lalu</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">Dedi Pratama sedang koordinasi vendor</p>
                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs">Dalam
                                Proses</span>
                        </div>

                        <div class="bg-white p-4 rounded-lg border-l-4 border-blue-500">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-semibold text-gray-800">Meeting Tim Mingguan</h4>
                                <span class="text-xs text-gray-500">1 hari lalu</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">Evaluasi proyek dan planning minggu depan</p>
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs">Meeting</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Actions -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Aksi Cepat</h2>
                <p class="text-gray-600">Kelola tim dan anggota dengan mudah</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <a href="#" class="bg-white p-6 rounded-xl shadow-lg card-hover text-center group">
                    <div
                        class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-blue-700 transition-colors">
                        <i class="fas fa-user-plus text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Tambah Anggota</h3>
                    <p class="text-gray-600 text-sm">Rekrut anggota tim baru</p>
                </a>

                <a href="#" class="bg-white p-6 rounded-xl shadow-lg card-hover text-center group">
                    <div
                        class="w-16 h-16 bg-yellow-500 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-yellow-600 transition-colors">
                        <i class="fas fa-chart-bar text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Laporan Kinerja</h3>
                    <p class="text-gray-600 text-sm">Analisis performa tim</p>
                </a>

                <a href="#" class="bg-white p-6 rounded-xl shadow-lg card-hover text-center group">
                    <div
                        class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-blue-700 transition-colors">
                        <i class="fas fa-calendar-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Jadwal Tim</h3>
                    <p class="text-gray-600 text-sm">Atur jadwal kerja</p>
                </a>

                <a href="#" class="bg-white p-6 rounded-xl shadow-lg card-hover text-center group">
                    <div
                        class="w-16 h-16 bg-yellow-500 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-yellow-600 transition-colors">
                        <i class="fas fa-graduation-cap text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Pelatihan</h3>
                    <p class="text-gray-600 text-sm">Program pengembangan</p>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    @include('front.footer')

    <script>
        // Add smooth scrolling and interactive features
        document.addEventListener('DOMContentLoaded', function() {
            // Animate cards on scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Observe all cards
            document.querySelectorAll('.card-hover').forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(card);
            });
        });
    </script>
</body>

</html>
