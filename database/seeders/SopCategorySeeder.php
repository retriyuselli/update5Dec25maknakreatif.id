<?php

namespace Database\Seeders;

use App\Models\SopCategory;
use Illuminate\Database\Seeder;

class SopCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Keuangan',
                'description' => 'SOP terkait proses keuangan dan akuntansi',
                'color' => '#10B981',
                'icon' => 'heroicon-o-banknotes',
                'is_active' => true,
            ],
            [
                'name' => 'SDM',
                'description' => 'SOP terkait manajemen sumber daya manusia',
                'color' => '#8B5CF6',
                'icon' => 'heroicon-o-users',
                'is_active' => true,
            ],
            [
                'name' => 'Operasional',
                'description' => 'SOP terkait operasional harian perusahaan',
                'color' => '#F59E0B',
                'icon' => 'heroicon-o-cog-8-tooth',
                'is_active' => true,
            ],
            [
                'name' => 'IT',
                'description' => 'SOP terkait teknologi informasi dan sistem',
                'color' => '#3B82F6',
                'icon' => 'heroicon-o-computer-desktop',
                'is_active' => true,
            ],
            [
                'name' => 'Marketing',
                'description' => 'SOP terkait pemasaran dan promosi',
                'color' => '#EF4444',
                'icon' => 'heroicon-o-megaphone',
                'is_active' => true,
            ],
            [
                'name' => 'Penjualan',
                'description' => 'SOP terkait proses penjualan dan customer service',
                'color' => '#06B6D4',
                'icon' => 'heroicon-o-shopping-cart',
                'is_active' => true,
            ],
            [
                'name' => 'Administrasi',
                'description' => 'SOP terkait administrasi umum dan dokumentasi',
                'color' => '#84CC16',
                'icon' => 'heroicon-o-document-text',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            SopCategory::create($category);
        }
    }
}
