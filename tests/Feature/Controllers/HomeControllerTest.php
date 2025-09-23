<?php

use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

describe('HomeController', function () {
    describe('Index Method', function () {
        it('renders index page successfully', function () {
            $response = $this->get(route('home'));
            
            $response->assertStatus(200);
            $response->assertViewIs('pages.home.index');
        });

        it('is accessible without authentication', function () {
            $response = $this->get('/');
            
            $response->assertStatus(200);
        });
    });

    describe('Terms of Service Method', function () {
        it('renders tos page successfully', function () {
            $response = $this->get(route('tos'));
            
            $response->assertStatus(200);
            $response->assertViewIs('pages.home.tos');
        });

        it('redirects tos page via redirect', function () {
            $response = $this->get('/tos');
            
            $response->assertRedirect('/terms-of-service');
        });

        it('renders terms-of-service page successfully', function () {
            $response = $this->get('/terms-of-service');
            
            $response->assertStatus(200);
            $response->assertViewIs('pages.home.tos');
        });
    });

    describe('About Method', function () {
        it('renders about page successfully', function () {
            $response = $this->get(route('about'));
            
            $response->assertStatus(200);
            $response->assertViewIs('pages.home.about');
        });

        it('is accessible without authentication', function () {
            $response = $this->get('/about');
            
            $response->assertStatus(200);
        });
    });

    describe('Cara Belajar Method', function () {
        it('renders cara belajar page successfully', function () {
            $response = $this->get(route('cara_belajar'));
            
            $response->assertStatus(200);
            $response->assertViewIs('pages.home.cara_belajar');
        });

        it('is accessible without authentication', function () {
            $response = $this->get('/cara-belajar');
            
            $response->assertStatus(200);
        });
    });

    describe('Courses Method', function () {
        it('renders courses page successfully', function () {
            $response = $this->get(route('courses'));
            
            $response->assertStatus(200);
            $response->assertViewIs('pages.home.course.index');
        });

        it('shows all categories', function () {
            $category1 = CourseCategory::factory()->create(['name' => 'Web Development']);
            $category2 = CourseCategory::factory()->create(['name' => 'Mobile Development']);
            
            $response = $this->get(route('courses'));
            
            $response->assertStatus(200);
            $response->assertViewHas('categories');
            
            $categories = $response->viewData('categories');
            expect($categories)->toHaveCount(2);
            expect($categories->pluck('name'))->toContain('Web Development', 'Mobile Development');
        });

        it('handles empty categories', function () {
            $response = $this->get(route('courses'));
            
            $response->assertStatus(200);
            $response->assertViewHas('categories');
            
            $categories = $response->viewData('categories');
            expect($categories)->toHaveCount(0);
        });
    });

    describe('Course Detail Method', function () {
        it('renders course detail page for published course', function () {
            $course = Course::factory()->create([
                'is_published' => true,
                'slug' => 'test-course'
            ]);
            
            $response = $this->get(route('course.show', ['course' => $course->slug]));
            
            $response->assertStatus(200);
            $response->assertViewIs('pages.home.course.show');
            $response->assertViewHas('course');
            
            $viewCourse = $response->viewData('course');
            expect($viewCourse->id)->toBe($course->id);
        });

        it('returns 404 for unpublished course', function () {
            $course = Course::factory()->create([
                'is_published' => false,
                'slug' => 'unpublished-course'
            ]);
            
            $response = $this->get(route('course.show', ['course' => $course->slug]));
            
            $response->assertStatus(404);
        });

        it('returns 404 for non-existent course', function () {
            $response = $this->get(route('course.show', ['course' => 'non-existent-course']));
            
            $response->assertStatus(404);
        });

        it('shows correct course data', function () {
            $category = CourseCategory::factory()->create();
            $course = Course::factory()->create([
                'is_published' => true,
                'slug' => 'laravel-basics',
                'title' => 'Laravel Basics',
                'description' => 'Learn Laravel fundamentals',
                'category_id' => $category->id
            ]);
            
            $response = $this->get(route('course.show', ['course' => $course->slug]));
            
            $response->assertStatus(200);
            $response->assertSee('Laravel Basics');
            $response->assertSee('Learn Laravel fundamentals');
        });
    });

    describe('Course Level Methods', function () {
        describe('Pemula Level', function () {
            it('renders pemula courses page successfully', function () {
                $response = $this->get(route('courses.pemula'));
                
                $response->assertStatus(200);
                $response->assertViewIs('pages.home.course.level.pemula');
            });

            it('shows only pemula level categories', function () {
                $pemulaCategory = CourseCategory::factory()->create(['level' => 'pemula']);
                $menengahCategory = CourseCategory::factory()->create(['level' => 'menengah']);
                $lanjutCategory = CourseCategory::factory()->create(['level' => 'lanjut']);
                
                $response = $this->get(route('courses.pemula'));
                
                $response->assertStatus(200);
                $response->assertViewHas('categories');
                
                $categories = $response->viewData('categories');
                expect($categories)->toHaveCount(1);
                expect($categories->first()->level)->toBe('pemula');
            });

            it('handles no pemula categories', function () {
                CourseCategory::factory()->create(['level' => 'menengah']);
                
                $response = $this->get(route('courses.pemula'));
                
                $response->assertStatus(200);
                $response->assertViewHas('categories');
                
                $categories = $response->viewData('categories');
                expect($categories)->toHaveCount(0);
            });
        });

        describe('Menengah Level', function () {
            it('renders menengah courses page successfully', function () {
                $response = $this->get(route('courses.menengah'));
                
                $response->assertStatus(200);
                $response->assertViewIs('pages.home.course.level.menengah');
            });

            it('shows only menengah level categories', function () {
                $pemulaCategory = CourseCategory::factory()->create(['level' => 'pemula']);
                $menengahCategory1 = CourseCategory::factory()->create(['level' => 'menengah']);
                $menengahCategory2 = CourseCategory::factory()->create(['level' => 'menengah']);
                $lanjutCategory = CourseCategory::factory()->create(['level' => 'lanjut']);
                
                $response = $this->get(route('courses.menengah'));
                
                $response->assertStatus(200);
                $response->assertViewHas('categories');
                
                $categories = $response->viewData('categories');
                expect($categories)->toHaveCount(2);
                expect($categories->every(fn($cat) => $cat->level === 'menengah'))->toBe(true);
            });
        });

        describe('Lanjut Level', function () {
            it('renders lanjut courses page successfully', function () {
                $response = $this->get(route('courses.lanjut'));
                
                $response->assertStatus(200);
                $response->assertViewIs('pages.home.course.level.lanjut');
            });

            it('shows only lanjut level categories', function () {
                $pemulaCategory = CourseCategory::factory()->create(['level' => 'pemula']);
                $menengahCategory = CourseCategory::factory()->create(['level' => 'menengah']);
                $lanjutCategory1 = CourseCategory::factory()->create(['level' => 'lanjut']);
                $lanjutCategory2 = CourseCategory::factory()->create(['level' => 'lanjut']);
                $lanjutCategory3 = CourseCategory::factory()->create(['level' => 'lanjut']);
                
                $response = $this->get(route('courses.lanjut'));
                
                $response->assertStatus(200);
                $response->assertViewHas('categories');
                
                $categories = $response->viewData('categories');
                expect($categories)->toHaveCount(3);
                expect($categories->every(fn($cat) => $cat->level === 'lanjut'))->toBe(true);
            });
        });
    });

    describe('Route Resolution', function () {
        it('has all routes properly registered', function () {
            expect(Route::has('home'))->toBe(true);
            expect(Route::has('tos'))->toBe(true);
            expect(Route::has('about'))->toBe(true);
            expect(Route::has('cara_belajar'))->toBe(true);
            expect(Route::has('courses'))->toBe(true);
            expect(Route::has('course.show'))->toBe(true);
            expect(Route::has('courses.pemula'))->toBe(true);
            expect(Route::has('courses.menengah'))->toBe(true);
            expect(Route::has('courses.lanjut'))->toBe(true);
        });

        it('works with route parameters correctly', function () {
            $course = Course::factory()->create(['is_published' => true]);
            
            $url = route('course.show', ['course' => $course->slug]);
            expect($url)->toContain($course->slug);
        });
    });

    describe('View Data Consistency', function () {
        it('passes categories correctly in courses method', function () {
            $categories = CourseCategory::factory()->count(3)->create();
            
            $response = $this->get(route('courses'));
            
            $response->assertViewHas('categories');
            $viewCategories = $response->viewData('categories');
            
            expect($viewCategories->count())->toBe(3);
            expect($viewCategories->pluck('id')->toArray())->toBe($categories->pluck('id')->toArray());
        });

        it('passes correct course in course method', function () {
            $course = Course::factory()->create(['is_published' => true]);
            
            $response = $this->get(route('course.show', ['course' => $course->slug]));
            
            $response->assertViewHas('course');
            $viewCourse = $response->viewData('course');
            
            expect($viewCourse->id)->toBe($course->id);
            expect($viewCourse->slug)->toBe($course->slug);
        });

        it('passes correct filtered categories in level methods', function () {
            $pemulaCategories = CourseCategory::factory()->count(2)->create(['level' => 'pemula']);
            $menengahCategories = CourseCategory::factory()->count(3)->create(['level' => 'menengah']);
            
            // Test pemula
            $response = $this->get(route('courses.pemula'));
            $response->assertViewHas('categories');
            expect($response->viewData('categories')->count())->toBe(2);
            
            // Test menengah
            $response = $this->get(route('courses.menengah'));
            $response->assertViewHas('categories');
            expect($response->viewData('categories')->count())->toBe(3);
        });
    });

    describe('Error Handling', function () {
        it('handles course with null slug gracefully', function () {
            // This tests edge cases in route model binding
            $response = $this->get('/course/');
            $response->assertStatus(404);
        });

        it('handles invalid course slug format', function () {
            $response = $this->get('/course/invalid-slug-with-special-chars!!!');
            $response->assertStatus(404);
        });
    });

    describe('Security Considerations', function () {
        it('properly validates published status in course detail method', function () {
            $unpublishedCourse = Course::factory()->create(['is_published' => false]);
            
            $response = $this->get(route('course.show', ['course' => $unpublishedCourse->slug]));
            
            $response->assertStatus(404);
        });

        it('does not expose sensitive course data', function () {
            $course = Course::factory()->create([
                'is_published' => true,
                'internal_notes' => 'This should not be visible'
            ]);
            
            $response = $this->get(route('course.show', ['course' => $course->slug]));
            
            $response->assertDontSee('This should not be visible');
        });
    });
});
