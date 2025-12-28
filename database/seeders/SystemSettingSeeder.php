<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = User::where('user_type', 'super_admin')->first();

        $settings = [
            [
                'setting_key' => 'reservation_archive_days',
                'setting_value' => '30',
                'description' => 'Number of days before archiving admin reservations',
            ],
            [
                'setting_key' => 'auto_archive_enabled',
                'setting_value' => 'true',
                'description' => 'Enable automatic archiving of admin reservations',
            ],
            [
                'setting_key' => 'max_upload_size_mb',
                'setting_value' => '10',
                'description' => 'Maximum file upload size in megabytes',
            ],
            [
                'setting_key' => 'allow_hotel_owner_create_roles',
                'setting_value' => 'true',
                'description' => 'Allow hotel owners to create custom roles',
            ],
            [
                'setting_key' => 'default_reservation_status',
                'setting_value' => 'pending',
                'description' => 'Default status for new reservations',
            ],
            [
                'setting_key' => 'system_name',
                'setting_value' => 'Hotel Room Management SaaS',
                'description' => 'System name displayed in the application',
            ],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['setting_key' => $setting['setting_key']],
                array_merge($setting, [
                    'updated_by' => $superAdmin?->id,
                ])
            );
        }

        $this->command->info('System settings seeded successfully!');
    }
}

