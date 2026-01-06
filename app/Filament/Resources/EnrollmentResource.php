<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnrollmentResource\Pages;
use App\Models\Course;
use App\Models\Enrollment;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Filament\Tables\Enums\FiltersLayout;


// Forms
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Get;

// Tables
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class EnrollmentResource extends Resource
{
    protected static ?string $model = Enrollment::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Enrollments';

    protected static ?string $navigationGroup = 'Enrollment Management';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('ENROLL STUDENT TO COURSE')
                ->schema([
                    Select::make('student_id')
                        ->label('Student')
                        ->relationship('student', 'form_number')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->reactive()
                        ->getOptionLabelFromRecordUsing(
                            fn($record) => "{$record->form_number} — {$record->first_name} {$record->last_name}"
                        )
                        // ✅ Teacher cannot change student once created
                        ->disabled(fn() => auth()->user()?->hasRole('teacher') ?? false),

                    // ⚠️ WARNING when no courses are available
                    Placeholder::make('no_courses_warning')
                        ->content('⚠️ This student is already enrolled in all available courses.')
                        ->visible(function (Get $get) {
                            $studentId = $get('student_id');

                            if (!$studentId) {
                                return false;
                            }

                            $totalCourses = Course::count();
                            $enrolledCourses = Enrollment::where('student_id', $studentId)->count();

                            return $totalCourses > 0 && $totalCourses === $enrolledCourses;
                        }),

                    MultiSelect::make('course_ids')
                        ->label('Courses')
                        ->required()
                        ->searchable()
                        ->reactive()
                        ->options(function (Get $get) {
                            $studentId = $get('student_id');

                            // If no student selected yet, show all courses
                            if (!$studentId) {
                                return Course::query()
                                    ->orderBy('title')
                                    ->pluck('title', 'id')
                                    ->toArray();
                            }

                            // Hide courses already enrolled by this student
                            $enrolledCourseIds = Enrollment::query()
                                ->where('student_id', $studentId)
                                ->pluck('course_id')
                                ->toArray();

                            return Course::query()
                                ->whereNotIn('id', $enrolledCourseIds)
                                ->orderBy('title')
                                ->pluck('title', 'id')
                                ->toArray();
                        })
                        // ✅ Teacher cannot change courses once created
                        ->disabled(fn() => auth()->user()?->hasRole('teacher') ?? false),

                    DatePicker::make('start_date')
                        ->label('Start Date')
                        ->required()
                        // ✅ Teacher cannot change dates once created
                        ->disabled(fn() => auth()->user()?->hasRole('teacher') ?? false),

                    DatePicker::make('end_date')
                        ->label('End Date')
                        ->required()
                        // ✅ Teacher cannot change dates once created
                        ->disabled(fn() => auth()->user()?->hasRole('teacher') ?? false),
                ])
                ->columns(2)
        ]);
    }

    public static function infolist(\Filament\Infolists\Infolist $infolist): \Filament\Infolists\Infolist
    {
        return $infolist
            ->schema([
                \Filament\Infolists\Components\Section::make('Enrollment Details')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('student.form_number')
                            ->label('Form No.'),

                        \Filament\Infolists\Components\TextEntry::make('student.first_name')
                            ->label('Student')
                            ->formatStateUsing(fn($record) => $record->student?->first_name . ' ' . $record->student?->last_name),

                        \Filament\Infolists\Components\TextEntry::make('course.title')
                            ->label('Course'),

                        \Filament\Infolists\Components\TextEntry::make('start_date')
                            ->date(),

                        \Filament\Infolists\Components\TextEntry::make('end_date')
                            ->date(),

                        \Filament\Infolists\Components\TextEntry::make('status')
                            ->badge(),

                        \Filament\Infolists\Components\TextEntry::make('remarks')
                            ->label('Teachers Comment')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.form_number')
                    ->label('Form No.')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('student.first_name')
                    ->label('Student')
                    ->formatStateUsing(fn($state, $record) => $record->student?->first_name . ' ' . $record->student?->last_name)
                    ->searchable(),

                TextColumn::make('course.title')
                    ->label('Course')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('start_date')->date()->sortable(),
                TextColumn::make('end_date')->date()->sortable(),

                TextColumn::make('status')->badge()->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // ✅ Status
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'completed' => 'Completed',
                        'dropped' => 'Dropped',
                    ])
                    ->searchable(),

                // ✅ Course (searchable)
                Tables\Filters\SelectFilter::make('course_id')
                    ->label('Course')
                    ->relationship('course', 'title')
                    ->searchable()
                    ->preload(),

                // ✅ Student dropdown (Form No — Name)
                Tables\Filters\SelectFilter::make('student_id')
                    ->label('Student')
                    ->relationship('student', 'form_number')
                    ->searchable()
                    ->preload()
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->form_number} — {$record->first_name} {$record->last_name}"),

                // ✅ Start date range (based on start_date)
                Tables\Filters\Filter::make('start_date_range')
                    ->label('Start Date Range')
                    ->form([
                        DatePicker::make('from')->label('From'),
                        DatePicker::make('until')->label('Until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'] ?? null, fn($q, $date) => $q->whereDate('start_date', '>=', $date))
                            ->when($data['until'] ?? null, fn($q, $date) => $q->whereDate('start_date', '<=', $date));
                    }),

                // ✅ Quick preset (created_at)
                Tables\Filters\SelectFilter::make('period')
                    ->label('Period')
                    ->options([
                        'this_month' => 'This month',
                        'this_year' => 'This year',
                    ])
                    ->query(function ($query, $state) {
                        return match ($state) {
                            'this_month' => $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]),
                            'this_year' => $query->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()]),
                            default => $query,
                        };
                    }),

                // ✅ Teacher filter (ADMIN ONLY) - only if enrollments has teacher_id
                ...(Schema::hasColumn('enrollments', 'teacher_id')
                    ? [
                        Tables\Filters\SelectFilter::make('teacher_id')
                            ->label('Teacher')
                            ->options(function () {
                                // If you use spatie roles:
                                return User::query()
                                    ->whereHas('roles', fn($q) => $q->where('name', 'teacher'))
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->searchable()
                            ->visible(fn() => auth()->user()?->hasRole('admin') ?? false),
                    ]
                    : []),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(4)
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn() => auth()->user()?->can('enrollments.update') ?? false),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn() => auth()->user()?->can('enrollments.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => auth()->user()?->can('enrollments.delete') ?? false),
                ]),
            ]);
    }



    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEnrollments::route('/'),
            'create' => Pages\CreateEnrollment::route('/create'),
            'edit' => Pages\EditEnrollment::route('/{record}/edit'),
        ];
    }

    // ✅ Enrollment permissions (Spatie)
    public static function canViewAny(): bool
    {
        return auth()->user()?->can('enrollments.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('enrollments.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('enrollments.update') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('enrollments.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('enrollments.delete') ?? false;
    }
}
