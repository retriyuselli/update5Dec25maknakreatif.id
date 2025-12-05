<?php

namespace Database\Seeders;

use App\Models\Sop;
use App\Models\SopCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class SopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first user as creator
        $user = User::first();
        if (! $user) {
            $this->command->error('No users found. Please create a user first.');

            return;
        }

        // Get categories
        $keuanganCategory = SopCategory::where('name', 'Keuangan')->first();
        $sdmCategory = SopCategory::where('name', 'SDM')->first();
        $operasionalCategory = SopCategory::where('name', 'Operasional')->first();
        $itCategory = SopCategory::where('name', 'IT')->first();

        // Sample SOPs
        $sops = [
            [
                'title' => 'Prosedur Pengajuan Reimbursement',
                'description' => 'Panduan langkah demi langkah untuk mengajukan reimbursement biaya operasional dan perjalanan dinas.',
                'category_id' => $keuanganCategory->id,
                'steps' => [
                    [
                        'step_number' => 1,
                        'title' => 'Persiapan Dokumen',
                        'description' => 'Kumpulkan semua kwitansi atau nota pembelian asli yang akan direimbursement. Pastikan kwitansi mencantumkan tanggal, nama merchant, dan jumlah pembayaran dengan jelas.',
                        'notes' => 'Kwitansi yang tidak lengkap atau tidak jelas akan ditolak sistem.',
                    ],
                    [
                        'step_number' => 2,
                        'title' => 'Login ke Sistem',
                        'description' => 'Masuk ke portal karyawan menggunakan username dan password yang telah diberikan oleh IT.',
                        'notes' => null,
                    ],
                    [
                        'step_number' => 3,
                        'title' => 'Isi Form Pengajuan',
                        'description' => 'Pilih menu "Reimbursement" dan isi form dengan lengkap termasuk tanggal transaksi, nominal, dan deskripsi pengeluaran.',
                        'notes' => 'Pastikan kategori pengeluaran dipilih dengan benar.',
                    ],
                    [
                        'step_number' => 4,
                        'title' => 'Upload Dokumen',
                        'description' => 'Upload foto atau scan kwitansi dalam format JPG, PNG, atau PDF dengan ukuran maksimal 5MB.',
                        'notes' => null,
                    ],
                    [
                        'step_number' => 5,
                        'title' => 'Submit dan Tunggu Approval',
                        'description' => 'Klik tombol submit dan tunggu persetujuan dari atasan langsung. Status pengajuan dapat dilihat di dashboard.',
                        'notes' => 'Proses approval maksimal 3 hari kerja.',
                    ],
                ],
                'version' => '1.0',
                'is_active' => true,
                'effective_date' => now(),
                'review_date' => now()->addMonths(6),
                'created_by' => $user->id,
                'updated_by' => $user->id,
                'keywords' => 'reimbursement, kwitansi, penggantian, biaya, keuangan',
            ],
            [
                'title' => 'Prosedur Rekrutmen Karyawan Baru',
                'description' => 'Panduan lengkap proses rekrutmen mulai dari posting lowongan hingga onboarding karyawan baru.',
                'category_id' => $sdmCategory->id,
                'steps' => [
                    [
                        'step_number' => 1,
                        'title' => 'Analisis Kebutuhan SDM',
                        'description' => 'Departemen yang membutuhkan karyawan baru mengajukan request ke HRD dengan spesifikasi posisi, kualifikasi, dan budget yang dibutuhkan.',
                        'notes' => 'Request harus disetujui oleh manager departemen.',
                    ],
                    [
                        'step_number' => 2,
                        'title' => 'Pembuatan Job Description',
                        'description' => 'HRD bekerjasama dengan departemen terkait membuat job description yang detail dan jelas.',
                        'notes' => null,
                    ],
                    [
                        'step_number' => 3,
                        'title' => 'Posting Lowongan',
                        'description' => 'Publikasikan lowongan di website perusahaan, job portal, dan media sosial perusahaan.',
                        'notes' => 'Periode posting minimal 2 minggu untuk posisi senior.',
                    ],
                    [
                        'step_number' => 4,
                        'title' => 'Seleksi CV',
                        'description' => 'Lakukan screening CV berdasarkan kualifikasi minimum yang telah ditetapkan.',
                        'notes' => null,
                    ],
                    [
                        'step_number' => 5,
                        'title' => 'Interview',
                        'description' => 'Laksanakan interview tahap pertama dengan HRD, dilanjutkan interview dengan manager departemen.',
                        'notes' => 'Dokumentasikan hasil interview dalam form evaluasi.',
                    ],
                    [
                        'step_number' => 6,
                        'title' => 'Background Check',
                        'description' => 'Lakukan verifikasi dokumen, referensi kerja, dan background check kandidat terpilih.',
                        'notes' => null,
                    ],
                    [
                        'step_number' => 7,
                        'title' => 'Onboarding',
                        'description' => 'Lakukan orientasi karyawan baru, setup workspace, dan pengenalan sistem perusahaan.',
                        'notes' => 'Periode probation 3 bulan dengan evaluasi bulanan.',
                    ],
                ],
                'version' => '2.1',
                'is_active' => true,
                'effective_date' => now()->subDays(30),
                'review_date' => now()->addYear(),
                'created_by' => $user->id,
                'updated_by' => $user->id,
                'keywords' => 'rekrutmen, hiring, karyawan, interview, onboarding, sdm',
            ],
            [
                'title' => 'Prosedur Backup Data Harian',
                'description' => 'Prosedur backup data sistem secara otomatis dan manual untuk memastikan keamanan data perusahaan.',
                'category_id' => $itCategory->id,
                'steps' => [
                    [
                        'step_number' => 1,
                        'title' => 'Verifikasi Sistem Backup Otomatis',
                        'description' => 'Cek status backup otomatis yang berjalan setiap pukul 02:00 WIB melalui dashboard monitoring.',
                        'notes' => 'Jika backup gagal, segera lakukan backup manual.',
                    ],
                    [
                        'step_number' => 2,
                        'title' => 'Backup Database Manual',
                        'description' => 'Jika diperlukan, lakukan backup database manual menggunakan command mysqldump atau tools backup yang tersedia.',
                        'notes' => 'Simpan backup dengan format nama: backup_YYYYMMDD_HHMMSS.sql',
                    ],
                    [
                        'step_number' => 3,
                        'title' => 'Backup File Sistem',
                        'description' => 'Backup folder aplikasi, dokumen user, dan file konfigurasi ke storage eksternal.',
                        'notes' => null,
                    ],
                    [
                        'step_number' => 4,
                        'title' => 'Verifikasi Integritas Backup',
                        'description' => 'Test restore sample data untuk memastikan backup dapat dipulihkan dengan baik.',
                        'notes' => 'Lakukan test restore minimal 1x seminggu.',
                    ],
                    [
                        'step_number' => 5,
                        'title' => 'Dokumentasi dan Laporan',
                        'description' => 'Catat hasil backup dalam log sistem dan laporkan jika ada masalah ke supervisor IT.',
                        'notes' => 'Simpan log backup minimal 30 hari.',
                    ],
                ],
                'version' => '1.3',
                'is_active' => true,
                'effective_date' => now()->subDays(10),
                'review_date' => now()->addMonths(3),
                'created_by' => $user->id,
                'updated_by' => $user->id,
                'keywords' => 'backup, data, database, sistem, it, keamanan',
            ],
            [
                'title' => 'Prosedur Pembukaan dan Penutupan Kantor',
                'description' => 'Panduan operasional harian untuk membuka dan menutup kantor dengan aman.',
                'category_id' => $operasionalCategory->id,
                'steps' => [
                    [
                        'step_number' => 1,
                        'title' => 'Pembukaan Kantor Pagi',
                        'description' => 'Matikan alarm keamanan, nyalakan lampu dan AC, cek kondisi umum kantor dan laporkan jika ada kerusakan.',
                        'notes' => 'Jam operasional: 08:00 - 17:00 WIB',
                    ],
                    [
                        'step_number' => 2,
                        'title' => 'Persiapan Ruang Kerja',
                        'description' => 'Pastikan ruang meeting bersih, pantry tersedia air dan perlengkapan, serta peralatan kantor berfungsi normal.',
                        'notes' => null,
                    ],
                    [
                        'step_number' => 3,
                        'title' => 'Penutupan Kantor Sore',
                        'description' => 'Pastikan semua karyawan telah pulang, matikan semua peralatan elektronik kecuali server dan CCTV.',
                        'notes' => 'Cek dua kali kondisi pintu dan jendela.',
                    ],
                    [
                        'step_number' => 4,
                        'title' => 'Aktivasi Keamanan',
                        'description' => 'Aktifkan sistem alarm keamanan dan pastikan semua akses terkunci dengan baik.',
                        'notes' => 'Tunggu konfirmasi alarm aktif sebelum meninggalkan gedung.',
                    ],
                ],
                'version' => '1.0',
                'is_active' => true,
                'effective_date' => now()->subDays(5),
                'review_date' => now()->addMonths(12),
                'created_by' => $user->id,
                'updated_by' => $user->id,
                'keywords' => 'operasional, kantor, keamanan, harian, buka, tutup',
            ],
        ];

        foreach ($sops as $sopData) {
            Sop::create($sopData);
        }

        $this->command->info('Sample SOPs created successfully!');
    }
}
