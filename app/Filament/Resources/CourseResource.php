<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Models\Course;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

// Forms
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;

// Tables
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

// Carbon (for date diff)
use Carbon\Carbon;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    public static function getNavigationIcon(): ?string
    {
        return null;
    }
    protected static ?string $navigationLabel = 'Courses';
    protected static ?string $navigationGroup = 'Academics';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->columnSpanFull(),

                DatePicker::make('start_date')
                    ->reactive()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        if ($get('start_date') && $get('end_date')) {
                            $days = Carbon::parse($get('start_date'))
                                ->diffInDays(Carbon::parse($get('end_date'))) + 1;

                            $set('duration_days', $days);
                        }
                    }),

                DatePicker::make('end_date')
                    ->reactive()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        if ($get('start_date') && $get('end_date')) {
                            $days = Carbon::parse($get('start_date'))
                                ->diffInDays(Carbon::parse($get('end_date'))) + 1;

                            $set('duration_days', $days);
                        }
                    }),

                TextInput::make('duration_days')
                    ->label('Duration (days)')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(),

                Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'completed' => 'Completed',
                        'draft' => 'Draft',
                    ])
                    ->required()
                    ->default('active'),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('start_date')->date(),
                TextColumn::make('end_date')->date(),

                TextColumn::make('duration_days')
                    ->label('Days'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit'   => Pages\EditCourse::route('/{record}/edit'),
        ];
    }

    // ✅ Permissions (Spatie)
    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole(['admin', 'teacher', 'read_only_admin']) 
            || (auth()->user()?->can('courses.view') ?? false);
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('courses.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('courses.update') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('courses.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('courses.delete') ?? false;
    }
}
