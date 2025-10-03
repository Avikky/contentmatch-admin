<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('hashtags', function (Blueprint $table) {
			$table->id();
			$table->string('tag')->unique();
			$table->unsignedInteger('usage_count')->default(0);
			$table->timestamps();
		});

		Schema::create('community_hashtag', function (Blueprint $table) {
			$table->id();
			$table->foreignId('community_id')->constrained()->cascadeOnDelete();
			$table->foreignId('hashtag_id')->constrained()->cascadeOnDelete();
			$table->timestamps();

			$table->unique(['community_id', 'hashtag_id']);
		});

		Schema::create('engagement_metrics', function (Blueprint $table) {
			$table->id();
			$table->foreignId('community_id')->nullable()->constrained()->nullOnDelete();
			$table->date('recorded_for');
			$table->unsignedInteger('posts')->default(0);
			$table->unsignedInteger('comments')->default(0);
			$table->unsignedInteger('likes')->default(0);
			$table->unsignedInteger('shares')->default(0);
			$table->unsignedBigInteger('impressions')->default(0);
			$table->timestamps();

			$table->unique(['community_id', 'recorded_for']);
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('engagement_metrics');
		Schema::dropIfExists('community_hashtag');
		Schema::dropIfExists('hashtags');
	}
};
