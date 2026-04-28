<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProductionAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Institute — skip if already exists
        $institute = DB::table('institutes')->where('code', 'AAYAM-SIK')->first();

        if (!$institute) {
            $instituteId = DB::table('institutes')->insertGetId([
                'name'              => 'Aayam Sikar',
                'code'              => 'AAYAM-SIK',
                'address'           => 'Sikar, Rajasthan',
                'phone'             => '',
                'email'             => 'info@aayamsikar.in',
                'timezone'          => 'Asia/Kolkata',
                'is_active'         => 1,
                'subscription_tier' => 'trial',
                'student_limit'     => 1000,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
            $this->command->info('  Institute created: Aayam Sikar');
        } else {
            $instituteId = $institute->id;
            $this->command->line('  Institute already exists — skipped');
        }

        // Admin user — skip if already exists
        $exists = DB::table('users')->where('username', 'kavish101')->exists();

        if (!$exists) {
            DB::table('users')->insert([
                'institute_id' => $instituteId,
                'name'         => 'Kavish Sharma',
                'username'     => 'kavish101',
                'email'        => 'kavish@aayamsikar.in',
                'password'     => Hash::make('password123'),
                'role'         => 'admin',
                'is_active'    => 1,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
            $this->command->info('  Admin created: kavish101 / password123');
        } else {
            $this->command->line('  Admin kavish101 already exists — skipped');
        }

        $this->command->info('Production seed complete. Login: kavish101 / password123');
    }
}
