<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Aplikasi Prospek Bisnis - {{ config('app.name') }}</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom Prospect CSS -->
    <link rel="stylesheet" href="{{ asset('assets/prospect/app.css') }}">

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body>
    <div class="prospect-container">
        <div class="prospect-max-width">
            <!-- Header -->
            <div class="prospect-header">
                <div class="prospect-header-icon">
                    <i class="fas fa-handshake"></i>
                </div>
                <h1 class="prospect-header-title">Aplikasi Financial System</h1>
                <p class="prospect-header-subtitle">
                    Bergabunglah dengan kami untuk mengembangkan bisnis Anda.<br>
                    Isi formulir dibawah ini untuk memulai konsultasi. </p>
            </div>

            <!-- Main Form Card -->
            <div class="prospect-form-card" x-data="prospectForm()">
                <div class="prospect-form-content">
                    @if (session('success'))
                        <div class="prospect-alert prospect-alert-success">
                            <div class="prospect-alert-content">
                                <div class="prospect-alert-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div>
                                    <h3 class="prospect-alert-title">Berhasil!</h3>
                                    <p class="prospect-alert-message">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="prospect-alert prospect-alert-error">
                            <div class="prospect-alert-content">
                                <div class="prospect-alert-icon">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <div>
                                    <h3 class="prospect-alert-title">Terdapat kesalahan:</h3>
                                    <ul class="prospect-alert-list">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('prospect-app.store') }}" method="POST" class="prospect-form">
                        @csrf

                        <!-- Contact Information Section -->
                        <div class="prospect-section">
                            <div class="prospect-section-header">
                                <div class="prospect-section-title-wrapper">
                                    <div class="prospect-section-icon prospect-section-icon--primary">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <h2 class="prospect-section-title">Informasi Kontak</h2>
                                </div>
                            </div>

                            <div class="prospect-grid prospect-grid--lg-2">
                                <div class="prospect-field">
                                    <label for="full_name" class="prospect-label">
                                        <i class="fas fa-user"></i>
                                        Nama Lengkap <span class="prospect-required">*</span>
                                    </label>
                                    <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}"
                                        required class="prospect-input" placeholder="Masukkan nama lengkap Anda">
                                </div>

                                <div class="prospect-field">
                                    <label for="email" class="prospect-label">
                                        <i class="fas fa-envelope"></i>
                                        Email <span class="prospect-required">*</span>
                                    </label>
                                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                                        required class="prospect-input" placeholder="nama@email.com">
                                </div>

                                <div class="prospect-field">
                                    <label for="phone" class="prospect-label">
                                        <i class="fas fa-phone"></i>
                                        Nomor Telepon <span class="prospect-required">*</span>
                                    </label>
                                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                                        required class="prospect-input" placeholder="08xx-xxxx-xxxx">
                                </div>

                                <div class="prospect-field">
                                    <label for="position" class="prospect-label">
                                        <i class="fas fa-user-tie"></i>
                                        Posisi/Jabatan
                                    </label>
                                    <input type="text" id="position" name="position" value="{{ old('position') }}"
                                        class="prospect-input" placeholder="CEO, Manager, dll">
                                </div>
                            </div>
                        </div>

                        <!-- Company Information Section -->
                        <div class="prospect-section">
                            <div class="prospect-section-header">
                                <div class="prospect-section-title-wrapper">
                                    <div class="prospect-section-icon prospect-section-icon--blue">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <h2 class="prospect-section-title">Informasi Perusahaan</h2>
                                </div>
                            </div>

                            <div class="prospect-grid prospect-grid--lg-2">
                                <div class="prospect-field">
                                    <label for="company_name" class="prospect-label">
                                        <i class="fas fa-building"></i>
                                        Nama Perusahaan <span class="prospect-required">*</span>
                                    </label>
                                    <input type="text" id="company_name" name="company_name"
                                        value="{{ old('company_name') }}" required class="prospect-input"
                                        placeholder="PT. Nama Perusahaan">
                                </div>

                                <div class="prospect-field">
                                    <label for="industry_id" class="prospect-label">
                                        <i class="fas fa-industry"></i>
                                        Industri <span class="prospect-required">*</span>
                                    </label>
                                    <select id="industry_id" name="industry_id" required class="prospect-select">
                                        <option value="">Pilih Industri</option>
                                        @foreach ($industries as $industry)
                                            <option value="{{ $industry->id }}"
                                                {{ old('industry_id') == $industry->id ? 'selected' : '' }}>
                                                {{ $industry->industry_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="prospect-field">
                                    <label for="name_of_website" class="prospect-label">
                                        <i class="fas fa-globe"></i>
                                        Website/Domain
                                    </label>
                                    <input type="text" id="name_of_website" name="name_of_website"
                                        value="{{ old('name_of_website') }}" class="prospect-input"
                                        placeholder="namawebsite.com atau www.namawebsite.com">
                                </div>

                                <div class="prospect-field">
                                    <label for="user_size" class="prospect-label">
                                        <i class="fas fa-users"></i>
                                        Ukuran Perusahaan
                                    </label>
                                    <select id="user_size" name="user_size" class="prospect-select">
                                        <option value="">Pilih Ukuran Perusahaan</option>
                                        <option value="1-10" {{ old('user_size') == '1-10' ? 'selected' : '' }}>1-10
                                            karyawan</option>
                                        <option value="11-50" {{ old('user_size') == '11-50' ? 'selected' : '' }}>
                                            11-50 karyawan</option>
                                        <option value="51-200" {{ old('user_size') == '51-200' ? 'selected' : '' }}>
                                            51-200 karyawan</option>
                                        <option value="201-500" {{ old('user_size') == '201-500' ? 'selected' : '' }}>
                                            201-500 karyawan</option>
                                        <option value="501-1000"
                                            {{ old('user_size') == '501-1000' ? 'selected' : '' }}>501-1000 karyawan
                                        </option>
                                        <option value="1000+" {{ old('user_size') == '1000+' ? 'selected' : '' }}>
                                            1000+ karyawan</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Application Details Section -->
                        <div class="prospect-section">
                            <div class="prospect-section-header">
                                <div class="prospect-section-title-wrapper">
                                    <div class="prospect-section-icon prospect-section-icon--green">
                                        <i class="fas fa-clipboard-list"></i>
                                    </div>
                                    <h2 class="prospect-section-title">Detail Aplikasi</h2>
                                </div>
                            </div>

                            <div class="prospect-field">
                                <label for="reason_for_interest" class="prospect-label">
                                    <i class="fas fa-lightbulb"></i>
                                    Alasan Minat & Kebutuhan Anda
                                </label>
                                <textarea id="reason_for_interest" name="reason_for_interest" class="prospect-textarea"
                                    placeholder="Jelaskan mengapa Anda tertarik dengan layanan kami dan kebutuhan spesifik bisnis Anda...">{{ old('reason_for_interest') }}</textarea>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="prospect-submit-wrapper">
                            <button type="submit" class="prospect-submit-btn" x-bind:disabled="submitting"
                                x-bind:class="{ 'prospect-submit-btn--disabled': submitting }">
                                <span x-show="!submitting">
                                    <i class="fas fa-paper-plane"></i>
                                    Kirim Aplikasi
                                </span>
                                <span x-show="submitting">
                                    <div class="prospect-spinner"></div>
                                    Mengirim...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Information Card -->
            <div class="prospect-info-card">
                <div class="prospect-info-header">
                    <div class="prospect-info-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <h3 class="prospect-info-title">Mengapa Memilih Kami?</h3>
                </div>

                <div class="prospect-info-grid">
                    <div class="prospect-info-item">
                        <div class="prospect-info-item-icon prospect-info-item-icon--green">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h4 class="prospect-info-item-title">Respons Cepat</h4>
                        <p class="prospect-info-item-description">Tim akan menghubungi dalam 24 jam</p>
                    </div>

                    <div class="prospect-info-item">
                        <div class="prospect-info-item-icon prospect-info-item-icon--blue">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <h4 class="prospect-info-item-title">Konsultasi Gratis</h4>
                        <p class="prospect-info-item-description">Konsultasi awal tanpa biaya</p>
                    </div>

                    <div class="prospect-info-item">
                        <div class="prospect-info-item-icon prospect-info-item-icon--purple">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <h4 class="prospect-info-item-title">Solusi Custom</h4>
                        <p class="prospect-info-item-description">Disesuaikan dengan kebutuhan</p>
                    </div>

                    <div class="prospect-info-item">
                        <div class="prospect-info-item-icon prospect-info-item-icon--orange">
                            <i class="fas fa-support"></i>
                        </div>
                        <h4 class="prospect-info-item-title">Support 24/7</h4>
                        <p class="prospect-info-item-description">Dukungan berkelanjutan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function prospectForm() {
            return {
                submitting: false,

                init() {
                    this.setupFormValidation();
                },

                setupFormValidation() {
                    const form = this.$el.querySelector('form');

                    form.addEventListener('submit', (e) => {
                        e.preventDefault();
                        this.handleSubmit(form);
                    });
                },

                async handleSubmit(form) {
                    if (this.submitting) return;

                    this.submitting = true;

                    // Simulate loading time
                    await new Promise(resolve => setTimeout(resolve, 1000));

                    // Submit form normally
                    form.submit();
                }
            }
        }
    </script>

    <!-- Footer -->
    @include('components.footer-simple')
</body>

</html>
