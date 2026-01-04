<?php

namespace App\Filament\Resources\EnrollmentResource\Pages;

use App\Filament\Resources\EnrollmentResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListEnrollments extends ListRecords
{
    protected static string $resource = EnrollmentResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),

            'active' => Tab::make('Active')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'active')),

            'completed' => Tab::make('Completed')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'completed')),

            'dropped' => Tab::make('Dropped')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'dropped')),

            'this_month' => Tab::make('This Month')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])),

            'this_year' => Tab::make('This Year')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()])),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
