@extends('layouts.app')

@section('title', 'Manajemen Project')

@section('content')
    <!-- Access Control handled in Controller -->

    @if(!$hasAccess)
        <!-- Access Denied Section -->
        <section class="min-h-screen bg-gray-50 flex items-center justify-center">
            <div class="text-center px-6 py-8">
                <div class="bg-white rounded-2xl shadow-lg p-8 max-w-md mx-auto">
                    <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-4">Akses Ditolak</h1>
                    <p class="text-gray-600 mb-6">
                        Anda tidak memiliki akses ke halaman Project Management. 
                        Hanya <strong>Super Admin</strong>, <strong>Account Manager</strong>, dan <strong>Finance</strong> yang dapat mengakses halaman ini.
                    </p>
                    <div class="space-y-3">
                        <a href="{{ route('filament.admin.pages.dashboard') }}" 
                           class="block w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-medium hover:bg-blue-700 transition-colors">
                            Kembali ke Dashboard
                        </a>
                        @if(auth()->check())
                            <p class="text-sm text-gray-500">
                                Role Anda saat ini: 
                                <span class="font-medium">
                                    @if(method_exists(auth()->user(), 'roles'))
                                        {{ auth()->user()->roles->pluck('name')->join(', ') ?: 'Tidak ada role' }}
                                    @else
                                        {{ auth()->user()->role ?: 'Tidak ada role' }}
                                    @endif
                                </span>
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @else
        <!-- Original Content -->
        <!-- Header -->
        @include('front.header')

    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-gray-900 via-blue-900 to-black text-white py-20">
        <div class="container mx-auto px-6">
            <div class="text-center max-w-4xl mx-auto">
                <h1 class="text-5xl md:text-6xl font-bold mb-6 leading-tight">
                    <span class="bg-gradient-to-r from-blue-400 to-purple-600 bg-clip-text text-transparent">
                        Project Management
                    </span>
                </h1>
                <p class="text-xl text-gray-300 mb-8 max-w-2xl mx-auto">
                    Kelola semua project wedding organizer dengan sistem manajemen yang komprehensif dan modern
                </p>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-6">
            <!-- Section Header -->
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-blue-600 mb-4">Statistik Project</h2>
                <p class="text-gray-600">Dashboard overview untuk monitoring performa project</p>
            </div>

            <!-- Main Stats Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Projects -->
                <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-lg transition-shadow duration-300">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold text-blue-600 mb-1">{{ $stats['total_projects'] ?? 0 }}</div>
                        <div class="text-sm text-gray-600">Total Project</div>
                    </div>
                </div>

                <!-- Active Projects -->
                <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-lg transition-shadow duration-300">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold text-blue-600 mb-1">{{ $stats['active_projects'] ?? 0 }}</div>
                        <div class="text-sm text-gray-600">Project Aktif</div>
                    </div>
                </div>

                <!-- Completed Projects -->
                <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-lg transition-shadow duration-300">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold text-blue-600 mb-1">{{ $stats['completed_projects'] ?? 0 }}</div>
                        <div class="text-sm text-gray-600">Project Selesai</div>
                    </div>
                </div>

                <!-- Revenue -->
                <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-lg transition-shadow duration-300">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                </path>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold text-blue-600 mb-1">Rp
                            {{ number_format($stats['total_revenue_this_year'] ?? 0, 0, ',', '.') }}</div>
                        <div class="text-sm text-gray-600">Revenue 2025</div>
                    </div>
                </div>
            </div>

            <!-- Additional Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Payment Stats -->
                <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-blue-600">Status Pembayaran</h3>
                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Lunas</span>
                            <span class="font-semibold text-blue-600">{{ $stats['paid_projects'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Belum Lunas</span>
                            <span class="font-semibold text-blue-600">{{ $stats['unpaid_projects'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                <!-- Average Value -->
                <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-blue-600">Rata-rata Nilai</h3>
                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-blue-600">
                        Rp {{ number_format($stats['average_project_value'] ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="text-sm text-gray-600">Per project</div>
                </div>

                <!-- Monthly Stats -->
                <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-blue-600">Bulan Ini</h3>
                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['projects_this_month'] ?? 0 }}</div>
                    <div class="text-sm text-gray-600">Project baru</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Filter & Search Section -->
    <section class="py-8 bg-white border-b shadow-sm">
        <div class="container mx-auto px-6">
            <div class="flex flex-col lg:flex-row gap-6 items-center justify-between">
                <!-- Filter Buttons -->
                {{-- <div class="flex flex-wrap gap-3" x-data="{ activeFilter: 'all' }">
                    <button @click="activeFilter = 'all'; filterProjects('all')"
                        :class="activeFilter === 'all' ? 'bg-blue-600 text-white shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        class="px-5 py-2.5 rounded-lg font-medium transition-all duration-200 flex items-center">
                        <i class="fas fa-list mr-2"></i>
                        <span>Semua Project</span>
                        <span class="ml-2 bg-white/20 px-2 py-0.5 rounded-full text-xs">{{ $projects->count() }}</span>
                    </button>
                    <button @click="activeFilter = 'processing'; filterProjects('processing')"
                        :class="activeFilter === 'processing' ? 'bg-green-600 text-white shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        class="px-5 py-2.5 rounded-lg font-medium transition-all duration-200 flex items-center">
                        <i class="fas fa-cogs mr-2"></i>
                        <span>Processing</span>
                        <span class="ml-2 bg-white/20 px-2 py-0.5 rounded-full text-xs">
                            {{ $projects->where('status', \App\Enums\OrderStatus::Processing)->count() }}
                        </span>
                    </button>
                    <button @click="activeFilter = 'done'; filterProjects('done')"
                        :class="activeFilter === 'done' ? 'bg-blue-800 text-white shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        class="px-5 py-2.5 rounded-lg font-medium transition-all duration-200 flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span>Done</span>
                        <span class="ml-2 bg-white/20 px-2 py-0.5 rounded-full text-xs">
                            {{ $projects->where('status', \App\Enums\OrderStatus::Done)->count() }}
                        </span>
                    </button>
                    <button @click="activeFilter = 'pending'; filterProjects('pending')"
                        :class="activeFilter === 'pending' ? 'bg-yellow-600 text-white shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        class="px-5 py-2.5 rounded-lg font-medium transition-all duration-200 flex items-center">
                        <i class="fas fa-clock mr-2"></i>
                        <span>Pending</span>
                        <span class="ml-2 bg-white/20 px-2 py-0.5 rounded-full text-xs">
                            {{ $projects->where('status', \App\Enums\OrderStatus::Pending)->count() }}
                        </span>
                    </button>
                    <button @click="activeFilter = 'cancelled'; filterProjects('cancelled')"
                        :class="activeFilter === 'cancelled' ? 'bg-red-600 text-white shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        class="px-5 py-2.5 rounded-lg font-medium transition-all duration-200 flex items-center">
                        <i class="fas fa-times-circle mr-2"></i>
                        <span>Cancelled</span>
                        <span class="ml-2 bg-white/20 px-2 py-0.5 rounded-full text-xs">
                            {{ $projects->where('status', \App\Enums\OrderStatus::Cancelled)->count() }}
                        </span>
                    </button>
                </div> --}}

                <!-- Search & Actions -->
                <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">

                    <!-- Search Input -->
                    <div x-data="{ query: '' }" class="relative flex-grow lg:flex-grow-0">
                        <label for="search" class="sr-only">Cari project</label>
                        <input id="search" type="text" x-model="query" @input="searchProjects(query)"
                            placeholder="Cari project berdasarkan nama, klien..."
                            class="w-full lg:w-80 pl-11 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-gray-50 focus:bg-white text-sm" />

                        <!-- Search Icon -->
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
                            </svg>
                        </div>

                        <!-- Clear Button -->
                        <button x-show="query.length" @click="query=''; searchProjects('')" type="button"
                            aria-label="Clear search"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Sort Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" type="button"
                            class="px-4 py-3 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-all duration-200 inline-flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4h18M4 9h16M6 14h12M8 19h8" />
                            </svg>
                            <span>Urutkan</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 ml-1 text-gray-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" @click.away="open = false" x-transition
                            class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-md border border-gray-200 z-50 overflow-hidden">
                            <ul class="py-1 text-sm text-gray-700">
                                <li><button @click="sortProjects('newest'); open=false"
                                        class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6h4" />
                                        </svg> Terbaru
                                    </button></li>
                                <li><button @click="sortProjects('oldest'); open=false"
                                        class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 18v-6H8" />
                                        </svg> Terlama
                                    </button></li>
                                <li><button @click="sortProjects('name'); open=false"
                                        class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 9l7-7 7 7" />
                                        </svg> Nama A-Z
                                    </button></li>
                                <li><button @click="sortProjects('budget'); open=false"
                                        class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-4.418 0-8 1.79-8 4v4h16v-4c0-2.21-3.582-4-8-4z" />
                                        </svg> Budget
                                    </button></li>
                            </ul>
                        </div>
                    </div>

                    <!-- New Project Button -->
                    @auth
                        <a href="/admin/orders/create"
                            class="px-5 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg font-medium hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-md hover:shadow-lg inline-flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>New Project</span>
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Active Filter Indicator -->
            <div class="mt-4 flex items-center gap-2" x-data
                x-show="document.querySelector('[x-data] button.bg-blue-600')">
                <span class="text-sm text-gray-500">Filter aktif:</span>
                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium"
                    x-text="getActiveFilterText()"></span>
                <button @click="resetFilters()" class="text-sm text-blue-600 hover:text-blue-800 underline">
                    Reset semua filter
                </button>
            </div>
        </div>
    </section>

    <!-- Projects Grid -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-6">
            <!-- Section Header -->
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Project Gallery</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Lihat semua project wedding dalam tampilan card yang modern
                </p>
            </div>

            <!-- Project Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" data-projects-container>
                @forelse($projects as $project)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300"
                        data-project-card data-project-id="{{ $project->id }}"
                        data-project-status="{{ $project->status ? $project->status->getLabel() : 'N/A' }}"
                        data-project-title="{{ $project->name }}"
                        data-project-client="{{ $project->client_name ?? 'N/A' }}"
                        data-project-date="{{ $project->created_at->format('Y-m-d') }}"
                        data-project-budget="{{ $project->total_budget ?? 0 }}">

                        <!-- Card Header -->
                        <div
                            class="bg-gradient-to-r 
                            @if ($project->status === \App\Enums\OrderStatus::Processing) from-blue-600 to-blue-800
                            @elseif($project->status === \App\Enums\OrderStatus::Done) from-gray-800 to-black
                            @elseif($project->status === \App\Enums\OrderStatus::Pending) from-blue-500 to-blue-700
                            @elseif($project->status === \App\Enums\OrderStatus::Cancelled) from-gray-600 to-gray-800
                            @else from-blue-400 to-blue-600 @endif p-6 text-white">

                            <div class="flex items-center justify-between mb-4">
                                <span
                                    class="
                                    @if ($project->status === \App\Enums\OrderStatus::Processing) bg-blue-100 text-blue-800
                                    @elseif($project->status === \App\Enums\OrderStatus::Done) bg-gray-100 text-gray-800  
                                    @elseif($project->status === \App\Enums\OrderStatus::Pending) bg-blue-50 text-blue-700
                                    @elseif($project->status === \App\Enums\OrderStatus::Cancelled) bg-gray-200 text-gray-700
                                    @else bg-blue-50 text-blue-600 @endif px-3 py-1 rounded-full text-sm font-semibold"
                                    data-project-status>
                                    {{ $project->status ? $project->status->getLabel() : 'N/A' }}
                                </span>
                                <i class="fas fa-heart text-blue-200 text-xl"></i>
                            </div>

                            <h3 class="text-xl font-bold mb-2 whitespace-nowrap overflow-hidden text-ellipsis"
                                data-project-title>{{ $project->name }}</h3>
                            <p class="text-blue-200 text-sm" data-project-description>{{ $project->number }}</p>
                            @if ($project->no_kontrak)
                                <p class="text-blue-200 text-xs mt-1">Kontrak: {{ $project->no_kontrak }}</p>
                            @endif
                        </div>

                        <!-- Card Body -->
                        <div class="p-6">
                            <div class="space-y-3 mb-6">
                                <!-- Client Info -->
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-user w-5 text-blue-600 mr-3"></i>
                                    <span class="text-sm" data-project-client>{{ $project->user?->name ?? 'N/A' }}</span>
                                </div>

                                <!-- Employee Info -->
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-user-tie w-5 text-gray-800 mr-3"></i>
                                    <span class="text-sm">{{ $project->employee?->name ?? 'N/A' }}</span>
                                </div>

                                <!-- Pax Info -->
                                @if ($project->pax)
                                    <div class="flex items-center text-gray-600">
                                        <i class="fas fa-users w-5 text-blue-500 mr-3"></i>
                                        <span class="text-sm">{{ $project->pax }} Tamu</span>
                                    </div>
                                @endif

                                <!-- Price Info -->
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-money-bill-wave w-5 text-blue-700 mr-3"></i>
                                    <span class="text-sm font-semibold text-blue-700" data-project-budget>
                                        Rp {{ number_format($project->total_price, 0, ',', '.') }}
                                    </span>
                                </div>

                                <!-- Payment Status -->
                                <div class="flex items-center text-gray-600">
                                    <i
                                        class="fas fa-credit-card w-5 {{ $project->is_paid ? 'text-blue-600' : 'text-gray-600' }} mr-3"></i>
                                    <span
                                        class="text-sm font-medium {{ $project->is_paid ? 'text-blue-600' : 'text-gray-600' }}">
                                        {{ $project->is_paid ? 'Paid' : 'Unpaid' }}
                                        @if ($project->paid_amount > 0 && !$project->is_paid)
                                            <span class="text-xs text-gray-500">
                                                ({{ number_format(($project->paid_amount / $project->total_price) * 100, 0) }}%)
                                            </span>
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <!-- Progress Bar (Payment Progress) -->
                            @if ($project->total_price > 0)
                                @php
                                    $paidAmount = $project->paid_amount ?? 0;
                                    $totalPrice = $project->total_price ?? 1;
                                    $progressPercentage = $totalPrice > 0 ? ($paidAmount / $totalPrice) * 100 : 0;
                                    $progressPercentage = min($progressPercentage, 100); // Cap at 100%

                                    // Debug: simulate some progress if paid_amount is 0
                                    if ($paidAmount == 0 && $project->status === \App\Enums\OrderStatus::Processing) {
                                        $paidAmount = $totalPrice * 0.3; // 30% paid for processing
                                        $progressPercentage = 30;
                                    } elseif ($paidAmount == 0 && $project->status === \App\Enums\OrderStatus::Done) {
                                        $paidAmount = $totalPrice; // 100% paid for done
                                        $progressPercentage = 100;
                                    }
                                @endphp
                                <div class="mb-4 bg-gray-50 p-4 rounded-lg border border-gray-100">
                                    <div class="flex justify-between items-center mb-3">
                                        <div class="flex items-center">
                                            <i
                                                class="fas fa-chart-line mr-2 
                                            @if ($project->status === \App\Enums\OrderStatus::Processing) text-blue-600
                                            @elseif($project->status === \App\Enums\OrderStatus::Done) text-gray-800
                                            @elseif($project->status === \App\Enums\OrderStatus::Pending) text-blue-500
                                            @elseif($project->status === \App\Enums\OrderStatus::Cancelled) text-gray-600
                                            @else text-blue-400 @endif"></i>
                                            <span class="font-semibold text-gray-700">Payment Progress</span>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span
                                                class="
                                            @if ($project->status === \App\Enums\OrderStatus::Processing) text-blue-600
                                            @elseif($project->status === \App\Enums\OrderStatus::Done) text-gray-800
                                            @elseif($project->status === \App\Enums\OrderStatus::Pending) text-blue-500
                                            @elseif($project->status === \App\Enums\OrderStatus::Cancelled) text-gray-600
                                            @else text-blue-400 @endif font-bold text-lg">
                                                {{ number_format($progressPercentage, 0) }}%
                                            </span>
                                            @if ($project->is_paid || $progressPercentage >= 100)
                                                <span
                                                    class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full font-medium">
                                                    <i class="fas fa-check-circle mr-1"></i>LUNAS
                                                </span>
                                            @else
                                                <span
                                                    class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full font-medium">
                                                    <i class="fas fa-clock mr-1"></i>BELUM LUNAS
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Progress Bar with Gradient and Animation -->
                                    <div class="relative w-full bg-gray-300 rounded-full h-4 overflow-hidden shadow-inner">
                                        <div class="
                                        @if ($project->status === \App\Enums\OrderStatus::Processing) bg-gradient-to-r from-blue-400 via-blue-500 to-blue-600
                                        @elseif($project->status === \App\Enums\OrderStatus::Done) bg-gradient-to-r from-gray-600 via-gray-700 to-gray-800
                                        @elseif($project->status === \App\Enums\OrderStatus::Pending) bg-gradient-to-r from-blue-300 via-blue-400 to-blue-500
                                        @elseif($project->status === \App\Enums\OrderStatus::Cancelled) bg-gradient-to-r from-gray-400 via-gray-500 to-gray-600
                                        @else bg-gradient-to-r from-blue-200 via-blue-300 to-blue-400 @endif h-4 rounded-full shadow-lg transition-all duration-700 ease-out relative overflow-hidden"
                                            style="width: {{ $progressPercentage }}%">
                                            <!-- Shine Effect -->
                                            <div
                                                class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-transparent opacity-30 animate-pulse">
                                            </div>
                                        </div>
                                        <!-- Progress indicator text inside bar -->
                                        @if ($progressPercentage > 15)
                                            <div class="absolute inset-0 flex items-center pl-3">
                                                <span class="text-white text-xs font-bold drop-shadow-sm">
                                                    {{ number_format($progressPercentage, 0) }}%
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Payment Details -->
                                    <div class="flex justify-between items-center mt-3 text-sm">
                                        <div class="text-gray-600">
                                            <span class="font-medium">Terbayar:</span>
                                            <span class="text-blue-700 font-bold">Rp
                                                {{ number_format($paidAmount, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="text-gray-600">
                                            <span class="font-medium">Sisa:</span>
                                            <span class="text-gray-800 font-bold">Rp
                                                {{ number_format($totalPrice - $paidAmount, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="text-gray-600">
                                            <span class="font-medium">Total:</span>
                                            <span class="text-gray-900 font-bold">Rp
                                                {{ number_format($totalPrice, 0, ',', '.') }}</span>
                                        </div>
                                    </div>

                                    <!-- Payment Milestone Tracker -->
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-xs font-medium text-gray-600">Payment Milestones</span>
                                            <span class="text-xs text-gray-500">
                                                @php
                                                    if ($progressPercentage >= 100) {
                                                        echo 'Completed';
                                                    } elseif ($progressPercentage >= 75) {
                                                        echo '3 of 4 milestones';
                                                    } elseif ($progressPercentage >= 50) {
                                                        echo '2 of 4 milestones';
                                                    } elseif ($progressPercentage >= 25) {
                                                        echo '1 of 4 milestones';
                                                    } else {
                                                        echo '0 of 4 milestones';
                                                    }
                                                @endphp
                                            </span>
                                        </div>
                                        <div class="flex items-center space-x-1">
                                            @for ($i = 1; $i <= 4; $i++)
                                                @php
                                                    $milestone = $i * 25;
                                                    $reached = $progressPercentage >= $milestone;
                                                @endphp
                                                <div class="flex-1 flex flex-col items-center">
                                                    <div
                                                        class="w-6 h-6 rounded-full border-2 flex items-center justify-center text-xs font-bold
                                                    @if ($reached) @if ($project->status === \App\Enums\OrderStatus::Processing) bg-blue-600 border-blue-600 text-white
                                                        @elseif($project->status === \App\Enums\OrderStatus::Done) bg-gray-800 border-gray-800 text-white
                                                        @else bg-blue-500 border-blue-500 text-white @endif
@else
border-gray-300 text-gray-400 bg-white
                                                    @endif">
                                                        @if ($reached)
                                                            <i class="fas fa-check text-xs"></i>
                                                        @else
                                                            {{ $i }}
                                                        @endif
                                                    </div>
                                                    <span
                                                        class="text-xs mt-1 
                                                    @if ($reached) @if ($project->status === \App\Enums\OrderStatus::Processing) text-blue-600
                                                        @elseif($project->status === \App\Enums\OrderStatus::Done) text-gray-800
                                                        @else text-blue-500 @endif font-medium
@else
text-gray-400
                                                    @endif">{{ $milestone }}%</span>
                                                </div>
                                                @if ($i < 4)
                                                    <div
                                                        class="flex-1 h-0.5 mx-1 
                                                    @if ($progressPercentage >= ($i + 1) * 25) @if ($project->status === \App\Enums\OrderStatus::Processing) bg-blue-600
                                                        @elseif($project->status === \App\Enums\OrderStatus::Done) bg-gray-800
                                                        @else bg-blue-500 @endif
@else
bg-gray-300
                                                    @endif">
                                                    </div>
                                                @endif
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="flex gap-2">
                                @auth
                                    <a href="{{ route('project.show', $project) }}"
                                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition-colors text-sm font-medium text-center">
                                        <i class="fas fa-eye mr-2"></i>Masuk
                                    </a>
                                    <button onclick="editProject({{ $project->id }})"
                                        class="flex-1 bg-gray-200 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-300 transition-colors text-sm font-medium">
                                        <i class="fas fa-edit mr-2"></i>Edit
                                    </button>
                                @else
                                    <a href="{{ route('login') }}"
                                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition-colors text-sm font-medium text-center">
                                        <i class="fas fa-sign-in-alt mr-2"></i>Login untuk Masuk
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- Empty State -->
                    <div class="col-span-full text-center py-12">
                        <div class="text-6xl mb-4">📝</div>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum ada project</h3>
                        <p class="text-gray-500 mb-6">Mulai dengan menambahkan project wedding pertama Anda</p>
                        <button onclick="addNewProject()"
                            class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Tambah Project Baru
                        </button>
                    </div>
                @endforelse
            </div>

            <!-- Pagination for Grid View -->
            @if ($projects->hasPages())
                <div class="mt-12 flex justify-center">
                    {{ $projects->links() }}
                </div>
            @endif

            <!-- Load More Button Alternative -->
            <div class="text-center mt-12">
                <div class="flex flex-wrap justify-center gap-4">
                    <button onclick="addNewProject()"
                        class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Tambah Project Baru
                    </button>
                    <button onclick="exportProjects()"
                        class="bg-gray-800 text-white px-8 py-3 rounded-lg font-semibold hover:bg-black transition-colors">
                        <i class="fas fa-download mr-2"></i>Export Data
                    </button>
                    <a href="{{ route('project') }}"
                        class="bg-blue-500 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-600 transition-colors">
                        <i class="fas fa-table mr-2"></i>View Table
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Actions Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Quick Actions</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Akses cepat untuk mengelola project wedding Anda
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div
                    class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-xl border border-blue-200 hover:shadow-lg transition-shadow cursor-pointer">
                    <div class="bg-blue-600 w-12 h-12 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-plus text-white text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">New Project</h3>
                    <p class="text-gray-600 text-sm">Buat project wedding baru</p>
                </div>
                <div
                    class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-6 rounded-xl border border-yellow-200 hover:shadow-lg transition-shadow cursor-pointer">
                    <div class="bg-yellow-500 w-12 h-12 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-calendar-check text-white text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Schedule Event</h3>
                    <p class="text-gray-600 text-sm">Atur jadwal acara wedding</p>
                </div>
                <div
                    class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-xl border border-green-200 hover:shadow-lg transition-shadow cursor-pointer">
                    <div class="bg-green-600 w-12 h-12 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-chart-line text-white text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">View Reports</h3>
                    <p class="text-gray-600 text-sm">Lihat laporan project</p>
                </div>
                <div
                    class="bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-xl border border-purple-200 hover:shadow-lg transition-shadow cursor-pointer">
                    <div class="bg-purple-600 w-12 h-12 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-users text-white text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Manage Team</h3>
                    <p class="text-gray-600 text-sm">Kelola tim wedding organizer</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Recent Activities -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Recent Activities</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Aktivitas terbaru dari semua project wedding
                </p>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="space-y-4">
                    <div class="flex items-center p-4 bg-blue-50 rounded-lg">
                        <div class="bg-blue-600 w-10 h-10 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-check text-white"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">Wedding Ahmad & Siti - Venue confirmed</p>
                            <p class="text-gray-600 text-sm">Grand Ballroom Hotel Mulia telah dikonfirmasi</p>
                        </div>
                        <span class="text-gray-500 text-sm">2 hours ago</span>
                    </div>
                    <div class="flex items-center p-4 bg-yellow-50 rounded-lg">
                        <div class="bg-yellow-500 w-10 h-10 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-camera text-white"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">Wedding Doni & Lisa - Photographer booked</p>
                            <p class="text-gray-600 text-sm">Fotografer profesional telah dibooking</p>
                        </div>
                        <span class="text-gray-500 text-sm">5 hours ago</span>
                    </div>
                    <div class="flex items-center p-4 bg-green-50 rounded-lg">
                        <div class="bg-green-600 w-10 h-10 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-money-bill-wave text-white"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">Wedding Budi & Rina - Payment received</p>
                            <p class="text-gray-600 text-sm">Pembayaran tahap kedua telah diterima</p>
                        </div>
                        <span class="text-gray-500 text-sm">1 day ago</span>
                    </div>
                    <div class="flex items-center p-4 bg-purple-50 rounded-lg">
                        <div class="bg-purple-600 w-10 h-10 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-utensils text-white"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">Wedding Eko & Maya - Catering menu finalized</p>
                            <p class="text-gray-600 text-sm">Menu catering telah difinalisasi dengan klien</p>
                        </div>
                        <span class="text-gray-500 text-sm">2 days ago</span>
                    </div>
                </div>
                <div class="text-center mt-6">
                    <button class="text-blue-600 font-semibold hover:text-blue-700 transition-colors">
                        View All Activities <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    @include('front.footer')

    <script>
        // Add some interactive functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Filter buttons functionality
            const filterButtons = document.querySelectorAll(
                'button[class*="bg-gray-200"], button[class*="bg-blue-600"]');
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons
                    filterButtons.forEach(btn => {
                        btn.classList.remove('bg-blue-600', 'text-white');
                        btn.classList.add('bg-gray-200', 'text-gray-700');
                    });
                    // Add active class to clicked button
                    this.classList.remove('bg-gray-200', 'text-gray-700');
                    this.classList.add('bg-blue-600', 'text-white');
                });
            });

            // Search functionality
            const searchInput = document.querySelector('input[placeholder="Cari project..."]');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    // Add search functionality here
                    console.log('Searching for:', this.value);
                });
            }
        });

        // Enhanced Project Filter, Search & Sort Functions
        let allProjects = [];
        let filteredProjects = [];
        let currentFilter = 'all';
        let currentSearchTerm = '';
        let currentSort = 'newest';

        // Initialize projects data
        document.addEventListener('DOMContentLoaded', function() {
            // Store all project elements
            allProjects = Array.from(document.querySelectorAll('[data-project-card]'));
            filteredProjects = [...allProjects];

            console.log(`Found ${allProjects.length} projects`);

            // Debug: log status values
            allProjects.forEach((project, index) => {
                const status = project.querySelector('[data-project-status]')?.textContent.trim();
                console.log(`Project ${index + 1}: Status = "${status}"`);
            });
        });

        // Search functionality
        function searchProjects(searchTerm) {
            currentSearchTerm = searchTerm.toLowerCase();
            console.log('Searching for:', currentSearchTerm);
            applyFiltersAndSearch();
        }

        // Filter functionality  
        function filterProjects(status) {
            currentFilter = status;
            console.log('Filtering by status:', status);
            applyFiltersAndSearch();
        }

        // Sort functionality
        function sortProjects(sortType) {
            currentSort = sortType;
            console.log('Sorting by:', sortType);
            applyFiltersAndSearch();
        }

        // Apply all filters, search, and sorting
        function applyFiltersAndSearch() {
            let results = [...allProjects];

            // Apply status filter
            if (currentFilter !== 'all') {
                console.log(`Filtering by: ${currentFilter}`);

                results = results.filter(project => {
                    const statusElement = project.querySelector('[data-project-status]');
                    const status = statusElement ? statusElement.textContent.trim().toLowerCase() : '';

                    console.log(`Project status found: "${status}" | Looking for: "${currentFilter}"`);

                    switch (currentFilter) {
                        case 'processing':
                            return status === 'processing';
                        case 'done':
                            return status === 'done' || status === 'completed' || status === 'selesai';
                        case 'pending':
                            return status === 'pending' || status === 'menunggu';
                        case 'cancelled':
                            return status === 'cancelled' || status === 'canceled' || status === 'dibatalkan';
                        default:
                            return true;
                    }
                });

                console.log(`After filtering: ${results.length} projects`);
            }

            // Apply search filter
            if (currentSearchTerm) {
                results = results.filter(project => {
                    const title = project.querySelector('[data-project-title]')?.textContent.toLowerCase() || '';
                    const client = project.querySelector('[data-project-client]')?.textContent.toLowerCase() || '';
                    const description = project.querySelector('[data-project-description]')?.textContent
                        .toLowerCase() || '';

                    return title.includes(currentSearchTerm) ||
                        client.includes(currentSearchTerm) ||
                        description.includes(currentSearchTerm);
                });
            }

            // Apply sorting
            results.sort((a, b) => {
                switch (currentSort) {
                    case 'newest':
                        const dateA = new Date(a.getAttribute('data-project-date') || '');
                        const dateB = new Date(b.getAttribute('data-project-date') || '');
                        return dateB - dateA;

                    case 'oldest':
                        const dateA2 = new Date(a.getAttribute('data-project-date') || '');
                        const dateB2 = new Date(b.getAttribute('data-project-date') || '');
                        return dateA2 - dateB2;

                    case 'name':
                        const titleA = a.querySelector('[data-project-title]')?.textContent || '';
                        const titleB = b.querySelector('[data-project-title]')?.textContent || '';
                        return titleA.localeCompare(titleB);

                    case 'budget':
                        const budgetA = parseInt(a.getAttribute('data-project-budget') || '0');
                        const budgetB = parseInt(b.getAttribute('data-project-budget') || '0');
                        return budgetB - budgetA;

                    default:
                        return 0;
                }
            });

            // Update display
            updateProjectDisplay(results);
            updateResultsCount(results.length);
        }

        // Update project display
        function updateProjectDisplay(projects) {
            const container = document.querySelector('[data-projects-container]');
            if (!container) return;

            console.log(`Displaying ${projects.length} out of ${allProjects.length} projects`);

            // Hide all projects
            allProjects.forEach(project => {
                project.style.display = 'none';
                project.style.animation = '';
            });

            // Show filtered projects with animation
            projects.forEach((project, index) => {
                project.style.display = 'block';
                project.style.animation = `fadeInUp 0.5s ease forwards ${index * 0.1}s`;
            });

            // Show no results message if needed
            showNoResultsMessage(projects.length === 0, container);
        }

        // Show/hide no results message
        function showNoResultsMessage(show, container) {
            const noResults = container.querySelector('[data-no-results]');

            if (show && !noResults) {
                const noResultsDiv = document.createElement('div');
                noResultsDiv.setAttribute('data-no-results', '');
                noResultsDiv.className = 'col-span-full text-center py-12';
                noResultsDiv.innerHTML = `
                    <div class="text-gray-400 mb-4">
                        <i class="fas fa-search text-6xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">Tidak ada project ditemukan</h3>
                    <p class="text-gray-500 mb-4">Coba ubah filter atau kata kunci pencarian Anda</p>
                    <div class="space-y-2 text-sm text-gray-400">
                        <p>Filter aktif: <span class="font-medium">${getActiveFilterText()}</span></p>
                        ${currentSearchTerm ? `<p>Kata kunci: <span class="font-medium">"${currentSearchTerm}"</span></p>` : ''}
                    </div>
                    <button onclick="resetFilters()" class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-refresh mr-2"></i>Reset Filter
                    </button>
                `;
                container.appendChild(noResultsDiv);
            } else if (!show && noResults) {
                noResults.remove();
            }
        }

        // Update results count
        function updateResultsCount(count) {
            const counters = document.querySelectorAll('[data-results-count]');
            counters.forEach(counter => {
                counter.textContent = count;
            });
        }

        // Get active filter text
        function getActiveFilterText() {
            switch (currentFilter) {
                case 'processing':
                    return 'Processing';
                case 'done':
                    return 'Done';
                case 'pending':
                    return 'Pending';
                case 'cancelled':
                    return 'Cancelled';
                default:
                    return 'Semua Project';
            }
        }

        // Reset all filters
        function resetFilters() {
            currentFilter = 'all';
            currentSearchTerm = '';
            currentSort = 'newest';

            // Reset search input
            const searchInput = document.querySelector('input[type="text"]');
            if (searchInput) searchInput.value = '';

            // Reset Alpine.js activeFilter
            const filterContainer = document.querySelector('[x-data]');
            if (filterContainer && filterContainer.__x) {
                filterContainer.__x.$data.activeFilter = 'all';
            }

            applyFiltersAndSearch();
        }

        // Add CSS animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .transition-all {
                transition: all 0.3s ease;
            }
            
            [data-project-card]:hover {
                transform: translateY(-5px);
            }
        `;
        document.head.appendChild(style);

        // Project action functions
        function editProject(projectId) {
            // Redirect to edit page or open modal
            alert('Edit Project #' + projectId + ' - This functionality will be implemented');
        }

        function addNewProject() {
            // Open new project form
            alert('Add New Project - This functionality will be implemented');
        }

        function exportProjects() {
            // Export projects data
            window.location.href = '/project-export';
        }
    </script>
@endif
@endsection
