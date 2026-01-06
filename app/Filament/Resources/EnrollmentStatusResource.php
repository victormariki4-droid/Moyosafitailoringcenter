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

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Enrollment Status';

    protected static ?string $navigationGroup = 'Enrollment Management';

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
                    ->modalHeading('Update Enrollment Status'),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEnrollmentStatuses::route('/'),
            // We don't need create/edit pages if we use modal actions, but let's keep standard structure just in case.
            // Actually, for "Status" updates, a simple modal on the index page is often best.
            // Let's rely on the EditAction in the table above which (by default) opens a modal or redirects.
            // If we want a separate page, we can register it. For now, let's stick to Index only to keep it simple as requested "Dropdown -> Page".
        ];
    }
}
