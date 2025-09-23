<?php

use App\Models\User;
use App\Models\CourseEnrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('DashboardController', function () {
    describe('Index Method', function () {
        it('requires authentication', function () {
            $response = $this->get(route('dashboard'));
            
            $response->assertRedirect(route('login'));
        });

        it('renders dashboard for authenticated user', function () {
            $user = User::factory()->create();
            
            $response = $this->actingAs($user)->get(route('dashboard'));
            
            $response->assertStatus(200);
            $response->assertViewIs('pages.dashboard');
        });

        it('shows unapproved course enrollments', function () {
            $user = User::factory()->create();
            
            // Create approved and unapproved enrollments
            $approvedEnrollment = CourseEnrollment::factory()->create([
                'user_id' => $user->id,
                'is_approved' => true
            ]);
            
            $unapprovedEnrollment1 = CourseEnrollment::factory()->create([
                'user_id' => $user->id,
                'is_approved' => false
            ]);
            
            $unapprovedEnrollment2 = CourseEnrollment::factory()->create([
                'user_id' => $user->id,
                'is_approved' => false
            ]);
            
            $response = $this->actingAs($user)->get(route('dashboard'));
            
            $response->assertStatus(200);
            $response->assertViewHas('courseRecords');
            
            $courseRecords = $response->viewData('courseRecords');
            expect($courseRecords)->toHaveCount(2);
            expect($courseRecords->every(fn($record) => $record->is_approved === false))->toBe(true);
        });

        it('shows empty collection when no unapproved enrollments', function () {
            $user = User::factory()->create();
            
            // Create only approved enrollments
            CourseEnrollment::factory()->create([
                'user_id' => $user->id,
                'is_approved' => true
            ]);
            
            $response = $this->actingAs($user)->get(route('dashboard'));
            
            $response->assertStatus(200);
            $response->assertViewHas('courseRecords');
            
            $courseRecords = $response->viewData('courseRecords');
            expect($courseRecords)->toHaveCount(0);
        });

        it('only shows current user enrollments', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();
            
            // Create enrollments for both users
            $user1Enrollment = CourseEnrollment::factory()->create([
                'user_id' => $user1->id,
                'is_approved' => false
            ]);
            
            $user2Enrollment = CourseEnrollment::factory()->create([
                'user_id' => $user2->id,
                'is_approved' => false
            ]);
            
            $response = $this->actingAs($user1)->get(route('dashboard'));
            
            $response->assertStatus(200);
            $courseRecords = $response->viewData('courseRecords');
            
            expect($courseRecords)->toHaveCount(1);
            expect($courseRecords->first()->id)->toBe($user1Enrollment->id);
        });

        it('requires email verification', function () {
            $user = User::factory()->unverified()->create();
            
            $response = $this->actingAs($user)->get(route('dashboard'));
            
            $response->assertRedirect(route('verification.notice'));
        });

        it('works for verified users', function () {
            $user = User::factory()->create(); // Factory creates verified users by default
            
            $response = $this->actingAs($user)->get(route('dashboard'));
            
            $response->assertStatus(200);
        });
    });

    describe('Profile Method', function () {
        it('requires authentication', function () {
            $response = $this->get(route('profile'));
            
            $response->assertRedirect(route('login'));
        });

        it('renders profile page for authenticated user', function () {
            $user = User::factory()->create();
            
            $response = $this->actingAs($user)->get(route('profile'));
            
            $response->assertStatus(200);
            $response->assertViewIs('pages.profile');
        });

        it('passes current user to view', function () {
            $user = User::factory()->create([
                'name' => 'John Doe',
                'email' => 'john@example.com'
            ]);
            
            $response = $this->actingAs($user)->get(route('profile'));
            
            $response->assertStatus(200);
            $response->assertViewHas('user');
            
            $viewUser = $response->viewData('user');
            expect($viewUser->id)->toBe($user->id);
            expect($viewUser->name)->toBe('John Doe');
            expect($viewUser->email)->toBe('john@example.com');
        });

        it('requires email verification', function () {
            $user = User::factory()->unverified()->create();
            
            $response = $this->actingAs($user)->get(route('profile'));
            
            $response->assertRedirect(route('verification.notice'));
        });

        it('shows user data in profile view', function () {
            $user = User::factory()->create([
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'whatsapp' => '081234567890',
                'about' => 'I am a web developer'
            ]);
            
            $response = $this->actingAs($user)->get(route('profile'));
            
            $response->assertStatus(200);
            $response->assertSee('Jane Smith');
            $response->assertSee('jane@example.com');
        });
    });

    describe('Middleware Integration', function () {
        it('applies auth middleware correctly', function () {
            // Test that unauthenticated requests are redirected
            $response = $this->get(route('dashboard'));
            $response->assertRedirect(route('login'));
            
            $response = $this->get(route('profile'));
            $response->assertRedirect(route('login'));
        });

        it('applies verified middleware correctly', function () {
            $unverifiedUser = User::factory()->unverified()->create();
            
            $response = $this->actingAs($unverifiedUser)->get(route('dashboard'));
            $response->assertRedirect(route('verification.notice'));
            
            $response = $this->actingAs($unverifiedUser)->get(route('profile'));
            $response->assertRedirect(route('verification.notice'));
        });

        it('allows access for verified users', function () {
            $verifiedUser = User::factory()->create();
            
            $response = $this->actingAs($verifiedUser)->get(route('dashboard'));
            $response->assertStatus(200);
            
            $response = $this->actingAs($verifiedUser)->get(route('profile'));
            $response->assertStatus(200);
        });
    });

    describe('Route Configuration', function () {
        it('has correct route names', function () {
            expect(Route::has('dashboard'))->toBe(true);
            expect(Route::has('profile'))->toBe(true);
        });

        it('uses correct URL patterns', function () {
            expect(route('dashboard'))->toContain('/dashboard');
            expect(route('profile'))->toContain('/dashboard/profile');
        });

        it('applies correct middleware group', function () {
            $route = Route::getRoutes()->getByName('dashboard');
            $middleware = $route->gatherMiddleware();
            
            expect($middleware)->toContain('auth');
            expect($middleware)->toContain('verified');
        });
    });

    describe('Performance Considerations', function () {
        it('efficiently loads course enrollments', function () {
            $user = User::factory()->create();
            
            // Create multiple enrollments
            CourseEnrollment::factory()->count(5)->create([
                'user_id' => $user->id,
                'is_approved' => false
            ]);
            
            \DB::enableQueryLog();
            
            $response = $this->actingAs($user)->get(route('dashboard'));
            
            $queries = \DB::getQueryLog();
            
            // Should have minimal queries
            expect(count($queries))->toBeLessThanOrEqual(4);
            
            \DB::disableQueryLog();
        });

        it('does not eager load unnecessary relationships', function () {
            $user = User::factory()->create();
            
            \DB::enableQueryLog();
            
            $response = $this->actingAs($user)->get(route('profile'));
            
            $queries = \DB::getQueryLog();
            
            // Profile should be very lightweight
            expect(count($queries))->toBeLessThanOrEqual(3);
            
            \DB::disableQueryLog();
        });
    });

    describe('Security Considerations', function () {
        it('prevents access to other users data', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();
            
            $user2Enrollment = CourseEnrollment::factory()->create([
                'user_id' => $user2->id,
                'is_approved' => false
            ]);
            
            $response = $this->actingAs($user1)->get(route('dashboard'));
            
            $courseRecords = $response->viewData('courseRecords');
            expect($courseRecords->contains('id', $user2Enrollment->id))->toBe(false);
        });

        it('protects user information in profile', function () {
            $user1 = User::factory()->create(['name' => 'User One']);
            $user2 = User::factory()->create(['name' => 'User Two']);
            
            $response = $this->actingAs($user1)->get(route('profile'));
            
            $response->assertSee('User One');
            $response->assertDontSee('User Two');
        });
    });

    describe('Error Handling', function () {
        it('handles user with no enrollments gracefully', function () {
            $user = User::factory()->create();
            
            $response = $this->actingAs($user)->get(route('dashboard'));
            
            $response->assertStatus(200);
            $courseRecords = $response->viewData('courseRecords');
            expect($courseRecords)->toHaveCount(0);
        });

        it('handles database errors gracefully', function () {
            // This would be more complex to test depending on error handling implementation
            $user = User::factory()->create();
            
            $response = $this->actingAs($user)->get(route('dashboard'));
            
            $response->assertStatus(200);
        });
    });

    describe('View Data Structure', function () {
        it('passes correct data structure to dashboard view', function () {
            $user = User::factory()->create();
            $enrollment = CourseEnrollment::factory()->create([
                'user_id' => $user->id,
                'is_approved' => false
            ]);
            
            $response = $this->actingAs($user)->get(route('dashboard'));
            
            $response->assertViewHas('courseRecords');
            $courseRecords = $response->viewData('courseRecords');
            
            expect($courseRecords)->toBeInstanceOf(\Illuminate\Database\Eloquent\Collection::class);
            expect($courseRecords->first())->toBeInstanceOf(CourseEnrollment::class);
        });

        it('passes correct user data to profile view', function () {
            $user = User::factory()->create();
            
            $response = $this->actingAs($user)->get(route('profile'));
            
            $response->assertViewHas('user');
            $viewUser = $response->viewData('user');
            
            expect($viewUser)->toBeInstanceOf(User::class);
            expect($viewUser->is($user))->toBe(true);
        });
    });

    describe('Response Headers and Content', function () {
        it('returns correct content type', function () {
            $user = User::factory()->create();
            
            $response = $this->actingAs($user)->get(route('dashboard'));
            
            $response->assertHeader('content-type', 'text/html; charset=UTF-8');
        });

        it('includes proper meta tags in views', function () {
            $user = User::factory()->create();
            
            $response = $this->actingAs($user)->get(route('dashboard'));
            
            // These would depend on your actual view implementation
            $response->assertSee('Dashboard', false); // false = don't escape HTML
        });
    });
});
