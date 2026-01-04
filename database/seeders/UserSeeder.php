<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Super Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@school.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        // 2. Teacher
        $teacher = User::firstOrCreate(
            ['email' => 'teacher@school.com'],
            [
                'name' => 'Teacher User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $teacher->assignRole('teacher');

        // 3. Student
        $student = User::firstOrCreate(
            ['email' => 'student@school.com'],
            [
                'name' => 'Student User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $student->assignRole('student');
    }
}
