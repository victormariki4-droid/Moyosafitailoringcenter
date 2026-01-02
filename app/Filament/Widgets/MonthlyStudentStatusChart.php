<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class MonthlyStudentStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Monthly Summary (Enrollments / Graduates / Dropouts)';

    protected int|string|array $columnSpan = 'full';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        // ✅ Adjust these if your enrollment status values differ
        $graduatedStatus = 'graduated';
        $droppedStatus   = 'dropped';

        $year = (int) ($this->filter ?? now()->year);

        $labels = collect(range(1, 12))
            ->map(fn ($m) => Carbon::create($year, $m, 1)->format('M'))
            ->values()
            ->all();

        // Enrollments per month (created_at)
        $enrollments = DB::table('enrollments')
            ->selectRaw('MONTH(created_at) as m, COUNT(*) as total')
            ->whereYear('created_at', $year)
            ->groupBy('m')
            ->pluck('total', 'm');

        // Graduates per month (updated_at used as status change time)
        $graduates = DB::table('enrollments')
            ->selectRaw('MONTH(updated_at) as m, COUNT(*) as total')
            ->whereYear('updated_at', $year)
            ->where('status', $graduatedStatus)
            ->groupBy('m')
            ->pluck('total', 'm');

        // Dropouts per month (updated_at used as status change time)
        $dropouts = DB::table('enrollments')
            ->selectRaw('MONTH(updated_at) as m, COUNT(*) as total')
            ->whereYear('updated_at', $year)
            ->where('status', $droppedStatus)
            ->groupBy('m')
            ->pluck('total', 'm');

        $toSeries = fn ($map) => collect(range(1, 12))->map(fn ($m) => (int) ($map[$m] ?? 0))->all();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Enrollments',
                    'data' => $toSeries($enrollments),
                    'backgroundColor' => '#3B82F6',
                ],
                [
                    'label' => 'Graduates',
                    'data' => $toSeries($graduates),
                    'backgroundColor' => '#22C55E',
                ],
                [
                    'label' => 'Dropouts',
                    'data' => $toSeries($dropouts),
                    'backgroundColor' => '#EF4444',
                ],
            ],
        ];
    }

    protected function getFilters(): ?array
    {
        $currentYear = now()->year;

        return collect(range($currentYear - 3, $currentYear))
            ->mapWithKeys(fn ($y) => [$y => (string) $y])
            ->all();
    }
}
