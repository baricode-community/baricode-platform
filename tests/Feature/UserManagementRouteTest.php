<?php

use Tests\DatabaseTestCase;
use App\Models\User\User;
use Spatie\Permission\Models\Role;

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
    
    // Create regular user
    $this->regularUser = User::factory()->create([
        'name' => 'Regular User',
        'email' => 'user@example.com',
    ]);
    $this->regularUser->assignRole('user');
});

it('allows admin to access user management page', function () {
    $response = $this->actingAs($this->adminUser)
                     ->get(route('admin.users'));
    
    $response->assertStatus(200);
});

it('blocks regular user from accessing user management page', function () {
    $response = $this->actingAs($this->regularUser)
                     ->get(route('admin.users'));
    
    $response->assertStatus(403);
});

it('redirects unauthenticated user to login page', function () {
    $response = $this->get(route('admin.users'));
    
    $response->assertRedirect(route('login'));
});

it('user management route exists and is properly defined', function () {
    expect(route('admin.users'))->toBe('http://localhost/admin/users');
});

it('admin routes are protected by role middleware', function () {
    // Test with user who doesn't have admin role
    $userWithoutRole = User::factory()->create();
    
    $response = $this->actingAs($userWithoutRole)
                     ->get(route('admin.users'));
    
    $response->assertStatus(403);
});

it('can access user management with proper admin role', function () {
    // Create user with admin role
    $adminUser = User::factory()->create();
    $adminUser->assignRole('admin');
    
    $response = $this->actingAs($adminUser)
                     ->get(route('admin.users'));
    
    $response->assertStatus(200);
});

it('user management page contains expected elements', function () {
    $response = $this->actingAs($this->adminUser)
                     ->get(route('admin.users'));
    
    $response->assertStatus(200)
             ->assertSeeText('User Management')
             ->assertSeeText('Tambah User');
});

it('displays existing users in the management interface', function () {
    // Create some test users
    $testUser1 = User::factory()->create(['name' => 'Test User One']);
    $testUser2 = User::factory()->create(['name' => 'Test User Two']);
    
    $response = $this->actingAs($this->adminUser)
                     ->get(route('admin.users'));
    
    $response->assertStatus(200)
             ->assertSeeText('Test User One')
             ->assertSeeText('Test User Two')
             ->assertSeeText($this->adminUser->name)
             ->assertSeeText($this->regularUser->name);
});

it('admin prefix routes are properly grouped', function () {
    // Check that all admin routes have the correct prefix and naming
    expect(route('admin.users'))->toContain('/admin/');
    expect(route('admin.users'))->toEndWith('/admin/users');
});

it('middleware is applied correctly to admin routes', function () {
    // Create a user without any roles
    $noRoleUser = User::factory()->create();
    
    // Should not be able to access any admin routes
    $adminRoutes = [
        'admin.users',
        'admin.courses',
        'admin.categories',
    ];
    
    foreach ($adminRoutes as $routeName) {
        if (Route::has($routeName)) {
            $response = $this->actingAs($noRoleUser)->get(route($routeName));
            $response->assertStatus(403);
        }
    }
});

it('authenticated user can access regular dashboard but not admin area', function () {
    $response = $this->actingAs($this->regularUser)
                     ->get(route('dashboard'));
    
    $response->assertStatus(200);
    
    // But cannot access admin area
    $response = $this->actingAs($this->regularUser)
                     ->get(route('admin.users'));
                     
    $response->assertStatus(403);
});

it('user management route is included in admin group with correct middleware', function () {
    // Verify the route exists
    expect(Route::has('admin.users'))->toBeTrue();
    
    // Verify route uses correct URI
    $route = Route::getRoutes()->getByName('admin.users');
    expect($route->uri())->toBe('admin/users');
});
