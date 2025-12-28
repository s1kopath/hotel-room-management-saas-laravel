<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SuperAdminSeeder::class,
            RoleSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
            SystemSettingSeeder::class,
            // UserSeeder should run after roles are created
            UserSeeder::class,
        ]);

        $this->command->info('Database seeding completed successfully!');
    }
}
