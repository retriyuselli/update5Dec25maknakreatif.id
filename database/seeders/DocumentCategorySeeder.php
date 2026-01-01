<?php

namespace Database\Seeders;

use App\Models\DocumentCategory;
use Illuminate\Database\Seeder;

class DocumentCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Surat Keputusan',
                'code' => 'SK',
                'type' => 'internal',
                'format_number' => '{SEQ}/SK/MKI/{ROMAN_MONTH}/{Y}',
                'is_approval_required' => true,
            ],
            [
                'name' => 'Surat Tugas',
                'code' => 'ST',
                'type' => 'internal',
                'format_number' => '{SEQ}/ST/MKI/{ROMAN_MONTH}/{Y}',
                'is_approval_required' => true,
            ],
            [
                'name' => 'Memo Internal',
                'code' => 'MEMO',
                'type' => 'internal',
                'format_number' => '{SEQ}/MEMO/MKI/{ROMAN_MONTH}/{Y}',
                'is_approval_required' => false,
            ],
            [
                'name' => 'Berita Acara',
                'code' => 'BA',
                'type' => 'internal',
                'format_number' => '{SEQ}/BA/MKI/{ROMAN_MONTH}/{Y}',
                'is_approval_required' => true,
            ],
            [
                'name' => 'Surat Keluar',
                'code' => 'OUT',
                'type' => 'outbound',
                'format_number' => '{SEQ}/MKI-OUT/{ROMAN_MONTH}/{Y}',
                'is_approval_required' => true,
            ],
            [
                'name' => 'Surat Masuk',
                'code' => 'IN',
                'type' => 'inbound',
                'format_number' => '{SEQ}/IN/MKI/{ROMAN_MONTH}/{Y}',
                'is_approval_required' => false,
            ],
        ];

        foreach ($categories as $category) {
            DocumentCategory::firstOrCreate(
                ['code' => $category['code']],
                $category
            );
        }
    }
}
