<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Company data...');

        $companies = [
            [
                'company_name' => 'PT Makna Kreatif Indonesia',
                'business_license' => 'SIUP-2020-001',
                'owner_name' => 'Rama Dhona Utama',
                'email' => 'info@maknaonline.com',
                'phone' => '+62-21-1234-5678',
                'address' => 'Jl. Sudirman No. 123',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'postal_code' => '10220',
                'website' => 'https://maknaonline.com',
                'description' => 'Wedding organizer & creative studio.',
                'logo_url' => null,
                'favicon_url' => null,
                'established_year' => 2015,
                'employee_count' => 25,
                'legal_entity_type' => 'PT',
                'deed_of_establishment' => 'AHU-001/2020',
                'deed_date' => '2020-01-15',
                'notary_name' => 'Notaris Putri',
                'notary_license_number' => 'NTR-2020-08',
                'nib_number' => 'NIB-2020-001',
                'nib_issued_date' => '2020-02-01',
                'nib_valid_until' => '2030-02-01',
                'npwp_number' => '12.345.678.9-012.345',
                'npwp_issued_date' => '2020-02-10',
                'tax_office' => 'KPP Pratama Jakarta Selatan',
                'legal_documents' => [],
                'legal_document_status' => 'complete',
            ],
        ];

        $created = 0;
        foreach ($companies as $data) {
            Company::firstOrCreate(
                ['company_name' => $data['company_name']],
                $data
            );
            $created++;
        }

        $this->command->info("Created {$created} companies.");
    }
}

