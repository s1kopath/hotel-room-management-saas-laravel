<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if super admin already exists
        $superAdmin = User::where('user_type', 'super_admin')->first();

        if (!$superAdmin) {
            $superAdmin = User::create([
                'username' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('pa$$word'), // Change this in production!
                'full_name' => 'Super Administrator',
                'user_type' => 'super_admin',
                'status' => 'active',
                'created_by' => null, // First user, no creator
            ]);

            $this->command->info('Super Admin created successfully!');
            $this->command->info('Username: admin');
            $this->command->info('Email: admin@gmail.com');
            $this->command->warn('Password: pa$$word (Please change this in production!)');
        } else {
            $this->command->info('Super Admin already exists.');
        }
    }
}

