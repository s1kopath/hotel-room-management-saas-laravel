<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = User::where('user_type', 'super_admin')->first();

        if (!$superAdmin) {
            $this->command->error('Super Admin not found. Please run SuperAdminSeeder first.');
            return;
        }

        $systemRoles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'System administrator with full access',
                'scope' => 'system',
                'is_default' => false,
            ],
            [
                'name' => 'Hotel Owner',
                'slug' => 'hotel-owner',
                'description' => 'Hotel owner who can manage hotels and staff',
                'scope' => 'system',
                'is_default' => true,
            ],
        ];

        $defaultStaffRoles = [
            [
                'name' => 'Manager',
                'slug' => 'manager',
                'description' => 'Hotel manager with full hotel management permissions',
                'scope' => 'system',
                'is_default' => false,
            ],
            [
                'name' => 'Receptionist',
                'slug' => 'receptionist',
                'description' => 'Front desk staff for check-in/check-out and reservations',
                'scope' => 'system',
                'is_default' => false,
            ],
            [
                'name' => 'Housekeeping',
                'slug' => 'housekeeping',
                'description' => 'Housekeeping staff for room status updates',
                'scope' => 'system',
                'is_default' => false,
            ],
        ];

        // Create system roles
        foreach ($systemRoles as $roleData) {
            Role::updateOrCreate(
                ['slug' => $roleData['slug'], 'scope' => 'system'],
                array_merge($roleData, [
                    'created_by' => $superAdmin->id,
                    'hotel_owner_id' => null,
                ])
            );
        }

        // Create default staff roles
        foreach ($defaultStaffRoles as $roleData) {
            Role::updateOrCreate(
                ['slug' => $roleData['slug'], 'scope' => 'system'],
                array_merge($roleData, [
                    'created_by' => $superAdmin->id,
                    'hotel_owner_id' => null,
                ])
            );
        }

        $this->command->info('Roles seeded successfully!');
    }
}

