<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Enrollment;
use App\Models\Course;

class Student extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::creating(function (Student $student) {
            if (empty($student->form_number)) {
                $next = (static::max('id') ?? 0) + 1;
                $student->form_number = 'F-' . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
            }

            if (empty($student->registration_number)) {
                $year = $student->intake_year ?: date('Y');
                $next = (static::max('id') ?? 0) + 1;
                $student->registration_number = 'REG-' . $year . '-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'enrollments');
    }
    public function user()
{
    return $this->belongsTo(\App\Models\User::class);
}
public function progressReports()
{
    return $this->hasMany(\App\Models\ProgressReport::class);
}




}
