<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['status_name' => 'Finance'],
            ['status_name' => 'Account Manager'],
            ['status_name' => 'Admin'],
            ['status_name' => 'HRD'],
            ['status_name' => 'Staff'],
        ];

        foreach ($statuses as $status) {
            // Menggunakan firstOrCreate untuk mencegah duplikasi data jika seeder dijalankan lagi
            Status::firstOrCreate(
                ['status_name' => $status['status_name']],
                $status
            );
        }
        $this->command->info('✅ Status seeder completed!');
        $this->command->newLine();
    }
}
