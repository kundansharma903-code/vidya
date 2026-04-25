<?php

namespace Database\Seeders;

use App\Models\Institute;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
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

        User::create([
            'institute_id' => $institute->id,
            'name'         => 'Shri Sita Ram',
            'username'     => 'Shrisitaram',
            'email'        => 'shrisitaram@abccoaching.com',
            'password'     => Hash::make('password123'),
            'role'         => 'admin',
            'is_active'    => true,
        ]);

        $this->command->info('✅ Admin user created: username=Shrisitaram / password=password123');
    }
}
