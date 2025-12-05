<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Berhasil Dikirim - {{ config('app.name') }}</title>

    <!-- Poppins Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'poppins': ['Poppins', 'system-ui', 'sans-serif'],
                        'sans': ['Poppins', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    },
                    animation: {
                        'bounce-slow': 'bounce 2s infinite',
                        'pulse-slow': 'pulse 3s infinite',
                    }
                }
            }
        }
    </script>
</head>

<body class="font-poppins bg-gradient-to-br from-green-50 via-blue-50 to-primary-50 min-h-screen">
    <!-- Header -->
    @include('front.header')
    
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8 pt-24 pb-16">
        <div class="max-w-2xl w-full" x-data="{ show: false }" x-init="setTimeout(() => show = true, 300)">
            <div class="bg-white rounded-3xl shadow-2xl p-8 lg:p-12 text-center" x-show="show"
                x-transition.duration.700ms>

                <!-- Success Animation -->
                <div class="mb-8">
                    <div class="relative inline-block">
                        <!-- Success Icon -->
                        <div
                            class="w-24 h-24 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-2xl animate-bounce-slow">
                            <i class="fas fa-check text-white text-4xl"></i>
                        </div>

                        <!-- Decorative Ring -->
                        <div class="absolute -inset-4 border-4 border-green-200 rounded-full animate-pulse-slow"></div>
                    </div>

                    <!-- Success Bar -->
                    <div class="w-32 h-1 bg-gradient-to-r from-green-400 to-emerald-500 rounded-full mx-auto"></div>
                </div>

                <!-- Main Content -->
                <div class="mb-10">
                    <h1 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                        Aplikasi Berhasil Dikirim!
                    </h1>

                    <div class="space-y-4 text-sm text-gray-600 mb-8">
                        <p class="flex items-center justify-center">
                            <i class="fas fa-paper-plane text-primary-500 mr-3"></i>
                            Terima kasih telah mengirimkan aplikasi prospek Anda.
                        </p>
                        <p class="flex items-center justify-center">
                            <i class="fas fa-clock text-amber-500 mr-3"></i>
                            Tim kami akan menghubungi Anda dalam <span class="font-semibold text-gray-900 mx-1">24
                                jam</span> ke depan.
                        </p>
                        <p class="flex items-center justify-center">
                            <i class="fas fa-envelope text-green-500 mr-3"></i>
                            Email konfirmasi telah dikirim ke alamat email Anda.
                        </p>
                    </div>
                </div>

                <!-- Feature Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-2xl border border-blue-200">
                        <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-headset text-white text-xl"></i>
                        </div>
                        <h3 class="font-semibold text-blue-900 mb-2">Tim Support</h3>
                        <p class="text-sm text-blue-700">Siap membantu Anda 24/7</p>
                    </div>

                    <div
                        class="bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-2xl border border-purple-200">
                        <div class="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-rocket text-white text-xl"></i>
                        </div>
                        <h3 class="font-semibold text-purple-900 mb-2">Proses Cepat</h3>
                        <p class="text-sm text-purple-700">Respons maksimal 24 jam</p>
                    </div>

                    <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-2xl border border-green-200">
                        <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-shield-alt text-white text-xl"></i>
                        </div>
                        <h3 class="font-semibold text-green-900 mb-2">Data Aman</h3>
                        <p class="text-sm text-green-700">Privasi terjamin 100%</p>
                    </div>
                </div>

                <!-- Next Steps -->
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-2xl p-8 mb-8 text-left">
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-primary-600 rounded-xl mb-4">
                            <i class="fas fa-list-check text-white text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">Langkah Selanjutnya</h3>
                    </div>

                    <div class="max-w-lg mx-auto space-y-6">
                        <div class="flex items-start space-x-4">
                            <div
                                class="flex items-center justify-center w-10 h-10 bg-primary-600 text-white rounded-full text-sm font-bold flex-shrink-0">
                                1
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Verifikasi Data</h4>
                                <p class="text-sm text-gray-600">Tim kami akan memverifikasi informasi yang Anda berikan
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div
                                class="flex items-center justify-center w-10 h-10 bg-primary-600 text-white rounded-full text-sm font-bold flex-shrink-0">
                                2
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Konsultasi Awal</h4>
                                <p class="text-sm text-gray-600">Account Manager akan menghubungi untuk konsultasi
                                    gratis</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div
                                class="flex items-center justify-center w-10 h-10 bg-primary-600 text-white rounded-full text-sm font-bold flex-shrink-0">
                                3
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Proposal & Penawaran</h4>
                                <p class="text-sm text-gray-600">Kami akan menyusun proposal sesuai kebutuhan Anda</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div
                                class="flex items-center justify-center w-10 h-10 bg-green-600 text-white rounded-full text-sm font-bold flex-shrink-0">
                                4
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Mulai Kerjasama</h4>
                                <p class="text-sm text-gray-600">Setelah sepakat, kami akan mulai mengerjakan proyek
                                    Anda</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="bg-primary-50 border border-primary-200 rounded-xl p-6 mb-8">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-info-circle text-white"></i>
                            </div>
                        </div>
                        <div class="text-left">
                            <h3 class="font-semibold text-primary-900 mb-2">Butuh bantuan sekarang?</h3>
                            <p class="text-primary-800 text-sm">
                                Hubungi kami di <span class="font-semibold">WhatsApp: +62 813-7318-3794</span> atau
                                <span class="font-semibold">Email: info@maknaspace.com</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('home') }}"
                        class="inline-flex items-center justify-center px-6 py-3 bg-white text-gray-700 font-semibold rounded-xl border-2 border-gray-300 hover:border-primary-500 hover:text-primary-600 transition-all duration-200">
                        <i class="fas fa-home mr-2"></i>
                        Kembali ke Home
                    </a>

                    <a href="https://wa.me/6281373183794" target="_blank"
                        class="inline-flex items-center justify-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200">
                        <i class="fab fa-whatsapp mr-2"></i>
                        Hubungi WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    @include('front.footer')

    <script>
        // Enhanced confetti animation
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                createConfettiShower();
            }, 1200);

            function createConfettiShower() {
                const colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'];

                for (let i = 0; i < 60; i++) {
                    setTimeout(() => {
                        createConfettiPiece(colors);
                    }, i * 50);
                }
            }

            function createConfettiPiece(colors) {
                const confetti = document.createElement('div');
                const color = colors[Math.floor(Math.random() * colors.length)];

                confetti.style.cssText = `
                    position: fixed;
                    width: ${Math.random() * 10 + 5}px;
                    height: ${Math.random() * 10 + 5}px;
                    background: ${color};
                    left: ${Math.random() * 100}vw;
                    top: -20px;
                    border-radius: ${Math.random() > 0.5 ? '50%' : '0'};
                    pointer-events: none;
                    z-index: 9999;
                    opacity: 0.8;
                `;

                document.body.appendChild(confetti);

                const animation = confetti.animate([{
                        transform: 'translateY(-20px) rotate(0deg)',
                        opacity: 0.8
                    },
                    {
                        transform: `translateY(100vh) rotate(${Math.random() * 720}deg)`,
                        opacity: 0
                    }
                ], {
                    duration: Math.random() * 3000 + 2000,
                    easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)'
                });

                animation.addEventListener('finish', () => {
                    confetti.remove();
                });
            }
        });
    </script>
</body>

</html>
