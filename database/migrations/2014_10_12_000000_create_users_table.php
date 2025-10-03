<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            // Primary key - BIGINT IDENTITY(1,1)
            $table->bigIncrements('id');
            
            // User identification fields
            $table->string('user_id', 255);
            $table->string('password', 255)->nullable();
            $table->string('name', 100)->nullable();
            $table->string('full_name', 255)->nullable();
            $table->string('user_name', 255)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('mobile_no', 15)->nullable();

            // Region and branch information
            $table->string('region_code', 20)->nullable();
            $table->string('region_name', 50)->nullable();
            $table->string('branch_code', 20)->nullable();
            $table->string('branch_description', 50)->nullable();

            // Security fields
            $table->string('pin', 255)->nullable();
            $table->string('company_name', 10)->nullable();
            $table->string('activation_code', 255)->nullable();

            // Date fields - using datetime to match SQL Server datetime
            $table->dateTime('expiration_date')->nullable();
            $table->dateTime('password_expire_date')->nullable();
            
            // Lock status - tinyint with default 0
            $table->tinyInteger('is_locked')->nullable()->default(0);
            
            // Password history fields
            $table->string('old_password', 30)->nullable();
            $table->string('new_password_1', 30)->nullable();
            $table->string('new_password_2', 30)->nullable();
            $table->string('unencrypted_password', 255)->nullable();

            // Laravel authentication fields
            $table->dateTime('email_verified_at')->nullable();
            $table->string('remember_token', 100)->nullable();

            // Timestamps - using datetime with default current timestamp
            $table->dateTime('created_at')->nullable()->useCurrent();
            $table->dateTime('updated_at')->nullable()->useCurrent();
            
            // Indexes for performance
            $table->unique('user_id', 'IX_users_user_id');
            $table->index('email', 'IX_users_email');
            $table->index('user_name', 'IX_users_user_name');
        });
        
        // Add trigger to automatically update updated_at column (SQL Server style)
        if (DB::getDriverName() === 'main') {
            DB::unprepared("
                CREATE TRIGGER TR_users_updated_at 
                ON users 
                AFTER UPDATE
                AS
                BEGIN
                    SET NOCOUNT ON;
                    UPDATE users 
                    SET updated_at = GETDATE() 
                    FROM users u
                    INNER JOIN inserted i ON u.id = i.id
                END
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop trigger if it exists (SQL Server)
        if (DB::getDriverName() === 'main') {
            DB::unprepared("
                IF OBJECT_ID('TR_users_updated_at', 'TR') IS NOT NULL
                    DROP TRIGGER TR_users_updated_at
            ");
        }
        
        Schema::dropIfExists('users');
    }
};
