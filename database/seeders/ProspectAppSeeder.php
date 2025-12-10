<?php

namespace Database\Seeders;

use App\Models\Industry;
use App\Models\ProspectApp;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ProspectAppSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $industries = Industry::all();

        if ($industries->isEmpty()) {
            $this->command->info('No industries found. Please run IndustrySeeder first.');

            return;
        }

        $prospectApps = [
            [
                'full_name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@elegantevents.com',
                'position' => 'Event Coordinator',
                'phone' => '+62-811-2345-6789',
                'company_name' => 'Elegant Events Co.',
                'industry_id' => $industries->where('industry_name', 'Wedding Organizer')->first()->id ?? $industries->first()->id,
                'name_of_website' => 'https://elegantevents.com',
                'user_size' => '11-50',
                'reason_for_interest' => 'Kami mencari sistem manajemen pernikahan yang komprehensif untuk menyederhanakan operasi dan meningkatkan kepuasan klien.',
                'status' => 'pending',
                'submitted_at' => Carbon::now()->subDays(2),
            ],
            [
                'full_name' => 'Michael Chen',
                'email' => 'michael@dreamweddings.co',
                'position' => 'Founder & CEO',
                'phone' => '+62-812-3456-7890',
                'company_name' => 'Dream Weddings Co.',
                'industry_id' => $industries->where('industry_name', 'Wedding Organizer')->first()->id ?? $industries->first()->id,
                'name_of_website' => 'https://dreamweddings.co',
                'user_size' => '1-10',
                'reason_for_interest' => 'Sebagai bisnis wedding planning yang berkembang, kami membutuhkan tools yang lebih baik untuk mengelola klien, vendor, dan acara secara efisien.',
                'status' => 'approved',
                'submitted_at' => Carbon::now()->subDays(5),
            ],
            [
                'full_name' => 'Amanda Rodriguez',
                'email' => 'amanda@beautifulbrides.id',
                'position' => 'Head Makeup Artist',
                'phone' => '+62-813-4567-8901',
                'company_name' => 'Beautiful Brides Studio',
                'industry_id' => $industries->where('industry_name', 'Bridal / Makeup Artist')->first()->id ?? $industries->first()->id,
                'name_of_website' => 'https://beautifulbrides.id',
                'user_size' => '1-10',
                'reason_for_interest' => 'Kami ingin mengorganisir jadwal klien dan portfolio layanan makeup dengan lebih baik.',
                'status' => 'pending',
                'submitted_at' => Carbon::now()->subDays(1),
            ],
            [
                'full_name' => 'David Wilson',
                'email' => 'david@goldenvenue.co.id',
                'position' => 'Venue Manager',
                'phone' => '+62-814-5678-9012',
                'company_name' => 'Golden Venue Bali',
                'industry_id' => $industries->where('industry_name', 'Venue Pernikahan')->first()->id ?? $industries->first()->id,
                'name_of_website' => 'https://goldenvenue.co.id',
                'user_size' => '11-50',
                'reason_for_interest' => 'Kami mengelola beberapa venue pernikahan dan membutuhkan sistem terpusat untuk booking dan koordinasi vendor.',
                'status' => 'rejected',
                'submitted_at' => Carbon::now()->subDays(7),
            ],
            [
                'full_name' => 'Jennifer Liu',
                'email' => 'jennifer@royalcatering.id',
                'position' => 'Operations Manager',
                'phone' => '+62-815-6789-0123',
                'company_name' => 'Royal Catering Services',
                'industry_id' => $industries->where('industry_name', 'Katering Pernikahan')->first()->id ?? $industries->first()->id,
                'name_of_website' => 'https://royalcatering.id',
                'user_size' => '51-200',
                'reason_for_interest' => 'Kami membutuhkan sistem untuk mengelola menu, booking katering, dan koordinasi dengan tim kitchen.',
                'status' => 'pending',
                'submitted_at' => Carbon::now()->subHours(6),
            ],
            [
                'full_name' => 'Rina Sari',
                'email' => 'rina@flashmoments.id',
                'position' => 'Lead Photographer',
                'phone' => '+62-816-7890-1234',
                'company_name' => 'Flash Moments Photography',
                'industry_id' => $industries->where('industry_name', 'Photography & Videography')->first()->id ?? $industries->first()->id,
                'name_of_website' => 'https://flashmoments.id',
                'user_size' => '1-10',
                'reason_for_interest' => 'Kami ingin sistem untuk mengelola portfolio, jadwal shooting, dan komunikasi dengan klien.',
                'status' => 'approved',
                'submitted_at' => Carbon::now()->subDays(3),
            ],
            [
                'full_name' => 'Budi Santoso',
                'email' => 'budi@elegantdekor.co.id',
                'position' => 'Creative Director',
                'phone' => '+62-817-8901-2345',
                'company_name' => 'Elegant Dekor Indonesia',
                'industry_id' => $industries->where('industry_name', 'Dekorasi Pernikahan')->first()->id ?? $industries->first()->id,
                'name_of_website' => 'https://elegantdekor.co.id',
                'user_size' => '11-50',
                'reason_for_interest' => 'Kami memerlukan platform untuk showcase design dan koordinasi dengan tim dekorasi.',
                'status' => 'pending',
                'submitted_at' => Carbon::now()->subDays(4),
            ],
        ];

        foreach ($prospectApps as $prospectApp) {
            ProspectApp::updateOrCreate(
                ['email' => $prospectApp['email']],
                $prospectApp
            );
        }

        $this->command->info('ProspectApp seeder completed successfully!');
        $this->command->info('Created '.count($prospectApps).' prospect applications.');
    }
}
