@extends('layouts.app')

@section('title', $editRequest ? 'Edit Permohonan Cuti' : 'Ajukan Cuti Baru')

@section('content')
    @include('front.header')

    <div class="min-h-screen bg-gray-50 py-12" style="font-family: 'Poppins', sans-serif;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <!-- Header Section -->
                    <div class="text-center mb-12">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-600 rounded-full mb-6">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h1 class="text-4xl font-bold text-gray-900 mb-4">
                            {{ $editRequest ? 'Edit Permohonan Cuti' : 'Ajukan Permohonan Cuti' }}
                        </h1>
                        <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                            {{ $editRequest ? 'Ubah detail permohonan cuti Anda. Pastikan semua informasi telah diperbarui dengan benar.' : 'Lengkapi formulir di bawah untuk mengajukan permohonan cuti. Pastikan semua informasi yang diperlukan telah diisi dengan benar.' }}
                        </p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Main Form -->
                        <div class="lg:col-span-2">
                            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                                <!-- Form Header -->
                                <div class="bg-blue-600 px-8 py-6">
                                    <h2 class="text-2xl font-bold text-white">
                                        {{ $editRequest ? 'Edit Permohonan Cuti' : 'Formulir Permohonan Cuti' }}
                                    </h2>
                                    <p class="text-blue-100 mt-2">
                                        {{ $editRequest ? 'Perbarui detail permohonan cuti Anda' : 'Silakan isi semua field yang diperlukan' }}
                                    </p>
                                </div>

                                <!-- Form Content -->
                                <div class="p-8">
                                    @if ($errors->any())
                                        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                                            <div class="flex items-center mb-2">
                                                <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <h4 class="text-red-800 font-semibold">Terdapat kesalahan dalam form:</h4>
                                            </div>
                                            <ul class="text-red-700 text-sm space-y-1">
                                                @foreach ($errors->all() as $error)
                                                    <li>• {{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if (session('success'))
                                        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                                            <div class="flex items-center">
                                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                                <p class="text-green-800 font-medium">{{ session('success') }}</p>
                                            </div>
                                        </div>
                                    @endif

                                    <form id="leaveRequestForm"
                                        action="{{ $editRequest ? route('leave.update', $editRequest->id) : route('leave.store') }}"
                                        method="POST" enctype="multipart/form-data" class="space-y-8">
                                        @csrf
                                        @if ($editRequest)
                                            @method('PUT')
                                        @endif

                                        <!-- Leave Type Selection -->
                                        <div>
                                            <label for="leave_type_id" class="block text-sm font-medium text-gray-700 mb-2">
                                                Jenis Cuti <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <select id="leave_type_id" name="leave_type_id" required
                                                    class="w-full px-4 py-3 pr-10 border rounded-lg appearance-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 {{ $errors->has('leave_type_id') ? 'border-red-500 bg-red-50' : 'border-gray-300' }}">
                                                    <option value="" disabled
                                                        {{ old('leave_type_id', $editRequest?->leave_type_id) ? '' : 'selected' }}>
                                                        Pilih jenis cuti...
                                                    </option>
                                                    @foreach ($leaveTypes as $type)
                                                        <option value="{{ $type->id }}" @selected(old('leave_type_id', $editRequest?->leave_type_id) == $type->id)>
                                                            {{ $type->name }} ({{ $type->max_days_per_year }}
                                                            hari/tahun)
                                                        </option>
                                                    @endforeach
                                                </select>

                                                <!-- Ikon dropdown -->
                                                <svg class="w-5 h-5 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </div>

                                            @error('leave_type_id')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Date Range -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <!-- Start Date -->
                                            <div>
                                                <label for="start_date"
                                                    class="block text-sm font-medium text-gray-700 mb-2">
                                                    Tanggal Mulai <span class="text-red-500">*</span>
                                                </label>
                                                <input type="date" id="start_date" name="start_date" required
                                                    value="{{ old('start_date', $editRequest?->start_date?->format('Y-m-d')) }}"
                                                    min="{{ date('Y-m-d') }}"
                                                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 {{ $errors->has('start_date') ? 'border-red-500' : 'border-gray-300' }}">
                                                @error('start_date')
                                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <!-- End Date -->
                                            <div>
                                                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                                                    Tanggal Selesai <span class="text-red-500">*</span>
                                                </label>
                                                <input type="date" id="end_date" name="end_date" required
                                                    value="{{ old('end_date', $editRequest?->end_date?->format('Y-m-d')) }}"
                                                    min="{{ date('Y-m-d') }}"
                                                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 {{ $errors->has('end_date') ? 'border-red-500' : 'border-gray-300' }}">
                                                @error('end_date')
                                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Total Days Display -->
                                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <span class="text-blue-800 font-medium">Total Hari Cuti:</span>
                                                </div>
                                                <span id="totalDays" class="text-2xl font-bold text-blue-600">0 hari</span>
                                            </div>
                                        </div>

                                        <!-- Reason -->
                                        <div>
                                            <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                                                Alasan Cuti <span class="text-red-500">*</span>
                                            </label>
                                            <textarea id="reason" name="reason" rows="4" required
                                                placeholder="Jelaskan alasan mengajukan cuti (minimal 10 karakter)..."
                                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 resize-none {{ $errors->has('reason') ? 'border-red-500' : 'border-gray-300' }}">{{ old('reason', $editRequest?->reason) }}</textarea>
                                            @error('reason')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                            <div class="mt-2 text-sm text-gray-500 flex justify-between">
                                                <span>Minimal 10 karakter</span>
                                                <span id="reasonCount">0/500 karakter</span>
                                            </div>
                                        </div>

                                        <!-- Emergency Contact -->
                                        <div>
                                            <label for="emergency_contact"
                                                class="block text-sm font-medium text-gray-700 mb-2">
                                                Kontak Darurat (Opsional)
                                            </label>
                                            <input type="text" id="emergency_contact" name="emergency_contact"
                                                value="{{ old('emergency_contact', $editRequest?->emergency_contact) }}"
                                                placeholder="Nama dan nomor telepon yang dapat dihubungi..."
                                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 {{ $errors->has('emergency_contact') ? 'border-red-500' : 'border-gray-300' }}">
                                            @error('emergency_contact')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Replacement Employee -->
                                        <div>
                                            <label for="replacement_employee_id"
                                                class="block text-sm font-medium text-gray-700 mb-2">
                                                Karyawan Pengganti (Opsional)
                                            </label>
                                            <p class="text-xs text-gray-500 mb-2">Pilih karyawan yang akan menggantikan tugas Anda selama cuti</p>
                                            <div class="relative">
                                                <select id="replacement_employee_id" name="replacement_employee_id"
                                                    class="w-full px-4 py-3 pr-10 border rounded-lg appearance-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 {{ $errors->has('replacement_employee_id') ? 'border-red-500 bg-red-50' : 'border-gray-300' }}">
                                                    <option value=""
                                                        {{ old('replacement_employee_id', $editRequest?->replacement_employee_id) ? '' : 'selected' }}>
                                                        Tidak ada pengganti
                                                    </option>
                                                    @foreach ($employees as $employee)
                                                        <option value="{{ $employee->id }}" @selected(old('replacement_employee_id', $editRequest?->replacement_employee_id) == $employee->id)>
                                                            {{ $employee->name }}
                                                            @if($employee->department)
                                                                - {{ $employee->department }}
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </select>

                                                <!-- Ikon dropdown -->
                                                <svg class="w-5 h-5 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </div>
                                            @error('replacement_employee_id')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                            <p class="mt-2 text-sm text-gray-500">
                                                💡 Pilih karyawan yang akan menggantikan tugas Anda selama cuti
                                            </p>
                                        </div>

                                        <!-- File Upload -->
                                        <div>
                                            <label for="documents" class="block text-sm font-medium text-gray-700 mb-2">
                                                Dokumen Pendukung (Opsional)
                                            </label>
                                            <div
                                                class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors duration-200">
                                                <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                                    </path>
                                                </svg>
                                                <input type="file" id="documents" name="documents[]" multiple
                                                    accept=".pdf,.jpg,.jpeg,.png" class="hidden">
                                                <div class="cursor-pointer" onclick="document.getElementById('documents').click()">
                                                    <span class="text-sm text-gray-600">Klik untuk upload atau drag and
                                                        drop</span>
                                                    <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG maksimal
                                                        2MB
                                                        setiap file</p>
                                                </div>
                                            </div>
                                            @error('documents.*')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Submit Buttons -->
                                        <div class="flex space-x-4 pt-6 border-t border-gray-200">
                                            <button type="submit" id="submitBtn"
                                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed">
                                                <span class="submit-text">
                                                    {{-- <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                                    </svg> --}}
                                                    {{ $editRequest ? 'Perbarui' : 'Kirim' }}
                                                </span>
                                                <span class="submit-loading hidden">
                                                    <svg class="animate-spin w-5 h-5 mr-2" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                                            stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor"
                                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                        </path>
                                                    </svg>
                                                    Memproses...
                                                </span>
                                            </button>
                                            <button type="button" onclick="saveDraft()"
                                                class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 flex items-center">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12">
                                                    </path>
                                                </svg>
                                                Simpan Draft
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar -->
                        <div class="space-y-6">
                            <!-- Leave Balance Card -->
                            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <div class="bg-green-100 p-2 rounded-lg mr-3">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    Saldo Cuti Anda
                                </h3>
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Total Jatah:</span>
                                        <span class="font-semibold text-gray-800">{{ $annualLeaveAllowance }} hari</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Telah Digunakan:</span>
                                        <span class="font-semibold text-red-600">{{ $usedLeave }} hari</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Sisa Tersedia:</span>
                                        <span class="font-semibold text-green-600">{{ $remainingLeave }} hari</span>
                                    </div>
                                    <!-- Progress Bar -->
                                    <div class="w-full bg-gray-200 rounded-full h-3">
                                        @php
                                            $usagePercent =
                                                $annualLeaveAllowance > 0
                                                    ? ($usedLeave / $annualLeaveAllowance) * 100
                                                    : 0;
                                        @endphp
                                        <div class="bg-blue-500 h-3 rounded-full transition-all duration-300"
                                            style="width: {{ min($usagePercent, 100) }}%"></div>
                                    </div>
                                    <div class="text-center text-sm text-gray-500">
                                        {{ number_format($usagePercent, 1) }}% terpakai
                                    </div>
                                </div>
                            </div>

                            <!-- Tips Card -->
                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                                <h3 class="text-lg font-semibold text-blue-800 mb-4 flex items-center">
                                    <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    Tips Pengajuan Cuti
                                </h3>
                                <ul class="space-y-3 text-sm text-blue-700">
                                    <li class="flex items-start">
                                        <svg class="w-4 h-4 text-blue-500 mr-2 mt-0.5 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        Ajukan permohonan minimal 3 hari sebelumnya
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-4 h-4 text-blue-500 mr-2 mt-0.5 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        Sertakan alasan yang jelas dan detail
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-4 h-4 text-blue-500 mr-2 mt-0.5 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        Periksa saldo cuti sebelum mengajukan
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-4 h-4 text-blue-500 mr-2 mt-0.5 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        Upload dokumen pendukung jika diperlukan
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-4 h-4 text-blue-500 mr-2 mt-0.5 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        Tentukan karyawan pengganti untuk kelancaran kerja
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-4 h-4 text-blue-500 mr-2 mt-0.5 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        Hubungi HR untuk situasi darurat
                                    </li>
                                </ul>
                            </div>

                            <!-- Quick Actions -->
                            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Aksi Cepat</h3>
                                <div class="space-y-3">
                                    <a href="{{ route('leave.status') }}"
                                        class="w-full bg-blue-50 hover:bg-blue-100 text-blue-700 font-medium py-3 px-4 rounded-lg border border-blue-200 hover:border-blue-300 transition-all duration-200 flex items-center justify-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h2m2-6v6a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2m-2 6V9a2 2 0 012-2h2" />
                                        </svg>
                                        Lihat Riwayat Cuti
                                    </a>
                                    <a href="{{ route('leave.status') }}"
                                        class="w-full bg-blue-50 hover:bg-blue-100 text-blue-700 font-medium py-3 px-4 rounded-lg border border-blue-200 hover:border-blue-300 transition-all duration-200 flex items-center justify-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Status Permohonan
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    </div>

    <script>
        // Calculate total days
        function calculateTotalDays() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;

            if (startDate && endDate) {
                const start = new Date(startDate);
                const end = new Date(endDate);

                if (end >= start) {
                    const diffTime = Math.abs(end - start);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                    document.getElementById('totalDays').textContent = diffDays + ' hari';
                } else {
                    document.getElementById('totalDays').textContent = '0 hari';
                }
            } else {
                document.getElementById('totalDays').textContent = '0 hari';
            }
        }

        // Character count for reason
        document.getElementById('reason').addEventListener('input', function() {
            const count = this.value.length;
            document.getElementById('reasonCount').textContent = count + '/500 karakter';
        });

        // Date change listeners
        document.getElementById('start_date').addEventListener('change', calculateTotalDays);
        document.getElementById('end_date').addEventListener('change', calculateTotalDays);

        // End date minimum validation
        document.getElementById('start_date').addEventListener('change', function() {
            const startDate = this.value;
            const endDateInput = document.getElementById('end_date');
            endDateInput.min = startDate;

            // If end date is before start date, clear it
            if (endDateInput.value && endDateInput.value < startDate) {
                endDateInput.value = '';
            }
        });

        // Fungsi simpan draft
        function saveDraft() {
            try {
                // Collect form data
                const formData = new FormData(document.getElementById('leaveRequestForm'));

                // Validate that at least one field is filled
                const hasData = formData.get('leave_type_id') || 
                              formData.get('start_date') || 
                              formData.get('end_date') || 
                              formData.get('reason') || 
                              formData.get('emergency_contact') || 
                              formData.get('replacement_employee_id');

                if (!hasData) {
                    alert('Tidak ada data untuk disimpan sebagai draft.');
                    return;
                }

                // Store in localStorage
                const draftData = {
                    leave_type_id: formData.get('leave_type_id'),
                    start_date: formData.get('start_date'),
                    end_date: formData.get('end_date'),
                    reason: formData.get('reason'),
                    emergency_contact: formData.get('emergency_contact'),
                    replacement_employee_id: formData.get('replacement_employee_id'),
                    timestamp: new Date().toISOString()
                };

                localStorage.setItem('leaveRequestDraft', JSON.stringify(draftData));

                // Show notification
                const notification = document.createElement('div');
                notification.className =
                    'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300';
                notification.textContent = 'Draft berhasil disimpan!';
                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.style.transform = 'translateX(100%)';
                    setTimeout(() => notification.remove(), 300);
                }, 3000);

            } catch (error) {
                console.error('Error saving draft:', error);
                alert('Gagal menyimpan draft. Silakan coba lagi.');
            }
        }

        // Submit form with loading state
        document.getElementById('leaveRequestForm').addEventListener('submit', function() {
            const submitBtn = document.getElementById('submitBtn');
            const submitText = submitBtn.querySelector('.submit-text');
            const submitLoading = submitBtn.querySelector('.submit-loading');

            // Show loading state
            submitText.classList.add('hidden');
            submitLoading.classList.remove('hidden');
            submitBtn.disabled = true;

            // Clear any saved draft on successful submit
            localStorage.removeItem('leaveRequestDraft');
        });

        // Enhanced file upload preview
        document.getElementById('documents').addEventListener('change', function(e) {
            const files = e.target.files;
            const uploadArea = e.target.parentNode;

            if (files.length > 0) {
                // Validate file size (2MB max per file to match backend)
                const maxSize = 2 * 1024 * 1024; // 2MB
                let hasOversizedFile = false;
                let hasInvalidFormat = false;
                const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];

                Array.from(files).forEach(file => {
                    if (file.size > maxSize) {
                        hasOversizedFile = true;
                    }
                    if (!allowedTypes.includes(file.type)) {
                        hasInvalidFormat = true;
                    }
                });

                if (hasOversizedFile) {
                    alert('Beberapa file melebihi ukuran maksimal 2MB. Silakan pilih file yang lebih kecil.');
                    e.target.value = '';
                    return;
                }

                if (hasInvalidFormat) {
                    alert('Format file tidak didukung. Hanya PDF, JPG, dan PNG yang diperbolehkan.');
                    e.target.value = '';
                    return;
                }

                // Update display
                const fileNames = Array.from(files).map(file => {
                    const name = file.name;
                    return name.length > 30 ? name.substring(0, 27) + '...' : name;
                }).join(', ');

                const label = uploadArea.querySelector('span');
                label.textContent = `${files.length} file dipilih`;

                // Show file list
                let fileList = uploadArea.querySelector('.file-list');
                if (!fileList) {
                    fileList = document.createElement('div');
                    fileList.className = 'file-list mt-2 text-xs text-gray-600';
                    uploadArea.appendChild(fileList);
                }
                fileList.textContent = fileNames;

                // Change border color to indicate files selected
                uploadArea.classList.remove('border-gray-300');
                uploadArea.classList.add('border-green-400', 'bg-green-50');
            } else {
                // Reset if no files
                const label = uploadArea.querySelector('span');
                label.textContent = 'Klik untuk upload atau drag and drop';

                const fileList = uploadArea.querySelector('.file-list');
                if (fileList) fileList.remove();

                uploadArea.classList.remove('border-green-400', 'bg-green-50');
                uploadArea.classList.add('border-gray-300');
            }
        });

        // Load draft on page load
        document.addEventListener('DOMContentLoaded', function() {
            try {
                const savedDraft = localStorage.getItem('leaveRequestDraft');
                if (savedDraft) {
                    const draftData = JSON.parse(savedDraft);

                    // Validate draft data
                    if (!draftData.timestamp) {
                        console.warn('Invalid draft data found, clearing...');
                        localStorage.removeItem('leaveRequestDraft');
                        return;
                    }

                    // Check if draft is too old (optional: remove drafts older than 7 days)
                    const draftAge = new Date() - new Date(draftData.timestamp);
                    const maxAge = 7 * 24 * 60 * 60 * 1000; // 7 days in milliseconds
                    
                    if (draftAge > maxAge) {
                        console.log('Draft is too old, removing...');
                        localStorage.removeItem('leaveRequestDraft');
                        return;
                    }

                    // Show restore notification
                    const restoreDiv = document.createElement('div');
                    restoreDiv.className = 'bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6';
                    restoreDiv.innerHTML = `
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-medium text-blue-800">Draft Tersimpan</h4>
                        <p class="text-sm text-blue-600">Draft terakhir disimpan pada ${new Date(draftData.timestamp).toLocaleString('id-ID')}</p>
                    </div>
                    <div class="space-x-2">
                        <button onclick="restoreDraft()" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Pulihkan</button>
                        <button onclick="clearDraft()" class="text-red-600 hover:text-red-800 text-sm font-medium">Hapus</button>
                    </div>
                </div>
            `;

                    const form = document.getElementById('leaveRequestForm');
                    form.parentNode.insertBefore(restoreDiv, form);
                }
            } catch (error) {
                console.error('Error loading draft:', error);
                // Clear corrupted draft
                localStorage.removeItem('leaveRequestDraft');
            }
        });

        // Restore draft function
        function restoreDraft() {
            try {
                const savedDraft = localStorage.getItem('leaveRequestDraft');
                if (savedDraft) {
                    const draftData = JSON.parse(savedDraft);

                    if (draftData.leave_type_id) document.getElementById('leave_type_id').value = draftData.leave_type_id;
                    if (draftData.start_date) document.getElementById('start_date').value = draftData.start_date;
                    if (draftData.end_date) document.getElementById('end_date').value = draftData.end_date;
                    if (draftData.reason) document.getElementById('reason').value = draftData.reason;
                    if (draftData.emergency_contact) document.getElementById('emergency_contact').value = draftData.emergency_contact;
                    if (draftData.replacement_employee_id) document.getElementById('replacement_employee_id').value = draftData.replacement_employee_id;

                    // Calculate days if dates are filled
                    if (draftData.start_date && draftData.end_date) {
                        calculateTotalDays();
                    }

                    // Update character count for reason
                    if (draftData.reason) {
                        document.getElementById('reasonCount').textContent = draftData.reason.length + '/500 karakter';
                    }

                    // Remove restore notification
                    const restoreDiv = document.querySelector('.bg-blue-50');
                    if (restoreDiv) restoreDiv.remove();

                    // Show success message
                    const notification = document.createElement('div');
                    notification.className = 'fixed top-4 right-4 bg-blue-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300';
                    notification.textContent = 'Draft berhasil dipulihkan!';
                    document.body.appendChild(notification);

                    setTimeout(() => {
                        notification.style.transform = 'translateX(100%)';
                        setTimeout(() => notification.remove(), 300);
                    }, 2000);
                }
            } catch (error) {
                console.error('Error restoring draft:', error);
                alert('Gagal memulihkan draft. Data mungkin rusak.');
                clearDraft(); // Clear corrupted draft
            }
        }

        // Clear draft function
        function clearDraft() {
            localStorage.removeItem('leaveRequestDraft');
            const restoreDiv = document.querySelector('.bg-blue-50');
            if (restoreDiv) restoreDiv.remove();
        }
    </script>
@endsection
