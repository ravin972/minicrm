<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Verifying demo user credentials:\n\n";

$users = User::whereIn('email', ['admin@crm.local', 'manager@crm.local', 'staff@crm.local'])
    ->get(['name', 'email', 'role', 'password']);

foreach ($users as $user) {
    echo "Role: " . $user->role . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Password hash: " . substr($user->password, 0, 30) . "...\n";
    echo "Password 'password' matches: " . (Hash::check('password', $user->password) ? 'YES' : 'NO') . "\n";
    echo "---\n";
}

echo "\nTotal users found: " . $users->count() . "\n";