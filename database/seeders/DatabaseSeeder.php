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
            // DepartmentSeeder::class,       // Departemen perusahaan.
            DocumentCategorySeeder::class, // Kategori dokumen.
            ChartOfAccountSeeder::class,   // Bagan Akun untuk akuntansi.
            FixedAssetChartOfAccountsSeeder::class, // Akun aset tetap dan akumulasi penyusutan.
            CompanySeeder::class,          // Data perusahaan.

            // 2. Data Pengguna (tergantung pada Status dan Role)
            UserSeeder::class,             // Pengguna sistem (admin, staff, dll).

            // 3. Data Master Bisnis (tergantung pada User, Category)
            VendorSeeder::class,           // Vendor/supplier.
            ProductSeeder::class,          // Produk/layanan yang ditawarkan.
            VendorPriceHistorySeeder::class, // Riwayat harga vendor.

            // 4. Data HR (tergantung pada User)
            EmployeeSeeder::class,         // Data karyawan.
            DataPribadiSeeder::class,      // Data pribadi karyawan.
            LeaveTypeSeeder::class,        // Jenis-jenis cuti karyawan.
            PayrollSeeder::class,          // Data gaji karyawan.
            LeaveRequestSeeder::class,     // Data permohonan cuti.
            AccountManagerTargetSeeder::class, // Target bulanan Account Manager.
            LeaveBalanceSeeder::class,     // Saldo cuti per user & tipe cuti.

            // 5. Data Bisnis (tergantung pada User, Industry, Product)
            ProspectSeeder::class,         // Calon klien.
            ProspectAppSeeder::class,      // Aplikasi dari calon klien.
            SimulasiProdukSeeder::class,   // Simulasi penawaran produk.

            // 6. Data Operasional (tergantung pada Prospect, User, Product, Vendor)
            OrderSeeder::class,            // Order/proyek wedding.
            NotaDinasSeeder::class,        // Nota dinas untuk pengeluaran.
            NotaDinasDetailSeeder::class,  // Detail nota dinas untuk pengeluaran.
            InternalMessageSeeder::class,  // Pesan internal antar user.

            // 7. Data Finansial (tergantung pada PaymentMethod, Order, NotaDinas)
            BankStatementSeeder::class,    // Laporan bank (opsional).
            BankTransactionSeeder::class,  // Transaksi bank per statement.
            BankReconciliationItemSeeder::class, // Item rekonsiliasi bank.
            ExpenseOpsSeeder::class,       // Pengeluaran operasional.
            PendapatanLainSeeder::class,   // Pendapatan di luar order.
            PengeluaranLainSeeder::class,  // Pengeluaran di luar order.
            FixedAssetSeeder::class,       // Aset tetap.
            AssetDepreciationSeeder::class, // Penyusutan aset tetap.
            PiutangSeeder::class,          // Piutang perusahaan.
            PembayaranPiutangSeeder::class, // Pembayaran piutang.

            // 8. Data Tambahan
            SopSeeder::class,              // SOP (tergantung pada User, SopCategory).
            SopRevisionSeeder::class,      // Revisi SOP.
            SopPermissionSeeder::class,    // Izin khusus untuk SOP.
            CompanyLogoSeeder::class,      // Logo perusahaan klien/partner.
            DocumentSeeder::class,         // Dokumen perusahaan.
        ]);

        $this->command->newLine();
        $this->command->info('✅ Database seeding completed successfully!');
    }
}
