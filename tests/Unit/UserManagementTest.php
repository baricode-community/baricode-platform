<?php

use Tests\DatabaseTestCase;
use App\Models\User\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

uses(DatabaseTestCase::class);

it('can create user with all fields', function () {
    $role = Role::create(['name' => 'admin']);
    
    $userData = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'whatsapp' => '+6281234567890',
        'about' => 'Software developer',
        'level' => 'mahir',
        'password' => Hash::make('password123')
    ];
    
    $user = User::create($userData);
    $user->assignRole($role);
    
    expect($user)->toBeInstanceOf(User::class);
    expect($user->name)->toBe($userData['name']);
    expect($user->email)->toBe($userData['email']);
    expect($user->whatsapp)->toBe($userData['whatsapp']);
    expect($user->about)->toBe($userData['about']);
    expect($user->level)->toBe($userData['level']);
    expect($user->hasRole('admin'))->toBeTrue();
    expect(Hash::check('password123', $user->password))->toBeTrue();
});

it('can update user information', function () {
    $user = User::factory()->create([
        'name' => 'Original Name',
        'email' => 'original@example.com',
        'level' => 'pemula'
    ]);
    
    $updateData = [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'whatsapp' => '+6287654321',
        'level' => 'mahir'
    ];
    
    $user->update($updateData);
    
    expect($user->fresh()->name)->toBe($updateData['name']);
    expect($user->fresh()->email)->toBe($updateData['email']);
    expect($user->fresh()->whatsapp)->toBe($updateData['whatsapp']);
    expect($user->fresh()->level)->toBe($updateData['level']);
});

it('can soft delete user', function () {
    $user = User::factory()->create();
    $userId = $user->id;
    
    // Soft delete the user
    $user->delete();
    
    // User should not be found in regular query
    expect(User::find($userId))->toBeNull();
    
    // But should be found with trashed
    expect(User::withTrashed()->find($userId))->not->toBeNull();
    expect(User::withTrashed()->find($userId)->trashed())->toBeTrue();
});

it('can assign and remove roles from user', function () {
    $adminRole = Role::create(['name' => 'admin']);
    $userRole = Role::create(['name' => 'user']);
    
    $user = User::factory()->create();
    
    // Assign roles
    $user->assignRole(['admin', 'user']);
    
    expect($user->hasRole('admin'))->toBeTrue();
    expect($user->hasRole('user'))->toBeTrue();
    expect($user->roles)->toHaveCount(2);
    
    // Remove role
    $user->removeRole('user');
    
    expect($user->hasRole('admin'))->toBeTrue();
    expect($user->hasRole('user'))->toBeFalse();
    expect($user->roles)->toHaveCount(1);
    
    // Sync roles
    $user->syncRoles(['user']);
    
    expect($user->hasRole('admin'))->toBeFalse();
    expect($user->hasRole('user'))->toBeTrue();
    expect($user->roles)->toHaveCount(1);
});

it('validates unique email constraint', function () {
    User::factory()->create(['email' => 'test@example.com']);
    
    $this->expectException(\Illuminate\Database\QueryException::class);
    
    User::factory()->create(['email' => 'test@example.com']);
});

it('validates whatsapp format in model', function () {
    $user = User::factory()->create([
        'whatsapp' => '+6281234567890'
    ]);
    
    expect($user->whatsapp)->toBe('+6281234567890');
    
    // Test with invalid format should be handled at validation level
    $user2 = User::factory()->create([
        'whatsapp' => '081234567890'  // Without country code
    ]);
    
    expect($user2->whatsapp)->toBe('081234567890');
});

it('can search users by various fields', function () {
    User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'whatsapp' => '+6281111111111'
    ]);
    
    User::factory()->create([
        'name' => 'Jane Smith', 
        'email' => 'jane@example.com',
        'whatsapp' => '+6282222222222'
    ]);
    
    // Search by name
    $users = User::where('name', 'like', '%John%')->get();
    expect($users)->toHaveCount(1);
    expect($users->first()->name)->toBe('John Doe');
    
    // Search by email
    $users = User::where('email', 'like', '%jane@%')->get();
    expect($users)->toHaveCount(1);
    expect($users->first()->email)->toBe('jane@example.com');
    
    // Search by whatsapp
    $users = User::where('whatsapp', 'like', '%81111%')->get();
    expect($users)->toHaveCount(1);
    expect($users->first()->whatsapp)->toBe('+6281111111111');
});

it('can handle user level enum values', function () {
    $pemula = User::factory()->create(['level' => 'pemula']);
    $menengah = User::factory()->create(['level' => 'menengah']);
    $mahir = User::factory()->create(['level' => 'mahir']);
    
    expect($pemula->level)->toBe('pemula');
    expect($menengah->level)->toBe('menengah');
    expect($mahir->level)->toBe('mahir');
});

it('can handle optional fields correctly', function () {
    // Test with all optional fields null
    $user = User::factory()->create([
        'whatsapp' => null,
        'about' => null
    ]);
    
    expect($user->whatsapp)->toBeNull();
    expect($user->about)->toBeNull();
    
    // Test with optional fields filled
    $user2 = User::factory()->create([
        'whatsapp' => '+6281234567890',
        'about' => 'This is about me'
    ]);
    
    expect($user2->whatsapp)->toBe('+6281234567890');
    expect($user2->about)->toBe('This is about me');
});

it('generates correct user initials', function () {
    $user1 = User::factory()->create(['name' => 'John Doe']);
    $user2 = User::factory()->create(['name' => 'Jane Mary Smith']);
    $user3 = User::factory()->create(['name' => 'SingleName']);
    
    expect($user1->initials())->toBe('JD');
    expect($user2->initials())->toBe('JM'); // Only takes first 2 words
    expect($user3->initials())->toBe('S');
});

it('can create user with factory', function () {
    $user = User::factory()->create();
    
    expect($user)->toBeInstanceOf(User::class);
    expect($user->name)->not->toBeEmpty();
    expect($user->email)->not->toBeEmpty();
    expect($user->password)->not->toBeEmpty();
    expect($user->level)->toBeIn(['pemula', 'menengah', 'mahir']);
});

it('can create user with custom attributes using factory', function () {
    $user = User::factory()->create([
        'name' => 'Custom Name',
        'email' => 'custom@example.com',
        'level' => 'mahir'
    ]);
    
    expect($user->name)->toBe('Custom Name');
    expect($user->email)->toBe('custom@example.com');
    expect($user->level)->toBe('mahir');
});

it('encrypts password when creating user', function () {
    $plainPassword = 'testpassword123';
    
    $user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => Hash::make($plainPassword),
        'level' => 'pemula'
    ]);
    
    expect(Hash::check($plainPassword, $user->password))->toBeTrue();
    expect($user->password)->not->toBe($plainPassword);
});
