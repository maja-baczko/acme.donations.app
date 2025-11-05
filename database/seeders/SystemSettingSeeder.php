<?php

namespace Database\Seeders;

use App\Modules\Administration\Models\SystemSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'app_name',
                'value' => 'ACME Donations',
                'type' => 'string',
                'description' => 'Application name displayed throughout the platform',
                'is_public' => true,
            ],
            [
                'key' => 'company_name',
                'value' => 'ACME Foundation',
                'type' => 'string',
                'description' => 'Organization name for legal documents and receipts',
                'is_public' => true,
            ],
            [
                'key' => 'contact_email',
                'value' => 'contact@acme-donations.org',
                'type' => 'string',
                'description' => 'Main contact email address',
                'is_public' => true,
            ],
            [
                'key' => 'min_donation_amount',
                'value' => '5',
                'type' => 'integer',
                'description' => 'Minimum donation amount in the default currency',
                'is_public' => true,
            ],
            [
                'key' => 'max_donation_amount',
                'value' => '50000',
                'type' => 'integer',
                'description' => 'Maximum donation amount in the default currency',
                'is_public' => true,
            ],
            [
                'key' => 'default_currency',
                'value' => 'EUR',
                'type' => 'string',
                'description' => 'Default currency code (ISO 4217)',
                'is_public' => true,
            ],
            [
                'key' => 'maintenance_mode',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Enable maintenance mode to restrict access',
                'is_public' => false,
            ],
            [
                'key' => 'registration_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Allow new user registrations',
                'is_public' => true,
            ],
            [
                'key' => 'donation_receipt_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Automatically send donation receipts via email',
                'is_public' => false,
            ],
            [
                'key' => 'campaign_approval_required',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Require admin approval before campaigns go live',
                'is_public' => false,
            ],
            [
                'key' => 'tax_receipt_footer',
                'value' => 'This receipt is valid for tax deduction purposes. ACME Foundation is a registered 501(c)(3) nonprofit organization.',
                'type' => 'text',
                'description' => 'Footer text displayed on tax receipts',
                'is_public' => false,
            ],
            [
                'key' => 'items_per_page',
                'value' => '20',
                'type' => 'integer',
                'description' => 'Default number of items per page in listings',
                'is_public' => false,
            ],
            [
                'key' => 'session_timeout',
                'value' => '120',
                'type' => 'integer',
                'description' => 'User session timeout in minutes',
                'is_public' => false,
            ],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
