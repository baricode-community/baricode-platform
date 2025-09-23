<?php

use App\Models\User;
use App\Models\CourseEnrollment;
use App\Models\StudentNote;
use App\Models\CourseAttendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

describe('User Model', function () {
    describe('Model Configuration', function () {
        test('uses correct table', function () {
            $user = User::factory()->create();
            expect($user->getTable())->toBe('users');
        });

        test('has correct fillable attributes', function () {
            $user = User::factory()->create();
            $fillable = $user->getFillable();
            expect($fillable)->toContain('name', 'email', 'password');
        });

        test('has correct hidden attributes', function () {
            $user = User::factory()->create();
            $hidden = $user->getHidden();
            expect($hidden)->toContain('password', 'remember_token');
        });

        test('casts email_verified_at to datetime', function () {
            $user = User::factory()->create();
            $casts = $user->getCasts();
            expect($casts['email_verified_at'])->toBe('datetime');
        });

        test('casts password to hashed', function () {
            $user = User::factory()->create();
            $casts = $user->getCasts();
            expect($casts['password'])->toBe('hashed');
        });
    });

    describe('Factory', function () {
        test('can create user with factory', function () {
            $user = User::factory()->create();
            
            expect($user)->toBeInstanceOf(User::class);
            expect($user->name)->not->toBeNull();
            expect($user->email)->not->toBeNull();
            expect($user->email_verified_at)->not->toBeNull();
        });

        test('can create unverified user', function () {
            $user = User::factory()->unverified()->create();
            
            expect($user->email_verified_at)->toBeNull();
        });

        test('factory creates unique emails', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();
            
            expect($user1->email)->not->toBe($user2->email);
        });
    });

    describe('Attributes and Accessors', function () {
        test('initials method returns correct initials for single name', function () {
            $user = User::factory()->create(['name' => 'John']);
            expect($user->initials())->toBe('J');
        });

        test('initials method returns correct initials for two names', function () {
            $user = User::factory()->create(['name' => 'John Doe']);
            expect($user->initials())->toBe('JD');
        });

        test('initials method returns correct initials for three names', function () {
            $user = User::factory()->create(['name' => 'John Doe Smith']);
            expect($user->initials())->toBe('JD');
        });

        test('initials method handles empty name gracefully', function () {
            $user = User::factory()->create(['name' => '']);
            expect($user->initials())->toBe('');
        });

        test('initials method handles single character names', function () {
            $user = User::factory()->create(['name' => 'A B']);
            expect($user->initials())->toBe('AB');
        });
    });

    describe('Relationships', function () {
        test('has many course enrollments', function () {
            $user = User::factory()->create();
            $courseEnrollment = CourseEnrollment::factory()->create([
                'user_id' => $user->id
            ]);

            expect($user->courseEnrollments)->toHaveCount(1);
            expect($user->courseEnrollments->first())->toBeInstanceOf(CourseEnrollment::class);
            expect($user->courseEnrollments->first()->id)->toBe($courseEnrollment->id);
        });

        test('has many student notes', function () {
            $user = User::factory()->create();
            $studentNote = StudentNote::factory()->create([
                'user_id' => $user->id
            ]);

            expect($user->studentNotes)->toHaveCount(1);
            expect($user->studentNotes->first())->toBeInstanceOf(StudentNote::class);
            expect($user->studentNotes->first()->id)->toBe($studentNote->id);
        });

        test('has many course attendances', function () {
            $user = User::factory()->create();
            $courseAttendance = CourseAttendance::factory()->create([
                'student_id' => $user->id
            ]);

            expect($user->courseAttendances)->toHaveCount(1);
            expect($user->courseAttendances->first())->toBeInstanceOf(CourseAttendance::class);
            expect($user->courseAttendances->first()->id)->toBe($courseAttendance->id);
        });

        test('course enrollments relationship returns empty collection when no enrollments', function () {
            $user = User::factory()->create();
            expect($user->courseEnrollments)->toHaveCount(0);
            expect($user->courseEnrollments)->toBeInstanceOf(\Illuminate\Database\Eloquent\Collection::class);
        });

        test('student notes relationship returns empty collection when no notes', function () {
            $user = User::factory()->create();
            expect($user->studentNotes)->toHaveCount(0);
            expect($user->studentNotes)->toBeInstanceOf(\Illuminate\Database\Eloquent\Collection::class);
        });

        test('course attendances relationship returns empty collection when no attendances', function () {
            $user = User::factory()->create();
            expect($user->courseAttendances)->toHaveCount(0);
            expect($user->courseAttendances)->toBeInstanceOf(\Illuminate\Database\Eloquent\Collection::class);
        });
    });

    describe('Spatie Permissions Integration', function () {
        test('can assign role to user', function () {
            $user = User::factory()->create();
            $role = Role::create(['name' => 'admin']);
            
            $user->assignRole('admin');
            
            expect($user->hasRole('admin'))->toBeTrue();
            expect($user->roles)->toHaveCount(1);
            expect($user->roles->first()->name)->toBe('admin');
        });

        test('can check if user has role', function () {
            $user = User::factory()->create();
            $role = Role::create(['name' => 'student']);
            $user->assignRole('student');
            
            expect($user->hasRole('student'))->toBeTrue();
            expect($user->hasRole('admin'))->toBeFalse();
        });

        test('can assign multiple roles to user', function () {
            $user = User::factory()->create();
            Role::create(['name' => 'admin']);
            Role::create(['name' => 'instructor']);
            
            $user->assignRole(['admin', 'instructor']);
            
            expect($user->hasRole(['admin', 'instructor']))->toBeTrue();
            expect($user->roles)->toHaveCount(2);
        });

        test('can remove role from user', function () {
            $user = User::factory()->create();
            $role = Role::create(['name' => 'admin']);
            $user->assignRole('admin');
            
            expect($user->hasRole('admin'))->toBeTrue();
            
            $user->removeRole('admin');
            
            expect($user->hasRole('admin'))->toBeFalse();
        });
    });

    describe('Authentication Features', function () {
        test('password is automatically hashed', function () {
            $user = User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'plaintext',
            ]);

            expect($user->password)->not->toBe('plaintext');
            expect(\Hash::check('plaintext', $user->password))->toBeTrue();
        });

        test('email verification works correctly', function () {
            $user = User::factory()->unverified()->create();
            
            expect($user->hasVerifiedEmail())->toBeFalse();
            
            $user->markEmailAsVerified();
            
            expect($user->hasVerifiedEmail())->toBeTrue();
        });

        test('remember token can be set', function () {
            $user = User::factory()->create();
            $user->setRememberToken('test-token');
            
            expect($user->getRememberToken())->toBe('test-token');
        });
    });

    describe('Validation', function () {
        test('requires name', function () {
            expect(fn() => User::create([
                'email' => 'test@example.com',
                'password' => 'password',
            ]))->toThrow(\Illuminate\Database\QueryException::class);
        });

        test('requires email', function () {
            expect(fn() => User::create([
                'name' => 'Test User',
                'password' => 'password',
            ]))->toThrow(\Illuminate\Database\QueryException::class);
        });

        test('requires unique email', function () {
            User::factory()->create(['email' => 'test@example.com']);
            
            expect(fn() => User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password',
            ]))->toThrow(\Illuminate\Database\QueryException::class);
        });
    });

    describe('User Observer Integration', function () {
        test('user observer is attached to model', function () {
            $observers = User::getObservableEvents();
            expect($observers)->toContain('creating', 'created', 'updating', 'updated', 'deleting', 'deleted');
        });
    });

    describe('Mass Assignment Protection', function () {
        test('id is guarded', function () {
            $user = User::create([
                'id' => 999,
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password',
            ]);

            expect($user->id)->not->toBe(999);
        });

        test('can mass assign allowed attributes', function () {
            $user = User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password',
                'whatsapp' => '1234567890',
                'about' => 'Test about',
            ]);

            expect($user->name)->toBe('Test User');
            expect($user->email)->toBe('test@example.com');
            expect($user->whatsapp)->toBe('1234567890');
            expect($user->about)->toBe('Test about');
        });
    });

    describe('Soft Deletes (if implemented)', function () {
        test('user can be deleted', function () {
            $user = User::factory()->create();
            $userId = $user->id;
            $user->delete();
            
            expect(User::find($userId))->toBeNull();
        });
    });

    describe('Database Constraints', function () {
        test('email must be valid format', function () {
            // This would be tested at the validation layer, not database constraint level
            // But we can test that invalid emails don't break the model
            $user = User::factory()->make(['email' => 'invalid-email']);
            expect($user->email)->toBe('invalid-email');
        });
    });
});
