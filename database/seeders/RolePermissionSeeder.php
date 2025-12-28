<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin Role - All permissions
        $superAdminRole = Role::where('slug', 'super-admin')->first();
        if ($superAdminRole) {
            $allPermissions = Permission::all();
            $superAdminRole->permissions()->sync($allPermissions->pluck('id'));
            $this->command->info('Super Admin role assigned all permissions.');
        }

        // Hotel Owner Role
        $hotelOwnerRole = Role::where('slug', 'hotel-owner')->first();
        if ($hotelOwnerRole) {
            $hotelOwnerPermissions = Permission::whereIn('name', [
                'users.create',
                'users.edit',
                'users.delete',
                'users.view-own',
                'hotels.create',
                'hotels.edit-own',
                'hotels.delete-own',
                'hotels.view-own',
                'rooms.create',
                'rooms.edit',
                'rooms.delete',
                'rooms.change-status',
                'guests.create',
                'guests.edit',
                'guests.view-own',
                'reservations.create',
                'reservations.edit-own',
                'reservations.cancel',
                'reservations.checkin',
                'reservations.checkout',
                'reservations.view-own',
                'payments.receive',
                'payments.view',
                'roles.create',
                'roles.edit-own',
                'roles.delete-own',
                'permissions.assign',
                'reports.view-own',
            ])->pluck('id');
            $hotelOwnerRole->permissions()->sync($hotelOwnerPermissions);
            $this->command->info('Hotel Owner role permissions assigned.');
        }

        // Manager Role
        $managerRole = Role::where('slug', 'manager')->first();
        if ($managerRole) {
            $managerPermissions = Permission::whereIn('name', [
                'hotels.view-own',
                'rooms.create',
                'rooms.edit',
                'rooms.delete',
                'rooms.change-status',
                'guests.create',
                'guests.edit',
                'guests.view-own',
                'reservations.create',
                'reservations.edit-own',
                'reservations.cancel',
                'reservations.checkin',
                'reservations.checkout',
                'reservations.view-own',
                'payments.receive',
                'payments.view',
                'reports.view-own',
            ])->pluck('id');
            $managerRole->permissions()->sync($managerPermissions);
            $this->command->info('Manager role permissions assigned.');
        }

        // Receptionist Role
        $receptionistRole = Role::where('slug', 'receptionist')->first();
        if ($receptionistRole) {
            $receptionistPermissions = Permission::whereIn('name', [
                'hotels.view-own',
                'rooms.view-own',
                'rooms.change-status',
                'guests.create',
                'guests.edit',
                'guests.view-own',
                'reservations.create',
                'reservations.view-own',
                'reservations.checkin',
                'reservations.checkout',
                'payments.receive',
            ])->pluck('id');
            $receptionistRole->permissions()->sync($receptionistPermissions);
            $this->command->info('Receptionist role permissions assigned.');
        }

        // Housekeeping Role
        $housekeepingRole = Role::where('slug', 'housekeeping')->first();
        if ($housekeepingRole) {
            $housekeepingPermissions = Permission::whereIn('name', [
                'rooms.view-own',
                'rooms.change-status',
            ])->pluck('id');
            $housekeepingRole->permissions()->sync($housekeepingPermissions);
            $this->command->info('Housekeeping role permissions assigned.');
        }

        $this->command->info('Role permissions assigned successfully!');
    }
}

