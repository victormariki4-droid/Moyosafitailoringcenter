<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Filament\Clusters\Settings;
use Filament\Pages\Page;

class BackupAndExportData extends Page
{
    protected static ?string $cluster = Settings::class;
    protected static ?string $navigationLabel = 'Backup & Export Data';
    protected static ?string $title = 'System Controls';
    protected static string $view = 'filament.pages.backup-and-export';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole(['admin', 'read_only_admin']) ?? false;
    }
}
