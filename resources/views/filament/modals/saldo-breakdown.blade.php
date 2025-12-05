<div class="space-y-6">
    {{-- Header Info --}}
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Saldo Awal</h4>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                    Rp {{ number_format($breakdown['saldo_awal'], 0, ',', '.') }}
                </p>
                <p class="text-xs text-gray-500">
                    Tanggal: {{ \Carbon\Carbon::parse($breakdown['tanggal_pembukuan'])->format('d F Y') }}
                </p>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Saldo Akhir</h4>
                <p
                    class="text-lg font-semibold {{ $breakdown['saldo_akhir'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    Rp {{ number_format($breakdown['saldo_akhir'], 0, ',', '.') }}
                </p>
                <div class="flex items-center gap-1 text-xs">
                    @if ($breakdown['status'] === 'naik')
                        <span class="text-green-600">↗️ Naik</span>
                    @elseif($breakdown['status'] === 'turun')
                        <span class="text-red-600">↘️ Turun</span>
                    @else
                        <span class="text-gray-600">➡️ Tetap</span>
                    @endif
                    <span class="text-gray-500">
                        Rp {{ number_format(abs($breakdown['perubahan']), 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Uang Masuk --}}
    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
        <h4 class="text-sm font-medium text-green-800 dark:text-green-200 mb-3 flex items-center gap-2">
            💰 Uang Masuk
        </h4>
        <div class="space-y-2">
            <div class="flex justify-between text-sm">
                <span class="text-gray-600 dark:text-gray-400">Wedding/Pesanan:</span>
                <span class="font-medium text-green-700 dark:text-green-300">
                    Rp {{ number_format($breakdown['uang_masuk']['wedding'], 0, ',', '.') }}
                </span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600 dark:text-gray-400">Pendapatan Lain:</span>
                <span class="font-medium text-green-700 dark:text-green-300">
                    Rp {{ number_format($breakdown['uang_masuk']['lainnya'], 0, ',', '.') }}
                </span>
            </div>
            <hr class="border-green-200 dark:border-green-700">
            <div class="flex justify-between font-semibold">
                <span class="text-green-800 dark:text-green-200">Total Masuk:</span>
                <span class="text-green-800 dark:text-green-200">
                    Rp {{ number_format($breakdown['uang_masuk']['total'], 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

    {{-- Uang Keluar --}}
    <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
        <h4 class="text-sm font-medium text-red-800 dark:text-red-200 mb-3 flex items-center gap-2">
            💸 Uang Keluar
        </h4>
        <div class="space-y-2">
            <div class="flex justify-between text-sm">
                <span class="text-gray-600 dark:text-gray-400">Biaya Wedding:</span>
                <span class="font-medium text-red-700 dark:text-red-300">
                    Rp {{ number_format($breakdown['uang_keluar']['wedding'], 0, ',', '.') }}
                </span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600 dark:text-gray-400">Biaya Operasional:</span>
                <span class="font-medium text-red-700 dark:text-red-300">
                    Rp {{ number_format($breakdown['uang_keluar']['operasional'], 0, ',', '.') }}
                </span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600 dark:text-gray-400">Pengeluaran Lain:</span>
                <span class="font-medium text-red-700 dark:text-red-300">
                    Rp {{ number_format($breakdown['uang_keluar']['lainnya'], 0, ',', '.') }}
                </span>
            </div>
            <hr class="border-red-200 dark:border-red-700">
            <div class="flex justify-between font-semibold">
                <span class="text-red-800 dark:text-red-200">Total Keluar:</span>
                <span class="text-red-800 dark:text-red-200">
                    Rp {{ number_format($breakdown['uang_keluar']['total'], 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

    {{-- Formula Perhitungan --}}
    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
        <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-3 flex items-center gap-2">
            📊 Formula Perhitungan
        </h4>
        <div class="text-sm space-y-1">
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Saldo Awal:</span>
                <span class="font-mono">Rp {{ number_format($breakdown['saldo_awal'], 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-green-600">
                <span>+ Total Uang Masuk:</span>
                <span class="font-mono">Rp {{ number_format($breakdown['uang_masuk']['total'], 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-red-600">
                <span>- Total Uang Keluar:</span>
                <span class="font-mono">Rp {{ number_format($breakdown['uang_keluar']['total'], 0, ',', '.') }}</span>
            </div>
            <hr class="border-blue-200 dark:border-blue-700">
            <div class="flex justify-between font-bold text-blue-800 dark:text-blue-200">
                <span>= Saldo Akhir:</span>
                <span class="font-mono">Rp {{ number_format($breakdown['saldo_akhir'], 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- Catatan --}}
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
        <p class="text-xs text-gray-600 dark:text-gray-400">
            <strong>Catatan:</strong> Perhitungan saldo berdasarkan transaksi mulai dari tanggal pembukuan
            ({{ \Carbon\Carbon::parse($breakdown['tanggal_pembukuan'])->format('d F Y') }})
            hingga saat ini. Transaksi sebelum tanggal tersebut tidak dihitung.
        </p>
    </div>
</div>
