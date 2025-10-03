<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('communities', function (Blueprint $table) {
			$table->id();
			$table->string('name');
			$table->string('slug')->unique();
			$table->string('avatar_path')->nullable();
			$table->string('category')->nullable();
			$table->text('description')->nullable();
			$table->enum('visibility', ['public', 'private'])->default('public');
			$table->enum('status', ['active', 'archived'])->default('active');
			$table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
			$table->timestamps();
		});

		Schema::create('community_user', function (Blueprint $table) {
			$table->id();
			$table->foreignId('community_id')->constrained()->cascadeOnDelete();
			$table->foreignId('user_id')->constrained()->cascadeOnDelete();
			$table->enum('role', ['member', 'moderator', 'owner'])->default('member');
			$table->timestamps();

			$table->unique(['community_id', 'user_id']);
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('community_user');
		Schema::dropIfExists('communities');
	}
};
