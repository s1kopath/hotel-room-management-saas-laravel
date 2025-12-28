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
        Schema::create('admin_reservations_archive', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('original_history_id')->nullable();
            $table->unsignedBigInteger('reservation_id')->nullable();
            $table->unsignedBigInteger('room_id')->nullable();
            $table->unsignedBigInteger('hotel_id')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->enum('action_type', ['created', 'modified', 'released'])->nullable();
            $table->timestamp('action_at')->nullable();
            $table->string('archive_month', 7); // YYYY-MM format
            $table->timestamp('archived_at')->useCurrent();
            $table->text('notes')->nullable();

            // Indexes
            $table->index('archive_month');
            $table->index('reservation_id');
            $table->index('room_id');
            $table->index('hotel_id');
            $table->index('archived_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_reservations_archive');
    }
};

