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
            $table->string('name', 100)->nullable();
            $table->string('display_name', 150)->nullable();

            $table->string('email', 191)->unique();
            $table->string('user_name', 80)->unique();
            $table->string('phone', 30)->nullable();

            $table->string('password');
            $table->timestamp('password_changed_at')->nullable();
            $table->boolean('force_password_reset')->default(false);

            $table->string('role', 50)->enum(['admin', 'superadmin, moderator'])->default('admin');
            $table->unsignedTinyInteger('role_weight')->default(10);
            $table->json('permissions')->nullable();

            $table->enum('status', ['pending', 'active', 'suspended', 'disabled'])->default('pending');
            $table->timestamp('email_verified_at')->nullable();

            $table->string('avatar_path', 255)->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['role', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
