<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

use App\Models\Student;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;

class OverviewStats extends BaseWidget
{
    // ✅ Force this widget to appear first (top)
    protected static ?int $sort = -100;

    // ✅ Prevent layout shifting (loads immediately)
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $graduatedStatus = 'graduated';
        $droppedStatus   = 'dropped';

        return [
            Stat::make('Students', Student::query()->count())
                ->icon('heroicon-o-academic-cap'),

            Stat::make('Users', User::query()->count())
                ->icon('heroicon-o-users'),

            Stat::make('Courses', Course::query()->count())
                ->icon('heroicon-o-book-open'),

            Stat::make('Enrollments', Enrollment::query()->count())
                ->icon('heroicon-o-clipboard-document-check'),

            Stat::make('Graduates', Enrollment::query()->where('status', $graduatedStatus)->count())
                ->icon('heroicon-o-trophy')
                ->description('Completed'),

            Stat::make('Dropouts', Enrollment::query()->where('status', $droppedStatus)->count())
                ->icon('heroicon-o-x-circle')
                ->description('Left school'),
        ];
    }
}
