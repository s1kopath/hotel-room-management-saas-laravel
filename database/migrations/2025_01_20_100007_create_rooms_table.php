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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->onDelete('cascade');
            $table->string('room_number', 50);
            $table->string('room_type', 100)->nullable();
            $table->integer('floor_number')->nullable();
            $table->integer('capacity')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['vacant', 'reserved', 'occupied', 'admin_reserved'])->default('vacant');
            $table->timestamp('last_status_change')->useCurrent();
            $table->foreignId('status_updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Indexes
            $table->index('hotel_id');
            $table->index('status');
            $table->index('room_number');
            $table->unique(['hotel_id', 'room_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};

