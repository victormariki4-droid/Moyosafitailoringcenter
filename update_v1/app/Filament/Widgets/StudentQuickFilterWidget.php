<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

use App\Models\Enrollment;
use App\Models\Course;

class StudentQuickFilterWidget extends BaseWidget
{
    protected static ?string $heading = 'Quick Filters (Students)';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query($this->baseQuery())
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label('Student')
                    ->searchable(),

                Tables\Columns\TextColumn::make('course.title')
                    ->label('Course')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Enrolled On')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Filter')
                    ->options([
                        'enrolled'  => 'Enrollments',
                        'graduated' => 'Graduates',
                        'dropped'   => 'Dropouts',
                    ])
                    ->default('enrolled'),

                Tables\Filters\SelectFilter::make('course_id')
                    ->label('Course')
                    ->options(fn () => Course::query()->orderBy('title')->pluck('title', 'id')->toArray())
                    ->searchable(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    private function baseQuery(): Builder
    {
        return Enrollment::query()->with(['student', 'course']);
    }
}
