<?php

namespace App\Filament\Resources\ProgressReportResource\Pages;

use App\Filament\Resources\ProgressReportResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProgressReport extends CreateRecord
{
    protected static string $resource = ProgressReportResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['teacher_id'] = auth()->id();
        return $data;
    }
}
