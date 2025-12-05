<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Berhasil Dikirim - {{ config('app.name', 'Makna Online') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        'fade-in': 'fadeIn 0.8s ease-in-out',
                        'slide-up': 'slideUp 0.6s ease-out',
                        'bounce-slow': 'bounce 2s infinite',
                        'pulse-slow': 'pulse 3s infinite'
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': {
                                opacity: '0'
                            },
                            '100%': {
                                opacity: '1'
                            }
                        },
                        slideUp: {
                            '0%': {
                                transform: 'translateY(20px)',
                                opacity: '0'
                            },
                            '100%': {
                                transform: 'translateY(0)',
                                opacity: '1'
                            }
                        }
                    }
                }
            }
        }
    </script>
</head>

<body
    class="bg-gradient-to-br from-purple-50 via-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full animate-fade-in" x-data="{ show: false }" x-init="setTimeout(() => show = true, 200)">
        <div class="bg-white rounded-3xl shadow-2xl p-8 md:p-12 text-center" x-show="show" x-transition.duration.600ms>
            <!-- Success Icon -->
            <div class="mb-8">
                <div
                    class="w-24 h-24 bg-gradient-to-r from-green-400 to-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6 animate-bounce-slow">
                    <i class="fas fa-check text-white text-4xl"></i>
                </div>
                <div
                    class="w-32 h-1 bg-gradient-to-r from-green-400 to-emerald-500 rounded-full mx-auto animate-pulse-slow">
                </div>
            </div>

            <!-- Main Content -->
            <div class="mb-8 animate-slide-up">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                    Aplikasi Berhasil Dikirim!
                </h1>

                <div class="text-lg text-gray-600 mb-6 leading-relaxed">
                    <p class="mb-3">
                        <i class="fas fa-paper-plane text-blue-500 mr-2"></i>
                        Terima kasih telah mengirimkan aplikasi prospek Anda.
                    </p>
                    <p class="mb-3">
                        <i class="fas fa-clock text-purple-500 mr-2"></i>
                        Tim kami akan menghubungi Anda dalam <strong>24 jam</strong> ke depan.
                    </p>
                    <p>
                        <i class="fas fa-envelope text-green-500 mr-2"></i>
                        Email konfirmasi telah dikirim ke alamat email Anda.
                    </p>
                </div>
            </div>

            <!-- Information Cards -->
            <div class="grid md:grid-cols-3 gap-4 mb-8">
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-4 rounded-xl border border-blue-200">
                    <i class="fas fa-headset text-blue-600 text-2xl mb-2"></i>
                    <h3 class="font-semibold text-blue-800">Tim Support</h3>
                    <p class="text-sm text-blue-600">Siap membantu Anda</p>
                </div>

                <div class="bg-gradient-to-r from-purple-50 to-purple-100 p-4 rounded-xl border border-purple-200">
                    <i class="fas fa-rocket text-purple-600 text-2xl mb-2"></i>
                    <h3 class="font-semibold text-purple-800">Proses Cepat</h3>
                    <p class="text-sm text-purple-600">Respons dalam 24 jam</p>
                </div>

                <div class="bg-gradient-to-r from-green-50 to-green-100 p-4 rounded-xl border border-green-200">
                    <i class="fas fa-shield-alt text-green-600 text-2xl mb-2"></i>
                    <h3 class="font-semibold text-green-800">Data Aman</h3>
                    <p class="text-sm text-green-600">Privasi terjamin</p>
                </div>
            </div>

            <!-- What's Next Section -->
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-2xl p-6 mb-8 text-left">
                <h3 class="text-xl font-bold text-gray-800 mb-4 text-center">
                    <i class="fas fa-list-check text-indigo-600 mr-2"></i>
                    Langkah Selanjutnya
                </h3>

                <div class="space-y-3">
                    <div class="flex items-start">
                        <div
                            class="w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3 mt-0.5">
                            1</div>
                        <div>
                            <p class="font-semibold text-gray-800">Verifikasi Data</p>
                            <p class="text-sm text-gray-600">Tim kami akan memverifikasi informasi yang Anda berikan</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div
                            class="w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3 mt-0.5">
                            2</div>
                        <div>
                            <p class="font-semibold text-gray-800">Konsultasi Awal</p>
                            <p class="text-sm text-gray-600">Sesi konsultasi gratis untuk memahami kebutuhan Anda</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div
                            class="w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3 mt-0.5">
                            3</div>
                        <div>
                            <p class="font-semibold text-gray-800">Proposal Solusi</p>
                            <p class="text-sm text-gray-600">Kami akan menyiapkan proposal yang sesuai dengan kebutuhan
                                Anda</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('prospect.form') }}"
                    class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-8 py-4 rounded-xl font-semibold hover:from-indigo-700 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 flex items-center justify-center">
                    <i class="fas fa-plus mr-2"></i>
                    Kirim Aplikasi Lain
                </a>

                <a href="{{ route('home') }}"
                    class="bg-white text-gray-700 px-8 py-4 rounded-xl font-semibold border-2 border-gray-200 hover:border-gray-300 hover:bg-gray-50 transition-all duration-300 flex items-center justify-center">
                    <i class="fas fa-home mr-2"></i>
                    Kembali ke Beranda
                </a>
            </div>

            <!-- Contact Information -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <p class="text-sm text-gray-500 mb-2">
                    Butuh bantuan segera? Hubungi kami:
                </p>

                <div class="flex flex-wrap justify-center gap-6 text-sm">
                    <a href="mailto:support@maknaonline.com"
                        class="text-blue-600 hover:text-blue-800 transition-colors">
                        <i class="fas fa-envelope mr-1"></i>
                        support@maknaonline.com
                    </a>

                    <a href="tel:+621234567890" class="text-green-600 hover:text-green-800 transition-colors">
                        <i class="fas fa-phone mr-1"></i>
                        +62 123 456 7890
                    </a>

                    <a href="https://wa.me/621234567890"
                        class="text-emerald-600 hover:text-emerald-800 transition-colors">
                        <i class="fab fa-whatsapp mr-1"></i>
                        WhatsApp
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        @include('components.footer-simple')
    </div>

    <!-- Auto redirect script (optional) -->
    <script>
        // Optional: Auto redirect to home after 30 seconds
        // setTimeout(() => {
        //     window.location.href = "{{ route('home') }}";
        // }, 30000);
    </script>
</body>

</html>
