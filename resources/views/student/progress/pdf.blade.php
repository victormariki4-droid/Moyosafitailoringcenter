<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Progress Report PDF</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { margin-bottom: 12px; }
        .box { border: 1px solid #ddd; padding: 10px; border-radius: 6px; }
        .muted { color: #666; }
        h2 { margin: 0 0 6px; }
        h3 { margin: 12px 0 6px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Student Progress Report</h2>
        <div class="muted">
            Student: <b>{{ $student->form_number ?? 'N/A' }}</b>
            — {{ $student->first_name }} {{ $student->last_name }}
        </div>
        <div class="muted">
            Date: <b>{{ \Illuminate\Support\Carbon::parse($report->report_date)->format('d M Y') }}</b>
            | Teacher: <b>{{ $report->teacher?->name ?? 'N/A' }}</b>
        </div>
        @if($report->title)
            <div class="muted">Title: <b>{{ $report->title }}</b></div>
        @endif
    </div>

    <div class="box">
        <h3>Progress Notes</h3>
        <div>{!! nl2br(e($report->progress_notes)) !!}</div>

        @if($report->next_steps)
            <h3>Next Steps</h3>
            <div>{!! nl2br(e($report->next_steps)) !!}</div>
        @endif
    </div>
</body>
</html>
