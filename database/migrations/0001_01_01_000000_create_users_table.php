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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50)->unique();
            $table->string('email', 100)->unique();
            $table->string('password'); // Laravel uses 'password' by default, but schema shows 'password_hash'
            $table->string('full_name', 200)->nullable();
            $table->string('phone', 20)->nullable();
            $table->enum('user_type', ['super_admin', 'hotel_owner', 'staff'])->default('staff');
            $table->unsignedBigInteger('parent_user_id')->nullable();
            $table->enum('status', ['active', 'suspended', 'deleted'])->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->rememberToken();
            $table->timestamps();

            // Indexes
            $table->index('username');
            $table->index('email');
            $table->index('user_type');
            $table->index('status');
            $table->index('parent_user_id');
        });

        // Add self-referencing foreign keys after table creation
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('parent_user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token')->nullable();
            $table->string('otp', 6);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->integer('failed_attempts')->default(0);
            $table->integer('resent_count')->default(0);
            $table->timestamp('suspend_duration')->nullable();
            $table->timestamp('expires_at')->useCurrent();
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
