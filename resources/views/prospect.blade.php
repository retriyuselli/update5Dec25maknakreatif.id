<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Prospek</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts - Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            line-height: 1.5;
            background-color: #f8f9fa;
            color: #495057;
        }

        .container {
            background-color: #fff;
            padding: 2.5rem;
            border-radius: 0.75rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            margin-top: 3rem;
            margin-bottom: 4rem;
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.75rem;
            color: #343a40;
        }

        .form-control,
        .form-select {
            border-radius: 0.5rem;
            padding: 0.85rem 1rem;
            border: 1px solid #ced4da;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }

        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
            padding: 0.85rem 1.5rem;
            font-weight: 500;
            border-radius: 0.5rem;
            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.2);
            transition: all 0.3s ease-in-out;
        }

        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(13, 110, 253, 0.3);
        }

        .page-title {
            color: #2c3e50;
            font-weight: 700;
        }

        .form-control::placeholder {
            color: #6c757d;
            opacity: 1;
            font-size: 14px;
        }
    </style>
</head>

<body>
    @if (session('success'))
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100">
            <div id="successToast" class="toast align-items-center text-bg-success border-0" role="alert"
                aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h1 class="page-title display-6 mt-3 mb-0">Formulir Data Konsumen</h1>
        </div>

        <form action="{{ route('prospect.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name_event" class="form-label">Nama Event <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name_event') is-invalid @enderror" id="name_event"
                        name="name_event" value="{{ old('name_event') }}" required>
                    @error('name_event')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="venue" class="form-label">Lokasi Venue <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('venue') is-invalid @enderror" id="venue"
                        name="venue" value="{{ old('venue') }}" required>
                    @error('venue')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name_cpp" class="form-label">Nama Mempelai Pria <span
                            class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name_cpp') is-invalid @enderror" id="name_cpp"
                        name="name_cpp" value="{{ old('name_cpp') }}" required>
                    @error('name_cpp')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="name_cpw" class="form-label">Nama Mempelai Wanita <span
                            class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name_cpw') is-invalid @enderror" id="name_cpw"
                        name="name_cpw" value="{{ old('name_cpw') }}" required>
                    @error('name_cpw')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="date_lamaran" class="form-label">Tanggal Lamaran</label>
                    <input type="date" class="form-control @error('date_lamaran') is-invalid @enderror"
                        id="date_lamaran" name="date_lamaran" value="{{ old('date_lamaran') }}">
                    @error('date_lamaran')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="date_akad" class="form-label">Tanggal Akad Nikah</label>
                    <input type="date" class="form-control @error('date_akad') is-invalid @enderror" id="date_akad"
                        name="date_akad" value="{{ old('date_akad') }}">
                    @error('date_akad')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="date_resepsi" class="form-label">Tanggal Resepsi</label>
                    <input type="date" class="form-control @error('date_resepsi') is-invalid @enderror"
                        id="date_resepsi" name="date_resepsi" value="{{ old('date_resepsi') }}">
                    @error('date_resepsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">+62</span>
                        <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                            id="phone" name="phone" value="{{ old('phone') }}"
                            placeholder="8123456789 (tanpa 0 di depan)" required>
                    </div>
                    @error('phone')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="address" class="form-label">Alamat <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('address') is-invalid @enderror" id="address"
                        name="address" value="{{ old('address') }}" required>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="notes" class="form-label">Catatan Tambahan</label>
                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="4"
                    placeholder="Informasi tambahan atau permintaan khusus">{{ old('notes') }}</textarea>
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary">Kirim</button>
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
                }
            });
        @endif
    </script>

    <!-- Footer -->
    @include('components.footer-simple')
</body>

</html>
