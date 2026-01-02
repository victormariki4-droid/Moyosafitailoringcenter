<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$users = \App\Models\User::with('roles')->get();

if ($users->isEmpty()) {
    echo "No users found.\n";
} else {
    foreach ($users as $u) {
        echo "Email: " . $u->email . " | Roles: " . $u->roles->pluck('name')->implode(', ') . "\n";
    }
}
