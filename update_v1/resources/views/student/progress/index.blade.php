<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Student Progress</title>
    <style>
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial; background:#0b1220; color:#e5e7eb; margin:0; padding:32px; }
        .wrap { max-width: 980px; margin: 0 auto; }
        .card { background: rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.08); border-radius:16px; padding:20px; }
        .top { display:flex; gap:12px; align-items:center; justify-content:space-between; flex-wrap:wrap; }
        .title { font-size:26px; margin:0; }
        .muted { color:#9ca3af; }
        .btn { padding:8px 12px; border-radius:10px; border:1px solid rgba(255,255,255,.12); background:rgba(255,255,255,.06); color:#e5e7eb; cursor:pointer; }
        .btn:hover { background:rgba(255,255,255,.10); }
        .flash { margin-top:12px; padding:12px; border-radius:12px; border:1px solid rgba(34,197,94,.35); background:rgba(34,197,94,.12); }
        .warn { margin-top:12px; padding:12px; border-radius:12px; border:1px solid rgba(239,68,68,.35); background:rgba(239,68,68,.12); }
        .report { margin-top:16px; padding-top:16px; border-top:1px solid rgba(255,255,255,.10); }
        .badge { display:inline-block; padding:4px 10px; border-radius:999px; background:rgba(59,130,246,.15); border:1px solid rgba(59,130,246,.35); color:#bfdbfe; font-size:12px; }
        .label { color:#cbd5e1; font-weight:600; margin-top:10px; }
        .actions { display:flex; gap:10px; margin-top:10px; flex-wrap:wrap; }
        a.link { color:#93c5fd; text-decoration:none; }
        a.link:hover { text-decoration:underline; }
        .empty { margin-top:16px; padding:14px; border:1px dashed rgba(255,255,255,.20); border-radius:12px; color:#cbd5e1; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <div class="top">
            <div>
                <h1 class="title">Student Progress</h1>
                <div class="muted">
                    Student: <b>{{ $student->form_number ?? 'N/A' }}</b>
                    — {{ $student->first_name ?? '' }} {{ $student->last_name ?? '' }}
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn" type="submit">Logout</button>
            </form>
        </div>

        @if(session('success'))
            <div class="flash">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="warn">{{ session('error') }}</div>
        @endif

        {{-- Timeline --}}
        @forelse($reports as $r)
            <div class="report">
                <div class="badge">
                    {{ \Illuminate\Support\Carbon::parse($r->report_date)->format('d M Y') }}
                </div>

                <div class="muted" style="margin-top:8px;">
                    Teacher: <b>{{ $r->teacher?->name ?? 'N/A' }}</b>
                    @if($r->title) · Title: <b>{{ $r->title }}</b>@endif
                </div>

                <div class="label">Progress Notes</div>
                <div>{{ $r->progress_notes }}</div>

                @if($r->next_steps)
                    <div class="label">Next Steps</div>
                    <div>{{ $r->next_steps }}</div>
                @endif

                <div class="actions">
                    <a class="link" href="{{ route('student.progress.pdf', $r->id) }}">Download PDF</a>
                </div>
            </div>
        @empty
            <div class="empty">No progress reports yet.</div>
        @endforelse
    </div>
</div>
</body>
</html>
