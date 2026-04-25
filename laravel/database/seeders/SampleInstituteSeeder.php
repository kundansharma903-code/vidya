<?php

namespace Database\Seeders;

use App\Models\Institute;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleInstituteSeeder extends Seeder
{
    public function run(): void
    {
        $institute = Institute::create([
            'name'              => 'ABC Coaching Institute',
            'code'              => 'ABC-JAI',
            'address'           => 'Jaipur, Rajasthan',
            'phone'             => '9876543210',
            'email'             => 'info@abccoaching.in',
            'timezone'          => 'Asia/Kolkata',
            'is_active'         => true,
            'subscription_tier' => 'trial',
            'student_limit'     => 500,
        ]);

        $users = [
            ['name' => 'Rajesh Owner',    'username' => 'owner',        'email' => 'owner@abccoaching.in',    'role' => 'owner'],
            ['name' => 'Priya Head',      'username' => 'academic_head','email' => 'head@abccoaching.in',     'role' => 'academic_head'],
            ['name' => 'Amit Admin',      'username' => 'admin',        'email' => 'admin@abccoaching.in',    'role' => 'admin'],
            ['name' => 'Suresh SubAdmin', 'username' => 'subadmin',     'email' => 'subadmin@abccoaching.in', 'role' => 'sub_admin'],
            ['name' => 'Kavita Teacher',  'username' => 'teacher',      'email' => 'teacher@abccoaching.in',  'role' => 'teacher'],
            ['name' => 'Ravi Typist',     'username' => 'typist',       'email' => 'typist@abccoaching.in',   'role' => 'typist'],
        ];

        foreach ($users as $userData) {
            User::create([
                'institute_id' => $institute->id,
                'name'         => $userData['name'],
                'username'     => $userData['username'],
                'email'        => $userData['email'],
                'password'     => Hash::make('password123'),
                'role'         => $userData['role'],
                'is_active'    => true,
            ]);
        }

        $this->command->info('✅ Sample institute + 6 users created (password: password123)');
    }
}
