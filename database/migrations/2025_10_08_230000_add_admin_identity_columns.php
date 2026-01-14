<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'external_id')) {
                $table->uuid('external_id')->after('id');
                $table->unique('external_id');
            }

            if (! Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name', 100)->nullable()->after('external_id');
            }

            if (! Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name', 100)->nullable()->after('first_name');
            }

            if (! Schema::hasColumn('users', 'invitation_token')) {
                $table->string('invitation_token', 64)->nullable()->unique()->after('last_login_ip');
            }

            if (! Schema::hasColumn('users', 'invitation_expires_at')) {
                $table->timestamp('invitation_expires_at')->nullable()->after('invitation_token');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'invitation_expires_at')) {
                $table->dropColumn('invitation_expires_at');
            }

            if (Schema::hasColumn('users', 'invitation_token')) {
                $table->dropUnique(['invitation_token']);
                $table->dropColumn('invitation_token');
            }

            if (Schema::hasColumn('users', 'last_name')) {
                $table->dropColumn('last_name');
            }

            if (Schema::hasColumn('users', 'first_name')) {
                $table->dropColumn('first_name');
            }

            if (Schema::hasColumn('users', 'external_id')) {
                $table->dropUnique(['external_id']);
                $table->dropColumn('external_id');
            }
        });
    }
};
