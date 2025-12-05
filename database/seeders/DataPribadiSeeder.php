<?php

namespace Database\Seeders;

use App\Models\DataPribadi;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DataPribadiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teamMembers = [
            [
                'nama_lengkap' => 'Sarah Wijaya Sari',
                'email' => 'sarah.wijaya@maknaonline.com',
                'nomor_telepon' => '81234567890',
                'tanggal_lahir' => '1988-03-15',
                'tanggal_mulai_gabung' => '2020-01-01',
                'jenis_kelamin' => 'Perempuan',
                'alamat' => 'Jl. Sudirman No. 123, Menteng, Jakarta Pusat 10310',
                'pekerjaan' => 'CEO & Founder',
                'gaji' => 25000000,
                'motivasi_kerja' => 'Ingin menciptakan momen pernikahan yang tak terlupakan bagi setiap pasangan. Membangun bisnis yang dapat memberikan dampak positif bagi industri wedding organizer di Indonesia dengan standar internasional.',
                'pelatihan' => '<p><strong>Pelatihan yang telah diikuti:</strong></p><ul><li>Wedding Planning Certification - International Association of Wedding Planners (2019)</li><li>Luxury Event Management - Singapore Hotel Association (2020)</li><li>Digital Marketing for Wedding Business - Google Digital Garage (2021)</li><li>Leadership & Team Management - Dale Carnegie Indonesia (2022)</li></ul>',
            ],
            [
                'nama_lengkap' => 'Michael Chen Wijaya',
                'email' => 'michael.chen@maknaonline.com',
                'nomor_telepon' => '81234567891',
                'tanggal_lahir' => '1990-07-22',
                'tanggal_mulai_gabung' => '2020-01-01',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Jl. Gatot Subroto No. 456, Kuningan, Jakarta Selatan 12950',
                'pekerjaan' => 'COO & Co-Founder',
                'gaji' => 20000000,
                'motivasi_kerja' => 'Mengoptimalkan operasional perusahaan untuk memberikan layanan terbaik. Fokus pada efisiensi proses dan inovasi teknologi dalam industri wedding organizer.',
                'pelatihan' => '<p><strong>Pelatihan yang telah diikuti:</strong></p><ul><li>Operations Management Certification - PMI Indonesia (2020)</li><li>Financial Management for SME - LPPI (2021)</li><li>Project Management Professional (PMP) - PMI (2021)</li><li>Business Process Optimization - McKinsey Academy (2022)</li></ul>',
            ],
            [
                'nama_lengkap' => 'Rani Sari Dewi Putri',
                'email' => 'rani.sari@maknaonline.com',
                'nomor_telepon' => '81234567892',
                'tanggal_lahir' => '1992-11-08',
                'tanggal_mulai_gabung' => '2021-03-15',
                'jenis_kelamin' => 'Perempuan',
                'alamat' => 'Jl. Kemang Raya No. 789, Kemang, Jakarta Selatan 12560',
                'pekerjaan' => 'Senior Account Manager',
                'gaji' => 12000000,
                'motivasi_kerja' => 'Membangun hubungan yang kuat dengan klien dan memastikan setiap detail pernikahan terlaksana dengan sempurna. Menjadi konsultan terpercaya untuk mewujudkan impian pernikahan klien.',
                'pelatihan' => '<p><strong>Pelatihan yang telah diikuti:</strong></p><ul><li>Customer Relationship Management - Salesforce Trailhead (2021)</li><li>Advanced Wedding Consultation Techniques - WeddingWire Academy (2021)</li><li>Luxury Service Excellence - Ritz Carlton Leadership Center (2022)</li><li>Conflict Resolution & Negotiation - Harvard Business School Online (2023)</li></ul>',
            ],
            [
                'nama_lengkap' => 'David Pranata Kusuma',
                'email' => 'david.pranata@maknaonline.com',
                'nomor_telepon' => '81234567893',
                'tanggal_lahir' => '1993-05-12',
                'tanggal_mulai_gabung' => '2021-08-01',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Jl. Senopati No. 321, Kebayoran Baru, Jakarta Selatan 12190',
                'pekerjaan' => 'Account Manager',
                'gaji' => 10000000,
                'motivasi_kerja' => 'Mengembangkan skill dalam menangani berbagai tipe klien dan event. Terus belajar untuk menjadi wedding consultant yang profesional dan dapat diandalkan.',
                'pelatihan' => '<p><strong>Pelatihan yang telah diikuti:</strong></p><ul><li>Wedding Planning Fundamentals - Wedding Planning Institute (2021)</li><li>Sales Techniques for Service Industry - Dale Carnegie (2022)</li><li>Event Budgeting & Cost Management - Event Management Institute (2022)</li><li>Digital Portfolio Management - Adobe Creative Suite (2023)</li></ul>',
            ],
            [
                'nama_lengkap' => 'Luna Kartika Sari',
                'email' => 'luna.kartika@maknaonline.com',
                'nomor_telepon' => '81234567894',
                'tanggal_lahir' => '1989-09-30',
                'tanggal_mulai_gabung' => '2020-06-01',
                'jenis_kelamin' => 'Perempuan',
                'alamat' => 'Jl. Pejaten Raya No. 654, Pasar Minggu, Jakarta Selatan 12520',
                'pekerjaan' => 'Senior Event Manager',
                'gaji' => 13000000,
                'motivasi_kerja' => 'Memastikan eksekusi event berjalan sempurna sesuai timeline. Mengkoordinasikan semua vendor dan tim untuk menghasilkan pernikahan yang luar biasa dan memorable.',
                'pelatihan' => '<p><strong>Pelatihan yang telah diikuti:</strong></p><ul><li>Event Coordination Mastery - International Live Events Association (2020)</li><li>Vendor Management Excellence - Event Industry Council (2021)</li><li>Crisis Management in Events - Event Safety Alliance (2021)</li><li>Advanced Timeline Management - Wedding MBA (2022)</li></ul>',
            ],
            [
                'nama_lengkap' => 'Agus Hermawan Saputra',
                'email' => 'agus.hermawan@maknaonline.com',
                'nomor_telepon' => '81234567895',
                'tanggal_lahir' => '1991-12-18',
                'tanggal_mulai_gabung' => '2021-01-10',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Jl. Fatmawati No. 987, Cilandak, Jakarta Selatan 12430',
                'pekerjaan' => 'Event Manager',
                'gaji' => 11000000,
                'motivasi_kerja' => 'Spesialisasi dalam outdoor wedding dan destination wedding. Senang menghadapi tantangan logistik yang kompleks dan menciptakan pengalaman unik untuk setiap pasangan.',
                'pelatihan' => '<p><strong>Pelatihan yang telah diikuti:</strong></p><ul><li>Outdoor Event Management - Adventure Wedding Association (2021)</li><li>Destination Wedding Planning - International Association of Destination Wedding Professionals (2021)</li><li>Weather Contingency Planning - Event Weather Services (2022)</li><li>Logistics Coordination - Supply Chain Management Institute (2022)</li></ul>',
            ],
            [
                'nama_lengkap' => 'Sinta Maharani Putri',
                'email' => 'sinta.maharani@maknaonline.com',
                'nomor_telepon' => '81234567896',
                'tanggal_lahir' => '1994-04-25',
                'tanggal_mulai_gabung' => '2021-11-01',
                'jenis_kelamin' => 'Perempuan',
                'alamat' => 'Jl. Radio Dalam No. 147, Kebayoran Baru, Jakarta Selatan 12140',
                'pekerjaan' => 'Finance Manager',
                'gaji' => 9500000,
                'motivasi_kerja' => 'Mengelola keuangan perusahaan dengan transparan dan akurat. Membantu tim dalam perencanaan budget event dan optimalisasi profit margin setiap project.',
                'pelatihan' => '<p><strong>Pelatihan yang telah diikuti:</strong></p><ul><li>Financial Management for Creative Industry - Indonesian Institute of Accountants (2021)</li><li>Event Budgeting & Financial Planning - Event Financial Management (2022)</li><li>Tax Planning for SME - Tax Consultant Association (2022)</li><li>Digital Accounting Systems - Accurate Software Training (2023)</li></ul>',
            ],
            [
                'nama_lengkap' => 'Eko Prasetyo Nugroho',
                'email' => 'eko.prasetyo@maknaonline.com',
                'nomor_telepon' => '81234567897',
                'tanggal_lahir' => '1995-08-14',
                'tanggal_mulai_gabung' => '2022-02-14',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Jl. Cilandak KKO No. 258, Cilandak, Jakarta Selatan 12560',
                'pekerjaan' => 'Senior Crew & Setup Coordinator',
                'gaji' => 7500000,
                'motivasi_kerja' => 'Ahli dalam setup dekorasi dan koordinasi teknis. Memastikan semua elemen visual dan teknis wedding terpasang dengan sempurna sesuai konsep yang diinginkan.',
                'pelatihan' => '<p><strong>Pelatihan yang telah diikuti:</strong></p><ul><li>Event Setup & Production - Indonesian Event Organizer Association (2022)</li><li>Floral Design & Decoration - Jakarta Floral Academy (2022)</li><li>Audio Visual Technical Training - Sound & Lighting Institute (2023)</li><li>Safety & Security in Events - Event Safety Certification (2023)</li></ul>',
            ],
            [
                'nama_lengkap' => 'Maya Indira Sari',
                'email' => 'maya.indira@maknaonline.com',
                'nomor_telepon' => '81234567898',
                'tanggal_lahir' => '1996-01-20',
                'tanggal_mulai_gabung' => '2022-06-01',
                'jenis_kelamin' => 'Perempuan',
                'alamat' => 'Jl. Ampera Raya No. 369, Kemang, Jakarta Selatan 12550',
                'pekerjaan' => 'Crew & Catering Coordinator',
                'gaji' => 6500000,
                'motivasi_kerja' => 'Spesialis koordinasi catering dan vendor makanan. Memastikan kualitas makanan dan service yang excellent untuk kepuasan tamu undangan.',
                'pelatihan' => '<p><strong>Pelatihan yang telah diikuti:</strong></p><ul><li>Food & Beverage Service Excellence - Hotel & Restaurant Association (2022)</li><li>Catering Coordination & Quality Control - Indonesian Catering Association (2022)</li><li>Halal Food Certification Management - MUI Training Center (2023)</li><li>Customer Service in F&B - Service Excellence Academy (2023)</li></ul>',
            ],
            [
                'nama_lengkap' => 'Rizki Aditya Pratama',
                'email' => 'rizki.aditya@maknaonline.com',
                'nomor_telepon' => '81234567899',
                'tanggal_lahir' => '1997-10-05',
                'tanggal_mulai_gabung' => '2023-03-01',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Jl. TB Simatupang No. 123, Cilandak, Jakarta Selatan 12430',
                'pekerjaan' => 'Junior Crew & Digital Content Creator',
                'gaji' => 5500000,
                'motivasi_kerja' => 'Mengembangkan skill di bidang wedding industry sambil mengasah kemampuan content creation. Ingin menjadi bagian dari tim yang menciptakan momen berharga untuk setiap pasangan.',
                'pelatihan' => '<p><strong>Pelatihan yang telah diikuti:</strong></p><ul><li>Basic Wedding Coordination - Wedding Planner Training Institute (2023)</li><li>Social Media Content Creation - Digital Marketing Institute (2023)</li><li>Photography & Videography Basics - Visual Arts Academy (2023)</li><li>Event Documentation - Professional Event Photographer Association (2023)</li></ul>',
            ],
        ];

        foreach ($teamMembers as $memberData) {
            // Convert string dates to Carbon instances
            $memberData['tanggal_lahir'] = Carbon::parse($memberData['tanggal_lahir']);
            $memberData['tanggal_mulai_gabung'] = Carbon::parse($memberData['tanggal_mulai_gabung']);

            DataPribadi::firstOrCreate([
                'email' => $memberData['email'],
            ], $memberData);
        }

        $this->command->info('✅ DataPribadiSeeder completed! Created '.count($teamMembers).' team member profiles.');
    }
}
