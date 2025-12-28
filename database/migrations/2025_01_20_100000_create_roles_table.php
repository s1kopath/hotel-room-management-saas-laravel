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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100);
            $table->text('description')->nullable();
            $table->enum('scope', ['system', 'hotel_owner'])->default('hotel_owner');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('hotel_owner_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            // Indexes
            $table->index('slug');
            $table->index('scope');
            $table->index('hotel_owner_id');
            $table->unique(['slug', 'hotel_owner_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};

