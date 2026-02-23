<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Result;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class SyncLiveDataSeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = database_path('seeders/data/full_live_data.json');
        
        if (!File::exists($jsonPath)) {
            $this->command->error("JSON data file not found at: {$jsonPath}");
            return;
        }

        $data = json_decode(File::get($jsonPath), true);

        // CLEAR out the tables to ensure an EXACT match with live numbers
        DB::table('enrollments')->delete();
        DB::table('results')->delete();
        DB::table('students')->delete();
        DB::table('courses')->delete();
        
        // Remove non-essential users
        User::whereNotIn('email', ['admin@school.com', 'teacher@school.com', 'student@school.com'])->delete();

        // 1. Sync Users
        foreach ($data['users'] as $uData) {
            $user = User::firstOrCreate(
                ['email' => $uData['email']],
                [
                    'name' => $uData['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
            
            if (!$user->hasRole($uData['role'])) {
                $user->assignRole($uData['role']);
            }
        }

        // 2. Sync Courses
        foreach ($data['courses'] as $cData) {
            Course::updateOrCreate(
                ['title' => $cData['title']],
                [
                    'status' => $cData['status'] ?? 'active',
                    'start_date' => $cData['start_date'] ?? null,
                    'end_date' => $cData['end_date'] ?? null,
                    'duration_days' => $cData['days'] ?? null,
                ]
            );
        }

        // 3. Sync Students
        foreach ($data['students'] ?? [] as $sData) {
            // We use registration_number as the unique identifier
            Student::updateOrCreate(
                ['registration_number' => $sData['registration_number']],
                [
                    'form_number' => $sData['form_number'] ?? null,
                    'first_name' => $sData['first_name'],
                    'last_name' => $sData['last_name'],
                    'status' => $sData['status'],
                    'intake_year' => $sData['intake_year'],
                    'date_of_birth' => $sData['date_of_birth'] ?? null,
                    'gender' => $sData['gender'] ?? null,
                    'student_email' => $sData['student_email'] ?? null,
                    'parent_name' => $sData['parent_name'] ?? null,
                    'parent_phone' => $sData['parent_phone'] ?? null,
                    'status_date' => $sData['status_date'] ?? null,
                    'status_reason' => $sData['status_reason'] ?? null,
                ]
            );
        }

        // 4. Sync Enrollments
        foreach ($data['enrollments'] ?? [] as $eData) {
            $student = Student::where('form_number', $eData['form_number'])->first();
            
            $course = Course::where('title', $eData['course_name'])->first();

            if ($student && $course) {
                Enrollment::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'course_id' => $course->id,
                    ],
                    [
                        'status' => strtolower($eData['status']),
                        'start_date' => $eData['start_date'] ?? null,
                    ]
                );
            } else {
                if (!$student) $this->command->warn("Missing student for form: " . $eData['form_number']);
                if (!$course) $this->command->warn("Missing course: " . $eData['course_name']);
            }
        }

        // 5. Sync Results
        foreach ($data['results'] ?? [] as $rData) {
            $parts = explode(' ', $rData['student_name']);
            $first = $parts[0];
            $last = end($parts);

            $student = Student::where('first_name', 'like', '%' . $first . '%')
                ->where('last_name', 'like', '%' . $last . '%')
                ->first();
            
            $course = Course::where('title', $rData['course_name'])->first();

            if ($student && $course) {
                Result::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'course_id' => $course->id,
                    ],
                    [
                        'percentage' => $rData['percentage'],
                        'grade' => $rData['grade'],
                        'assessed_at' => $rData['assessed_at'],
                        'teacher_id' => User::role('teacher')->first()?->id ?? User::first()->id,
                    ]
                );
            }
        }

        $this->command->info('Live site data synced successfully! Dashboard numbers should now exactly match the live site.');
    }
}
