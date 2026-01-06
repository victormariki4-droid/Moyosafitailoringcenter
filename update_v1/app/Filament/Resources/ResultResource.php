<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResultResource\Pages;
use App\Models\Course;
use App\Models\Result;
use App\Models\Student;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;


use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Get;
use Filament\Forms\Set;

use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class ResultResource extends Resource
{
    protected static ?string $model = Result::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';
    protected static ?string $navigationLabel = 'Results';

    /**
     * ✅ Teachers see ONLY their own results (Admin sees all)
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()?->hasRole('teacher')) {
            $query->where('teacher_id', auth()->id());
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('STUDENT RESULT')
                ->schema([
                    Select::make('student_id')
                        ->label('Student')
                        ->options(
                            Student::query()
                                ->orderBy('form_number')
                                ->get()
                                ->mapWithKeys(fn ($s) => [
                                    $s->id => "{$s->form_number} — {$s->first_name} {$s->last_name}",
                                ])
                                ->toArray()
                        )
                        ->searchable()
                        ->preload()
                        ->required()
                        ->reactive()
                        ->live(),

                    Select::make('course_id')
                        ->label('Course')
                        ->options(
                            Course::query()
                                ->orderBy('title')
                                ->pluck('title', 'id')
                                ->toArray()
                        )
                        ->searchable()
                        ->preload()
                        ->required(),

                    TextInput::make('percentage')
                        ->label('Percentage (%)')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->step(0.01)
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, Set $set) {
                            $p = (float) $state;
                            $grade = match (true) {
                                $p >= 75 => 'A',
                                $p >= 60 => 'B',
                                $p >= 45 => 'C',
                                default  => 'D',
                            };
                            $set('grade', $grade);
                        }),

                    TextInput::make('grade')
                        ->label('Grade (auto)')
                        ->disabled()
                        ->dehydrated(),

                    DatePicker::make('assessed_at')
                        ->label('Assessed Date')
                        ->default(fn () => now())
                        ->required(),

                    Textarea::make('comments')
                        ->label('Teacher Comments')
                        ->columnSpanFull(),

                    // ✅ IMPORTANT: Ensure teacher_id is saved so filtering works
                    TextInput::make('teacher_id')
                        ->default(fn () => auth()->id())
                        ->hidden()
                        ->dehydrated(),
                ])
                ->columns(2),

            Section::make('STUDENT REPORT (TAARIFA ZA SHULE)')
                ->description('Update student status and intake details used for dashboards/reports.')
                ->schema([
                    Placeholder::make('select_student_hint')
                        ->content('👆 Please select a student above to edit Student Status / Intake details.')
                        ->visible(fn (Get $get) => ! (bool) $get('student_id')),

                    Section::make('Student Status & Intake')
                        ->visible(fn (Get $get) => (bool) $get('student_id'))
                        ->relationship('student')
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
                                ])
                                ->required()
                                ->default('active'),

                            DatePicker::make('status_date')
                                ->label('Status Date (Dropped/Graduated)'),

                            Textarea::make('status_reason')
                                ->label('Reason / Notes')
                                ->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->disabled(fn () => !(auth()->user()?->can('students.update_school_info') ?? false)),
                ]),

            Section::make('REPORT FILES (PDF / IMAGES)')
                ->schema([
                    Repeater::make('files')
                        ->relationship('files')
                        ->schema([
                            FileUpload::make('file_path')
                                ->label('Upload File')
                                ->disk('public')
                                ->directory('result-reports')
                                ->preserveFilenames()
                                ->acceptedFileTypes([
                                    'application/pdf',
                                    'image/jpeg',
                                    'image/png',
                                    'image/webp',
                                ])
                                ->required()
                                ->afterStateUpdated(function ($state, Set $set) {
                                    if ($state) {
                                        $set('original_name', basename((string) $state));
                                    }
                                }),

                            TextInput::make('original_name')
                                ->label('File Name')
                                ->disabled()
                                ->dehydrated(),

                            TextInput::make('uploaded_by')
                                ->label('Uploaded By')
                                ->default(fn () => auth()->id())
                                ->disabled()
                                ->dehydrated(),
                        ])
                        ->columns(2),
                ]),
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
                    ->formatStateUsing(fn ($state, $record) => trim(($record->student?->first_name ?? '') . ' ' . ($record->student?->last_name ?? '')))
                    ->searchable(),

                TextColumn::make('course.title')
                    ->label('Course')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('percentage')->label('%')->sortable(),
                TextColumn::make('grade')->badge()->sortable(),

                TextColumn::make('teacher.name')
                    ->label('Teacher')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('assessed_at')->date()->sortable(),
                TextColumn::make('created_at')->dateTime()->since()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('files_count')
    ->label('Files')
    ->counts('files')
    ->badge()
    ->sortable(),

            ])
            ->defaultSort('assessed_at', 'desc')
           ->actions([
    Tables\Actions\Action::make('files')
        ->label('Files')
        ->icon('heroicon-o-paper-clip')
        ->modalHeading('Report Files')
        ->modalSubmitAction(false)
        ->modalCancelActionLabel('Close')
        ->visible(fn ($record) => $record->files()->exists())
        ->modalContent(function ($record) {
            $items = $record->files()->latest()->get();

            $html = '<div style="display:flex;flex-direction:column;gap:12px;">';

            foreach ($items as $f) {
                $name = e($f->original_name ?? basename($f->file_path));
                $url  = e($f->url);

                $preview = '';
                if ($f->is_image) {
                    $preview = "
                        <div style='margin-top:8px;'>
                            <img src='{$url}'
                                 alt='{$name}'
                                 style='max-width:100%;border-radius:10px;border:1px solid #e5e7eb;' />
                        </div>
                    ";
                }

                $uploadedBy = e(optional($f->uploader)->name ?? 'N/A');

                $html .= "
                    <div style='padding:12px;border:1px solid #e5e7eb;border-radius:12px;'>
                        <div style='display:flex;justify-content:space-between;align-items:center;gap:10px;'>
                            <div style='font-weight:600;'>{$name}</div>
                            <a href='{$url}' target='_blank' rel='noopener noreferrer'>Open</a>
                        </div>
                        <div style='margin-top:6px;font-size:12px;opacity:.8;'>
                            Uploaded by: {$uploadedBy}
                        </div>
                        {$preview}
                    </div>
                ";
            }

            $html .= '</div>';

            return new HtmlString($html);
        }),

    Tables\Actions\EditAction::make()
        ->visible(fn () => auth()->user()?->can('results.update') ?? false),

    Tables\Actions\DeleteAction::make()
        ->visible(fn () => auth()->user()?->can('results.delete') ?? false),
])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('results.delete') ?? false),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListResults::route('/'),
            'create' => Pages\CreateResult::route('/create'),
            'edit'   => Pages\EditResult::route('/{record}/edit'),
        ];
    }

    // ✅ Spatie permissions
    public static function canViewAny(): bool
    {
        return auth()->user()?->can('results.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('results.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('results.update') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('results.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('results.delete') ?? false;
    }
}
