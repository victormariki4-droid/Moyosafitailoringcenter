<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Models\Student;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

// Forms
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Get;

// Tables
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Students';
    protected static ?string $navigationGroup = 'Student Management';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        $isTeacher = fn(): bool => auth()->user()?->hasRole('teacher') ?? false;

        return $form->schema([
            Section::make('Student Registration Form')
                ->description('Form Number is auto-generated.')
                ->schema([
                    TextInput::make('form_number')
                        ->label('Form Number')
                        ->disabled()
                        ->dehydrated()
                        ->placeholder('Example: F-00001'),

                    TextInput::make('registration_number')
                        ->label('Registration Number')
                        ->disabled()
                        ->dehydrated()
                        ->placeholder('Example: REG-2026-0001'),
                ])
                ->columns(2),

            Section::make('1. General Personal Information')
                ->schema([
                    // ✅ Student Image Upload
                    FileUpload::make('profile_photo_path')
                        ->label('Student Photo')
                        ->image()
                        ->imageEditor() // Allows manual cropping
                        ->imageEditorAspectRatios([
                            null,
                            '16:9',
                            '4:3',
                            '1:1',
                        ])
                        ->directory('student-photos')
                        ->maxSize(2048) // Limit to 2MB
                        ->imageResizeMode('cover')
                        ->imageCropAspectRatio('1:1')
                        ->imageResizeTargetWidth('400') // Resize to 400px width
                        ->imageResizeTargetHeight('400') // Resize to 400px height
                        ->columnSpanFull(),

                    TextInput::make('first_name')
                        ->label('First Name')
                        ->required()
                        ->maxLength(255)
                        // ✅ teachers cannot edit bio
                        ->disabled($isTeacher),

                    TextInput::make('last_name')
                        ->label('Last Name')
                        ->required()
                        ->maxLength(255)
                        ->disabled($isTeacher),

                    DatePicker::make('date_of_birth')
                        ->label('Date of Birth')
                        ->disabled($isTeacher),

                    Select::make('gender')
                        ->label('Gender')
                        ->options([
                            'male' => 'Male',
                            'female' => 'Female',
                            'other' => 'Other',
                        ])
                        ->disabled($isTeacher),

                    TextInput::make('student_phone')
                        ->label('Phone Number')
                        ->tel()
                        ->disabled($isTeacher),

                    TextInput::make('student_email')
                        ->label('Email Address')
                        ->email()
                        ->disabled($isTeacher),
                ])
                ->columns(2),

            Section::make('2. Parent / Guardian Information')
                ->schema([
                    TextInput::make('parent_name')
                        ->label('Parent/Guardian Full Name')
                        ->disabled($isTeacher),

                    TextInput::make('parent_phone')
                        ->label('Parent/Guardian Phone')
                        ->tel()
                        ->disabled($isTeacher),

                    TextInput::make('parent_email')
                        ->label('Parent/Guardian Email')
                        ->email()
                        ->disabled($isTeacher),
                ])
                ->columns(2),

            Section::make('School Information')
                ->visibleOn('edit')
                ->schema([
                    TextInput::make('intake_year')
                        ->label('Intake Year')
                        ->numeric()
                        ->minValue(2000)
                         ->maxValue((int) date('Y') + 1),

                    Select::make('status')
                        ->label('Student Status')
                        ->options([
                            'active' => 'Active',
                            'dropped' => 'Dropped',
                            'graduated' => 'Graduated',
                            'employed' => 'Employed (Alumni)',
                            'self-employed' => 'Self-Employed (Alumni)',
                        ])
                        ->required()
                        ->default('active'),

                    DatePicker::make('status_date')
                        ->label('Status Date (Dropped/Graduated)'),

                    Textarea::make('status_reason')
                        ->label('Reason / Notes')
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Alumni & Career Tracking')
                ->description('Track student post-graduation career status.')
                ->schema([
                    Select::make('is_employed')
                        ->label('Is Currently Employed?')
                        ->options([
                            1 => 'Yes',
                            0 => 'No',
                        ])
                        ->default(0)
                        ->live(),

                    Select::make('employment_type')
                        ->label('Employment Type')
                        ->options([
                            'employed' => 'Employed (Salaried)',
                            'self-employed' => 'Self-Employed (Business)',
                            'internship' => 'Internship / Apprenticeship',
                            'contract' => 'Contract / Freelance',
                        ])
                        ->visible(fn (Get $get) => $get('is_employed')),

                    TextInput::make('employer_name')
                        ->label('Employer / Business Name')
                        ->placeholder('e.g. ABC Tailoring Shop or My Tailoring Business')
                        ->visible(fn (Get $get) => $get('is_employed')),

                    TextInput::make('employer_location')
                        ->label('Employer Location / Address')
                        ->placeholder('e.g. Arusha Town, Block 5')
                        ->visible(fn (Get $get) => $get('is_employed')),

                    TextInput::make('job_title')
                        ->label('Job Title / Position')
                        ->placeholder('e.g. Senior Tailor, Shop Manager')
                        ->visible(fn (Get $get) => $get('is_employed')),

                    TextInput::make('monthly_salary')
                        ->label('Monthly Income / Salary')
                        ->numeric()
                        ->prefix('Tsh')
                        ->placeholder('e.g. 300000')
                        ->visible(fn (Get $get) => $get('is_employed')),

                    DatePicker::make('employment_start_date')
                        ->label('Employment Start Date')
                        ->visible(fn (Get $get) => $get('is_employed')),

                    Textarea::make('career_notes')
                        ->label('Career Updates / General Notes')
                        ->placeholder('Add any other updates about where the student is now...')
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('form_number')->label('Form No.')->searchable()->sortable(),
                TextColumn::make('registration_number')->label('Reg No.')->searchable()->sortable(),

                TextColumn::make('first_name')->label('First')->searchable(),
                TextColumn::make('last_name')->label('Last')->searchable(),

                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('intake_year')->label('Intake')->sortable(),

                TextColumn::make('created_at')->dateTime()->since()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn() => auth()->user()?->can('students.update_school_info') ?? false),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn() => auth()->user()?->can('students.delete') ?? false),

                Tables\Actions\Action::make('print')
                    ->label('Print Form')
                    ->icon('heroicon-o-printer')
                    ->url(fn(Student $record) => route('students.print', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => auth()->user()?->can('students.delete') ?? false),
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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }

    // ✅ Permissions (Spatie)
    public static function canViewAny(): bool
    {
        return auth()->user()?->can('students.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('students.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('students.update_school_info') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('students.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('students.delete') ?? false;
    }
}
