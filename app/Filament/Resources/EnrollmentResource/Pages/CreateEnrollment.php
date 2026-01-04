<?php

namespace App\Filament\Resources\EnrollmentResource\Pages;

use App\Filament\Resources\EnrollmentResource;
use App\Models\Course;
use App\Models\Enrollment;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;
use App\Notifications\EnrollmentConfirmed;

class CreateEnrollment extends CreateRecord
{
    protected static string $resource = EnrollmentResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $studentId = $data['student_id'];
        $courseIds = $data['course_ids'] ?? [];

        $firstEnrollment = null;

        foreach ($courseIds as $courseId) {
            $course = Course::find($courseId);

            if (!$course) {
                continue;
            }

            // Use provided dates or fallback to course dates
            $startDate = $data['start_date'] ?? $course->start_date;
            $endDate   = $data['end_date'] ?? $course->end_date;

            $enrollment = Enrollment::create([
                'student_id' => $studentId,
                'course_id'  => $courseId,
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'status'     => 'active',
            ]);

            // Send email notification
            $student = $enrollment->student;

            if (!empty($student?->student_email)) {
                Notification::route('mail', $student->student_email)
                    ->notify(new EnrollmentConfirmed($course));
            }

            if (!empty($student?->parent_email)) {
                Notification::route('mail', $student->parent_email)
                    ->notify(new EnrollmentConfirmed($course));
            }

            $firstEnrollment ??= $enrollment;
        }

        return $firstEnrollment ?? Enrollment::make();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
