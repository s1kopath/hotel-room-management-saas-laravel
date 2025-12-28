<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // User Management
            ['name' => 'users.create', 'display_name' => 'Create Users', 'category' => 'users', 'description' => 'Create new user accounts'],
            ['name' => 'users.edit', 'display_name' => 'Edit Users', 'category' => 'users', 'description' => 'Edit user details'],
            ['name' => 'users.delete', 'display_name' => 'Delete Users', 'category' => 'users', 'description' => 'Delete user accounts'],
            ['name' => 'users.view-all', 'display_name' => 'View All Users', 'category' => 'users', 'description' => 'View all system users'],
            ['name' => 'users.view-own', 'display_name' => 'View Own Users', 'category' => 'users', 'description' => 'View users created by self'],

            // Hotel Management
            ['name' => 'hotels.create', 'display_name' => 'Create Hotels', 'category' => 'hotels', 'description' => 'Create new hotels'],
            ['name' => 'hotels.edit-own', 'display_name' => 'Edit Own Hotels', 'category' => 'hotels', 'description' => 'Edit hotels owned by self'],
            ['name' => 'hotels.edit-all', 'display_name' => 'Edit All Hotels', 'category' => 'hotels', 'description' => 'Edit any hotel (admin only)'],
            ['name' => 'hotels.delete-own', 'display_name' => 'Delete Own Hotels', 'category' => 'hotels', 'description' => 'Delete hotels owned by self'],
            ['name' => 'hotels.view-all', 'display_name' => 'View All Hotels', 'category' => 'hotels', 'description' => 'View all hotels in system'],
            ['name' => 'hotels.view-own', 'display_name' => 'View Own Hotels', 'category' => 'hotels', 'description' => 'View only own hotels'],

            // Room Management
            ['name' => 'rooms.create', 'display_name' => 'Create Rooms', 'category' => 'rooms', 'description' => 'Add new rooms'],
            ['name' => 'rooms.edit', 'display_name' => 'Edit Rooms', 'category' => 'rooms', 'description' => 'Edit room details'],
            ['name' => 'rooms.delete', 'display_name' => 'Delete Rooms', 'category' => 'rooms', 'description' => 'Delete rooms'],
            ['name' => 'rooms.change-status', 'display_name' => 'Change Room Status', 'category' => 'rooms', 'description' => 'Change room availability status'],
            ['name' => 'rooms.admin-reserve', 'display_name' => 'Admin Reserve Rooms', 'category' => 'rooms', 'description' => 'Make admin override reservations (blue status)'],
            ['name' => 'rooms.view-own', 'display_name' => 'View Own Rooms', 'category' => 'rooms', 'description' => 'View rooms in assigned hotels'],

            // Guest Management
            ['name' => 'guests.create', 'display_name' => 'Create Guests', 'category' => 'guests', 'description' => 'Add new guests'],
            ['name' => 'guests.edit', 'display_name' => 'Edit Guests', 'category' => 'guests', 'description' => 'Edit guest information'],
            ['name' => 'guests.delete', 'display_name' => 'Delete Guests', 'category' => 'guests', 'description' => 'Delete guest records'],
            ['name' => 'guests.view-own', 'display_name' => 'View Own Guests', 'category' => 'guests', 'description' => 'View guests in own hotels'],
            ['name' => 'guests.view-all', 'display_name' => 'View All Guests', 'category' => 'guests', 'description' => 'View all guests (admin)'],

            // Reservation Management
            ['name' => 'reservations.create', 'display_name' => 'Create Reservations', 'category' => 'reservations', 'description' => 'Create new reservations'],
            ['name' => 'reservations.edit-own', 'display_name' => 'Edit Own Reservations', 'category' => 'reservations', 'description' => 'Edit reservations in own hotels'],
            ['name' => 'reservations.edit-all', 'display_name' => 'Edit All Reservations', 'category' => 'reservations', 'description' => 'Edit any reservation (admin)'],
            ['name' => 'reservations.cancel', 'display_name' => 'Cancel Reservations', 'category' => 'reservations', 'description' => 'Cancel reservations'],
            ['name' => 'reservations.checkin', 'display_name' => 'Check In Guests', 'category' => 'reservations', 'description' => 'Check guests in'],
            ['name' => 'reservations.checkout', 'display_name' => 'Check Out Guests', 'category' => 'reservations', 'description' => 'Check guests out'],
            ['name' => 'reservations.view-own', 'display_name' => 'View Own Reservations', 'category' => 'reservations', 'description' => 'View reservations in own hotels'],
            ['name' => 'reservations.view-all', 'display_name' => 'View All Reservations', 'category' => 'reservations', 'description' => 'View all reservations (admin)'],
            ['name' => 'reservations.override', 'display_name' => 'Admin Override Reservations', 'category' => 'reservations', 'description' => 'Create admin override reservations'],

            // Payment Management
            ['name' => 'payments.receive', 'display_name' => 'Receive Payments', 'category' => 'payments', 'description' => 'Receive payments from guests'],
            ['name' => 'payments.refund', 'display_name' => 'Process Refunds', 'category' => 'payments', 'description' => 'Process payment refunds'],
            ['name' => 'payments.view', 'display_name' => 'View Payments', 'category' => 'payments', 'description' => 'View payment history'],

            // Reports
            ['name' => 'reports.view-own', 'display_name' => 'View Own Reports', 'category' => 'reports', 'description' => 'View reports for own hotels'],
            ['name' => 'reports.view-all', 'display_name' => 'View All Reports', 'category' => 'reports', 'description' => 'View all system reports'],

            // System Management
            ['name' => 'system.settings', 'display_name' => 'Manage System Settings', 'category' => 'system', 'description' => 'Manage system-wide settings'],
            ['name' => 'system.logs', 'display_name' => 'View Activity Logs', 'category' => 'system', 'description' => 'View system activity logs'],

            // Role & Permission Management
            ['name' => 'roles.create', 'display_name' => 'Create Roles', 'category' => 'roles', 'description' => 'Create custom roles'],
            ['name' => 'roles.edit-own', 'display_name' => 'Edit Own Roles', 'category' => 'roles', 'description' => 'Edit custom roles created by self'],
            ['name' => 'roles.delete-own', 'display_name' => 'Delete Own Roles', 'category' => 'roles', 'description' => 'Delete custom roles created by self'],
            ['name' => 'permissions.assign', 'display_name' => 'Assign Permissions', 'category' => 'roles', 'description' => 'Assign permissions to roles'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }

        $this->command->info('Permissions seeded successfully!');
    }
}

