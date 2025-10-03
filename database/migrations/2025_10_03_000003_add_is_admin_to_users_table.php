<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::table('users', function (Blueprint $table) {
			$table->boolean('is_admin')->default(false)->after('remember_token');
			$table->string('role', 50)->nullable()->after('is_admin');
			$table->string('title', 100)->nullable()->after('role');
			$table->string('avatar_path')->nullable()->after('title');
			$table->text('bio')->nullable()->after('avatar_path');
		});
	}

	public function down(): void
	{
		Schema::table('users', function (Blueprint $table) {
			$table->dropColumn(['is_admin', 'role', 'title', 'avatar_path', 'bio']);
		});
	}
};
