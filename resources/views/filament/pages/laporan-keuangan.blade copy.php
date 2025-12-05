<x-filament::page>
    <div class="space-y-6">
        {{-- Filter Form --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm ring-1 ring-gray-950/5">
            <div class="p-4 sm:p-6">
                <form wire:submit.prevent="filter"
                    class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 items-end">
                    {{-- Tanggal Awal --}}
                    <div class="w-full">
                        <label for="tanggal_awal"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-200">Tanggal Awal</label>
                        <input type="date" id="tanggal_awal" wire:model.defer="tanggal_awal"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-primary-500 focus:border-primary-500" />
                    </div>
                    {{-- Tanggal Akhir --}}
                    <div class="w-full">
                        <label for="tanggal_akhir"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-200">Tanggal Akhir</label>
                        <input type="date" id="tanggal_akhir" wire:model.defer="tanggal_akhir"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-primary-500 focus:border-primary-500" />
                    </div>
                    {{-- Jenis Transaksi --}}
                    <div class="w-full">
                        <label for="filter_jenis"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-200">Jenis</label>
                        <select id="filter_jenis" wire:model.defer="filter_jenis"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                            <option value="semua">Semua Jenis</option>
                            <option value="Masuk (Wedding)">Masuk (Wedding)</option>
                            <option value="Masuk (Lain-lain)">Masuk (Lain-lain)</option>
                            <option value="Keluar (Wedding)">Keluar (Wedding)</option>
                            <option value="Keluar (Operasional)">Keluar (Operasional)</option>
                            <option value="Keluar (Lain-lain)">Keluar (Lain-lain)</option>
                        </select>
                    </div>
                    {{-- Kata Kunci --}}
                    <div class="w-full">
                        <label for="filter_keyword"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-200">Kata Kunci</label>
                        <input type="text" id="filter_keyword" wire:model.defer="filter_keyword"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                            placeholder="Cari deskripsi..." />
                    </div>
                    {{-- Action Buttons --}}
                    <div class="flex items-center gap-x-3">
                        <x-filament::button type="submit">
                            Filter
                        </x-filament::button>
                        <x-filament::button type="button" color="gray" wire:click="resetFilters">
                            Reset
                        </x-filament::button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- Total Masuk --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 ring-1 ring-gray-950/5 flex items-center gap-6">
                <div
                    class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center bg-green-100 dark:bg-green-500/20">
                    <x-heroicon-o-arrow-down-tray class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Masuk</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">Rp
                        {{ number_format($total_masuk, 0, ',', '.') }}
                    </p>
                </div>
            </div>
            {{-- Total Keluar --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 ring-1 ring-gray-950/5 flex items-center gap-6">
                <div
                    class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center bg-red-100 dark:bg-red-500/20">
                    <x-heroicon-o-arrow-up-tray class="w-6 h-6 text-red-600 dark:text-red-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Keluar</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">Rp
                        {{ number_format($total_keluar, 0, ',', '.') }}
                    </p>
                </div>
            </div>
            {{-- Saldo Akhir --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 ring-1 ring-gray-950/5 flex items-center gap-6">
                <div
                    class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center bg-blue-100 dark:bg-blue-500/20">
                    <x-heroicon-o-wallet class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Saldo Akhir</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">Rp
                        {{ number_format($total_masuk - $total_keluar, 0, ',', '.') }}
                    </p>
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
                        <th scope="col" class="px-6 py-3">Deskripsi</th>
                        <th scope="col" class="px-6 py-3">Prospect/Event</th>
                        <th scope="col" class="px-6 py-3">Rekening</th>
                        <th scope="col" class="px-6 py-3 text-right">Jumlah</th>
                        <th scope="col" class="px-6 py-3 text-right">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksi as $item)
                        <tr
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <span @class([ 'inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium'
                                    , 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'=>
                                    str_contains(
                                    $item->jenis,
                                    'Masuk'),
                                    'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' => str_contains(
                                    $item->jenis,
                                    'Keluar'),
                                    ])>
                                    {{ $item->jenis }}
                                </span>
                            </td>
                            <td class="px-6 py-4">{{ $item->deskripsi }}</td>
                            <td class="px-6 py-4 text-xs">
                                {{ $item->prospect_name ?? '-' }}</td>
                            <td class="px-6 py-4">
                                {{ $item->payment_method_details ?? '-' }}</td>
                            <td
                                class="px-6 py-4 font-medium text-gray-900 dark:text-white text-right whitespace-nowrap">
                                Rp
                                {{ number_format($item->jumlah, 0, ',', '.') }}
                            </td>
                            <td
                                class="px-6 py-4 font-semibold text-gray-900 dark:text-white text-right whitespace-nowrap">
                                Rp
                                {{ number_format($item->saldo ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="flex flex-col items-center justify-center text-center py-12">
                                    <div class="mb-4">
                                        <x-heroicon-o-circle-stack class="w-12 h-12 text-gray-400" />
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Tidak Ada Data
                                        Transaksi</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Coba ubah filter Anda atau
                                        tambahkan data baru.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament::page>
