<div class="space-y-4">
    @if($notes && $notes !== 'Tidak ada riwayat')
        <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">📋 Riwayat Perubahan Payroll</h3>
            <div class="prose prose-sm max-w-none">
                {!! nl2br(e($notes)) !!}
            </div>
        </div>
    @else
        <div class="text-center py-8">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100">
                <svg class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada riwayat</h3>
            <p class="mt-1 text-sm text-gray-500">
                Belum ada perubahan atau catatan untuk payroll ini.
            </p>
        </div>
    @endif
</div>
