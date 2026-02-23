<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\ProgressReport;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Demo Courses
        $course1 = Course::firstOrCreate(['title' => 'Beginner Tailoring'], ['description' => 'Basics of sewing and cutting.']);
        $course2 = Course::firstOrCreate(['title' => 'Fashion Design'], ['description' => 'Advanced pattern making.']);

        // 2. Create Demo Students
        $student1 = Student::create([
            'first_name' => 'Victor',
            'last_name' => 'Mariki',
            'gender' => 'male',
            'registration_number' => 'DEMO-001',
            'intake_year' => 2024,
            'status' => 'active',
        ]);

        $student2 = Student::create([
            'first_name' => 'Sarah',
            'last_name' => 'John',
            'gender' => 'female',
            'registration_number' => 'DEMO-002',
            'intake_year' => 2023,
            'status' => 'graduated',
            'is_employed' => true,
            'employment_type' => 'self-employed',
            'employer_name' => 'Sarah Designs Shop',
            'employer_location' => 'Arusha',
            'job_title' => 'Owner / Head Tailor',
            'monthly_salary' => 450000,
            'career_notes' => 'Succesfully opened her own shop after graduation.',
        ]);

        // 3. Enrollments
        $e1 = Enrollment::create([
            'student_id' => $student1->id,
            'course_id' => $course1->id,
            'start_date' => '2024-01-10',
            'end_date' => '2024-06-10',
            'status' => 'active',
        ]);

        // 4. Progress Report
        ProgressReport::create([
            'student_id' => $student1->id,
            'enrollment_id' => $e1->id,
            'teacher_id' => User::where('email', 'admin@school.com')->first()->id,
            'report_date' => now(),
            'progress_level' => 'excellent',
            'title' => 'Excellent Progress on Sewing',
            'progress_notes' => 'Victor is showing great promise in machine operation.',
            'next_steps' => 'Move to complex pattern cutting next month.',
        ]);
    }
}
