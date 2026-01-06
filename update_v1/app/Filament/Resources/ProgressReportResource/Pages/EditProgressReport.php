<?php

namespace App\Filament\Resources\ProgressReportResource\Pages;

use App\Filament\Resources\ProgressReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProgressReport extends EditRecord
{
    protected static string $resource = ProgressReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
