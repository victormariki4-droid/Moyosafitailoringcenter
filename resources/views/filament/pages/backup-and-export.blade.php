<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            System Backup
        </x-slot>

        <x-slot name="description">
            Download a full snapshot of your current SQLite database. This includes all students, enrollments, users, and progress reports.
        </x-slot>

        <x-filament::button tag="a" href="{{ route('system.backup') }}" target="_blank" color="primary">
            Download .sqlite Backup File
        </x-filament::button>
    </x-filament::section>

    <x-filament::section class="border-danger-500">
        <x-slot name="heading">
            <span class="text-danger-600">Log Out</span>
        </x-slot>

        <x-slot name="description">
            Securely end your session and log out of the system entirely.
        </x-slot>

        <x-filament::button tag="a" href="{{ route('system.logout') }}" color="danger">
            Log Out Now
        </x-filament::button>
    </x-filament::section>
</x-filament-panels::page>
