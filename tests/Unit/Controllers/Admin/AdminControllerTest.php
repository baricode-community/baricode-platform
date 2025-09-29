<?php

use Tests\DatabaseTestCase;
use App\Http\Controllers\AdminController;
use App\Models\User\User;
use App\Models\Course\Course;
use Spatie\Permission\Models\Role;

uses(DatabaseTestCase::class);

beforeEach(function () {
    // Create admin role if it doesn't exist
    if (!Role::where('name', 'admin')->exists()) {
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
    }
    
    // Create admin user
    $this->adminUser = User::factory()->create();
    $this->adminUser->assignRole('admin');
    
    // Create regular user
    $this->regularUser = User::factory()->create(['email_verified_at' => now()]);
    
    // Create test courses
    $this->publishedCourse = Course::factory()->create([
        'is_published' => true,
        'is_active' => true
    ]);
    
    $this->unpublishedCourse = Course::factory()->create([
        'is_published' => false,
        'is_active' => true
    ]);
    
    $this->controller = new AdminController();
});

describe('AdminController Index Method', function () {
    it('can display admin dashboard with correct statistics', function () {
        $response = $this->actingAs($this->adminUser)
                         ->get(route('admin'));
        
        $response->assertStatus(200)
                 ->assertViewIs('pages.admin.index')
                 ->assertViewHas('users_count')
                 ->assertViewHas('courses_count');
    });
    
    it('displays correct user count', function () {
        // Create additional verified users
        User::factory()->count(3)->create(['email_verified_at' => now()]);
        
        $response = $this->actingAs($this->adminUser)
                         ->get(route('admin'));
        
        $response->assertStatus(200);
        
        // Should count admin user + regular user + 3 additional users = 5 total
        $expectedUserCount = User::where('email_verified_at', '!=', null)->count();
        $response->assertViewHas('users_count', $expectedUserCount);
    });
    
    it('displays correct published courses count', function () {
        // Create additional published courses
        Course::factory()->count(2)->create(['is_published' => true]);
        
        $response = $this->actingAs($this->adminUser)
                         ->get(route('admin'));
        
        $response->assertStatus(200);
        
        // Should count all published courses
        $expectedCourseCount = Course::where('is_published', true)->count();
        $response->assertViewHas('courses_count', $expectedCourseCount);
    });
    
    it('excludes unpublished courses from count', function () {
        // Create more unpublished courses
        Course::factory()->count(3)->create(['is_published' => false]);
        
        $response = $this->actingAs($this->adminUser)
                         ->get(route('admin'));
        
        $response->assertStatus(200);
        
        // Should only count published courses
        $expectedCourseCount = Course::where('is_published', true)->count();
        $response->assertViewHas('courses_count', $expectedCourseCount);
        
        // Verify unpublished courses are not counted
        $totalCourses = Course::count();
        expect($expectedCourseCount)->toBeLessThan($totalCourses);
    });
    
    it('excludes unverified users from count', function () {
        // Create unverified users
        User::factory()->count(2)->create(['email_verified_at' => null]);
        
        $response = $this->actingAs($this->adminUser)
                         ->get(route('admin'));
        
        $response->assertStatus(200);
        
        // Should only count verified users
        $expectedUserCount = User::where('email_verified_at', '!=', null)->count();
        $response->assertViewHas('users_count', $expectedUserCount);
        
        // Verify unverified users are not counted
        $totalUsers = User::count();
        expect($expectedUserCount)->toBeLessThan($totalUsers);
    });
});

describe('AdminController Access Control', function () {
    it('requires authentication to access admin dashboard', function () {
        $response = $this->get(route('admin'));
        
        $response->assertRedirect(route('login'));
    });
    
    it('requires admin role to access admin dashboard', function () {
        $response = $this->actingAs($this->regularUser)
                         ->get(route('admin'));
        
        $response->assertStatus(403);
    });
    
    it('allows admin users to access admin dashboard', function () {
        $response = $this->actingAs($this->adminUser)
                         ->get(route('admin'));
        
        $response->assertStatus(200);
    });
});

describe('AdminController View Components', function () {
    it('loads admin index view with required data', function () {
        $response = $this->actingAs($this->adminUser)
                         ->get(route('admin'));
        
        $response->assertStatus(200)
                 ->assertViewIs('pages.admin.index')
                 ->assertSee('Admin Panel')
                 ->assertSee('Manajemen Pengguna')
                 ->assertSee('Manajemen Kursus');
    });
    
    it('displays user information in view', function () {
        $response = $this->actingAs($this->adminUser)
                         ->get(route('admin'));
        
        $response->assertStatus(200)
                 ->assertSee($this->adminUser->name)
                 ->assertSee($this->adminUser->email);
    });
    
    it('includes navigation links to admin sections', function () {
        $response = $this->actingAs($this->adminUser)
                         ->get(route('admin'));
        
        $response->assertStatus(200)
                 ->assertSee(route('admin.users'))
                 ->assertSee('admin/course-management');
    });
});

describe('AdminController Performance', function () {
    it('efficiently calculates statistics without N+1 queries', function () {
        // Create test data
        User::factory()->count(10)->create(['email_verified_at' => now()]);
        Course::factory()->count(15)->create(['is_published' => true]);
        
        // Monitor database queries
        $queryCount = 0;
        \DB::listen(function () use (&$queryCount) {
            $queryCount++;
        });
        
        $response = $this->actingAs($this->adminUser)
                         ->get(route('admin'));
        
        $response->assertStatus(200);
        
        // Should not have excessive queries (max 5 for basic operations)
        expect($queryCount)->toBeLessThanOrEqual(5);
    });
});

describe('AdminController Edge Cases', function () {
    it('handles empty database correctly', function () {
        // Clear all test data
        User::query()->delete();
        Course::query()->delete();
        
        // Recreate only admin user
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        $response = $this->actingAs($adminUser)
                         ->get(route('admin'));
        
        $response->assertStatus(200)
                 ->assertViewHas('users_count', 0)  // No verified users (admin is not verified in this test)
                 ->assertViewHas('courses_count', 0);
    });
    
    it('handles large numbers of records correctly', function () {
        // Create large dataset
        User::factory()->count(1000)->create(['email_verified_at' => now()]);
        Course::factory()->count(500)->create(['is_published' => true]);
        
        $response = $this->actingAs($this->adminUser)
                         ->get(route('admin'));
        
        $response->assertStatus(200);
        
        $expectedUserCount = User::where('email_verified_at', '!=', null)->count();
        $expectedCourseCount = Course::where('is_published', true)->count();
        
        $response->assertViewHas('users_count', $expectedUserCount)
                 ->assertViewHas('courses_count', $expectedCourseCount);
    });
});
