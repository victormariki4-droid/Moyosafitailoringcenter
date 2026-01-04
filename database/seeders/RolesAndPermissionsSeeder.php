<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // If you ever use multiple guards, set guard_name explicitly.
        $permissions = [
            // Students
            'students.view',
            'students.update_school_info', // limited fields only

            // Enrollments
            'enrollments.view',
            'enrollments.create',
            'enrollments.update',
            'enrollments.delete',

            // Results (coming next)
            'results.view',
            'results.create',
            'results.update',
            'results.delete',

            // Reports (file attachments)
            'reports.upload',
            'reports.view',
            'reports.delete',
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $teacher = Role::firstOrCreate(['name' => 'teacher']);
        $student = Role::firstOrCreate(['name' => 'student']);

        // Admin gets everything
        $admin->syncPermissions(Permission::all());

        // Teacher: limited set
        $teacher->syncPermissions([
            'students.view',
            'students.update_school_info',
            'enrollments.view',
            'enrollments.create',
            'enrollments.update',
            'results.view',
            'results.create',
            'results.update',
            'reports.upload',
            'reports.view',
        ]);

        // Student: typically none for Filament
        $student->syncPermissions([]);
    }
}
