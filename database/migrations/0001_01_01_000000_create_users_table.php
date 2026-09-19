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
            $table->string('name');
            $table->string('avatar')->nullable();
            // Nullable: a team member can be added directly by an admin/
            // manager (no email yet) before they're invited to claim the
            // account - see Invitation/InvitationController::storeMember().
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            // Null until this person has actually set their own password and
            // logged in - distinguishes a "shell" record added purely to
            // track from a real, self-serve account. See User::isClaimed().
            $table->timestamp('claimed_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->boolean('deleted')->default(false);
            // Captured automatically from the browser - see
            // CaptureUserTimezone middleware.
            $table->string('timezone')->nullable();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
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
