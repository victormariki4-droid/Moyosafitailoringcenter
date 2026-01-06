<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\OverviewStats;
use App\Filament\Widgets\MonthlyStudentStatusChart;
use App\Filament\Widgets\StudentQuickFilterWidget;
use App\Filament\Widgets\CoursePerformanceWidget;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    // ✅ MUST be public in your Filament version
    public function getHeaderWidgets(): array
    {
        return [
            OverviewStats::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int
    {
        return 3; // change to 4 if you want
    }

    public function getWidgets(): array
    {
        return [
            MonthlyStudentStatusChart::class,
            StudentQuickFilterWidget::class,
            CoursePerformanceWidget::class,
        ];
    }
}
