<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Pribadi</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts - Noto Sans (Filament Font) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Datacrew Custom CSS -->
    <link href="{{ asset('assets/datacrew/datacrew.css') }}" rel="stylesheet">
</head>

@php
    $companyName = 'Makna Wedding & Event Planner';
    if (\Illuminate\Support\Facades\Schema::hasTable('companies')) {
        $val = \App\Models\Company::value('company_name');
        if ($val) {
            $companyName = $val;
        }
    }
@endphp

<body>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100">
            <div id="successToast" class="toast align-items-center text-bg-success border-0" role="alert"
                aria-live="assertive" aria-atomic="true" data-bs-delay="8000">
                <div class="d-flex">
                    <div class="toast-body">
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    fill="currentColor" class="bi bi-heart-fill text-white" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314" />
                                </svg>
                            </div>
                            <div>
                                <strong>Terima kasih sudah menjadi bagian dari {{ $companyName }}!</strong>
                                <br>
                                <small class="text-light opacity-75">Data Anda telah berhasil disimpan dengan
                                    baik.</small>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h1 class="page-title display-6 mt-3 mb-0">Formulir Data Pribadi</h1>
            <a href="{{ route('data-pribadi.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <form action="{{ route('data-pribadi.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror"
                        id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required
                        placeholder="Masukkan nama lengkap Anda" @error('nama_lengkap') aria-invalid="true" @enderror>
                    @error('nama_lengkap')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                        name="email" value="{{ old('email') }}" required placeholder="contoh@email.com"
                        @error('email') aria-invalid="true" @enderror>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nomor_telepon" class="form-label">Nomor Telepon</label>
                    <div class="input-group">
                        <span class="input-group-text">+62</span>
                        <input type="tel" class="form-control @error('nomor_telepon') is-invalid @enderror"
                            id="nomor_telepon" name="nomor_telepon" value="{{ old('nomor_telepon') }}"
                            placeholder="8123456789 (tanpa 0 di depan)"
                            @error('nomor_telepon') aria-invalid="true" @enderror>
                    </div>
                    @error('nomor_telepon')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                    <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror"
                        id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                        max="{{ date('Y-m-d') }}" @error('tanggal_lahir') aria-invalid="true" @enderror>
                    @error('tanggal_lahir')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                    <select class="form-select @error('jenis_kelamin') is-invalid @enderror" id="jenis_kelamin"
                        name="jenis_kelamin" @error('jenis_kelamin') aria-invalid="true" @enderror>
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>
                            Laki-laki
                        </option>
                        <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>
                            Perempuan
                        </option>
                    </select>
                    @error('jenis_kelamin')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="pekerjaan" class="form-label">Pekerjaan</label>
                    <input type="text" class="form-control @error('pekerjaan') is-invalid @enderror"
                        id="pekerjaan" name="pekerjaan" value="{{ old('pekerjaan') }}"
                        placeholder="Contoh: Web Developer" @error('pekerjaan') aria-invalid="true" @enderror>
                    @error('pekerjaan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3"
                    placeholder="Masukkan alamat lengkap Anda" @error('alamat') aria-invalid="true" @enderror>{{ old('alamat') }}</textarea>
                @error('alamat')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="gaji" class="form-label">Fee {{ $companyName }} (Rp)</label>
                    {{-- Kembalikan type ke number --}}
                    <input type="number" class="form-control @error('gaji') is-invalid @enderror" id="gaji"
                        name="gaji" value="{{ old('gaji') }}" placeholder="Contoh: 5000000" min="0"
                        @error('gaji') aria-invalid="true" @enderror>
                    @error('gaji')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="foto" class="form-label">Foto Profil <span class="text-danger">*</span></label>
                    <input class="form-control @error('foto') is-invalid @enderror" type="file" id="foto"
                        name="foto" accept="image/*" required @error('foto') aria-invalid="true" @enderror>
                    <div class="form-text mt-1">Unggah foto profil (maks. 1MB, format: jpg, png, gif).</div>
                    <img id="foto-preview" src="#" alt="Pratinjau Foto"
                        style="max-width: 200px; max-height: 200px; margin-top: 10px; display: none;" />
                    @error('foto')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="motivasi_kerja" class="form-label">Motivasi Kerja</label>
                    <textarea class="form-control @error('motivasi_kerja') is-invalid @enderror" id="motivasi_kerja"
                        name="motivasi_kerja" rows="3" placeholder="Jelaskan motivasi kerja Anda"
                        @error('motivasi_kerja') aria-invalid="true" @enderror>{{ old('motivasi_kerja') }}</textarea>
                    @error('motivasi_kerja')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="pelatihan" class="form-label">Pelatihan {{ $companyName }}</label>
                    <textarea class="form-control @error('pelatihan') is-invalid @enderror" id="pelatihan" name="pelatihan"
                        rows="3" placeholder="Jelaskan pelatihan yang pernah diikuti"
                        @error('pelatihan') aria-invalid="true" @enderror>{{ old('pelatihan') }}</textarea>
                    @error('pelatihan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>



            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary">Simpan Data</button>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script untuk menampilkan toast sukses
        @if (session('success'))
            document.addEventListener('DOMContentLoaded', function() {
                var toastEl = document.getElementById('successToast');
                if (toastEl) {
                    var successToast = new bootstrap.Toast(toastEl);
                    successToast.show();

                    // Reset form setelah success
                    document.querySelector('form').reset();

                    // Reset preview foto
                    const fotoPreview = document.getElementById('foto-preview');
                    if (fotoPreview) {
                        fotoPreview.style.display = 'none';
                        fotoPreview.src = '#';
                    }

                    // Scroll ke atas halaman untuk memastikan toast terlihat
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }
            });
        @endif

        // Script untuk pratinjau gambar
        const fotoInput = document.getElementById('foto');
        const fotoPreview = document.getElementById('foto-preview');

        fotoInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                // Validasi ukuran file (1MB = 1024 * 1024 bytes)
                if (file.size > 1024 * 1024) {
                    alert('Ukuran file terlalu besar! Maksimal 1MB.');
                    // Reset input file
                    fotoInput.value = '';
                    // Sembunyikan pratinjau jika ada
                    fotoPreview.style.display = 'none';
                    fotoPreview.src = '#';
                    return; // Hentikan proses lebih lanjut
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    fotoPreview.src = e.target.result;
                    fotoPreview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                // Jika tidak ada file yang dipilih (misalnya, pengguna membatalkan)
                fotoPreview.style.display = 'none';
                fotoPreview.src = '#';
            }
        });

        // Hapus script untuk format input gaji
    </script>

    <!-- Footer -->
    @include('components.footer-simple')
</body>

</html>
