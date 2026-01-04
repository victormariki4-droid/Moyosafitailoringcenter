<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $fillable = [
        'student_id',
        'course_id',
        'teacher_id',
        'percentage',
        'grade',
        'comments',
        'assessed_at',
    ];

    protected $casts = [
        'assessed_at' => 'date',
        'percentage'  => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (Result $result) {

            // ✅ Safety: always attach teacher when creating, if missing
            if (! $result->teacher_id && auth()->check()) {
                $result->teacher_id = auth()->id();
            }

            // ✅ Auto grade rubric
            $p = (float) $result->percentage;

            $result->grade = match (true) {
                $p >= 75 => 'A',
                $p >= 60 => 'B',
                $p >= 45 => 'C',
                default  => 'D',
            };
        });

        // ✅ OPTIONAL (enable if you want): avoid duplicate results same day
        /*
        static::creating(function (Result $result) {
            $exists = self::query()
                ->where('student_id', $result->student_id)
                ->where('course_id', $result->course_id)
                ->whereDate('assessed_at', $result->assessed_at)
                ->exists();

            if ($exists) {
                throw new \Exception('Result already exists for this student, course, and date.');
            }
        });
        */
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function files()
    {
        return $this->hasMany(ResultFile::class);
    }
}
