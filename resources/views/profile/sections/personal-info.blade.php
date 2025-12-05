<!-- Personal Information Section -->
<div style="font-family: 'Poppins', sans-serif;">
    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
        </svg>
        Informasi Pribadi
    </h3>
    <div class="space-y-4">
        <div>
            <label class="text-sm font-medium text-gray-500" style="font-family: 'Poppins', sans-serif; font-weight: 500;">Nama Lengkap</label>
            <p class="text-gray-900 text-sm" style="font-family: 'Poppins', sans-serif;">{{ Auth::user()->name }}</p>
        </div>
        <div>
            <label class="text-sm font-medium text-gray-500" style="font-family: 'Poppins', sans-serif; font-weight: 500;">Alamat Email</label>
            <p class="text-gray-900 text-sm" style="font-family: 'Poppins', sans-serif;">{{ Auth::user()->email }}</p>
        </div>
        <div>
            <label class="text-sm font-medium text-gray-500" style="font-family: 'Poppins', sans-serif; font-weight: 500;">Nomor Telepon</label>
            <p class="text-gray-900 text-sm" style="font-family: 'Poppins', sans-serif;">{{ Auth::user()->phone_number ?? 'Tidak ditentukan' }}</p>
        </div>
        <div>
            <label class="text-sm font-medium text-gray-500" style="font-family: 'Poppins', sans-serif; font-weight: 500;">Tanggal Lahir</label>
            <p class="text-gray-900 text-sm" style="font-family: 'Poppins', sans-serif;">{{ Auth::user()->date_of_birth ? Auth::user()->date_of_birth->format('d F Y') : 'Tidak ditentukan' }}</p>
        </div>
        <div>
            <label class="text-sm font-medium text-gray-500" style="font-family: 'Poppins', sans-serif; font-weight: 500;">Jenis Kelamin</label>
            <p class="text-gray-900 text-sm" style="font-family: 'Poppins', sans-serif;">{{ Auth::user()->gender ? ucfirst(Auth::user()->gender) : 'Tidak ditentukan' }}</p>
        </div>
    </div>
</div>
