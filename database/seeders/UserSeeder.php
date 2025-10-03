<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $primaryAdmin = User::create([
            'user_id' => User::generateIdentifier('ADM'),
            'user_name' => 'contentmatch.super',
            'name' => 'ContentMatch Super Admin',
            'full_name' => 'ContentMatch Super Admin',
            'email' => 'admin@contentmatch.io',
            'mobile_no' => '+234000000001',
            'password' => Hash::make('SuperSecure#123'),
            'unencrypted_password' => 'SuperSecure#123',
            'role' => 'super-admin',
            'title' => 'Head of Platform',
            'is_admin' => true,
            'region_name' => 'Global',
            'branch_description' => 'HQ',
            'activation_code' => Str::uuid(),
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        User::create([
            'user_id' => User::generateIdentifier('USR'),
            'user_name' => 'creator.first',
            'name' => 'First Creator',
            'full_name' => 'First Creator',
            'email' => 'creator@contentmatch.io',
            'mobile_no' => '+234000000002',
            'password' => Hash::make('Creator#123'),
            'unencrypted_password' => 'Creator#123',
            'role' => 'member',
            'title' => 'Community Curator',
            'is_admin' => false,
            'region_name' => 'Creator Hub',
            'branch_description' => 'Digital',
            'activation_code' => Str::uuid(),
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command?->info('Seeded ContentMatch admin user: admin@contentmatch.io (SuperSecure#123)');
        $this->command?->info('Seeded ContentMatch member: creator@contentmatch.io (Creator#123)');
    }
}