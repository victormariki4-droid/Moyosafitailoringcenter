<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgressReportResource\Pages;
use App\Models\ProgressReport;
use App\Models\Student;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;

use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class ProgressReportResource extends Resource
{
    protected static ?string $model = ProgressReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Progress Reports';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('STUDENT PROGRESS REPORT')
                ->schema([
                    Select::make('student_id')
                        ->label('Student')
                        ->options(
                            Student::query()
                                ->orderBy('form_number')
                                ->get()
                                ->mapWithKeys(fn ($s) => [
                                    $s->id => "{$s->form_number} — {$s->first_name} {$s->last_name}"
                                ])
                                ->toArray()
                        )
                        ->searchable()
                        ->required(),

                    DatePicker::make('report_date')
                        ->label('Report Date')
                        ->default(now())
                        ->required(),

                    Select::make('progress_level')
                        ->label('Progress Level')
                        ->options([
                            'excellent' => 'Excellent',
                            'good' => 'Good',
                            'average' => 'Average',
                            'needs_improvement' => 'Needs Improvement',
                        ])
                        ->required(),

                    Textarea::make('notes')
                        ->label('Teacher Notes')
                        ->required()
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('student.form_number')->label('Form No.')->searchable(),
            TextColumn::make('student.first_name')
                ->label('Student')
                ->formatStateUsing(fn ($state, $record) => $record->student?->first_name.' '.$record->student?->last_name)
                ->searchable(),
            TextColumn::make('progress_level')->badge()->sortable(),
            TextColumn::make('report_date')->date()->sortable(),
            TextColumn::make('teacher.name')->label('Teacher')->toggleable(isToggledHiddenByDefault: true),
        ])
        ->defaultSort('report_date', 'desc')
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProgressReports::route('/'),
            'create' => Pages\CreateProgressReport::route('/create'),
            'edit'   => Pages\EditProgressReport::route('/{record}/edit'),
        ];
    }

    // Permissions (Spatie)
    public static function canViewAny(): bool
    {
        return auth()->user()?->can('progress_reports.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('progress_reports.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('progress_reports.update') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('progress_reports.delete') ?? false;
    }
}
