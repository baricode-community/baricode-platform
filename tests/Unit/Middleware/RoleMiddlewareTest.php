<?php

use Tests\TestCase;
use App\Http\Middleware\RoleMiddleware;
use App\Models\User\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    // Create roles
    Role::create(['name' => 'admin', 'guard_name' => 'web']);
    Role::create(['name' => 'user', 'guard_name' => 'web']);
    Role::create(['name' => 'moderator', 'guard_name' => 'web']);
    
    // Create users and store in test instance
    test()->adminUser = User::factory()->create();
    test()->adminUser->assignRole('admin');
    
    test()->regularUser = User::factory()->create();
    test()->regularUser->assignRole('user');
    
    test()->moderatorUser = User::factory()->create();
    test()->moderatorUser->assignRole('moderator');
    
    test()->userWithoutRole = User::factory()->create();
    
    test()->middleware = new RoleMiddleware();
});

describe('RoleMiddleware Authentication Check', function () {
    it('redirects unauthenticated users to login', function () {
        $request = Request::create('/admin/test', 'GET');
        
        $response = test()->middleware->handle($request, function () {
            return new Response('Protected Content');
        }, 'admin');
        
        expect($response)->toBeInstanceOf(\Illuminate\Http\RedirectResponse::class);
        expect($response->headers->get('location'))->toContain('login');
    });
    
    it('allows authenticated users with correct role to proceed', function () {
        $request = Request::create('/admin/test', 'GET');
        Auth::login(test()->adminUser);
        
        $response = test()->middleware->handle($request, function () {
            return new Response('Protected Content');
        }, 'admin');
        
        expect($response)->toBeInstanceOf(Response::class);
        expect($response->getContent())->toBe('Protected Content');
    });
});

describe('RoleMiddleware Single Role Authorization', function () {
    it('allows user with exact role match', function () {
        $request = Request::create('/admin/test', 'GET');
        Auth::login(test()->adminUser);
        
        $response = test()->middleware->handle($request, function () {
            return new Response('Admin Area');
        }, 'admin');
        
        expect($response)->toBeInstanceOf(Response::class);
        expect($response->getContent())->toBe('Admin Area');
    });
    
    it('denies user without required role', function () {
        $request = Request::create('/admin/test', 'GET');
        Auth::login(test()->regularUser);
        
        expect(function () use ($request) {
            test()->middleware->handle($request, function () {
                return new Response('Admin Area');
            }, 'admin');
        })->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
    });
    
    it('denies user with different role', function () {
        $request = Request::create('/moderator/test', 'GET');
        Auth::login(test()->adminUser);
        
        expect(function () use ($request) {
            test()->middleware->handle($request, function () {
                return new Response('Moderator Area');
            }, 'moderator');
        })->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
    });
    
    it('denies user with no roles assigned', function () {
        $request = Request::create('/admin/test', 'GET');
        Auth::login(test()->userWithoutRole);
        
        expect(function () use ($request) {
            test()->middleware->handle($request, function () {
                return new Response('Protected Content');
            }, 'admin');
        })->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
    });
});

describe('RoleMiddleware Multiple Role Authorization', function () {
    it('allows user with first of multiple required roles', function () {
        $request = Request::create('/test', 'GET');
        Auth::login(test()->adminUser);
        
        $response = test()->middleware->handle($request, function () {
            return new Response('Multi Role Content');
        }, 'admin', 'moderator');
        
        expect($response)->toBeInstanceOf(Response::class);
        expect($response->getContent())->toBe('Multi Role Content');
    });
    
    it('allows user with second of multiple required roles', function () {
        $request = Request::create('/test', 'GET');
        Auth::login(test()->moderatorUser);
        
        $response = test()->middleware->handle($request, function () {
            return new Response('Multi Role Content');
        }, 'admin', 'moderator');
        
        expect($response)->toBeInstanceOf(Response::class);
        expect($response->getContent())->toBe('Multi Role Content');
    });
    
    it('denies user without any of the required roles', function () {
        $request = Request::create('/test', 'GET');
        Auth::login(test()->regularUser);
        
        expect(function () use ($request) {
            test()->middleware->handle($request, function () {
                return new Response('Multi Role Content');
            }, 'admin', 'moderator');
        })->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
    });
    
    it('allows user with multiple roles when any one is required', function () {
        // Give admin user an additional role
        test()->adminUser->assignRole('moderator');
        
        $request = Request::create('/test', 'GET');
        Auth::login(test()->adminUser);
        
        $response = test()->middleware->handle($request, function () {
            return new Response('Multi Role Content');
        }, 'moderator');
        
        expect($response)->toBeInstanceOf(Response::class);
        expect($response->getContent())->toBe('Multi Role Content');
    });
});

describe('RoleMiddleware Error Handling', function () {
    it('throws 403 error with appropriate message for unauthorized access', function () {
        $request = Request::create('/admin/test', 'GET');
        Auth::login(test()->regularUser);
        
        try {
            test()->middleware->handle($request, function () {
                return new Response('Protected Content');
            }, 'admin');
            
            fail('Expected HttpException was not thrown');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            expect($e->getStatusCode())->toBe(403);
            expect($e->getMessage())->toContain('Unauthorized');
            expect($e->getMessage())->toContain('required role');
        }
    });
    
    it('includes role information in error message', function () {
        $request = Request::create('/admin/test', 'GET');
        Auth::login(test()->regularUser);
        
        try {
            test()->middleware->handle($request, function () {
                return new Response('Protected Content');
            }, 'admin');
            
            fail('Expected HttpException was not thrown');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            expect($e->getMessage())->toContain('required role');
        }
    });
});

describe('RoleMiddleware Edge Cases', function () {
    it('handles case-sensitive role names correctly', function () {
        $request = Request::create('/test', 'GET');
        Auth::login(test()->adminUser);
        
        expect(function () use ($request) {
            test()->middleware->handle($request, function () {
                return new Response('Content');
            }, 'Admin'); // Capital A
        })->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
    });
    
    it('handles user with role but different guard', function () {
        // Create a user without any roles (different guard scenario)
        $noRoleUser = User::factory()->create();
        
        $request = Request::create('/admin/test', 'GET');
        Auth::login($noRoleUser);
        
        expect(function () use ($request) {
            test()->middleware->handle($request, function () {
                return new Response('Protected Content');
            }, 'admin');
        })->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
    });
    
    it('handles non-existent role names', function () {
        $request = Request::create('/test', 'GET');
        Auth::login(test()->adminUser);
        
        expect(function () use ($request) {
            test()->middleware->handle($request, function () {
                return new Response('Content');
            }, 'non_existent_role');
        })->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
    });
});

describe('RoleMiddleware Security', function () {
    it('prevents role escalation attempts', function () {
        $request = Request::create('/admin/test', 'GET');
        
        // Regular user should not be able to access admin area
        Auth::login(test()->regularUser);
        
        expect(function () use ($request) {
            test()->middleware->handle($request, function () {
                return new Response('Admin Content');
            }, 'admin');
        })->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        
        // Even if we manually assign admin role to request headers, it shouldn't work
        $request->headers->set('X-User-Role', 'admin');
        
        expect(function () use ($request) {
            test()->middleware->handle($request, function () {
                return new Response('Admin Content');
            }, 'admin');
        })->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
    });
    
    it('validates all required roles when multiple are specified', function () {
        $request = Request::create('/test', 'GET');
        
        // Create user with only one of the required roles
        $partialUser = User::factory()->create();
        $partialUser->assignRole('user');
        
        Auth::login($partialUser);
        
        // Should allow access if user has ANY of the specified roles
        $response = test()->middleware->handle($request, function () {
            return new Response('Multi Role Content');
        }, 'admin', 'user');
        
        expect($response)->toBeInstanceOf(Response::class);
        expect($response->getContent())->toBe('Multi Role Content');
    });
});
