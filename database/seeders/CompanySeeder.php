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
                // Informasi Perusahaan
                'company_name' => 'PT Makna Kreatif Indonesia',
                'business_license' => 'SIUP-2020-001',
                'owner_name' => 'Rama Dhona Utama',
                'legal_entity_type' => 'PT',
                'established_year' => 2015,
                'employee_count' => 25,
                
                // Kontak & Alamat
                'email' => 'info@maknaonline.com',
                'phone' => '+62 21 1234 5678', // Format sesuai regex /^[0-9+\s\-]+$/
                'address' => 'Jl. Sudirman No. 123',
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'postal_code' => '10220', // Format sesuai regex /^[0-9]+$/
                'website' => 'https://maknaonline.com',
                'logo_url' => null,
                'favicon_url' => null,
                'description' => 'Wedding organizer & creative studio yang berfokus pada menciptakan momen tak terlupakan.',

                // Legal Perusahaan
                'deed_of_establishment' => 'AHU-001/2020',
                'deed_date' => '2020-01-15',
                'notary_name' => 'Notaris Putri, S.H., M.Kn.',
                'notary_license_number' => 'NTR-2020-08',
                'nib_number' => 'NIB-2020-001',
                'nib_issued_date' => '2020-02-01',
                'nib_valid_until' => '2030-02-01',
                'npwp_number' => '12.345.678.9-012.345', // Format sesuai regex /^[0-9\.\-]+$/
                'npwp_issued_date' => '2020-02-10',
                'tax_office' => 'KPP Pratama Jakarta Selatan',
                
                // Dokumen
                'legal_documents' => [],
                'legal_document_status' => 'complete',
            ],
        ];

        $created = 0;
        foreach ($companies as $data) {
            Company::updateOrCreate(
                ['company_name' => $data['company_name']],
                $data
            );
            $created++;
        }

        $this->command->info("Created/Updated {$created} companies.");
    }
}
