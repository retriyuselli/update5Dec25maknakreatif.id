<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Urutan pemanggilan seeder ini SANGAT PENTING untuk menjaga integritas foreign key.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting database seeder...');
        $this->command->newLine();

        $this->call([
            // 1. Master Data (tidak ada dependency atau dependency minimal)
            StatusSeeder::class,           // Status untuk user, dll.
            IndustrySeeder::class,         // Industri untuk prospek, dll.
            CategorySeeder::class,         // Kategori untuk produk dan vendor.
            PaymentMethodSeeder::class,    // Metode pembayaran untuk transaksi.
            RoleSeeder::class,             // Peran dan izin pengguna (Spatie).
            SopCategorySeeder::class,      // Kategori untuk SOP.

            // 2. Data Pengguna (tergantung pada Status dan Role)
            UserSeeder::class,             // Pengguna sistem (admin, staff, dll).

            // 3. Data Master Bisnis (tergantung pada User, Category)
            VendorSeeder::class,           // Vendor/supplier.
            ProductSeeder::class,          // Produk/layanan yang ditawarkan.

            // 4. Data HR (tergantung pada User)
            EmployeeSeeder::class,         // Data karyawan.
            DataPribadiSeeder::class,      // Data pribadi karyawan.
            LeaveTypeSeeder::class,        // Jenis-jenis cuti karyawan.
            PayrollSeeder::class,          // Data gaji karyawan.
            LeaveRequestSeeder::class,     // Data permohonan cuti.

            // 5. Data Bisnis (tergantung pada User, Industry, Product)
            ProspectSeeder::class,         // Calon klien.
            ProspectAppSeeder::class,      // Aplikasi dari calon klien.
            SimulasiProdukSeeder::class,   // Simulasi penawaran produk.

            // 6. Data Operasional (tergantung pada Prospect, User, Product, Vendor)
            OrderSeeder::class,            // Order/proyek wedding.
            NotaDinasSeeder::class,        // Nota dinas untuk pengeluaran.

            // 7. Data Finansial (tergantung pada PaymentMethod, Order, NotaDinas)
            BankStatementSeeder::class,    // Laporan bank (opsional).
            ExpenseOpsSeeder::class,       // Pengeluaran operasional.
            PendapatanLainSeeder::class,   // Pendapatan di luar order.
            PengeluaranLainSeeder::class,  // Pengeluaran di luar order.

            // 8. Data Tambahan
            SopSeeder::class,              // SOP (tergantung pada User, SopCategory).
            SopPermissionSeeder::class,    // Izin khusus untuk SOP.
            CompanyLogoSeeder::class,      // Logo perusahaan klien/partner.
        ]);

        $this->command->newLine();
        $this->command->info('✅ Database seeding completed successfully!');
    }
}
