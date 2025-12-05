<!-- Employment Information Section -->
<div style="font-family: 'Poppins', sans-serif;">
    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 00-2 2H8a2 2 0 00-2-2V6m8 0H8m0 0v.01M8 6v6h8V6M8 12v.01"></path>
        </svg>
        Detail Pekerjaan
    </h3>
    <div class="space-y-4">
        <div>
            <label class="text-sm font-medium text-gray-500" style="font-family: 'Poppins', sans-serif; font-weight: 500;">Tanggal Bergabung</label>
            <p class="text-gray-900 text-sm" style="font-family: 'Poppins', sans-serif;">{{ Auth::user()->hire_date ? Auth::user()->hire_date->format('d F Y') : Auth::user()->created_at->format('d F Y') }}</p>
        </div>
        <div>
            <label class="text-sm font-medium text-gray-500" style="font-family: 'Poppins', sans-serif; font-weight: 500;">Status</label>
            <p class="text-gray-900 text-sm" style="font-family: 'Poppins', sans-serif;">
                @if(Auth::user()->status_id)
                    @php
                        $status = \App\Models\Status::find(Auth::user()->status_id);
                    @endphp
                    {{ $status ? $status->status_name : 'Status tidak ditemukan' }}
                @else
                    Tidak ada status yang ditetapkan
                @endif
            </p>
        </div>
        <div>
            <label class="text-sm font-medium text-gray-500" style="font-family: 'Poppins', sans-serif; font-weight: 500;">Pengalaman Kerja</label>
            <p class="text-gray-900 text-sm" style="font-family: 'Poppins', sans-serif;">{{ Auth::user()->hire_date ? Auth::user()->hire_date->diffForHumans() : Auth::user()->created_at->diffForHumans() }}</p>
        </div>
        <div>
            <label class="text-sm font-medium text-gray-500" style="font-family: 'Poppins', sans-serif; font-weight: 500;">Alamat</label>
            <p class="text-gray-900 text-sm" style="font-family: 'Poppins', sans-serif;">{{ Auth::user()->address ?? 'Tidak ditentukan' }}</p>
        </div>
            <div>
                <label class="text-sm font-medium text-gray-500" style="font-family: 'Poppins', sans-serif; font-weight: 500;">Tanggal Mulai Kerja</label>
                <p class="text-gray-900 text-sm" style="font-family: 'Poppins', sans-serif;">{{ Auth::user()->hire_date ? Auth::user()->hire_date->format('d F Y') : 'Tidak ditentukan' }}</p>
            </div>
    </div>
</div>
    