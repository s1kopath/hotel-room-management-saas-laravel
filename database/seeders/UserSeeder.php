<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get roles
        $hotelOwnerRole = Role::where('slug', 'hotel-owner')->first();
        $managerRole = Role::where('slug', 'manager')->first();
        $receptionistRole = Role::where('slug', 'receptionist')->first();
        $housekeepingRole = Role::where('slug', 'housekeeping')->first();

        if (!$hotelOwnerRole) {
            $this->command->warn('Hotel Owner role not found. Please run RoleSeeder first.');
            return;
        }

        // Create 3 Hotel Owners
        $this->command->info('Creating hotel owners...');
        $hotelOwners = User::factory()
            ->count(3)
            ->hotelOwner()
            ->create();

        // Assign Hotel Owner role to all hotel owners
        foreach ($hotelOwners as $owner) {
            $owner->roles()->attach($hotelOwnerRole->id, [
                'assigned_by' => $owner->id,
                'assigned_at' => now(),
            ]);
        }

        $this->command->info("Created {$hotelOwners->count()} hotel owners.");

        // Create staff for each hotel owner
        foreach ($hotelOwners as $owner) {
            $this->command->info("Creating staff for hotel owner: {$owner->full_name}...");

            // Create 2 managers
            $managers = User::factory()
                ->count(2)
                ->staff($owner->id)
                ->createdBy($owner)
                ->create();

            foreach ($managers as $manager) {
                $manager->roles()->attach($managerRole->id, [
                    'assigned_by' => $owner->id,
                    'assigned_at' => now(),
                ]);
            }

            // Create 3 receptionists
            $receptionists = User::factory()
                ->count(3)
                ->staff($owner->id)
                ->createdBy($owner)
                ->create();

            foreach ($receptionists as $receptionist) {
                $receptionist->roles()->attach($receptionistRole->id, [
                    'assigned_by' => $owner->id,
                    'assigned_at' => now(),
                ]);
            }

            // Create 5 housekeeping staff
            $housekeeping = User::factory()
                ->count(5)
                ->staff($owner->id)
                ->createdBy($owner)
                ->create();

            foreach ($housekeeping as $staff) {
                $staff->roles()->attach($housekeepingRole->id, [
                    'assigned_by' => $owner->id,
                    'assigned_at' => now(),
                ]);
            }

            $this->command->info("Created 2 managers, 3 receptionists, and 5 housekeeping staff for {$owner->full_name}.");
        }

        // Create some suspended users for testing
        $this->command->info('Creating suspended users for testing...');
        User::factory()
            ->count(2)
            ->staff()
            ->suspended()
            ->create();

        // Create some deleted users for testing
        $this->command->info('Creating deleted users for testing...');
        User::factory()
            ->count(1)
            ->staff()
            ->deleted()
            ->create();

        $totalUsers = User::count();
        $this->command->info("User seeding completed! Total users: {$totalUsers}");
        $this->command->info("Breakdown:");
        $this->command->info("- Super Admins: " . User::where('user_type', 'super_admin')->count());
        $this->command->info("- Hotel Owners: " . User::where('user_type', 'hotel_owner')->count());
        $this->command->info("- Staff: " . User::where('user_type', 'staff')->count());
        $this->command->info("- Active: " . User::where('status', 'active')->count());
        $this->command->info("- Suspended: " . User::where('status', 'suspended')->count());
        $this->command->info("- Deleted: " . User::where('status', 'deleted')->count());
    }
}

