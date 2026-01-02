<?php

namespace App\Filament\Widgets;

use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

use App\Models\Course;
use App\Models\Result;

class CoursePerformanceWidget extends BaseWidget
{
    protected static ?string $heading = 'Course Performance';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query($this->coursePerformanceQuery())
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Course')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('avg_score')
                    ->label('Avg %')
                    ->sortable()
                    ->formatStateUsing(fn ($state) =>
                        $state === null ? '-' : number_format((float) $state, 1) . '%'
                    ),

                Tables\Columns\TextColumn::make('results_count')
                    ->label('Results')
                    ->sortable(),
            ])
            ->defaultSort('avg_score', 'desc')
            ->paginated(false);
    }

    private function coursePerformanceQuery(): Builder
    {
        return Course::query()
            ->select('courses.*')
            ->selectSub(
                Result::query()
                    ->selectRaw('AVG(percentage)')
                    ->whereColumn('results.course_id', 'courses.id'),
                'avg_score'
            )
            ->selectSub(
                Result::query()
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('results.course_id', 'courses.id'),
                'results_count'
            );
    }
}
