<div class="space-y-6">
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-center space-x-2">
            <x-heroicon-o-information-circle class="w-5 h-5 text-blue-600"/>
            <h3 class="text-lg font-semibold text-blue-800">Panduan Form PDF Pendaftaran Karyawan</h3>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Form Kosong -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
            <div class="flex items-center space-x-2 mb-3">
                <x-heroicon-o-document class="w-5 h-5 text-gray-600"/>
                <h4 class="font-semibold text-gray-800">Form Kosong</h4>
            </div>
            <ul class="text-sm text-gray-600 space-y-2">
                <li class="flex items-start space-x-2">
                    <span class="text-blue-500 font-bold">1.</span>
                    <span>Klik "Download Form Kosong" di bagian atas tabel</span>
                </li>
                <li class="flex items-start space-x-2">
                    <span class="text-blue-500 font-bold">2.</span>
                    <span>Print form PDF yang dihasilkan</span>
                </li>
                <li class="flex items-start space-x-2">
                    <span class="text-blue-500 font-bold">3.</span>
                    <span>Berikan kepada calon karyawan untuk diisi</span>
                </li>
                <li class="flex items-start space-x-2">
                    <span class="text-blue-500 font-bold">4.</span>
                    <span>Input data dari form ke sistem Filament</span>
                </li>
            </ul>
        </div>

        <!-- Form Terisi -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center space-x-2 mb-3">
                <x-heroicon-o-document-check class="w-5 h-5 text-green-600"/>
                <h4 class="font-semibold text-green-800">Form Terisi</h4>
            </div>
            <ul class="text-sm text-green-700 space-y-2">
                <li class="flex items-start space-x-2">
                    <span class="text-green-500 font-bold">1.</span>
                    <span>Klik "Form PDF" pada action user yang sudah ada</span>
                </li>
                <li class="flex items-start space-x-2">
                    <span class="text-green-500 font-bold">2.</span>
                    <span>Pilih "Form Terisi" untuk verifikasi data</span>
                </li>
                <li class="flex items-start space-x-2">
                    <span class="text-green-500 font-bold">3.</span>
                    <span>Review dan arsipkan untuk dokumentasi</span>
                </li>
            </ul>
        </div>
    </div>

    <!-- Field Mapping -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex items-center space-x-2 mb-3">
            <x-heroicon-o-map class="w-5 h-5 text-yellow-600"/>
            <h4 class="font-semibold text-yellow-800">Mapping Field Form ke Sistem</h4>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div class="space-y-2">
                <h5 class="font-medium text-yellow-700">Tab 1: Basic Information</h5>
                <ul class="text-yellow-600 space-y-1">
                    <li>• Nama Lengkap → <code>name</code></li>
                    <li>• Email → <code>email</code></li>
                    <li>• Nomor Telepon → <code>phone_number</code></li>
                    <li>• Tanggal Lahir → <code>date_of_birth</code></li>
                    <li>• Jenis Kelamin → <code>gender</code></li>
                    <li>• Departemen → <code>department</code></li>
                    <li>• Alamat → <code>address</code></li>
                </ul>
            </div>
            <div class="space-y-2">
                <h5 class="font-medium text-yellow-700">Tab 2: Personal & Employment</h5>
                <ul class="text-yellow-600 space-y-1">
                    <li>• Tanggal Mulai Kerja → <code>hire_date</code></li>
                    <li>• Status Jabatan → <code>status_id</code></li>
                    <li>• Role/Hak Akses → <code>roles</code></li>
                    <li>• Status Akun → <code>status</code></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Tips -->
    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
        <div class="flex items-center space-x-2 mb-3">
            <x-heroicon-o-light-bulb class="w-5 h-5 text-purple-600"/>
            <h4 class="font-semibold text-purple-800">Tips & Best Practices</h4>
        </div>
        <ul class="text-sm text-purple-700 space-y-2">
            <li class="flex items-start space-x-2">
                <span class="text-purple-500">💡</span>
                <span>Selalu verifikasi data dengan dokumen asli sebelum input ke sistem</span>
            </li>
            <li class="flex items-start space-x-2">
                <span class="text-purple-500">📄</span>
                <span>Upload dokumen kontrak dan identitas di Tab "Documents & Notes"</span>
            </li>
            <li class="flex items-start space-x-2">
                <span class="text-purple-500">🔐</span>
                <span>Set password temporary yang kuat untuk akun baru</span>
            </li>
            <li class="flex items-start space-x-2">
                <span class="text-purple-500">👥</span>
                <span>Assign role sesuai dengan jabatan dan tanggung jawab</span>
            </li>
            <li class="flex items-start space-x-2">
                <span class="text-purple-500">📝</span>
                <span>Isi kontak darurat dan catatan penting di Tab "Documents & Notes"</span>
            </li>
        </ul>
    </div>
</div>