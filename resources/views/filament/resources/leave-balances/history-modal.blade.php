<div class="overflow-x-auto">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-4 py-3">Tanggal</th>
                <th scope="col" class="px-4 py-3">Jumlah</th>
                <th scope="col" class="px-4 py-3">Alasan</th>
                <th scope="col" class="px-4 py-3">Oleh</th>
                <th scope="col" class="px-4 py-3">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($record->histories()->with('creator')->orderBy('transaction_date', 'desc')->get() as $history)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <td class="px-4 py-3">
                        {{ \Carbon\Carbon::parse($history->transaction_date)->format('d/m/Y') }}
                    </td>
                    <td class="px-4 py-3 font-medium text-green-600 dark:text-green-400">
                        +{{ $history->amount }}
                    </td>
                    <td class="px-4 py-3">
                        {{ $history->reason }}
                    </td>
                    <td class="px-4 py-3">
                        {{ $history->creator->name ?? '-' }}
                    </td>
                    <td class="px-4 py-3">
                        @if($history->status == 'approved')
                            <span class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Disetujui</span>
                        @elseif($history->status == 'pending')
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">Menunggu</span>
                        @else
                            <span class="bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">{{ ucfirst($history->status) }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-3 text-center italic text-gray-400">
                        Belum ada riwayat top up.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>