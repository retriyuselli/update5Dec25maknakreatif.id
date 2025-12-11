<x-filament::page>
    <div class="space-y-6">
        {{-- Filter Form --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm ring-1 ring-gray-950/5">
            <div class="p-4 sm:p-6">
                <form wire:submit.prevent="filter" class="space-y-6">
                    {{-- Baris 1: Range Tanggal --}}
                    <div class="grid grid-cols-12 gap-4 items-start">
                        <div class="col-span-12">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Rentang
                                Tanggal</label>

                            <div class="grid grid-cols-2 gap-4">
                                <!-- Tanggal Awal -->
                                <div>
                                    <label class="text-xs text-gray-500 mb-1 block" for="tanggal_awal">Awal</label>
                                    <div class="relative">
                                        <x-heroicon-o-calendar
                                            class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                                        <input type="date" id="tanggal_awal" wire:model.defer="tanggal_awal"
                                            wire:keydown.enter.prevent="filter"
                                            class="block w-full h-10 py-2 rounded-lg border-gray-300 pl-9 pr-3 text-sm shadow-sm placeholder:text-gray-400 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-primary-500 focus:border-primary-500" />
                                    </div>
                                </div>

                                <!-- Tanggal Akhir -->
                                <div>
                                    <label class="text-xs text-gray-500 mb-1 block" for="tanggal_akhir">Akhir</label>
                                    <div class="relative">
                                        <x-heroicon-o-calendar
                                            class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                                        <input type="date" id="tanggal_akhir" wire:model.defer="tanggal_akhir"
                                            wire:keydown.enter.prevent="filter"
                                            class="block w-full h-10 py-2 rounded-lg border-gray-300 pl-9 pr-3 text-sm shadow-sm placeholder:text-gray-400 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-primary-500 focus:border-primary-500" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Baris 2: Jenis Transaksi dan Kata Kunci --}}
                    <div class="grid grid-cols-12 gap-4 items-start">
                        {{-- Jenis Transaksi dengan Tabs --}}
                        <div class="col-span-12 sm:col-span-8">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Jenis
                                Transaksi</label>
                            <div
                                class="rounded-lg border border-gray-300 dark:border-gray-600 overflow-hidden shadow-sm">
                                {{-- Tabs Header --}}
                                <div
                                    class="flex border-b border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50">
                                    <button type="button" onclick="showTab('masuk')" id="tab-masuk"
                                        class="flex-1 px-4 py-2 text-sm font-medium text-center border-b-2 border-primary-500 text-primary-600 bg-white dark:bg-gray-800 dark:text-primary-400">
                                        Pemasukan
                                    </button>
                                    <button type="button" onclick="showTab('keluar')" id="tab-keluar"
                                        class="flex-1 px-4 py-2 text-sm font-medium text-center border-b-2 border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                        Pengeluaran
                                    </button>
                                </div>

                                {{-- Tab Pemasukan --}}
                                <div id="content-masuk" class="p-4 bg-white dark:bg-gray-800 space-y-3">
                                    <div class="space-y-2" x-data="{ showMasukStatus: false }">
                                        <div class="flex items-center">
                                            <input type="checkbox" id="masuk_wedding" wire:model.defer="filter_jenis"
                                                value="Masuk (Wedding)"
                                                x-on:change="showMasukStatus = $event.target.checked"
                                                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                            <label for="masuk_wedding"
                                                class="ml-2 text-sm text-gray-700 dark:text-gray-200">
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                    Wedding
                                                </span>
                                            </label>
                                        </div>
                                        {{-- Wedding Status Options --}}
                                        <div class="ml-6 bg-gray-50 dark:bg-gray-700/50 p-2 rounded-lg"
                                            x-show="showMasukStatus" x-cloak>
                                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">
                                                Status:</div>
                                            <div class="flex flex-wrap gap-2">
                                                <label class="flex items-center">
                                                    <input type="checkbox" wire:model.defer="filter_status"
                                                        value="done"
                                                        class="h-3 w-3 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                                    <span
                                                        class="ml-1.5 text-xs font-medium bg-green-50 text-green-700 px-2 py-0.5 rounded">
                                                        Done
                                                    </span>
                                                </label>
                                                <label class="flex items-center">
                                                    <input type="checkbox" wire:model.defer="filter_status"
                                                        value="processing"
                                                        class="h-3 w-3 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                                    <span
                                                        class="ml-1.5 text-xs font-medium bg-blue-50 text-blue-700 px-2 py-0.5 rounded">
                                                        Processing
                                                    </span>
                                                </label>
                                                <label class="flex items-center">
                                                    <input type="checkbox" wire:model.defer="filter_status"
                                                        value="pending"
                                                        class="h-3 w-3 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                                    <span
                                                        class="ml-1.5 text-xs font-medium bg-yellow-50 text-yellow-700 px-2 py-0.5 rounded">
                                                        Pending
                                                    </span>
                                                </label>
                                                <label class="flex items-center">
                                                    <input type="checkbox" wire:model.defer="filter_status"
                                                        value="cancelled"
                                                        class="h-3 w-3 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                                    <span
                                                        class="ml-1.5 text-xs font-medium bg-red-50 text-red-700 px-2 py-0.5 rounded">
                                                        Cancelled
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" id="masuk_lain" wire:model.defer="filter_jenis"
                                            value="Masuk (Lain-lain)"
                                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                        <label for="masuk_lain" class="ml-2 text-sm text-gray-700 dark:text-gray-200">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                Lain-lain
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                {{-- Tab Pengeluaran --}}
                                <div id="content-keluar" class="hidden p-4 bg-white dark:bg-gray-800 space-y-3">
                                    <div class="space-y-2">
                                        <div class="flex items-center">
                                            <input type="checkbox" id="keluar_wedding" wire:model.defer="filter_jenis"
                                                value="Keluar (Wedding)"
                                                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                            <label for="keluar_wedding"
                                                class="ml-2 text-sm text-gray-700 dark:text-gray-200">
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                                    Wedding
                                                </span>
                                            </label>
                                        </div>
                                        {{-- Wedding Status Options --}}
                                        <div class="ml-6 space-y-1"
                                            x-show="document.getElementById('keluar_wedding').checked">
                                            <div class="flex flex-wrap gap-2">
                                                <label class="flex items-center">
                                                    <input type="checkbox" wire:model.defer="filter_status"
                                                        value="done"
                                                        class="h-3 w-3 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                                    <span
                                                        class="ml-1.5 text-xs font-medium bg-green-50 text-green-700 px-2 py-0.5 rounded">
                                                        Done
                                                    </span>
                                                </label>
                                                <label class="flex items-center">
                                                    <input type="checkbox" wire:model.defer="filter_status"
                                                        value="processing"
                                                        class="h-3 w-3 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                                    <span
                                                        class="ml-1.5 text-xs font-medium bg-blue-50 text-blue-700 px-2 py-0.5 rounded">
                                                        Processing
                                                    </span>
                                                </label>
                                                <label class="flex items-center">
                                                    <input type="checkbox" wire:model.defer="filter_status"
                                                        value="pending"
                                                        class="h-3 w-3 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                                    <span
                                                        class="ml-1.5 text-xs font-medium bg-yellow-50 text-yellow-700 px-2 py-0.5 rounded">
                                                        Pending
                                                    </span>
                                                </label>
                                                <label class="flex items-center">
                                                    <input type="checkbox" wire:model.defer="filter_status"
                                                        value="cancelled"
                                                        class="h-3 w-3 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                                    <span
                                                        class="ml-1.5 text-xs font-medium bg-red-50 text-red-700 px-2 py-0.5 rounded">
                                                        Cancelled
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" id="keluar_ops" wire:model.defer="filter_jenis"
                                            value="Keluar (Operasional)"
                                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                        <label for="keluar_ops" class="ml-2 text-sm text-gray-700 dark:text-gray-200">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300">
                                                Operasional
                                            </span>
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" id="keluar_lain" wire:model.defer="filter_jenis"
                                            value="Keluar (Lain-lain)"
                                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                        <label for="keluar_lain"
                                            class="ml-2 text-sm text-gray-700 dark:text-gray-200">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                                Lain-lain
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <small class="text-xs text-gray-500 dark:text-gray-400 mt-1 block">
                                Pilih satu atau lebih jenis transaksi dari tab Pemasukan atau Pengeluaran
                            </small>
                        </div>
                        {{-- Tab Switch Script --}}
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                // Set initial active tab
                                showTab('masuk');
                            });

                            function showTab(tabName) {
                                // Hide all content first
                                document.getElementById('content-masuk').classList.add('hidden');
                                document.getElementById('content-keluar').classList.add('hidden');

                                // Remove active state from all tabs
                                document.getElementById('tab-masuk').classList.remove('border-primary-500', 'text-primary-600', 'bg-white',
                                    'dark:bg-gray-800');
                                document.getElementById('tab-keluar').classList.remove('border-primary-500', 'text-primary-600', 'bg-white',
                                    'dark:bg-gray-800');

                                document.getElementById('tab-masuk').classList.add('border-transparent', 'text-gray-500');
                                document.getElementById('tab-keluar').classList.add('border-transparent', 'text-gray-500');

                                // Show selected content and activate tab
                                document.getElementById('content-' + tabName).classList.remove('hidden');
                                document.getElementById('tab-' + tabName).classList.remove('border-transparent', 'text-gray-500');
                                document.getElementById('tab-' + tabName).classList.add('border-primary-500', 'text-primary-600', 'bg-white',
                                    'dark:bg-gray-800');
                            }
                        </script>

                        {{-- Kata Kunci --}}
                        <div class="col-span-12 sm:col-span-4">
                            <label for="filter_keyword"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Kata
                                Kunci</label>
                            <div class="relative">
                                <x-heroicon-o-magnifying-glass
                                    class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                                <input type="text" id="filter_keyword" wire:model.defer="filter_keyword"
                                    wire:keydown.enter.prevent="filter"
                                    class="block w-full h-10 rounded-lg border-gray-300 pl-9 pr-3 text-sm shadow-sm placeholder:text-gray-400 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                                    placeholder="Cari deskripsi, vendor, event..." />
                            </div>
                            <small class="text-xs text-gray-500 dark:text-gray-400 mt-2 block">Tekan Enter atau klik
                                Filter untuk menerapkan pencarian</small>
                            <div class="flex gap-2 mt-3 flex-wrap">
                                <x-filament::button type="submit" class="shrink-0">
                                    <x-heroicon-o-funnel class="w-5 h-5 mr-1" />
                                    Filter
                                </x-filament::button>
                                <x-filament::button type="button" color="gray" wire:click="resetFilters"
                                    class="shrink-0">
                                    <x-heroicon-o-arrow-path class="w-5 h-5 mr-1" />
                                    Reset
                                </x-filament::button>
                                <x-filament::button type="button" color="success" wire:click="downloadPdf"
                                    class="shrink-0">
                                    <x-heroicon-o-arrow-down-tray class="w-5 h-5" />
                                </x-filament::button>
                            </div>
                        </div>

                </form>
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        {{-- Total Masuk --}}
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-6 ring-1 ring-gray-950/5 flex items-center gap-4 sm:gap-6 w-full">
            <div
                class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center bg-green-100 dark:bg-green-500/20">
                <x-heroicon-o-arrow-down-tray class="w-5 h-5 sm:w-6 sm:h-6 text-green-600 dark:text-green-400" />
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Masuk</p>
                <p class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">Rp
                    {{ number_format($total_masuk, 0, ',', '.') }}</p>
            </div>
        </div>
        {{-- Total Keluar --}}
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-6 ring-1 ring-gray-950/5 flex items-center gap-4 sm:gap-6 w-full">
            <div
                class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center bg-red-100 dark:bg-red-500/20">
                <x-heroicon-o-arrow-up-tray class="w-5 h-5 sm:w-6 sm:h-6 text-red-600 dark:text-red-400" />
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Keluar</p>
                <p class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">Rp
                    {{ number_format($total_keluar, 0, ',', '.') }}</p>
            </div>
        </div>
        {{-- Saldo Akhir --}}
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-6 ring-1 ring-gray-950/5 flex items-center gap-4 sm:gap-6 w-full">
            <div
                class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center bg-blue-100 dark:bg-blue-500/20">
                <x-heroicon-o-wallet class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600 dark:text-blue-400" />
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Saldo Akhir</p>
                <p class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">Rp
                    {{ number_format($total_masuk - $total_keluar, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm ring-1 ring-gray-950/5">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">Tanggal</th>
                    <th scope="col" class="px-6 py-3">Jenis</th>
                    <th scope="col" class="px-6 py-3">Status</th>
                    <th scope="col" class="px-6 py-3">Deskripsi</th>
                    <th scope="col" class="px-6 py-3">Vendor</th>
                    <th scope="col" class="px-6 py-3">Prospect/Event</th>
                    <th scope="col" class="px-6 py-3">Rekening</th>
                    <th scope="col" class="px-6 py-3 text-right">Jumlah</th>
                    <th scope="col" class="px-6 py-3 text-right">Saldo</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transaksi as $item)
                    <tr
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <span @class([
                                'inline-flex items-center px-2 py-1 rounded-md text-xs font-medium',
                                'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' => str_contains(
                                    $item->jenis,
                                    'Masuk'),
                                'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' => str_contains(
                                    $item->jenis,
                                    'Keluar'),
                            ])>
                                {{ $item->jenis }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if (str_contains($item->jenis, 'Wedding') && $item->order_id)
                                @php
                                    $order = App\Models\Order::find($item->order_id);
                                    $status = $order?->status;
                                @endphp
                                @if ($status)
                                    <span @class([
                                        'inline-flex items-center px-2 py-1 rounded-md text-xs font-medium',
                                        'bg-green-50 text-green-700' => $status->value === 'done',
                                        'bg-blue-50 text-blue-700' => $status->value === 'processing',
                                        'bg-yellow-50 text-yellow-700' => $status->value === 'pending',
                                        'bg-red-50 text-red-700' => $status->value === 'cancelled',
                                    ])>
                                        {{ $status->getLabel() }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xs">{{ $item->deskripsi }}</td>
                        <td class="px-6 py-4">
                            @if ($item->vendor_name)
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                    {{ $item->vendor_name }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xs">{{ $item->prospect_name ?? '-' }}</td>
                        <td class="px-6 py-4 text-xs ">{{ $item->payment_method_details ?? '-' }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white text-right whitespace-nowrap">
                            Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white text-right whitespace-nowrap">
                            Rp {{ number_format($item->saldo ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="flex flex-col items-center justify-center text-center py-12">
                                <div class="mb-4">
                                    <x-heroicon-o-circle-stack class="w-12 h-12 text-gray-400" />
                                </div>
                                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Tidak Ada
                                    Data
                                    Transaksi</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Coba ubah filter Anda
                                    atau
                                    tambahkan data baru.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- JavaScript untuk handle download --}}
    <script>
        // Function untuk handle download PDF
        function handlePdfDownload(data) {
            console.log('handlePdfDownload called with data:', data);

            try {
                // Pastikan data tersedia - dengan logging yang lebih detail
                if (!data) {
                    console.error('Data is null or undefined:', data);
                    return false;
                }

                if (!data.content) {
                    console.error('Data.content is missing:', data);
                    return false;
                }

                if (!data.filename) {
                    console.error('Data.filename is missing:', data);
                    return false;
                }

                console.log('Processing PDF download for:', data.filename);
                console.log('Content length:', data.content.length);

                // Decode base64 content
                const pdfContent = atob(data.content);
                const bytes = new Uint8Array(pdfContent.length);
                for (let i = 0; i < pdfContent.length; i++) {
                    bytes[i] = pdfContent.charCodeAt(i);
                }

                console.log('PDF bytes created, length:', bytes.length);

                // Create blob dan download
                const blob = new Blob([bytes], {
                    type: 'application/pdf'
                });
                const url = window.URL.createObjectURL(blob);

                console.log('Blob created, URL:', url);

                // Create download link
                const a = document.createElement('a');
                a.href = url;
                a.download = data.filename;
                a.style.display = 'none';
                document.body.appendChild(a);

                console.log('Download link created, clicking...');
                a.click();

                // Cleanup
                setTimeout(() => {
                    document.body.removeChild(a);
                    window.URL.revokeObjectURL(url);
                    console.log('Cleanup completed');
                }, 100);

                return true;

            } catch (error) {
                console.error('Error downloading PDF:', error);
                // Hanya tampilkan alert jika ini benar-benar error critical
                if (error.message.includes('atob') || error.message.includes('Blob')) {
                    alert('Gagal mendownload PDF: ' + error.message);
                }
                return false;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, setting up event listeners...');

            // Handle download PDF lama (untuk compatibility)
            Livewire.on('download-pdf', function(data) {
                window.open(data.url, '_blank');
            });

            // Handle custom downloadPdf event
            window.addEventListener('downloadPdf', function(event) {
                console.log('Custom downloadPdf event received:', event.detail);
                handlePdfDownload(event.detail);
            });

            // Handle downloadPdfFromModal event
            window.addEventListener('downloadPdfFromModal', function(event) {
                console.log('downloadPdfFromModal event received from modal');

                // Cari dan panggil method Livewire
                const wireComponent = document.querySelector('[wire\\:id]');
                if (wireComponent && typeof Livewire !== 'undefined') {
                    const wireId = wireComponent.getAttribute('wire:id');
                    if (Livewire.find && Livewire.find(wireId)) {
                        console.log('Calling downloadPdfReport from modal event');
                        Livewire.find(wireId).call('downloadPdfReport');
                    }
                }
            });

            // Handle postMessage from modal iframe/content
            window.addEventListener('message', function(event) {
                if (event.data.type === 'downloadLaporanPdf' && event.data.action === 'triggerDownload') {
                    console.log('Received postMessage to trigger download');

                    // Call the global trigger function
                    if (window.triggerLaporanDownload) {
                        const success = window.triggerLaporanDownload();
                        if (success) {
                            // Send success message back to modal
                            event.source.postMessage({
                                type: 'pdfDownloadComplete',
                                success: true
                            }, '*');
                        }
                    }
                }
            });

            // Handle custom event for Laporan download
            document.addEventListener('triggerLaporanDownload', function(event) {
                console.log('triggerLaporanDownload custom event received');
                if (window.triggerLaporanDownload) {
                    window.triggerLaporanDownload();
                }
            });

            // Handle PDF download dari modal L/R - Livewire v3
            document.addEventListener('livewire:init', () => {
                console.log('Livewire init, setting up downloadPdf listener...');

                Livewire.on('downloadPdf', (data) => {
                    console.log('Livewire v3 downloadPdf event received:', data);
                    handlePdfDownload(data);
                });
            });
        });

        // Backup method untuk Livewire v2 compatibility
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Livewire !== 'undefined' && Livewire.on) {
                console.log('Setting up Livewire v2 compatibility...');

                Livewire.on('downloadPdf', function(data) {
                    console.log('Livewire v2 downloadPdf event received:', data);
                    handlePdfDownload(data);
                });
            }
        });

        // Global functions untuk manual testing dan PHP integration
        window.testPdfDownload = function(content, filename) {
            console.log('Manual test PDF download...');
            return handlePdfDownload({
                content: content,
                filename: filename
            });
        };

        // Make handlePdfDownload available globally for PHP to call
        window.handlePdfDownload = handlePdfDownload;

        // Global function untuk trigger download dari modal
        window.triggerLaporanDownload = function() {
            console.log('triggerLaporanDownload called from modal');

            try {
                // Cari Livewire component LaporanKeuangan
                const laporanComponent = document.querySelector('[wire\\:id]');
                if (!laporanComponent) {
                    console.error('Livewire component not found');
                    alert('Error: Komponen Livewire tidak ditemukan');
                    return false;
                }

                const wireId = laporanComponent.getAttribute('wire:id');
                console.log('Found Livewire component with ID:', wireId);

                // Method 1: Livewire.find
                if (typeof Livewire !== 'undefined' && Livewire.find) {
                    const component = Livewire.find(wireId);
                    if (component) {
                        console.log('Calling downloadPdfReport via Livewire.find');
                        component.call('downloadPdfReport');
                        return true;
                    }
                }

                // Method 2: Direct __livewire call
                if (laporanComponent.__livewire) {
                    console.log('Calling downloadPdfReport via __livewire');
                    laporanComponent.__livewire.call('downloadPdfReport');
                    return true;
                }

                // Method 3: Simulate click on global hidden button
                const globalHiddenBtn = document.getElementById('globalHiddenDownloadBtn');
                if (globalHiddenBtn) {
                    console.log('Triggering global hidden button click');
                    globalHiddenBtn.click();
                    return true;
                }

                // Method 4: Simulate click on modal hidden button
                const hiddenBtn = document.getElementById('hiddenDownloadBtn');
                if (hiddenBtn) {
                    console.log('Triggering modal hidden button click');
                    hiddenBtn.click();
                    return true;
                }

                console.error('No available method to trigger download');
                alert('Error: Tidak dapat memicu download PDF');
                return false;

            } catch (error) {
                console.error('Error in triggerLaporanDownload:', error);
                alert('Error: ' + error.message);
                return false;
            }
        };
    </script>

    {{-- Hidden button untuk download L/R dari modal --}}
    <div style="display: none;">
        <button id="globalHiddenDownloadBtn" wire:click="downloadPdfReport" type="button">
            Global Hidden L/R Download Button
        </button>
    </div>

    </div> {{-- Close Summary Cards div dan main container div --}}
</x-filament::page>
