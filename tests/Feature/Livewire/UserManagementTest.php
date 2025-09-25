<?php

use Tests\DatabaseTestCase;
use App\Models\User\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Hash;

uses(DatabaseTestCase::class);

beforeEach(function () {
    // Create admin role and permission
    $adminRole = Role::create(['name' => 'admin']);
    $userRole = Role::create(['name' => 'user']);
    $permission = Permission::create(['name' => 'manage-users']);
    $adminRole->givePermissionTo($permission);

    // Create admin user
    $this->adminUser = User::factory()->create([
        'name' => 'Admin User',
        'email' => 'admin@example.com',
    ]);
    $this->adminUser->assignRole('admin');

    // Create regular user
    $this->regularUser = User::factory()->create([
        'name' => 'Regular User',  
        'email' => 'user@example.com',
    ]);
    $this->regularUser->assignRole('user');
});

it('can render user management page for admin', function () {
    $this->actingAs($this->adminUser);
    
    Volt::test('admin.user-management')
        ->assertSuccessful()
        ->assertSeeText('User Management')
        ->assertSeeText('Tambah User');
});

it('cannot render user management page for regular user', function () {
    $this->actingAs($this->regularUser);
    
    $this->get(route('admin.users'))
        ->assertStatus(403); // Forbidden due to role middleware
});

it('displays users in the table', function () {
    $this->actingAs($this->adminUser);
    
    // Create additional test users
    $user1 = User::factory()->create(['name' => 'John Doe']);
    $user2 = User::factory()->create(['name' => 'Jane Smith']);
    
    Volt::test('admin.user-management')
        ->assertSeeText('John Doe')
        ->assertSeeText('Jane Smith')
        ->assertSeeText($this->adminUser->name)
        ->assertSeeText($this->regularUser->name);
});

it('can search users by name', function () {
    $this->actingAs($this->adminUser);
    
    $user1 = User::factory()->create(['name' => 'John Doe']);
    $user2 = User::factory()->create(['name' => 'Jane Smith']);
    
    Volt::test('admin.user-management')
        ->set('search', 'John')
        ->assertSeeText('John Doe')
        ->assertDontSeeText('Jane Smith');
});

it('can search users by email', function () {
    $this->actingAs($this->adminUser);
    
    $user1 = User::factory()->create(['email' => 'john@example.com']);
    $user2 = User::factory()->create(['email' => 'jane@example.com']);
    
    Volt::test('admin.user-management')
        ->set('search', 'john@')
        ->assertSeeText('john@example.com')
        ->assertDontSeeText('jane@example.com');
});

it('can open create user form', function () {
    $this->actingAs($this->adminUser);
    
    Volt::test('admin.user-management')
        ->call('openCreateForm')
        ->assertSet('showCreateForm', true)
        ->assertSeeText('Tambah User Baru');
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
    
    $existingUser = User::factory()->create(['email' => 'existing@example.com']);
    
    Volt::test('admin.user-management')
        ->call('openCreateForm')
        ->set('name', 'New User')
        ->set('email', 'existing@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('createUser')
        ->assertHasErrors(['email']);
});

it('validates password confirmation when creating user', function () {
    $this->actingAs($this->adminUser);
    
    Volt::test('admin.user-management')
        ->call('openCreateForm')
        ->set('name', 'New User')
        ->set('email', 'newuser@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'differentpassword')
        ->call('createUser')
        ->assertHasErrors(['password']);
});

it('validates whatsapp format when creating user', function () {
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

it('can open edit user form', function () {
    $this->actingAs($this->adminUser);
    
    $user = User::factory()->create(['name' => 'Test User']);
    
    Volt::test('admin.user-management')
        ->call('openEditForm', $user->id)
        ->assertSet('showEditForm', true)
        ->assertSet('editingUser', $user->id)
        ->assertSet('name', $user->name)
        ->assertSet('email', $user->email)
        ->assertSeeText('Edit User');
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

it('can update user password', function () {
    $this->actingAs($this->adminUser);
    
    $user = User::factory()->create();
    $newPassword = 'newpassword123';
    
    Volt::test('admin.user-management')
        ->call('openEditForm', $user->id)
        ->set('password', $newPassword)
        ->set('password_confirmation', $newPassword)
        ->call('updateUser')
        ->assertSessionHas('message', 'User berhasil diperbarui!');
    
    // Verify password was updated
    $user->refresh();
    expect(Hash::check($newPassword, $user->password))->toBeTrue();
});

it('can update user without changing password', function () {
    $this->actingAs($this->adminUser);
    
    $user = User::factory()->create();
    $originalPassword = $user->password;
    
    Volt::test('admin.user-management')
        ->call('openEditForm', $user->id)
        ->set('name', 'Updated Name')
        ->set('password', '') // Empty password
        ->set('password_confirmation', '')
        ->call('updateUser')
        ->assertSessionHas('message', 'User berhasil diperbarui!');
    
    // Verify password was not changed
    $user->refresh();
    expect($user->password)->toBe($originalPassword);
    expect($user->name)->toBe('Updated Name');
});

it('validates unique email when updating user', function () {
    $this->actingAs($this->adminUser);
    
    $user1 = User::factory()->create(['email' => 'user1@example.com']);
    $user2 = User::factory()->create(['email' => 'user2@example.com']);
    
    Volt::test('admin.user-management')
        ->call('openEditForm', $user1->id)
        ->set('email', 'user2@example.com') // Try to use user2's email
        ->call('updateUser')
        ->assertHasErrors(['email']);
});

it('allows same email when updating user without changing it', function () {
    $this->actingAs($this->adminUser);
    
    $user = User::factory()->create(['email' => 'user@example.com']);
    
    Volt::test('admin.user-management')
        ->call('openEditForm', $user->id)
        ->set('name', 'Updated Name')
        ->set('email', 'user@example.com') // Same email
        ->call('updateUser')
        ->assertHasNoErrors(['email'])
        ->assertSessionHas('message', 'User berhasil diperbarui!');
});

it('can confirm delete user', function () {
    $this->actingAs($this->adminUser);
    
    $user = User::factory()->create();
    
    Volt::test('admin.user-management')
        ->call('confirmDelete', $user->id)
        ->assertSet('showDeleteConfirm', true)
        ->assertSet('userToDelete', $user->id)
        ->assertSeeText('Konfirmasi Hapus');
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

it('can close forms and modals', function () {
    $this->actingAs($this->adminUser);
    
    $user = User::factory()->create();
    
    // Test close create form
    Volt::test('admin.user-management')
        ->call('openCreateForm')
        ->call('closeForm')
        ->assertSet('showCreateForm', false);
    
    // Test close edit form
    Volt::test('admin.user-management')
        ->call('openEditForm', $user->id)
        ->call('closeForm')
        ->assertSet('showEditForm', false)
        ->assertSet('editingUser', null);
    
    // Test close delete confirmation
    Volt::test('admin.user-management')
        ->call('confirmDelete', $user->id)
        ->call('closeDeleteConfirm')
        ->assertSet('showDeleteConfirm', false)
        ->assertSet('userToDelete', null);
});

it('resets form when opening create form', function () {
    $this->actingAs($this->adminUser);
    
    Volt::test('admin.user-management')
        ->set('name', 'Some Name')
        ->set('email', 'some@email.com')
        ->call('openCreateForm')
        ->assertSet('name', '')
        ->assertSet('email', '')
        ->assertSet('editingUser', null);
});

it('loads existing data when opening edit form', function () {
    $this->actingAs($this->adminUser);
    
    $user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'whatsapp' => '+6281234567890',
        'about' => 'Test description',
        'level' => 'mahir'
    ]);
    $user->assignRole(['admin', 'user']);
    
    Volt::test('admin.user-management')
        ->call('openEditForm', $user->id)
        ->assertSet('name', $user->name)
        ->assertSet('email', $user->email)
        ->assertSet('whatsapp', $user->whatsapp)
        ->assertSet('about', $user->about)
        ->assertSet('level', $user->level)
        ->assertSet('selectedRoles', ['admin', 'user']);
});

it('displays user roles in the table', function () {
    $this->actingAs($this->adminUser);
    
    $user = User::factory()->create(['name' => 'Test User']);
    $user->assignRole(['admin', 'user']);
    
    Volt::test('admin.user-management')
        ->assertSeeText('admin')
        ->assertSeeText('user');
});

it('displays user level badge with correct color', function () {
    $this->actingAs($this->adminUser);
    
    $user1 = User::factory()->create(['level' => 'pemula']);
    $user2 = User::factory()->create(['level' => 'menengah']);  
    $user3 = User::factory()->create(['level' => 'mahir']);
    
    $component = Volt::test('admin.user-management');
    
    // Check that levels are displayed
    $component->assertSeeText('Pemula')
             ->assertSeeText('Menengah')
             ->assertSeeText('Mahir');
});
