<?php

use Tests\DatabaseTestCase;
use App\Models\User\User;

uses(DatabaseTestCase::class);

test('user can be created', function () {
    $user = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com'
    ]);
    
    expect($user)->toBeInstanceOf(User::class);
    expect($user->name)->toBe('John Doe');
    expect($user->email)->toBe('john@example.com');
});

test('user has initials method', function () {
    $user = User::factory()->create(['name' => 'John Doe']);
    
    expect($user->initials())->toBe('JD');
});
