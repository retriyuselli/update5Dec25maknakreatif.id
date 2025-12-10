<?php

namespace Database\Seeders;

use App\Models\InternalMessage;
use App\Models\User;
use Illuminate\Database\Seeder;

class InternalMessageSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        if ($users->count() < 2) {
            $this->command->error('Need at least 2 users. Please run UserSeeder first.');
            return;
        }

        $this->command->info('Seeding Internal Messages...');

        $messages = [
            [
                'subject' => 'Meeting Operasional Mingguan',
                'message' => 'Agenda: pembagian tugas, progres klien, dan kebutuhan vendor.',
                'type' => 'announcement',
                'priority' => 'medium',
                'status' => 'sent',
                'requires_response' => false,
                'department' => 'operasional',
            ],
            [
                'subject' => 'Review SOP Keuangan',
                'message' => 'Mohon review prosedur reimburse terbaru sebelum implementasi.',
                'type' => 'task',
                'priority' => 'high',
                'status' => 'pending',
                'requires_response' => true,
                'department' => 'keuangan',
            ],
        ];

        foreach ($messages as $data) {
            $sender = $users->random();
            $recipients = $users->where('id', '!=', $sender->id)->pluck('id')->take(3)->values()->all();

            InternalMessage::firstOrCreate(
                [
                    'subject' => $data['subject'],
                    'sender_id' => $sender->id,
                ],
                [
                    'message' => $data['message'],
                    'type' => $data['type'],
                    'priority' => $data['priority'],
                    'status' => $data['status'],
                    'recipient_ids' => $recipients,
                    'cc_ids' => [],
                    'bcc_ids' => [],
                    'attachments' => [],
                    'requires_response' => $data['requires_response'],
                    'due_date' => $data['requires_response'] ? now()->addDays(3) : null,
                    'tags' => ['seeder'],
                    'department' => $data['department'],
                    'is_public' => false,
                    'is_pinned' => false,
                ]
            );
        }

        $this->command->info('Internal messages seeded.');
    }
}

