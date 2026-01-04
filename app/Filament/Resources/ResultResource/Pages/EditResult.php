<?php

namespace App\Filament\Resources\ResultResource\Pages;

use App\Filament\Resources\ResultResource;
use Filament\Resources\Pages\EditRecord;

class EditResult extends EditRecord
{
    protected static string $resource = ResultResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // ✅ Lock teacher_id: keep the original teacher who created the result
        $data['teacher_id'] = $this->record->teacher_id;

        return $data;
    }
}
