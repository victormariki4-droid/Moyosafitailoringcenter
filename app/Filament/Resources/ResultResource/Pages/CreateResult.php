<?php

namespace App\Filament\Resources\ResultResource\Pages;

use App\Filament\Resources\ResultResource;
use Filament\Resources\Pages\CreateRecord;

class CreateResult extends CreateRecord
{
    protected static string $resource = ResultResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['teacher_id'] = auth()->id();

        return $data;
    }
}
