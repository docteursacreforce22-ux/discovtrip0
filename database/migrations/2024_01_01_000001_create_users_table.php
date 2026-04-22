<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            
            // Identity
            $table->string('first_name', 50);
            $table->string('last_name', 50)->nullable();
            $table->string('email', 254)->unique();
            $table->string('phone', 20)->nullable();
            $table->string('password');
            
            // Role & Status
            $table->enum('role', ['admin', 'moderator', 'partner', 'client'])->default('client');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_banned')->default(false);
            $table->string('ban_reason')->nullable();
            $table->timestamp('banned_at')->nullable();
            
            // Email Verification
            $table->boolean('email_verified')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('email_verification_token')->nullable();
            $table->timestamp('email_verification_token_expires_at')->nullable();
            
            // Password Reset
            $table->string('password_reset_token')->nullable();
            $table->timestamp('password_reset_token_expires_at')->nullable();
            $table->timestamp('last_password_change_at')->nullable();
            
            // Two-Factor Authentication
            $table->boolean('two_factor_enabled')->default(false);
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable(); // JSON
            $table->timestamp('two_factor_enabled_at')->nullable();
            
            // Sessions & Login
            $table->string('current_session_id')->nullable();
            $table->json('active_sessions')->nullable();
            $table->string('remember_token')->nullable();
            $table->timestamp('remember_token_expires_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->string('last_login_user_agent')->nullable();
            
            // Security
            $table->integer('failed_login_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            
            // Preferences
            $table->string('locale', 5)->default('fr');
            $table->string('timezone', 50)->default('UTC');
            $table->string('currency', 3)->nullable();
            $table->json('notification_preferences')->nullable();
            $table->json('privacy_settings')->nullable();
            
            // Profile
            $table->string('profile_picture')->nullable();
            $table->text('bio')->nullable();
            $table->date('birthday')->nullable();
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable();
            
            // Address (embedded)
            $table->string('address_street')->nullable();
            $table->string('address_city')->nullable();
            $table->string('address_postal_code')->nullable();
            $table->string('address_country')->nullable();
            $table->string('address_state')->nullable();
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('email');
            $table->index('role');
            $table->index(['is_active', 'is_banned']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
