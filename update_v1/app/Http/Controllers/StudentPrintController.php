<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Student;


class StudentPrintController extends Controller
{
    public function print(Student $record)
    {
        return view('students.print', ['student' => $record]);
    }
}
