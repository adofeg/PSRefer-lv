<?php
use App\Models\User;
use Spatie\Permission\Models\Role;
$user = User::where('email', 'partner@psrefer.com')->first();
if (;user) { echo 'User not found'; exit; }
// Force role assignment if missing
if (;user->hasRole('associate')) {
    echo 'Assigning associate role...';
    try {
        $user->assignRole('associate');
        echo ' Assigned.';
    } catch(\Exception $e) {
        echo ' Error: ' . $e->getMessage();
    }
}
echo 'User Roles: ' . implode(', ', $user->getRoleNames()->toArray()) . PHP_EOL;

