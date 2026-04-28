<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $institute = DB::table('institutes')->where('code', 'ABC-JAI')->first();

        if (!$institute) {
            $instituteId = DB::table('institutes')->insertGetId([
                'name'              => 'ABC Coaching Institute',
                'code'              => 'ABC-JAI',
                'address'           => 'Jaipur, Rajasthan',
                'phone'             => '9876543210',
                'email'             => 'info@abccoaching.in',
                'timezone'          => 'Asia/Kolkata',
                'is_active'         => 1,
                'subscription_tier' => 'trial',
                'student_limit'     => 500,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        } else {
            $instituteId = $institute->id;
        }

        $users = [
            ['name' => 'Kavish Sharma',    'username' => 'kavish101',      'email' => 'kavish@abccoaching.com',     'role' => 'admin'],
            ['name' => 'Priya Sharma',     'username' => 'priya_sharma',   'email' => 'priya@abccoaching.com',      'role' => 'sub_admin'],
            ['name' => 'Amit Gupta',       'username' => 'amit_gupta',     'email' => 'amit@abccoaching.com',       'role' => 'teacher'],
            ['name' => 'Meera Krishnan',   'username' => 'meera_krishnan', 'email' => 'meera@abccoaching.com',      'role' => 'academic_head'],
            ['name' => 'Sanjay Agarwal',   'username' => 'sanjay_agarwal', 'email' => 'sanjay@abccoaching.com',    'role' => 'owner'],
            ['name' => 'Neha Verma',       'username' => 'neha_verma',     'email' => 'neha@abccoaching.com',       'role' => 'reception'],
        ];

        foreach ($users as $u) {
            $exists = DB::table('users')->where('username', $u['username'])->exists();
            if (!$exists) {
                DB::table('users')->insert(array_merge($u, [
                    'institute_id' => $instituteId,
                    'password'     => Hash::make('password123'),
                    'is_active'    => 1,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]));
                $this->command->info("  Created [{$u['role']}] {$u['username']}");
            } else {
                $this->command->line("  Skipped [{$u['role']}] {$u['username']} (already exists)");
            }
        }

        $this->command->info('Admin seeder complete. Password for all: password123');
    }
}
