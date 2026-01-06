<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ProgressReport;
use Illuminate\Http\Request;

class ProgressReportController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $student = $user?->student;

        if (!$student) {
            return redirect('/login')->with('error', 'Your account is not linked to a student record. Please contact the admin.');
        }

        $reports = $student->progressReports()
            ->with('teacher:id,name')
            ->orderByDesc('report_date')
            ->get();

        return view('student.progress.index', compact('student', 'reports'));
    }

    // PDF download for a single progress report (student can only download their own)
    public function pdf(Request $request, ProgressReport $report)
    {
        $student = $request->user()?->student;

        if (!$student || $report->student_id !== $student->id) {
            abort(403);
        }

        // Requires DomPDF package (Step 3 below)
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('student.progress.pdf', [
            'student' => $student,
            'report'  => $report->load('teacher:id,name', 'student'),
        ]);

        $name = 'progress-' . ($student->form_number ?? $student->id) . '-' . $report->report_date . '.pdf';
        return $pdf->download($name);
    }
}
