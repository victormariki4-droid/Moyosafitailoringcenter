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

// Tables
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Students';

    public static function form(Form $form): Form
    {
        $isTeacher = fn (): bool => auth()->user()?->hasRole('teacher') ?? false;

        return $form->schema([
            Section::make('FOMU YA KUJIUNGA NA UFUNDI / KOZI')
                ->description('Namba ya Fomu hujaza yenyewe (Auto-generated).')
                ->schema([
                    TextInput::make('form_number')
                        ->label('Namba ya Fomu')
                        ->disabled()
                        ->dehydrated()
                        ->placeholder('Mfano: F-00001'),

                    TextInput::make('registration_number')
                        ->label('Namba ya Usajili')
                        ->disabled()
                        ->dehydrated()
                        ->placeholder('Mfano: REG-2026-0001'),
                ])
                ->columns(2),

            Section::make('1. TAARIFA BINAFSI KWA UJUMLA KWA KILA UFUNDI')
                ->schema([
                    TextInput::make('first_name')
                        ->label('Jina la Kwanza')
                        ->required()
                        ->maxLength(255)
                        // ✅ teachers cannot edit bio
                        ->disabled($isTeacher),

                    TextInput::make('last_name')
                        ->label('Jina la Mwisho')
                        ->required()
                        ->maxLength(255)
                        ->disabled($isTeacher),

                    DatePicker::make('date_of_birth')
                        ->label('Tarehe ya Kuzaliwa')
                        ->disabled($isTeacher),

                    Select::make('gender')
                        ->label('Jinsia')
                        ->options([
                            'male' => 'Mwanaume',
                            'female' => 'Mwanamke',
                            'other' => 'Nyingine',
                        ])
                        ->disabled($isTeacher),

                    TextInput::make('student_phone')
                        ->label('Namba ya Simu')
                        ->tel()
                        ->disabled($isTeacher),

                    TextInput::make('student_email')
                        ->label('Barua Pepe (Email)')
                        ->email()
                        ->disabled($isTeacher),
                ])
                ->columns(2),

            Section::make('3. MAELEZO YA MZAZI / MLEZI / MDHAMINI')
                ->schema([
                    TextInput::make('parent_name')
                        ->label('Jina Kamili la Mzazi/Mlezi')
                        ->disabled($isTeacher),

                    TextInput::make('parent_phone')
                        ->label('Namba ya Simu ya Mzazi/Mlezi')
                        ->tel()
                        ->disabled($isTeacher),

                    TextInput::make('parent_email')
                        ->label('Barua Pepe ya Mzazi/Mlezi')
                        ->email()
                        ->disabled($isTeacher),
                ])
                ->columns(2),

            // ✅ Only editable section for teachers
            Section::make('TAARIFA ZA SHULE')
                ->visibleOn('edit')
                ->schema([
                    TextInput::make('intake_year')
                        ->label('Mwaka wa Kujiunga (Intake Year)')
                        ->numeric()
                        ->minValue(2000)
                        ->maxValue((int) date('Y') + 1),

                    Select::make('status')
                        ->label('Hali ya Mwanafunzi')
                        ->options([
                            'active' => 'Anaendelea (Active)',
                            'dropped' => 'Ameacha (Dropped)',
                            'graduated' => 'Amemaliza (Graduated)',
                        ])
                        ->required()
                        ->default('active'),

                    DatePicker::make('status_date')
                        ->label('Tarehe ya Hali (Dropped/Graduated)'),

                    Textarea::make('status_reason')
                        ->label('Sababu / Maelezo')
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
                    ->visible(fn () => auth()->user()?->can('students.update_school_info') ?? false),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('students.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('students.delete') ?? false),
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
            'index'  => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit'   => Pages\EditStudent::route('/{record}/edit'),
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
