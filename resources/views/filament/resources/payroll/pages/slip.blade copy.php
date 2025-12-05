<x-filament-panels::page>
    <!-- Navigation buttons (print only hidden) -->
    <div class="no-print mb-4 flex justify-between items-center">
        <a href="{{ \App\Filament\Resources\PayrollResource::getUrl('index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Payroll
        </a>
        
        <button onclick="window.print()" 
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Print Slip Gaji
        </button>
    </div>

    <div class="slip-gaji-container bg-white p-8">
        <!-- Header Perusahaan -->
        <div class="header-section mb-8 text-center border-b-2 border-gray-300 pb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">PT. MAKNA SPACE</h1>
            <p class="text-gray-600 text-sm">Jl. Contoh Alamat No. 123, Jakarta Selatan</p>
            <p class="text-gray-600 text-sm">Telp: (021) 1234-5678 | Email: info@maknaspace.com</p>
            <div class="mt-4">
                <h2 class="text-xl font-semibold text-blue-600">SLIP GAJI KARYAWAN</h2>
                <p class="text-gray-500">Periode: {{ now()->format('F Y') }}</p>
            </div>
        </div>

        <!-- Informasi Karyawan -->
        <div class="employee-info grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="font-semibold text-gray-800 mb-3 border-b border-gray-300 pb-2">Data Karyawan</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nama:</span>
                        <span class="font-medium">{{ $this->record->user->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">ID Karyawan:</span>
                        <span class="font-medium">{{ $this->record->user->employee_id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Jabatan:</span>
                        <span class="font-medium">{{ $this->record->user->status?->status_name ?? 'Staff' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Departemen:</span>
                        <span class="font-medium">{{ ucfirst($this->record->user->department ?? 'General') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Email:</span>
                        <span class="font-medium text-sm">{{ $this->record->user->email }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="font-semibold text-gray-800 mb-3 border-b border-gray-300 pb-2">Data Gaji</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Gaji Bulanan:</span>
                        <span class="font-medium">Rp {{ number_format($this->record->monthly_salary, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Gaji Tahunan:</span>
                        <span class="font-medium">Rp {{ number_format($this->record->annual_salary, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Bonus:</span>
                        <span class="font-medium">Rp {{ number_format($this->record->bonus, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Kompensasi:</span>
                        <span class="font-medium text-green-600">Rp {{ number_format($this->record->total_compensation, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Gaji Bersih -->
        <div class="net-salary bg-blue-50 border-2 border-blue-300 rounded-lg p-6 mb-8">
            <div class="text-center">
                <h3 class="text-xl font-bold text-blue-800 mb-2">GAJI BERSIH</h3>
                <p class="text-3xl font-bold text-blue-600">Rp {{ number_format($this->record->monthly_salary, 0, ',', '.') }}</p>
                <p class="text-gray-600 text-sm mt-2">{{ now()->format('F Y') }}</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer mt-8 pt-6 border-t border-gray-300">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                <div>
                    <p class="text-sm text-gray-600 mb-2">Disiapkan Oleh:</p>
                    <div class="border-t border-gray-400 mt-8 pt-2">
                        <p class="text-sm font-medium">HRD Department</p>
                    </div>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-2">Disetujui Oleh:</p>
                    <div class="border-t border-gray-400 mt-8 pt-2">
                        <p class="text-sm font-medium">Finance Manager</p>
                    </div>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-2">Diterima Oleh:</p>
                    <div class="border-t border-gray-400 mt-8 pt-2">
                        <p class="text-sm font-medium">{{ $this->record->user->name }}</p>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-6">
                <p class="text-xs text-gray-500">
                    Slip gaji ini digenerate otomatis oleh sistem pada {{ now()->format('d F Y H:i') }}
                </p>
                <p class="text-xs text-gray-500">
                    Dokumen ini sah tanpa tanda tangan basah sesuai UU ITE
                </p>
            </div>
        </div>
    </div>

    <!-- Print Styles -->
    <style>
        @media print {
            .fi-header, .fi-sidebar, .fi-main-ctn > .fi-page > .fi-page-actions, 
            .fi-topbar, .fi-breadcrumbs, .no-print {
                display: none !important;
            }
            
            .slip-gaji-container {
                margin: 0;
                padding: 20px;
                box-shadow: none;
            }
            
            .fi-main {
                padding: 0 !important;
            }
            
            body {
                background: white !important;
            }
            
            .fi-page {
                background: white !important;
                box-shadow: none !important;
            }
        }
    </style>
    </style>
</x-filament-panels::page>
