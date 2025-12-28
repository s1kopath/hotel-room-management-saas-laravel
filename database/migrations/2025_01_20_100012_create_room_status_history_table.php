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
        Schema::create('room_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->enum('previous_status', ['vacant', 'reserved', 'occupied', 'admin_reserved'])->nullable();
            $table->enum('new_status', ['vacant', 'reserved', 'occupied', 'admin_reserved']);
            $table->foreignId('changed_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('changed_at')->useCurrent();
            $table->text('notes')->nullable();

            // Indexes
            $table->index('room_id');
            $table->index('changed_at');
            $table->index('changed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_status_history');
    }
};

