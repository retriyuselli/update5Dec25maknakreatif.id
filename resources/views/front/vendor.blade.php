@extends('layouts.app')

@section('title', 'Manajemen Vendor')

@section('content')
    <div class="min-h-screen bg-white">
        <!-- Header Navigation -->
        @include('front.header')

        <!-- Hero Section -->
        <section class="bg-gradient-to-br from-gray-900 via-blue-900 to-black text-white py-20">
            <div class="container mx-auto px-6">
                <div class="text-center max-w-4xl mx-auto">
                    <h1 class="text-5xl md:text-6xl font-bold mb-6 leading-tight">
                        <span class="bg-gradient-to-r from-blue-400 to-purple-600 bg-clip-text text-transparent">
                            Vendor Management
                        </span>
                    </h1>
                    <p class="text-xl text-gray-300 mb-8 max-w-2xl mx-auto">
                        Platform manajemen vendor terpadu untuk mengoptimalkan partnership dan kolaborasi bisnis
                    </p>
                </div>
            </div>
        </section>

        <!-- Statistics Section -->
        <section class="py-16 bg-gray-50">
            <div class="container mx-auto px-6">
                <!-- Section Header -->
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-blue-600 mb-4">Statistik Vendor</h2>
                    <p class="text-gray-600">Dashboard analytics untuk monitoring performa vendor</p>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Total Vendors -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-lg transition-shadow duration-300">
                        <div class="text-center">
                            <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                    </path>
                                </svg>
                            </div>
                            <div class="text-2xl font-bold text-blue-600 mb-1">{{ $stats['total'] }}</div>
                            <div class="text-sm text-gray-600">Total Vendor</div>
                        </div>
                    </div>

                    <!-- Active Vendors -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-lg transition-shadow duration-300">
                        <div class="text-center">
                            <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="text-2xl font-bold text-blue-600 mb-1">{{ $stats['active'] }}</div>
                            <div class="text-sm text-gray-600">Vendor Aktif</div>
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
                            <div class="text-2xl font-bold text-blue-600 mb-1">{{ $stats['revenue'] }}</div>
                            <div class="text-sm text-gray-600">Revenue {{ $stats['current_year'] }}</div>
                        </div>
                    </div>

                    <!-- Average Price -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-lg transition-shadow duration-300">
                        <div class="text-center">
                            <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <div class="text-2xl font-bold text-blue-600 mb-1">{{ $stats['average_price'] }}</div>
                            <div class="text-sm text-gray-600">Rata-rata Harga</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Filter & Search Section -->
        <section class="py-8 bg-white">
            <div class="container mx-auto px-6">
                <div class="bg-gray-50 rounded-2xl p-6 shadow-sm">
                    <form method="GET" action="{{ route('vendor') }}" class="flex flex-col items-center gap-6">
                        <!-- Hidden input untuk mempertahankan per_page -->
                        <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">

                        <!-- Search Bar -->
                        <div class="relative w-full max-w-md">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari vendor, PIC, atau telepon..."
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent bg-white">
                        </div>

                        <!-- Category Filter -->
                        {{-- <div class="relative">
                            <select name="category"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent appearance-none bg-white">
                                @if (isset($categories))
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ request('category') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div> --}}

                        <!-- Status Filter -->
                        {{-- <div class="relative">
                            <select name="status"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent appearance-none bg-white">
                                <option value="vendor" {{ request('status') == 'vendor' ? 'selected' : '' }}>Vendor
                                </option>
                                <option value="product" {{ request('status') == 'product' ? 'selected' : '' }}>Product
                                </option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div> --}}

                        <!-- Action Buttons -->
                        <div class="flex gap-2 justify-center">
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-lg transition-colors font-medium">
                                Filter
                            </button>
                            <a href="{{ route('vendor') }}?per_page={{ request('per_page', 10) }}"
                                class="bg-blue-500 hover:bg-blue-600 text-white py-3 px-6 rounded-lg transition-colors text-center font-medium">
                                Reset
                            </a>
                        </div>
                    </form>

                    <!-- Filter Summary -->
                    @if (request()->hasAny(['search', 'category', 'status']))
                        <div class="mt-4 p-4 bg-white rounded-lg border border-gray-200">
                            <div class="flex items-center justify-between flex-wrap gap-2">
                                <div class="text-sm text-gray-600">
                                    <strong class="text-blue-600">Filter aktif:</strong>
                                    @if (request('search'))
                                        <span class="ml-2 px-3 py-1 bg-blue-600 text-white rounded-full text-xs">Pencarian:
                                            "{{ request('search') }}"</span>
                                    @endif
                                    @if (request('category'))
                                        <span class="ml-2 px-3 py-1 bg-blue-600 text-white rounded-full text-xs">Kategori:
                                            {{ $categories->find(request('category'))->name ?? 'Unknown' }}</span>
                                    @endif
                                    @if (request('status'))
                                        <span class="ml-2 px-3 py-1 bg-blue-600 text-white rounded-full text-xs">Status:
                                            {{ ucfirst(request('status')) }}</span>
                                    @endif
                                </div>
                                <a href="{{ route('vendor') }}?per_page={{ request('per_page', 10) }}"
                                    class="text-gray-600 hover:text-blue-600 text-sm font-medium">
                                    Hapus semua filter
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <!-- Vendor Table Section -->
        <section class="py-8">
            <div class="container mx-auto px-6">
                <!-- Pagination Controls -->
                <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
                    <div class="flex justify-between items-center flex-wrap gap-4">
                        <div class="flex items-center space-x-4">
                            <!-- Per Page Selector -->
                            <div class="flex items-center space-x-2">
                                <label class="text-sm text-gray-600 font-medium">Tampilkan:</label>
                                <select onchange="changePerPage(this.value)"
                                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 
           focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 
           bg-white hover:border-gray-400 transition-colors">
                                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                </select>
                                <span class="text-sm text-gray-600">data per halaman</span>
                            </div>

                            <!-- Info jumlah data -->
                            @if (isset($vendors) && method_exists($vendors, 'total'))
                                <div class="text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
                                    Menampilkan {{ $vendors->firstItem() ?? 0 }} - {{ $vendors->lastItem() ?? 0 }} dari
                                    {{ $vendors->total() }} data
                                </div>
                            @endif
                        </div>

                        <!-- Quick Actions -->
                        <div class="flex items-center space-x-3">
                            <button onclick="exportVendors()"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm transition-colors font-medium">
                                📊 Export
                            </button>
                            <button onclick="addNewVendor()"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                ➕ Tambah Vendor
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table Container -->
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    <!-- Table Header -->
                    <div class="bg-blue-600 text-white px-6 py-4">
                        <h2 class="text-xl font-bold">Daftar Vendor</h2>
                    </div>

                    <!-- Table Content -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Vendor</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Kategori</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        PIC</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Harga</th>
                                    {{-- <th
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Status</th> --}}
                                    <th
                                        class="text-right px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($vendors as $vendor)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <!-- Vendor Name -->
                                        <td class="px-6 py-4">
                                            <div>
                                                <div class="font-bold text-sm text-blue-600">
                                                    {{ ucwords(strtolower($vendor->name)) }}</div>
                                                <div class="text-xs text-gray-600">{{ $vendor->status }}</div>
                                            </div>
                                        </td>

                                        <!-- Category -->
                                        <td class="px-6 py-4">
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $vendor->category->name ?? 'Tidak Berkategori' }}
                                            </span>
                                        </td>

                                        <!-- PIC -->
                                        <td class="px-6 py-4">
                                            <div>
                                                <div class="text-sm font-medium text-blue-600">
                                                    {{ $vendor->pic_name ?? '-' }}</div>
                                                <div class="text-xs text-gray-600">0{{ $vendor->phone ?? '08-' }}</div>
                                            </div>
                                        </td>

                                        <!-- Price -->
                                        <td class="px-6 py-4">
                                            <div>
                                                <div class="text-sm font-semibold text-blue-600">
                                                    Rp {{ number_format($vendor->harga_publish ?? 0, 0, ',', '.') }}
                                                </div>
                                                <div class="text-xs text-gray-600">
                                                    Cost: Rp {{ number_format($vendor->harga_vendor ?? 0, 0, ',', '.') }}
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Status -->
                                        {{-- <td class="px-6 py-4">
                                            @if ($vendor->status == 'vendor')
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-600 text-white">
                                                    Vendor
                                                </span>
                                            @elseif($vendor->status == 'product')
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-500 text-white">
                                                    Product
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-200 text-gray-800">
                                                    {{ ucfirst($vendor->status ?? 'Unknown') }}
                                                </span>
                                            @endif
                                        </td> --}}

                                        <!-- Actions -->
                                        <td class="px-6 py-4">
                                            <div class="flex justify-end gap-2">
                                                <button onclick="viewVendorDetail({{ $vendor->id }})"
                                                    class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-lg text-xs font-medium transition-colors flex items-center gap-1">
                                                    👁️ <span>Lihat</span>
                                                </button>
                                                <a href="/admin/vendors/{{ $vendor->id }}/edit"
                                                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-lg text-xs font-medium transition-colors flex items-center gap-1">
                                                    ✏️ <span>Edit</span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-16 text-center">
                                            <div class="flex flex-col items-center">
                                                <div
                                                    class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                                    <svg class="w-8 h-8 text-gray-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                                        </path>
                                                    </svg>
                                                </div>
                                                <div class="text-xl font-semibold text-blue-600 mb-2">Belum ada data vendor
                                                </div>
                                                <div class="text-gray-600 mb-6">Silakan tambahkan vendor pertama Anda</div>
                                                <button onclick="addNewVendor()"
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                                                    ➕ Tambah Vendor Sekarang
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination Section -->
                @if (isset($vendors) && method_exists($vendors, 'hasPages'))
                    @if ($vendors->hasPages())
                        <div class="mt-6 bg-white rounded-2xl shadow-sm p-6">
                            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                                <div class="text-sm text-gray-600">
                                    Menampilkan {{ $vendors->firstItem() }} sampai {{ $vendors->lastItem() }} dari
                                    {{ $vendors->total() }} hasil
                                </div>
                                <div class="flex items-center">
                                    {{ $vendors->appends(request()->query())->links() }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="mt-6 bg-white rounded-2xl shadow-sm p-4">
                            <div class="text-center text-gray-600">
                                @if (method_exists($vendors, 'total'))
                                    Total {{ $vendors->total() }} data vendor (semua ditampilkan dalam satu halaman)
                                @else
                                    Semua data vendor ditampilkan
                                @endif
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </section>
    </div>

    <!-- JavaScript Functions -->
    <script>
        // Function untuk mengubah per page
        function changePerPage(perPage) {
            const url = new URL(window.location);
            url.searchParams.set('per_page', perPage);
            url.searchParams.set('page', 1); // Reset ke halaman pertama
            window.location.href = url.toString();
        }

        // Function untuk menambah vendor baru
        function addNewVendor() {
            window.location.href = '/admin/vendors/create';
        }

        // Function untuk export vendors
        function exportVendors() {
            // Implementasi export - bisa redirect ke route export atau download langsung
            window.location.href = '/admin/vendors/export';
        }

        // Function untuk melihat detail vendor
        function viewVendorDetail(vendorId) {
            // Get vendor data from current page
            @if (isset($vendors) && method_exists($vendors, 'items'))
                const vendors = @json($vendors->items());
            @else
                const vendors = @json($vendors ?? []);
            @endif

            const selectedVendor = vendors.find(v => v.id === vendorId);

            if (selectedVendor) {
                showVendorModal(selectedVendor);
            } else {
                // Fallback: redirect to vendor detail page
                window.location.href = `/admin/vendors/${vendorId}`;
            }
        }

        // Function untuk menampilkan modal detail vendor
        function showVendorModal(vendor) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-blue-900 bg-opacity-50 flex items-center justify-center z-50 p-4';
            modal.innerHTML = `
                <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                    <div class="p-6">
                        <!-- Modal Header -->
                        <div class="flex justify-between items-start mb-6 pb-4 border-b border-gray-200">
                            <div>
                                <h3 class="text-2xl font-bold text-blue-600">${vendor.name}</h3>
                                <p class="text-gray-600">${vendor.slug || ''}</p>
                            </div>
                            <button onclick="closeVendorModal()" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">
                                ×
                            </button>
                        </div>

                        <!-- Modal Content -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Informasi Kontak -->
                            <div class="bg-gray-50 rounded-xl p-4">
                                <h4 class="text-lg font-semibold text-blue-600 mb-4">Informasi Kontak</h4>
                                <div class="space-y-3">
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">PIC:</label>
                                        <p class="text-blue-600 font-medium">${vendor.pic_name || '-'}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Telepon:</label>
                                        <p class="text-blue-600 font-medium">${vendor.phone || '-'}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Email:</label>
                                        <p class="text-blue-600 font-medium">${vendor.email || '-'}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Status:</label>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${vendor.status === 'vendor' ? 'bg-blue-600 text-white' : 'bg-gray-600 text-white'} mt-1">
                                            ${vendor.status === 'vendor' ? 'Vendor' : 'Product'}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Informasi Harga -->
                            <div class="bg-gray-50 rounded-xl p-4">
                                <h4 class="text-lg font-semibold text-blue-600 mb-4">Informasi Harga</h4>
                                <div class="space-y-4">
                                    <div class="bg-white rounded-lg p-3 border border-gray-200">
                                        <label class="text-sm font-medium text-gray-600">Harga Publish</label>
                                        <p class="text-xl font-bold text-blue-600">Rp ${vendor.harga_publish ? new Intl.NumberFormat('id-ID').format(vendor.harga_publish) : '0'}</p>
                                    </div>
                                    <div class="bg-white rounded-lg p-3 border border-gray-200">
                                        <label class="text-sm font-medium text-gray-600">Harga Vendor</label>
                                        <p class="text-xl font-bold text-blue-600">Rp ${vendor.harga_vendor ? new Intl.NumberFormat('id-ID').format(vendor.harga_vendor) : '0'}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Deskripsi (jika ada) -->
                            ${vendor.description ? `
                                        <div class="md:col-span-2 bg-gray-50 rounded-xl p-4">
                                            <h4 class="text-lg font-semibold text-blue-600 mb-3">📋 Deskripsi</h4>
                                            <div class="text-gray-600 leading-relaxed">
                                                ${vendor.description.split('\n').map((line, index) => {
                                                    if (line.trim()) {
                                                        return `<div class="flex items-start mb-2">
                                                        <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-100 text-blue-600 rounded-full text-xs font-medium mr-3 mt-0.5 flex-shrink-0">${index + 1}</span>
                                                        <span class="text-gray-700">${line.trim()}</span>
                                                    </div>`;
                                                    }
                                                    return '';
                                                }).join('')}
                                            </div>
                                        </div>
                                        ` : ''}
                        </div>

                        <!-- Modal Footer -->
                        <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                            <button onclick="closeVendorModal()" 
                                class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium">
                                Tutup
                            </button>
                            <a href="/admin/vendors/${vendor.id}/edit" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                Edit Vendor
                            </a>
                        </div>
                    </div>
                </div>
            `;

            document.body.appendChild(modal);

            // Close modal when clicking outside
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeVendorModal();
                }
            });
        }

        // Function untuk menutup modal
        function closeVendorModal() {
            const modal = document.querySelector('.fixed.inset-0.bg-blue-900.bg-opacity-50');
            if (modal) {
                modal.remove();
            }
        }

        // Close modal with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeVendorModal();
            }
        });

        // Smooth scrolling for internal links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
@endsection
