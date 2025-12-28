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
        Schema::create('admin_reservations_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations')->onDelete('cascade');
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->enum('action_type', ['created', 'modified', 'released']);
            $table->timestamp('action_at')->useCurrent();
            $table->text('notes')->nullable();
            $table->string('archive_month', 7)->nullable(); // YYYY-MM format

            // Indexes
            $table->index('reservation_id');
            $table->index('admin_id');
            $table->index('action_at');
            $table->index('archive_month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_reservations_history');
    }
};

