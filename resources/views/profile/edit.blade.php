@extends('layouts.app')

@section('title', 'Edit Profile - ' . Auth::user()->name)

@section('content')
    <!-- Include Header -->
    @include('front.header')
    
    <div class="relative min-h-screen bg-white py-8 overflow-hidden" style="font-family: 'Poppins', sans-serif;">
        <!-- Decorative background glows -->
        <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10">
            <div class="absolute -top-28 -left-24 w-[28rem] h-[28rem] rounded-full bg-blue-400/20 blur-[120px]"></div>
            <div class="absolute -top-10 right-[-8rem] w-[40rem] h-[40rem] rounded-full bg-purple-500/20 blur-[140px]">
            </div>
            <div
                class="absolute bottom-[-10rem] left-1/2 -translate-x-1/2 w-[48rem] h-[48rem] rounded-full bg-indigo-400/10 blur-[160px]">
            </div>
        </div>
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Alert Messages -->
            <div id="alert-container">
                @if (session('success'))
                    <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded-r-lg shadow-sm" role="alert">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-400 mr-3"></i>
                            <span class="text-green-800 font-medium">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg shadow-sm" role="alert">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-circle text-red-400 mr-3 mt-0.5"></i>
                            <div class="text-red-800">
                                <p class="font-medium mb-2">Harap perbaiki kesalahan berikut:</p>
                                <ul class="list-disc list-inside space-y-1 text-sm">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Header Section -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-4xl font-bold text-gray-900 mb-2">Edit Profil</h1>
                        <p class="text-gray-600 flex items-center">
                            <i class="fas fa-user-edit mr-2"></i>
                            Perbarui informasi pribadi dan pengaturan akun Anda
                        </p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('profile.show') }}"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition-all duration-200 shadow-sm">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali ke Profil
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Form Container -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden transition fade-in">
                <form id="profile-form" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Profile Picture Section -->
                    <div class="relative px-6 py-8 rounded-t-2xl"
                        style="background: linear-gradient(to right, #2563eb, #1e40af);">
                        <div class="flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-6">
                            <!-- Avatar Upload -->
                            <div id="avatar-drop-zone" class="relative group">
                                <div
                                    class="w-24 h-24 rounded-full overflow-hidden border-4 border-white shadow-lg bg-white">
                                    @if ($user->avatar_url)
                                        <img id="avatar-preview" src="{{ Storage::url($user->avatar_url) }}"
                                            alt="Profile Picture" class="w-full h-full object-cover">
                                    @else
                                        <div id="avatar-preview"
                                            class="w-full h-full bg-gray-200 flex items-center justify-center">
                                            <i class="fas fa-user text-gray-400 text-2xl"></i>
                                        </div>
                                    @endif
                                </div>
                                <label for="avatar"
                                    class="absolute inset-0 bg-black bg-opacity-50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                                    <i class="fas fa-camera text-white text-lg"></i>
                                </label>
                                <input type="file" id="avatar" name="avatar" accept="image/*" class="hidden">
                            </div>

                            <div class="text-center sm:text-left">
                                <h2 class="text-2xl font-bold text-white">{{ $user->name }}</h2>
                                <p class="text-blue-100 mb-2">{{ $user->email }}</p>
                                <div class="text-sm text-blue-200">
                                    <i class="fas fa-calendar-alt mr-1"></i>
                                    Anggota sejak {{ $user->created_at->format('M Y') }}
                                </div>
                            </div>
                        </div>
                        @error('avatar')
                            <p class="mt-2 text-sm text-red-200 bg-red-500 bg-opacity-20 px-3 py-1 rounded">{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Form Sections -->
                    <div class="p-0">
                        <!-- Basic Information -->
                        <div class="p-8 border-b border-gray-200">
                            <div class="flex items-center mb-6">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-900">Informasi Dasar</h3>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <!-- Full Name -->
                                <div class="form-group">
                                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Nama Lengkap <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="name" name="name"
                                            value="{{ old('name', $user->name) }}"
                                            class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white text-gray-900 @error('name') border-red-300 ring-red-200 @enderror"
                                            required>
                                        <i
                                            class="fas fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                    </div>
                                    @error('name')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="form-group">
                                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Alamat Email <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="email" id="email" name="email"
                                            value="{{ old('email', $user->email) }}"
                                            class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all @error('email') border-red-300 ring-red-200 @enderror"
                                            required>
                                        <i
                                            class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                    </div>
                                    @error('email')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Phone Number -->
                                <div class="form-group">
                                    <label for="phone_number" class="block text-sm font-semibold text-gray-700  mb-2">
                                        Nomor Telepon
                                    </label>
                                    <div class="relative">
                                        <input type="tel" id="phone_number" name="phone_number"
                                            value="{{ old('phone_number', $user->phone_number) }}"
                                            class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all @error('phone_number') border-red-300 ring-red-200 @enderror"
                                            placeholder="e.g., +62 812 3456 7890">
                                        <i
                                            class="fas fa-phone absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                    </div>
                                    @error('phone_number')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Date of Birth -->
                                <div class="form-group">
                                    <label for="date_of_birth" class="block text-sm font-semibold text-gray-700  mb-2">
                                        Tanggal Lahir
                                    </label>
                                    <div class="relative">
                                        <input type="date" id="date_of_birth" name="date_of_birth"
                                            value="{{ old('date_of_birth', $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '') }}"
                                            class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all @error('date_of_birth') border-red-300 ring-red-200 @enderror">
                                        <i
                                            class="fas fa-calendar-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                    </div>
                                    @error('date_of_birth')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Gender -->
                                <div class="form-group">
                                    <label for="gender" class="block text-sm font-semibold text-gray-700  mb-2">
                                        Jenis Kelamin
                                    </label>
                                    <div class="relative">
                                        <select id="gender" name="gender"
                                            class="w-full appearance-none px-4 py-3 pl-10 pr-10 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all @error('gender') border-red-300 ring-red-200 @enderror">
                                            <option value="">-- Pilih Jenis Kelamin --</option>
                                            <option value="male"
                                                {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Laki-laki
                                            </option>
                                            <option value="female"
                                                {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Perempuan
                                            </option>
                                        </select>
                                        <i
                                            class="fas fa-venus-mars absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <i
                                            class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                                    </div>
                                    @error('gender')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Hire Date -->
                                <div class="form-group">
                                    <label for="hire_date" class="block text-sm font-semibold text-gray-700  mb-2">
                                        Tanggal Mulai Bekerja
                                        <span class="text-xs text-blue-600 font-normal">(Hubungi HR untuk mengubah)</span>
                                    </label>
                                    <div class="relative">
                                        <input type="date" id="hire_date" name="hire_date"
                                            value="{{ old('hire_date', $user->hire_date ? $user->hire_date->format('Y-m-d') : '') }}"
                                            class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg shadow-sm bg-gray-50 text-gray-600 cursor-not-allowed @error('hire_date') border-red-300 @enderror"
                                            readonly>
                                        <i
                                            class="fas fa-briefcase absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <i
                                            class="fas fa-lock absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                    </div>
                                    @error('hire_date')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="mt-6">
                                <label for="address" class="block text-sm font-semibold text-gray-700  mb-2">
                                    Alamat
                                </label>
                                <div class="relative">
                                    <textarea id="address" name="address" rows="4"
                                        class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none @error('address') border-red-300 ring-red-200 @enderror"
                                        placeholder="Masukkan alamat lengkap Anda">{{ old('address', $user->address) }}</textarea>
                                    <i class="fas fa-map-marker-alt absolute left-3 top-4 text-gray-400"></i>
                                </div>
                                @error('address')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <!-- Password Change Section -->
                        <div class="p-8">
                            <div class="flex items-center mb-6">
                                <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-lock text-indigo-600"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-semibold text-gray-900 ">Ubah Password</h3>
                                    <p class="text-sm text-gray-500 mt-1">Biarkan kosong untuk tetap menggunakan password
                                        saat ini</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <!-- Current Password -->
                                <div class="form-group lg:col-span-2">
                                    <label for="current_password" class="block text-sm font-semibold text-gray-700  mb-2">
                                        Password Saat Ini
                                    </label>
                                    <div class="relative">
                                        <input type="password" id="current_password" name="current_password"
                                            class="w-full px-4 py-3 pl-10 pr-10 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all @error('current_password') border-red-300 ring-red-200 @enderror"
                                            placeholder="Masukkan password saat ini">
                                        <i
                                            class="fas fa-key absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <button type="button"
                                            class="toggle-password absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    @error('current_password')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- New Password -->
                                <div class="form-group">
                                    <label for="password" class="block text-sm font-semibold text-gray-700  mb-2">
                                        Password Baru
                                    </label>
                                    <div class="relative">
                                        <input type="password" id="password" name="password"
                                            class="w-full px-4 py-3 pl-10 pr-10 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all @error('password') border-red-300 ring-red-200 @enderror"
                                            placeholder="Masukkan password baru">
                                        <i
                                            class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <button type="button"
                                            class="toggle-password absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <!-- Password Strength Indicator -->
                                    <div id="password-strength" class="mt-2 hidden">
                                        <div class="flex items-center space-x-2">
                                            <div class="flex-1 bg-gray-200 rounded-full h-2">
                                                <div id="strength-bar"
                                                    class="h-2 rounded-full transition-all duration-300"></div>
                                            </div>
                                            <span id="strength-text" class="text-xs font-medium"></span>
                                        </div>
                                        <ul id="password-requirements" class="mt-2 text-xs space-y-1">
                                            <li id="length-req" class="flex items-center text-gray-500">
                                                <i class="fas fa-circle mr-2 text-xs"></i>Minimal 8 karakter
                                            </li>
                                            <li id="uppercase-req" class="flex items-center text-gray-500">
                                                <i class="fas fa-circle mr-2 text-xs"></i>Satu huruf besar
                                            </li>
                                            <li id="lowercase-req" class="flex items-center text-gray-500">
                                                <i class="fas fa-circle mr-2 text-xs"></i>Satu huruf kecil
                                            </li>
                                            <li id="number-req" class="flex items-center text-gray-500">
                                                <i class="fas fa-circle mr-2 text-xs"></i>Satu angka
                                            </li>
                                        </ul>
                                    </div>
                                    @error('password')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Confirm Password -->
                                <div class="form-group">
                                    <label for="password_confirmation"
                                        class="block text-sm font-semibold text-gray-700  mb-2">
                                        Konfirmasi Password Baru
                                    </label>
                                    <div class="relative">
                                        <input type="password" id="password_confirmation" name="password_confirmation"
                                            class="w-full px-4 py-3 pl-10 pr-10 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                            placeholder="Konfirmasi password baru">
                                        <i
                                            class="fas fa-check-circle absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <button type="button"
                                            class="toggle-password absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div id="password-match" class="mt-2 text-sm hidden"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div
                        class="bg-gray-50 border-t border-gray-200 px-8 py-6 flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-shield-alt mr-2 text-green-500"></i>
                            Informasi Anda dienkripsi dan aman
                        </div>

                        <div class="flex items-center space-x-4">
                            <a href="{{ route('profile.show') }}"
                                class="inline-flex items-center px-6 py-3 bg-white border-2 border-gray-300 rounded-lg font-semibold text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all duration-200">
                                <i class="fas fa-times mr-2"></i>
                                Batal
                            </a>

                            <button type="submit" id="submit-btn"
                                class="inline-flex items-center px-8 py-3 border border-transparent rounded-lg font-semibold text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transform hover:scale-105 transition-all duration-200 shadow-lg"
                                style="background: linear-gradient(to right, #2563eb, #4f46e5); transition: all 0.2s ease;"
                                onmouseover="this.style.background='linear-gradient(to right, #1d4ed8, #4338ca)'"
                                onmouseout="this.style.background='linear-gradient(to right, #2563eb, #4f46e5)'">
                                <span id="submit-text">
                                    <i class="fas fa-save mr-2"></i>
                                    Perbarui Profil
                                </span>
                                <span id="loading-text" class="hidden">
                                    <i class="fas fa-spinner fa-spin mr-2"></i>
                                    Memperbarui...
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Sticky Save Bar -->
            <div x-show="hasUnsaved" x-transition class="fixed bottom-4 left-1/2 -translate-x-1/2 z-40">
                <div class="bg-white border border-gray-200 shadow-xl rounded-full px-4 py-2 flex items-center gap-3">
                    <span class="text-sm text-gray-700">Perubahan belum disimpan</span>
                    <button type="button" @click="document.getElementById('profile-form').requestSubmit()"
                        class="px-3 py-1.5 rounded-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium">Simpan</button>
                </div>
            </div>

            <!-- Help Section -->
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-xl p-6">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-500 mr-3 mt-0.5"></i>
                    <div>
                        <h4 class="text-lg font-semibold text-blue-900 mb-2">Butuh Bantuan?</h4>
                        <p class="text-blue-800 text-sm mb-3">
                            Mengalami kesulitan memperbarui profil? Periksa panduan bantuan atau hubungi dukungan.
                        </p>
                        <div class="flex space-x-4">
                            <a href="#" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                <i class="fas fa-question-circle mr-1"></i>Panduan Bantuan
                            </a>
                            <a href="#" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                <i class="fas fa-envelope mr-1"></i>Hubungi Dukungan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    @include('front.footer')
@endsection

@push('styles')
    <style>
        .form-group {
            position: relative;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.15);
        }

        .toggle-password {
            cursor: pointer;
            transition: color 0.2s ease;
        }

        #avatar-preview {
            transition: transform 0.3s ease;
        }

        .group:hover #avatar-preview {
            transform: scale(1.05);
        }

        .password-requirement-met {
            color: #10b981 !important;
        }

        .password-requirement-met i {
            color: #10b981 !important;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeIn 0.3s ease-out;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Avatar preview functionality
            const avatarInput = document.getElementById('avatar');
            const avatarPreview = document.getElementById('avatar-preview');

            if (avatarInput && avatarPreview) {
                avatarInput.addEventListener('change', function(event) {
                    const file = event.target.files[0];
                    if (file) {
                        // Validate file size (2MB)
                        if (file.size > 2 * 1024 * 1024) {
                            showAlert('Ukuran file harus kurang dari 2MB', 'error');
                            this.value = '';
                            return;
                        }

                        // Validate file type
                        if (!file.type.match('image.*')) {
                            showAlert('Silakan pilih file gambar yang valid', 'error');
                            this.value = '';
                            return;
                        }

                        const reader = new FileReader();
                        reader.onload = function(e) {
                            if (avatarPreview.tagName === 'IMG') {
                                avatarPreview.src = e.target.result;
                            } else {
                                avatarPreview.innerHTML =
                                    `<img src="${e.target.result}" alt="Preview" class="w-full h-full object-cover">`;
                            }
                        };
                        reader.readAsDataURL(file);

                        // mark unsaved
                        window.dispatchEvent(new CustomEvent('unsaved-change'));
                    }
                });

                // Drag & drop support
                const dropZone = document.getElementById('avatar-drop-zone');
                if (dropZone) {
                    ;
                    ['dragenter', 'dragover'].forEach(evt => dropZone.addEventListener(evt, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        dropZone.classList.add('ring-2', 'ring-white/70');
                    }));;
                    ['dragleave', 'drop'].forEach(evt => dropZone.addEventListener(evt, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        dropZone.classList.remove('ring-2', 'ring-white/70');
                    }));
                    dropZone.addEventListener('drop', (e) => {
                        const dt = e.dataTransfer;
                        if (dt && dt.files && dt.files[0]) {
                            avatarInput.files = dt.files;
                            const changeEvent = new Event('change');
                            avatarInput.dispatchEvent(changeEvent);
                        }
                    });
                }
            }

            // Password toggle functionality
            document.querySelectorAll('.toggle-password').forEach(button => {
                button.addEventListener('click', function() {
                    const input = this.parentElement.querySelector('input');
                    const icon = this.querySelector('i');

                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                });
            });

            // Password strength checker
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('password_confirmation');
            const strengthIndicator = document.getElementById('password-strength');
            const strengthBar = document.getElementById('strength-bar');
            const strengthText = document.getElementById('strength-text');
            const passwordMatch = document.getElementById('password-match');

            if (passwordInput) {
                passwordInput.addEventListener('input', function() {
                    const password = this.value;

                    if (password.length > 0) {
                        strengthIndicator.classList.remove('hidden');
                        checkPasswordStrength(password);
                    } else {
                        strengthIndicator.classList.add('hidden');
                    }

                    checkPasswordMatch();
                });
            }

            if (confirmPasswordInput) {
                confirmPasswordInput.addEventListener('input', checkPasswordMatch);
            }

            function checkPasswordStrength(password) {
                let score = 0;
                const requirements = {
                    length: password.length >= 8,
                    uppercase: /[A-Z]/.test(password),
                    lowercase: /[a-z]/.test(password),
                    number: /[0-9]/.test(password)
                };

                // Update requirement indicators
                Object.keys(requirements).forEach(req => {
                    const element = document.getElementById(`${req}-req`);
                    if (element) {
                        if (requirements[req]) {
                            element.classList.add('password-requirement-met');
                            element.querySelector('i').classList.remove('fa-circle');
                            element.querySelector('i').classList.add('fa-check-circle');
                            score++;
                        } else {
                            element.classList.remove('password-requirement-met');
                            element.querySelector('i').classList.remove('fa-check-circle');
                            element.querySelector('i').classList.add('fa-circle');
                        }
                    }
                });

                // Update strength bar and text
                const strengthLevels = [{
                        score: 0,
                        width: '0%',
                        color: 'bg-gray-300',
                        text: 'Sangat Lemah',
                        textColor: 'text-gray-500'
                    },
                    {
                        score: 1,
                        width: '25%',
                        color: 'bg-red-500',
                        text: 'Lemah',
                        textColor: 'text-red-500'
                    },
                    {
                        score: 2,
                        width: '50%',
                        color: 'bg-yellow-500',
                        text: 'Cukup',
                        textColor: 'text-yellow-600'
                    },
                    {
                        score: 3,
                        width: '75%',
                        color: 'bg-blue-500',
                        text: 'Baik',
                        textColor: 'text-blue-600'
                    },
                    {
                        score: 4,
                        width: '100%',
                        color: 'bg-green-500',
                        text: 'Kuat',
                        textColor: 'text-green-600'
                    }
                ];

                const level = strengthLevels[score];
                strengthBar.style.width = level.width;
                strengthBar.className = `h-2 rounded-full transition-all duration-300 ${level.color}`;
                strengthText.textContent = level.text;
                strengthText.className = `text-xs font-medium ${level.textColor}`;
            }

            function checkPasswordMatch() {
                if (confirmPasswordInput.value.length > 0) {
                    passwordMatch.classList.remove('hidden');

                    if (passwordInput.value === confirmPasswordInput.value) {
                        passwordMatch.innerHTML =
                            '<i class="fas fa-check-circle text-green-500 mr-1"></i><span class="text-green-600">Password cocok</span>';
                        confirmPasswordInput.classList.remove('border-red-300', 'ring-red-200');
                        confirmPasswordInput.classList.add('border-green-300', 'ring-green-200');
                    } else {
                        passwordMatch.innerHTML =
                            '<i class="fas fa-times-circle text-red-500 mr-1"></i><span class="text-red-600">Password tidak cocok</span>';
                        confirmPasswordInput.classList.remove('border-green-300', 'ring-green-200');
                        confirmPasswordInput.classList.add('border-red-300', 'ring-red-200');
                    }
                } else {
                    passwordMatch.classList.add('hidden');
                    confirmPasswordInput.classList.remove('border-red-300', 'ring-red-200', 'border-green-300',
                        'ring-green-200');
                }
            }

            // Form submission with loading state
            const form = document.getElementById('profile-form');
            const submitBtn = document.getElementById('submit-btn');
            const submitText = document.getElementById('submit-text');
            const loadingText = document.getElementById('loading-text');

            if (form && submitBtn) {
                form.addEventListener('submit', function() {
                    submitBtn.disabled = true;
                    submitText.classList.add('hidden');
                    loadingText.classList.remove('hidden');
                    submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                });
            }

            // Phone number formatting
            const phoneInput = document.getElementById('phone_number');
            if (phoneInput) {
                phoneInput.addEventListener('input', function() {
                    let value = this.value.replace(/\D/g, '');
                    if (value.startsWith('62')) {
                        value = '+' + value;
                    } else if (value.startsWith('0')) {
                        value = '+62' + value.substring(1);
                    } else if (value.length > 0 && !value.startsWith('62')) {
                        value = '+62' + value;
                    }
                    this.value = value;
                });
            }

            // Auto-save draft functionality (optional)
            let saveTimeout;
            const formInputs = form.querySelectorAll(
                'input:not([type="file"]):not([type="password"]), textarea, select');

            formInputs.forEach(input => {
                input.addEventListener('input', function() {
                    clearTimeout(saveTimeout);
                    saveTimeout = setTimeout(() => {
                        saveDraft();
                    }, 2000); // Save after 2 seconds of inactivity
                    // unsaved marker
                    window.dispatchEvent(new CustomEvent('unsaved-change'));
                });
            });

            function saveDraft() {
                const formData = new FormData(form);
                const draftData = {};

                for (let [key, value] of formData.entries()) {
                    if (!['avatar', 'current_password', 'password', 'password_confirmation', '_token', '_method']
                        .includes(key)) {
                        draftData[key] = value;
                    }
                }

                localStorage.setItem('profile_draft', JSON.stringify(draftData));
                showAlert('Draft tersimpan otomatis', 'success', 2000);
            }

            // Load draft on page load
            function loadDraft() {
                const draftData = localStorage.getItem('profile_draft');
                if (draftData) {
                    try {
                        const data = JSON.parse(draftData);
                        Object.keys(data).forEach(key => {
                            const input = form.querySelector(`[name="${key}"]`);
                            if (input && !input.value) {
                                input.value = data[key];
                            }
                        });
                    } catch (e) {
                        console.error('Error loading draft:', e);
                    }
                }
            }

            // Clear draft on successful submission
            if (document.querySelector('.bg-green-50')) {
                localStorage.removeItem('profile_draft');
            }

            // Utility function to show alerts
            function showAlert(message, type = 'info', duration = 5000) {
                const alertContainer = document.getElementById('alert-container');
                const alertColors = {
                    success: 'bg-green-50 border-green-400 text-green-800',
                    error: 'bg-red-50 border-red-400 text-red-800',
                    info: 'bg-blue-50 border-blue-400 text-blue-800',
                    warning: 'bg-yellow-50 border-yellow-400 text-yellow-800'
                };

                const alertIcons = {
                    success: 'fa-check-circle',
                    error: 'fa-exclamation-circle',
                    info: 'fa-info-circle',
                    warning: 'fa-exclamation-triangle'
                };

                const alert = document.createElement('div');
                alert.className = `mb-6 border-l-4 p-4 rounded-r-lg shadow-sm fade-in ${alertColors[type]}`;
                alert.innerHTML = `
            <div class="flex items-center">
                <i class="fas ${alertIcons[type]} mr-3"></i>
                <span class="font-medium">${message}</span>
                <button class="ml-auto text-xl leading-none" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

                alertContainer.appendChild(alert);

                if (duration > 0) {
                    setTimeout(() => {
                        if (alert.parentNode) {
                            alert.remove();
                        }
                    }, duration);
                }
            }

            // Load draft if available
            loadDraft();

            // Confirm navigation away with unsaved changes
            let hasUnsavedChanges = false;
            formInputs.forEach(input => {
                input.addEventListener('input', () => {
                    hasUnsavedChanges = true;
                });
            });

            form.addEventListener('submit', () => {
                hasUnsavedChanges = false;
                window.dispatchEvent(new CustomEvent('saved-change'));
            });

            window.addEventListener('beforeunload', (e) => {
                if (hasUnsavedChanges) {
                    e.preventDefault();
                    e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                    return e.returnValue;
                }
            });

            // Enhanced form validation
            form.addEventListener('submit', function(e) {
                let isValid = true;
                const requiredFields = form.querySelectorAll('[required]');

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('border-red-300', 'ring-red-200');

                        // Show error message if not already present
                        if (!field.parentElement.querySelector('.error-message')) {
                            const errorMsg = document.createElement('p');
                            errorMsg.className =
                                'mt-2 text-sm text-red-600 flex items-center error-message';
                            errorMsg.innerHTML =
                                '<i class="fas fa-exclamation-circle mr-1"></i>This field is required';
                            field.parentElement.appendChild(errorMsg);
                        }
                    } else {
                        field.classList.remove('border-red-300', 'ring-red-200');
                        const errorMsg = field.parentElement.querySelector('.error-message');
                        if (errorMsg) {
                            errorMsg.remove();
                        }
                    }
                });

                // Email validation
                const emailField = document.getElementById('email');
                if (emailField && emailField.value) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(emailField.value)) {
                        isValid = false;
                        emailField.classList.add('border-red-300', 'ring-red-200');
                        showAlert('Silakan masukkan alamat email yang valid', 'error');
                    }
                }

                // Password confirmation validation
                if (passwordInput.value && confirmPasswordInput.value) {
                    if (passwordInput.value !== confirmPasswordInput.value) {
                        isValid = false;
                        showAlert('Konfirmasi password tidak cocok', 'error');
                    }
                }

                if (!isValid) {
                    e.preventDefault();
                    submitBtn.disabled = false;
                    submitText.classList.remove('hidden');
                    loadingText.classList.add('hidden');
                    submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                }
            });
        });
    </script>
@endpush
