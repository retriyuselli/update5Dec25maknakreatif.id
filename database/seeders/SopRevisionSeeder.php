<?php

namespace Database\Seeders;

use App\Models\Sop;
use App\Models\SopRevision;
use App\Models\User;
use Illuminate\Database\Seeder;

class SopRevisionSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding SOP Revisions...');

        $sops = Sop::all();
        $users = User::all();
        if ($sops->isEmpty() || $users->isEmpty()) {
            $this->command->error('Need SOPs and Users. Run SopSeeder & UserSeeder first.');
            return;
        }

        foreach ($sops->take(3) as $sop) {
            $revisor = $users->random();
            SopRevision::firstOrCreate(
                [
                    'sop_id' => $sop->id,
                    'version' => 2,
                ],
                [
                    'title' => $sop->title.' - Revisi',
                    'description' => 'Penyempurnaan langkah kerja dan penambahan catatan keamanan.',
                    'steps' => [
                        ['step_number' => 1, 'title' => 'Review Dokumen', 'description' => 'Review poin-poin yang perlu diperbarui.'],
                        ['step_number' => 2, 'title' => 'Diskusi Tim', 'description' => 'Diskusikan perubahan dengan departemen terkait.'],
                        ['step_number' => 3, 'title' => 'Finalisasi', 'description' => 'Finalisasi dokumen dan sosialisasi.'],
                    ],
                    'supporting_documents' => [],
                    'revised_by' => $revisor->id,
                    'revision_notes' => 'Menyesuaikan dengan kebijakan terbaru.',
                    'revision_date' => now()->subDays(7),
                ]
            );
        }

        $this->command->info('SOP revisions seeded.');
    }
}

