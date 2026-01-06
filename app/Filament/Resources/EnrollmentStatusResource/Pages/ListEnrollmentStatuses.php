<?php

namespace App\Filament\Resources\EnrollmentStatusResource\Pages;

use App\Filament\Resources\EnrollmentStatusResource;
use Filament\Resources\Pages\ListRecords;

class ListEnrollmentStatuses extends ListRecords
{
    protected static string $resource = EnrollmentStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
