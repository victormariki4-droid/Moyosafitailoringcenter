<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        // 1. Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@school.com'],
            ['name' => 'Admin User', 'password' => bcrypt('password')]
        );
        $admin->assignRole('admin');

        // 2. Teacher
        $teacher = User::firstOrCreate(
            ['email' => 'teacher@school.com'],
            ['name' => 'Teacher User', 'password' => bcrypt('password')]
        );
        $teacher->assignRole('teacher');

        // 3. Student
        $studentUser = User::firstOrCreate(
            ['email' => 'student@school.com'],
            ['name' => 'Student User', 'password' => bcrypt('password')]
        );
        $studentUser->assignRole('student');

        // Link User to Student Profile
        if (! \App\Models\Student::where('user_id', $studentUser->id)->exists()) {
            \App\Models\Student::create([
                'user_id' => $studentUser->id,
                'first_name' => 'Student',
                'last_name' => 'User',
                'intake_year' => date('Y'),
                'status' => 'active',
            ]);
        }
    }
}
