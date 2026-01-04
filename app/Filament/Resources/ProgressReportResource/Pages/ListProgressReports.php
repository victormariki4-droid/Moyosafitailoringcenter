<?php

namespace App\Filament\Resources\ProgressReportResource\Pages;

use App\Filament\Resources\ProgressReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProgressReports extends ListRecords
{
    protected static string $resource = ProgressReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
