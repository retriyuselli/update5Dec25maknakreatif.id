<?php

namespace Database\Seeders;

use App\Models\Industry;
use Illuminate\Database\Seeder;

class IndustrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Seeder untuk tabel Industries yang berisi daftar industri
     * yang relevan untuk sistem manajemen bisnis.
     */
    public function run(): void
    {
        $industries = [
            [
                'industry_name' => 'Wedding Organizer',
                'description' => 'Layanan perencanaan dan koordinasi acara pernikahan',
                'is_active' => true,
            ],
            [
                'industry_name' => 'Event Organizer',
                'description' => 'Layanan perencanaan dan penyelenggaraan berbagai jenis acara',
                'is_active' => true,
            ],
            [
                'industry_name' => 'Photography & Videography',
                'description' => 'Layanan fotografi dan videografi untuk berbagai kebutuhan',
                'is_active' => true,
            ],
            [
                'industry_name' => 'Bridal / Makeup Artist',
                'description' => 'Layanan tata rias dan makeup artist untuk pengantin',
                'is_active' => true,
            ],
            [
                'industry_name' => 'Dekorasi Pernikahan',
                'description' => 'Layanan dekorasi dan penataan khusus untuk acara pernikahan',
                'is_active' => true,
            ],
            [
                'industry_name' => 'Venue Pernikahan',
                'description' => 'Penyedia tempat dan lokasi untuk acara pernikahan',
                'is_active' => true,
            ],
            [
                'industry_name' => 'Katering Pernikahan',
                'description' => 'Layanan penyediaan makanan khusus untuk acara pernikahan',
                'is_active' => true,
            ],
            [
                'industry_name' => 'Hiburan (MC, Band, DJ)',
                'description' => 'Layanan hiburan seperti MC, band musik, dan DJ untuk acara',
                'is_active' => true,
            ],
            [
                'industry_name' => 'Percetakan Undangan / Souvenir',
                'description' => 'Layanan percetakan undangan pernikahan dan souvenir',
                'is_active' => true,
            ],
            [
                'industry_name' => 'Busana Pengantin',
                'description' => 'Penyedia busana dan pakaian untuk pengantin',
                'is_active' => true,
            ],
            [
                'industry_name' => 'Perhiasan / Cincin',
                'description' => 'Penyedia perhiasan dan cincin untuk acara pernikahan',
                'is_active' => true,
            ],
            [
                'industry_name' => 'Lainnya',
                'description' => 'Kategori lain yang berkaitan dengan industri pernikahan',
                'is_active' => true,
            ],
        ];

        foreach ($industries as $industry) {
            Industry::firstOrCreate(
                ['industry_name' => $industry['industry_name']],
                $industry
            );
        }

        $this->command->info('✅ Industry seeder completed successfully!');
        $this->command->info('📊 Created '.count($industries).' industry categories');
        $this->command->info('🏢 Industries for various business types');
        $this->command->newLine();
        $this->command->info('🔍 Total industries in database: '.Industry::count());
    }
}
