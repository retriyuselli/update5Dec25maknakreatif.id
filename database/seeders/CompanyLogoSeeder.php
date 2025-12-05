<?php

namespace Database\Seeders;

use App\Models\CompanyLogo;
use Illuminate\Database\Seeder;

class CompanyLogoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            // Trusted Companies
            [
                'company_name' => 'Google',
                'website_url' => 'https://google.com',
                'logo_path' => 'https://logo.clearbit.com/google.com',
                'category' => 'client',
                'partnership_type' => 'enterprise',
                'display_order' => 1,
                'is_active' => true,
                'alt_text' => 'Google Logo',
                'description' => 'Global technology company specializing in Internet-related services and products.',
            ],
            [
                'company_name' => 'Microsoft',
                'website_url' => 'https://microsoft.com',
                'logo_path' => 'https://logo.clearbit.com/microsoft.com',
                'category' => 'client',
                'partnership_type' => 'enterprise',
                'display_order' => 2,
                'is_active' => true,
                'alt_text' => 'Microsoft Logo',
                'description' => 'Multinational technology corporation producing computer software, consumer electronics, personal computers.',
            ],
            [
                'company_name' => 'Apple',
                'website_url' => 'https://apple.com',
                'logo_path' => 'https://logo.clearbit.com/apple.com',
                'category' => 'client',
                'partnership_type' => 'premium',
                'display_order' => 3,
                'is_active' => true,
                'alt_text' => 'Apple Logo',
                'description' => 'American multinational technology company headquartered in Cupertino, California.',
            ],
            [
                'company_name' => 'Amazon',
                'website_url' => 'https://amazon.com',
                'logo_path' => 'https://logo.clearbit.com/amazon.com',
                'category' => 'partner',
                'partnership_type' => 'enterprise',
                'display_order' => 4,
                'is_active' => true,
                'alt_text' => 'Amazon Logo',
                'description' => 'American multinational technology company focusing on e-commerce, cloud computing, digital streaming.',
            ],
            [
                'company_name' => 'Netflix',
                'website_url' => 'https://netflix.com',
                'logo_path' => 'https://logo.clearbit.com/netflix.com',
                'category' => 'client',
                'partnership_type' => 'premium',
                'display_order' => 5,
                'is_active' => true,
                'alt_text' => 'Netflix Logo',
                'description' => 'American subscription streaming service and production company.',
            ],
            [
                'company_name' => 'Spotify',
                'website_url' => 'https://spotify.com',
                'logo_path' => 'https://logo.clearbit.com/spotify.com',
                'category' => 'client',
                'partnership_type' => 'premium',
                'display_order' => 6,
                'is_active' => true,
                'alt_text' => 'Spotify Logo',
                'description' => 'Swedish audio streaming and media services provider.',
            ],
            [
                'company_name' => 'Tesla',
                'website_url' => 'https://tesla.com',
                'logo_path' => 'https://logo.clearbit.com/tesla.com',
                'category' => 'partner',
                'partnership_type' => 'enterprise',
                'display_order' => 7,
                'is_active' => true,
                'alt_text' => 'Tesla Logo',
                'description' => 'American electric vehicle and clean energy company.',
            ],
            [
                'company_name' => 'Meta',
                'website_url' => 'https://meta.com',
                'logo_path' => 'https://logo.clearbit.com/meta.com',
                'category' => 'client',
                'partnership_type' => 'enterprise',
                'display_order' => 8,
                'is_active' => true,
                'alt_text' => 'Meta Logo',
                'description' => 'American multinational technology conglomerate holding company.',
            ],
            [
                'company_name' => 'Adobe',
                'website_url' => 'https://adobe.com',
                'logo_path' => 'https://logo.clearbit.com/adobe.com',
                'category' => 'vendor',
                'partnership_type' => 'premium',
                'display_order' => 9,
                'is_active' => true,
                'alt_text' => 'Adobe Logo',
                'description' => 'American multinational computer software company.',
            ],
            [
                'company_name' => 'Slack',
                'website_url' => 'https://slack.com',
                'logo_path' => 'https://logo.clearbit.com/slack.com',
                'category' => 'vendor',
                'partnership_type' => 'premium',
                'display_order' => 10,
                'is_active' => true,
                'alt_text' => 'Slack Logo',
                'description' => 'Business communication platform offering many IRC-style features.',
            ],
            [
                'company_name' => 'Zoom',
                'website_url' => 'https://zoom.us',
                'logo_path' => 'https://logo.clearbit.com/zoom.us',
                'category' => 'vendor',
                'partnership_type' => 'premium',
                'display_order' => 11,
                'is_active' => true,
                'alt_text' => 'Zoom Logo',
                'description' => 'American communications technology company headquartered in San Jose, California.',
            ],
            [
                'company_name' => 'Shopify',
                'website_url' => 'https://shopify.com',
                'logo_path' => 'https://logo.clearbit.com/shopify.com',
                'category' => 'sponsor',
                'partnership_type' => 'enterprise',
                'display_order' => 12,
                'is_active' => true,
                'alt_text' => 'Shopify Logo',
                'description' => 'Canadian multinational e-commerce company headquartered in Ottawa, Ontario.',
            ],
        ];

        foreach ($companies as $company) {
            CompanyLogo::create($company);
        }
    }
}
