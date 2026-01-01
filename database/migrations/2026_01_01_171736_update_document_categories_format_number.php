<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update format numbers to place {SEQ} at the beginning
        $categories = [
            'Surat Keputusan' => '{SEQ}/SK/MKI/{ROMAN_MONTH}/{Y}',
            'Surat Tugas' => '{SEQ}/ST/MKI/{ROMAN_MONTH}/{Y}',
            'Memo Internal' => '{SEQ}/MEMO/MKI/{ROMAN_MONTH}/{Y}',
            'Berita Acara' => '{SEQ}/BA/MKI/{ROMAN_MONTH}/{Y}',
            'Surat Keluar' => '{SEQ}/MKI-OUT/{ROMAN_MONTH}/{Y}',
            'Surat Masuk' => '{SEQ}/IN/MKI/{ROMAN_MONTH}/{Y}',
        ];

        foreach ($categories as $name => $format) {
            DB::table('document_categories')
                ->where('name', $name)
                ->update(['format_number' => $format]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original formats (approximate)
        $categories = [
            'Surat Keputusan' => 'SK/{Y}/MKI/{SEQ}',
            'Surat Tugas' => 'ST/{Y}/MKI/{SEQ}',
            'Memo Internal' => 'MEMO/{DEPT}/{ROMAN_MONTH}/{Y}/{SEQ}',
            'Berita Acara' => 'BA/{Y}/MKI/{SEQ}',
            'Surat Keluar' => '{SEQ}/MKI-OUT/{ROMAN_MONTH}/{Y}',
            'Surat Masuk' => 'IN/{Y}/{SEQ}',
        ];

        foreach ($categories as $name => $format) {
            DB::table('document_categories')
                ->where('name', $name)
                ->update(['format_number' => $format]);
        }
    }
};
