<?php

use Tests\DatabaseTestCase;
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
});

describe('AdminController Index Method', function () {
    it('can display admin dashboard with correct statistics', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        Course::factory()->count(3)->create(['is_published' => true]);
        User::factory()->count(5)->create(['email_verified_at' => now()]);
        
        $response = $this->actingAs($adminUser)
                         ->get(route('admin'));
        
        $response->assertStatus(200)
                 ->assertViewIs('pages.admin.index')
                 ->assertViewHas('users_count')
                 ->assertViewHas('courses_count');
    });
    
    it('displays correct user count', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        // Create additional verified users
        User::factory()->count(3)->create(['email_verified_at' => now()]);
        
        $response = $this->actingAs($adminUser)
                         ->get(route('admin'));
        
        $response->assertStatus(200);
        
        $expectedUserCount = User::where('email_verified_at', '!=', null)->count();
        $response->assertViewHas('users_count', $expectedUserCount);
    });
    
    it('displays correct published courses count', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        // Create courses with different published status
        Course::factory()->count(3)->create(['is_published' => true]);
        Course::factory()->count(2)->create(['is_published' => false]);
        
        $response = $this->actingAs($adminUser)
                         ->get(route('admin'));
        
        $response->assertStatus(200);
        
        $expectedCourseCount = Course::where('is_published', true)->count();
        $response->assertViewHas('courses_count', $expectedCourseCount);
        
        // Verify it's exactly 3 published courses
        expect($expectedCourseCount)->toBe(3);
    });
    
    it('excludes unpublished courses from count', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        Course::factory()->count(2)->create(['is_published' => true]);
        Course::factory()->count(5)->create(['is_published' => false]);
        
        $response = $this->actingAs($adminUser)
                         ->get(route('admin'));
        
        $response->assertStatus(200);
        
        $publishedCount = Course::where('is_published', true)->count();
        $totalCount = Course::count();
        
        $response->assertViewHas('courses_count', $publishedCount);
        
        expect($publishedCount)->toBe(2);
        expect($totalCount)->toBe(7);
        expect($publishedCount)->toBeLessThan($totalCount);
    });
    
    it('excludes unverified users from count', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        User::factory()->count(3)->create(['email_verified_at' => now()]);
        User::factory()->count(2)->create(['email_verified_at' => null]);
        
        $response = $this->actingAs($adminUser)
                         ->get(route('admin'));
        
        $response->assertStatus(200);
        
        $verifiedCount = User::where('email_verified_at', '!=', null)->count();
        $totalCount = User::count();
        
        $response->assertViewHas('users_count', $verifiedCount);
        
        expect($verifiedCount)->toBeLessThan($totalCount);
    });
});

describe('AdminController Access Control', function () {
    it('requires authentication to access admin dashboard', function () {
        $response = $this->get(route('admin'));
        
        $response->assertRedirect(route('login'));
    });
    
    it('requires admin role to access admin dashboard', function () {
        $regularUser = User::factory()->create();
        
        $response = $this->actingAs($regularUser)
                         ->get(route('admin'));
        
        $response->assertStatus(403);
    });
    
    it('allows admin users to access admin dashboard', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        $response = $this->actingAs($adminUser)
                         ->get(route('admin'));
        
        $response->assertStatus(200);
    });
});

describe('AdminController View Components', function () {
    it('loads admin index view with required data', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        $response = $this->actingAs($adminUser)
                         ->get(route('admin'));
        
        $response->assertStatus(200)
                 ->assertViewIs('pages.admin.index')
                 ->assertSee('Admin Panel');
    });
    
    it('displays user information in view', function () {
        $adminUser = User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com'
        ]);
        $adminUser->assignRole('admin');
        
        $response = $this->actingAs($adminUser)
                         ->get(route('admin'));
        
        $response->assertStatus(200)
                 ->assertSee('Test Admin')
                 ->assertSee('admin@test.com');
    });
});

describe('AdminController Edge Cases', function () {
    it('handles empty database correctly', function () {
        // Clear all existing data
        User::query()->delete();
        Course::query()->delete();
        
        // Create only admin user (unverified)
        $adminUser = User::factory()->create(['email_verified_at' => null]);
        $adminUser->assignRole('admin');
        
        $response = $this->actingAs($adminUser)
                         ->get(route('admin'));
        
        $response->assertStatus(200)
                 ->assertViewHas('users_count', 0)
                 ->assertViewHas('courses_count', 0);
    });
});
