<?php

use Tests\DatabaseTestCase;
use App\Models\User\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Hash;

uses(DatabaseTestCase::class);

beforeEach(function () {
    // Create roles
    Role::create(['name' => 'admin']);
    Role::create(['name' => 'user']);
    
    // Create admin user
    $this->adminUser = User::factory()->create([
        'name' => 'Admin User',
        'email' => 'admin@example.com',
    ]);
    $this->adminUser->assignRole('admin');
});

it('can create a new user', function () {
    $this->actingAs($this->adminUser);
    
    $userData = [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'whatsapp' => '+6281234567890',
        'about' => 'Test user description',
        'level' => 'menengah',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'selectedRoles' => ['user']
    ];
    
    Volt::test('admin.user-management')
        ->call('openCreateForm')
        ->set('name', $userData['name'])
        ->set('email', $userData['email'])
        ->set('whatsapp', $userData['whatsapp'])
        ->set('about', $userData['about'])
        ->set('level', $userData['level'])
        ->set('password', $userData['password'])
        ->set('password_confirmation', $userData['password_confirmation'])
        ->set('selectedRoles', $userData['selectedRoles'])
        ->call('createUser')
        ->assertSet('showCreateForm', false)
        ->assertSessionHas('message', 'User berhasil dibuat!');
    
    // Verify user was created
    $user = User::where('email', $userData['email'])->first();
    expect($user)->not->toBeNull();
    expect($user->name)->toBe($userData['name']);
    expect($user->email)->toBe($userData['email']);
    expect($user->whatsapp)->toBe($userData['whatsapp']);
    expect($user->about)->toBe($userData['about']);
    expect($user->level)->toBe($userData['level']);
    expect($user->hasRole('user'))->toBeTrue();
    expect(Hash::check($userData['password'], $user->password))->toBeTrue();
});

it('validates required fields when creating user', function () {
    $this->actingAs($this->adminUser);
    
    Volt::test('admin.user-management')
        ->call('openCreateForm')
        ->set('name', '')
        ->set('email', '')
        ->set('password', '')
        ->call('createUser')
        ->assertHasErrors(['name', 'email', 'password']);
});

it('validates unique email when creating user', function () {
    $this->actingAs($this->adminUser);
    
    User::factory()->create(['email' => 'existing@example.com']);
    
    Volt::test('admin.user-management')
        ->call('openCreateForm')
        ->set('name', 'New User')
        ->set('email', 'existing@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('createUser')
        ->assertHasErrors(['email']);
});

it('can update existing user', function () {
    $this->actingAs($this->adminUser);
    
    $user = User::factory()->create([
        'name' => 'Original Name',
        'email' => 'original@example.com',
        'level' => 'pemula'
    ]);
    $user->assignRole('user');
    
    $updatedData = [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'whatsapp' => '+6287654321098',
        'about' => 'Updated description',
        'level' => 'mahir',
        'selectedRoles' => ['admin']
    ];
    
    Volt::test('admin.user-management')
        ->call('openEditForm', $user->id)
        ->set('name', $updatedData['name'])
        ->set('email', $updatedData['email'])
        ->set('whatsapp', $updatedData['whatsapp'])
        ->set('about', $updatedData['about'])
        ->set('level', $updatedData['level'])
        ->set('selectedRoles', $updatedData['selectedRoles'])
        ->call('updateUser')
        ->assertSet('showEditForm', false)
        ->assertSessionHas('message', 'User berhasil diperbarui!');
    
    // Verify user was updated
    $user->refresh();
    expect($user->name)->toBe($updatedData['name']);
    expect($user->email)->toBe($updatedData['email']);
    expect($user->whatsapp)->toBe($updatedData['whatsapp']);
    expect($user->about)->toBe($updatedData['about']);
    expect($user->level)->toBe($updatedData['level']);
    expect($user->hasRole('admin'))->toBeTrue();
    expect($user->hasRole('user'))->toBeFalse();
});

it('can delete user', function () {
    $this->actingAs($this->adminUser);
    
    $user = User::factory()->create(['name' => 'User To Delete']);
    
    Volt::test('admin.user-management')
        ->call('confirmDelete', $user->id)
        ->call('deleteUser')
        ->assertSet('showDeleteConfirm', false)
        ->assertSet('userToDelete', null)
        ->assertSessionHas('message', 'User berhasil dihapus!');
    
    // Verify user was soft deleted
    expect(User::withTrashed()->find($user->id)->trashed())->toBeTrue();
    expect(User::find($user->id))->toBeNull();
});

it('can search users', function () {
    $this->actingAs($this->adminUser);
    
    User::factory()->create(['name' => 'John Doe']);
    User::factory()->create(['name' => 'Jane Smith']);
    
    Volt::test('admin.user-management')
        ->set('search', 'John')
        ->assertSeeText('John Doe')
        ->assertDontSeeText('Jane Smith');
});

it('displays user management page correctly', function () {
    $this->actingAs($this->adminUser);
    
    Volt::test('admin.user-management')
        ->assertSuccessful()
        ->assertSeeText('User Management')
        ->assertSeeText('Tambah User');
});

it('can open and close forms', function () {
    $this->actingAs($this->adminUser);
    
    $user = User::factory()->create();
    
    // Test create form
    Volt::test('admin.user-management')
        ->call('openCreateForm')
        ->assertSet('showCreateForm', true)
        ->call('closeForm')
        ->assertSet('showCreateForm', false);
    
    // Test edit form
    Volt::test('admin.user-management')
        ->call('openEditForm', $user->id)
        ->assertSet('showEditForm', true)
        ->assertSet('editingUser', $user->id)
        ->call('closeForm')
        ->assertSet('showEditForm', false);
});

it('can update user password', function () {
    $this->actingAs($this->adminUser);
    
    $user = User::factory()->create();
    $newPassword = 'newpassword123';
    
    Volt::test('admin.user-management')
        ->call('openEditForm', $user->id)
        ->set('password', $newPassword)
        ->set('password_confirmation', $newPassword)
        ->call('updateUser');
    
    // Verify password was updated
    $user->refresh();
    expect(Hash::check($newPassword, $user->password))->toBeTrue();
});

it('validates whatsapp format', function () {
    $this->actingAs($this->adminUser);
    
    Volt::test('admin.user-management')
        ->call('openCreateForm')
        ->set('name', 'New User')
        ->set('email', 'newuser@example.com')
        ->set('whatsapp', 'invalid-whatsapp')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('createUser')
        ->assertHasErrors(['whatsapp']);
});
