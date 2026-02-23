<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnrollmentStatusResource\Pages;
use App\Models\Enrollment;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\FiltersLayout;
use App\Models\Course;
use Illuminate\Database\Eloquent\Builder;

class EnrollmentStatusResource extends Resource
{
    protected static ?string $model = Enrollment::class;

    public static function getNavigationIcon(): ?string
    {
        return null;
    }

    protected static ?string $navigationLabel = 'Enrollment Status';

    protected static ?string $navigationGroup = 'Enrollment Management';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('status')
                    ->label('Enrollment Status')
                    ->options([
                        'active' => 'Active',
                        'completed' => 'Completed',
                        'dropped' => 'Dropped',
                    ])
                    ->required()
                    ->default('active'),

                DatePicker::make('status_date')
                    ->label('Status Date (Completed/Dropped)'),

                Textarea::make('remarks')
                    ->label('Teachers Comment')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.form_number')
                    ->label('Form No')
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

                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'completed' => 'Completed',
                        'dropped' => 'Dropped',
                    ]),

                SelectFilter::make('course_id')
                    ->label('Course')
                    ->relationship('course', 'title')
                    ->searchable()
                    ->preload(),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                EditAction::make()
                    ->label('Update Status')
                    ->modalHeading('Update Enrollment Status')
                    ->visible(fn() => auth()->user()?->can('enrollments.update') ?? false),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEnrollmentStatuses::route('/'),
        ];
    }

    // ✅ Permissions (Spatie)
    public static function canViewAny(): bool
    {
        return auth()->user()?->can('enrollments.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return false; // Users shouldn't create "Enrollment Statuses", they only update them
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('enrollments.update') ?? false;
    }

    public static function canDelete($record): bool
    {
        return false; // Enrollment Statuses can't be deleted here, only from EnrollmentResource
    }
}
