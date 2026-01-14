<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create super admin for initial setup
        Admin::updateOrCreate(
            ['email' => 'superadmin@contentmatch.test'],
            [
                'full_name' => 'Super Admin',
                'email' => 'anihvictor57@gmail.com',
                'password' => Hash::make('password'), // Change this in production!
                'role' => 'superadmin',
                'is_active' => true,
            ]
        );

        // Create regular admin for testing
        Admin::updateOrCreate(
            ['email' => 'admin@contentmatch.test'],
            [
                'full_name' => 'Test Admin',
                'email' => 'admin@contentmatch.test',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
                'created_by' => 1,
            ]
        );

        // Create moderator for testing
        Admin::updateOrCreate(
            ['email' => 'moderator@contentmatch.test'],
            [
                'full_name' => 'Test Moderator',
                'email' => 'moderator@contentmatch.test',
                'password' => Hash::make('password'),
                'role' => 'moderator',
                'is_active' => true,
                'created_by' => 1,
            ]
        );
    }
}
