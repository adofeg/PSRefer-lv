<?php

use App\Models\User;
use App\Models\Employee;
use App\Models\Associate;
use App\Enums\EmployeeRole;
use App\Enums\AssociateRole;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('user can identify if they are an employee', function () {
    $employee = Employee::factory()->create();
    $user = User::factory()->create([
        'profileable_id' => $employee->id,
        'profileable_type' => Employee::class,
    ]);
    
    $user->assignRole(EmployeeRole::ADMIN->value);

    expect($user->isEmployee())->toBeTrue();
    expect($user->isAssociate())->toBeFalse();
    expect($user->isAdmin())->toBeTrue();
});

test('user can identify if they are an associate', function () {
    $associate = Associate::factory()->create();
    $user = User::factory()->create([
        'profileable_id' => $associate->id,
        'profileable_type' => Associate::class,
    ]);
    
    $user->assignRole(AssociateRole::ASSOCIATE->value);

    expect($user->isEmployee())->toBeFalse();
    expect($user->isAssociate())->toBeTrue();
    expect($user->isAdmin())->toBeFalse();
});

test('psadmin is an employee but not a full admin', function () {
    $employee = Employee::factory()->create();
    $user = User::factory()->create([
        'profileable_id' => $employee->id,
        'profileable_type' => Employee::class,
    ]);
    
    $user->assignRole(EmployeeRole::PSADMIN->value);

    expect($user->isEmployee())->toBeTrue();
    expect($user->isAdmin())->toBeFalse();
});

test('seeder creates tiered permissions', function () {
    $adminRole = Spatie\Permission\Models\Role::findByName(EmployeeRole::ADMIN->value);
    $associateRole = Spatie\Permission\Models\Role::findByName(AssociateRole::ASSOCIATE->value);

    expect($adminRole->hasPermissionTo('employee'))->toBeTrue();
    expect($adminRole->hasPermissionTo('associate'))->toBeFalse();
    
    expect($associateRole->hasPermissionTo('associate'))->toBeTrue();
    expect($associateRole->hasPermissionTo('employee'))->toBeFalse();
});
