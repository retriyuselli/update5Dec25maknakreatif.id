<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak Perusahaan - Makna Wedding Organizer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .text-gradient {
            background: linear-gradient(135deg, #3b82f6, #fbbf24);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .contact-form {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.05) 0%, rgba(251, 191, 36, 0.05) 100%);
        }
    </style>
</head>

<body class="bg-white text-gray-800">
    <!-- Header Navigation -->
    @include('front.header')

    <!-- Hero Section -->
    <section class="gradient-bg text-white py-20">
        <div class="container mx-auto px-6">
            <div class="text-center max-w-4xl mx-auto">
                <h1 class="text-5xl md:text-6xl font-bold mb-6">
                    <span class="text-gradient">Hubungi</span> Kami
                </h1>
                <p class="text-xl md:text-2xl text-gray-300 mb-8">
                    Tim profesional kami siap membantu kebutuhan internal perusahaan Anda
                </p>
                <div class="flex flex-wrap justify-center gap-4 text-sm">
                    <div class="bg-blue-600/20 backdrop-blur-sm px-4 py-2 rounded-full border border-blue-400/30">
                        <i class="fas fa-clock mr-2"></i>24/7 Support
                    </div>
                    <div class="bg-yellow-600/20 backdrop-blur-sm px-4 py-2 rounded-full border border-yellow-400/30">
                        <i class="fas fa-headset mr-2"></i>Tim Responsif
                    </div>
                    <div class="bg-blue-600/20 backdrop-blur-sm px-4 py-2 rounded-full border border-blue-400/30">
                        <i class="fas fa-shield-alt mr-2"></i>Keamanan Terjamin
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Contact Stats -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div
                        class="bg-blue-600 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-phone text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Telepon</h3>
                    <p class="text-gray-600">+62 21 1234 5678</p>
                    <p class="text-gray-600">+62 812 3456 7890</p>
                </div>
                <div class="text-center">
                    <div
                        class="bg-yellow-500 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-envelope text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Email</h3>
                    <p class="text-gray-600">info@maknakreatif.com</p>
                    <p class="text-gray-600">admin@maknawedding.com</p>
                </div>
                <div class="text-center">
                    <div
                        class="bg-blue-600 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-map-marker-alt text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Alamat</h3>
                    <p class="text-gray-600">Jl. Sudirman No. 123</p>
                    <p class="text-gray-600">Jakarta Pusat, 10220</p>
                </div>
                <div class="text-center">
                    <div
                        class="bg-yellow-500 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Jam Kerja</h3>
                    <p class="text-gray-600">Senin - Jumat: 08:00 - 17:00</p>
                    <p class="text-gray-600">Sabtu: 08:00 - 14:00</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form & Information -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Contact Form -->
                <div class="contact-form p-8 rounded-2xl">
                    <h2 class="text-3xl font-bold text-gray-800 mb-6">Kirim Pesan Internal</h2>
                    <p class="text-gray-600 mb-8">Gunakan form ini untuk komunikasi internal antar departemen atau tim
                    </p>

                    <form class="space-y-6" x-data="contactForm()">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap *</label>
                                <input type="text" x-model="form.name"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Masukkan nama lengkap">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Departemen *</label>
                                <select x-model="form.department"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Pilih Departemen</option>
                                    <option value="management">Management</option>
                                    <option value="wedding_planning">Wedding Planning</option>
                                    <option value="event_coordination">Event Coordination</option>
                                    <option value="marketing">Marketing</option>
                                    <option value="finance">Finance</option>
                                    <option value="hr">Human Resources</option>
                                    <option value="it">IT Support</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
                                <input type="email" x-model="form.email"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="nama@maknakreatif.com">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor Telepon</label>
                                <input type="tel" x-model="form.phone"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="+62 812 3456 7890">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Kategori Pesan *</label>
                            <select x-model="form.category"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Pilih Kategori</option>
                                <option value="project_inquiry">Pertanyaan Proyek</option>
                                <option value="technical_support">Dukungan Teknis</option>
                                <option value="hr_matter">Masalah HR</option>
                                <option value="finance_inquiry">Pertanyaan Keuangan</option>
                                <option value="general_inquiry">Pertanyaan Umum</option>
                                <option value="urgent_matter">Masalah Mendesak</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Prioritas</label>
                            <div class="flex space-x-4">
                                <label class="flex items-center">
                                    <input type="radio" x-model="form.priority" value="low"
                                        class="mr-2 text-blue-600">
                                    <span class="text-green-600">Rendah</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" x-model="form.priority" value="medium"
                                        class="mr-2 text-blue-600">
                                    <span class="text-yellow-600">Sedang</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" x-model="form.priority" value="high"
                                        class="mr-2 text-blue-600">
                                    <span class="text-red-600">Tinggi</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Pesan *</label>
                            <textarea x-model="form.message" rows="6"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Tuliskan pesan atau pertanyaan Anda di sini..."></textarea>
                        </div>

                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" x-model="form.urgent" class="mr-3 text-blue-600">
                                <span class="text-sm text-gray-700">Tandai sebagai pesan mendesak</span>
                            </label>
                        </div>

                        <button type="submit" @click.prevent="submitForm()"
                            class="w-full bg-blue-600 text-white py-4 px-6 rounded-lg hover:bg-blue-700 transition-colors font-semibold text-lg">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Kirim Pesan
                        </button>
                    </form>
                </div>

                <!-- Company Information -->
                <div class="space-y-8">
                    <!-- Company Profile -->
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-8 rounded-2xl">
                        <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-building text-blue-600 mr-3"></i>
                            Profil Perusahaan
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <h4 class="font-semibold text-gray-800 mb-2">PT. Makna Kreatif Indonesia</h4>
                                <p class="text-gray-600">Perusahaan yang bergerak di bidang wedding organizer dan event
                                    planner dengan pengalaman lebih dari 10 tahun dalam industri ini.</p>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800 mb-2">Visi</h4>
                                <p class="text-gray-600">Menjadi wedding organizer terdepan yang menciptakan momen tak
                                    terlupakan dengan pelayanan berkualitas tinggi.</p>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800 mb-2">Misi</h4>
                                <ul class="text-gray-600 space-y-1">
                                    <li>• Memberikan pelayanan wedding organizer terbaik</li>
                                    <li>• Menciptakan tim profesional yang berdedikasi</li>
                                    <li>• Mengutamakan kepuasan klien dalam setiap proyek</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Department Contacts -->
                    <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-8 rounded-2xl">
                        <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-users text-yellow-600 mr-3"></i>
                            Kontak Departemen
                        </h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center bg-white p-4 rounded-lg">
                                <div>
                                    <h4 class="font-semibold text-gray-800">Management</h4>
                                    <p class="text-sm text-gray-600">Direktur & Manajer</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-blue-600 font-semibold">Ext. 101</p>
                                    <p class="text-sm text-gray-600">management@maknakreatif.com</p>
                                </div>
                            </div>

                            <div class="flex justify-between items-center bg-white p-4 rounded-lg">
                                <div>
                                    <h4 class="font-semibold text-gray-800">Wedding Planning</h4>
                                    <p class="text-sm text-gray-600">Tim Perencana Wedding</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-blue-600 font-semibold">Ext. 102</p>
                                    <p class="text-sm text-gray-600">wedding@maknakreatif.com</p>
                                </div>
                            </div>

                            <div class="flex justify-between items-center bg-white p-4 rounded-lg">
                                <div>
                                    <h4 class="font-semibold text-gray-800">Event Coordination</h4>
                                    <p class="text-sm text-gray-600">Koordinator Acara</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-blue-600 font-semibold">Ext. 103</p>
                                    <p class="text-sm text-gray-600">event@maknakreatif.com</p>
                                </div>
                            </div>

                            <div class="flex justify-between items-center bg-white p-4 rounded-lg">
                                <div>
                                    <h4 class="font-semibold text-gray-800">Finance</h4>
                                    <p class="text-sm text-gray-600">Keuangan & Akuntansi</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-blue-600 font-semibold">Ext. 104</p>
                                    <p class="text-sm text-gray-600">finance@maknakreatif.com</p>
                                </div>
                            </div>

                            <div class="flex justify-between items-center bg-white p-4 rounded-lg">
                                <div>
                                    <h4 class="font-semibold text-gray-800">IT Support</h4>
                                    <p class="text-sm text-gray-600">Dukungan Teknologi</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-blue-600 font-semibold">Ext. 105</p>
                                    <p class="text-sm text-gray-600">it@maknakreatif.com</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Emergency Contacts -->
                    <div class="bg-gradient-to-br from-red-50 to-red-100 p-8 rounded-2xl">
                        <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-exclamation-triangle text-red-600 mr-3"></i>
                            Kontak Darurat
                        </h3>
                        <div class="space-y-4">
                            <div class="bg-white p-4 rounded-lg border-l-4 border-red-500">
                                <h4 class="font-semibold text-gray-800 mb-2">Emergency Hotline</h4>
                                <p class="text-red-600 font-bold text-xl">+62 811 9999 0000</p>
                                <p class="text-sm text-gray-600">24/7 untuk masalah mendesak</p>
                            </div>

                            <div class="bg-white p-4 rounded-lg border-l-4 border-yellow-500">
                                <h4 class="font-semibold text-gray-800 mb-2">Security Office</h4>
                                <p class="text-yellow-600 font-bold text-xl">+62 811 8888 0000</p>
                                <p class="text-sm text-gray-600">Keamanan kantor</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Office Location & Map -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Lokasi Kantor</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Kunjungi kantor pusat kami untuk meeting atau konsultasi
                    langsung</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Map Placeholder -->
                <div class="bg-gray-300 h-96 rounded-2xl flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-map-marked-alt text-6xl text-gray-500 mb-4"></i>
                        <p class="text-gray-600 font-semibold">Google Maps Integration</p>
                        <p class="text-sm text-gray-500">Jl. Sudirman No. 123, Jakarta Pusat</p>
                    </div>
                </div>

                <!-- Location Details -->
                <div class="space-y-6">
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-building text-blue-600 mr-3"></i>
                            Alamat Lengkap
                        </h3>
                        <div class="space-y-3">
                            <p class="text-gray-700">
                                <strong>PT. Makna Kreatif Indonesia</strong><br>
                                Jl. Sudirman No. 123, Lantai 15<br>
                                Gedung Makna Tower<br>
                                Jakarta Pusat, DKI Jakarta 10220
                            </p>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-route text-yellow-500 mr-3"></i>
                            Akses Transportasi
                        </h3>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <i class="fas fa-subway w-6 text-blue-600 mr-3"></i>
                                <span class="text-gray-700">MRT Bundaran HI (5 menit jalan kaki)</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-bus w-6 text-green-600 mr-3"></i>
                                <span class="text-gray-700">Halte TransJakarta Bundaran HI</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-car w-6 text-yellow-600 mr-3"></i>
                                <span class="text-gray-700">Parkir tersedia di basement</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-info-circle text-blue-600 mr-3"></i>
                            Informasi Kunjungan
                        </h3>
                        <div class="space-y-3">
                            <p class="text-gray-700">
                                <i class="fas fa-clock text-blue-600 mr-2"></i>
                                <strong>Jam Operasional:</strong><br>
                                Senin - Jumat: 08:00 - 17:00 WIB<br>
                                Sabtu: 08:00 - 14:00 WIB
                            </p>
                            <p class="text-gray-700">
                                <i class="fas fa-calendar-check text-green-600 mr-2"></i>
                                <strong>Appointment:</strong> Disarankan membuat janji terlebih dahulu
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Actions -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Aksi Cepat</h2>
                <p class="text-gray-600">Akses cepat untuk kebutuhan internal perusahaan</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <a href="#"
                    class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-xl card-hover text-center group">
                    <div
                        class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-blue-700 transition-colors">
                        <i class="fas fa-headset text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">IT Support</h3>
                    <p class="text-gray-600 text-sm">Bantuan teknis sistem</p>
                </a>

                <a href="#"
                    class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-6 rounded-xl card-hover text-center group">
                    <div
                        class="w-16 h-16 bg-yellow-500 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-yellow-600 transition-colors">
                        <i class="fas fa-user-tie text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">HR Department</h3>
                    <p class="text-gray-600 text-sm">Masalah kepegawaian</p>
                </a>

                <a href="#"
                    class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-xl card-hover text-center group">
                    <div
                        class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-blue-700 transition-colors">
                        <i class="fas fa-calculator text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Finance</h3>
                    <p class="text-gray-600 text-sm">Pertanyaan keuangan</p>
                </a>

                <a href="#"
                    class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-6 rounded-xl card-hover text-center group">
                    <div
                        class="w-16 h-16 bg-yellow-500 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-yellow-600 transition-colors">
                        <i class="fas fa-exclamation-triangle text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Emergency</h3>
                    <p class="text-gray-600 text-sm">Kontak darurat</p>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    @include('front.footer')

    <script>
        function contactForm() {
            return {
                form: {
                    name: '',
                    department: '',
                    email: '',
                    phone: '',
                    category: '',
                    priority: 'medium',
                    message: '',
                    urgent: false
                },

                submitForm() {
                    // Validate required fields
                    if (!this.form.name || !this.form.department || !this.form.email || !this.form.category || !this.form
                        .message) {
                        alert('Mohon lengkapi semua field yang wajib diisi');
                        return;
                    }

                    // Here you would typically send the form data to your backend
                    console.log('Form submitted:', this.form);
                    alert('Pesan berhasil dikirim! Tim kami akan segera merespons.');

                    // Reset form
                    this.form = {
                        name: '',
                        department: '',
                        email: '',
                        phone: '',
                        category: '',
                        priority: 'medium',
                        message: '',
                        urgent: false
                    };
                }
            }
        }

        // Add smooth scrolling and interactive features
        document.addEventListener('DOMContentLoaded', function() {
            // Animate cards on scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Observe all cards
            document.querySelectorAll('.card-hover').forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(card);
            });
        });
    </script>
</body>

</html>
