<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add composite index for user_hotel_access (optimizes hasAccessToHotel queries)
        Schema::table('user_hotel_access', function (Blueprint $table) {
            $table->index(['user_id', 'hotel_id', 'is_active'], 'idx_user_hotel_access_user_hotel_active');
        });

        // Add index for activity_logs entity lookups
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index(['entity_type', 'entity_id'], 'idx_activity_logs_entity');
        });

        // Add index for room status history queries
        Schema::table('room_status_history', function (Blueprint $table) {
            $table->index(['room_id', 'changed_at'], 'idx_room_status_history_room_changed');
        });

        // Add index for reservations date range queries
        Schema::table('reservations', function (Blueprint $table) {
            $table->index(['status', 'check_in_date', 'check_out_date'], 'idx_reservations_status_dates');
        });

        // Add index for guests search queries
        Schema::table('guests', function (Blueprint $table) {
            $table->index(['hotel_owner_id', 'vip_status'], 'idx_guests_owner_vip');
        });

        // Add index for roles scope queries
        Schema::table('roles', function (Blueprint $table) {
            $table->index(['scope', 'hotel_owner_id'], 'idx_roles_scope_owner');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_hotel_access', function (Blueprint $table) {
            $table->dropIndex('idx_user_hotel_access_user_hotel_active');
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex('idx_activity_logs_entity');
        });

        Schema::table('room_status_history', function (Blueprint $table) {
            $table->dropIndex('idx_room_status_history_room_changed');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropIndex('idx_reservations_status_dates');
        });

        Schema::table('guests', function (Blueprint $table) {
            $table->dropIndex('idx_guests_owner_vip');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropIndex('idx_roles_scope_owner');
        });
    }
};
