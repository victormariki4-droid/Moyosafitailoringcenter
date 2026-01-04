<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProgressReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'teacher_id',
        'report_date',
        'title',
        'progress_notes',
        'next_steps',
    ];

    protected $casts = [
        'report_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
