<?php

use Tests\DatabaseTestCase;
use App\Models\User\User;
use App\Models\Course\CourseCategory;
use App\Models\Course\Course;
use Spatie\Permission\Models\Role;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(DatabaseTestCase::class);

beforeEach(function () {
    // Create admin role if it doesn't exist
    if (!Role::where('name', 'admin')->exists()) {
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
    }
});

describe('CourseCategoryController Index Method', function () {
    it('can display category index with pagination', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        // Create categories with courses
        CourseCategory::factory()->count(15)->create()->each(function ($category) {
            Course::factory()->count(2)->create(['category_id' => $category->id]);
        });
        
        $response = $this->actingAs($adminUser)
                         ->get(route('admin.course-categories.index'));
        
        $response->assertStatus(200)
                 ->assertViewIs('admin.course-categories.index')
                 ->assertViewHas('categories');
        
        $categories = $response->viewData('categories');
        expect($categories)->toHaveCount(10); // First page of pagination
        expect($categories->total())->toBe(15); // Total count
    });
    
    it('loads categories with course count', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        $category = CourseCategory::factory()->create(['name' => 'Test Category']);
        Course::factory()->count(3)->create(['category_id' => $category->id]);
        
        $response = $this->actingAs($adminUser)
                         ->get(route('admin.course-categories.index'));
        
        $response->assertStatus(200);
        
        $categories = $response->viewData('categories');
        $testCategory = $categories->first();
        expect($testCategory->courses_count)->toBe(3);
    });
    
    it('orders categories by name', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        CourseCategory::factory()->create(['name' => 'Zebra Category']);
        CourseCategory::factory()->create(['name' => 'Alpha Category']);
        CourseCategory::factory()->create(['name' => 'Beta Category']);
        
        $response = $this->actingAs($adminUser)
                         ->get(route('admin.course-categories.index'));
        
        $response->assertStatus(200);
        
        $categories = $response->viewData('categories');
        expect($categories->pluck('name')->toArray())
            ->toBe(['Alpha Category', 'Beta Category', 'Zebra Category']);
    });
});

describe('CourseCategoryController Create Method', function () {
    it('can display create form', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        $response = $this->actingAs($adminUser)
                         ->get(route('admin.course-categories.create'));
        
        $response->assertStatus(200)
                 ->assertViewIs('admin.course-categories.create');
    });
});

describe('CourseCategoryController Store Method', function () {
    it('can create new category with valid data', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        $categoryData = [
            'name' => 'New Test Category',
            'description' => 'Test description',
            'level' => 'menengah'
        ];
        
        $response = $this->actingAs($adminUser)
                         ->post(route('admin.course-categories.store'), $categoryData);
        
        $response->assertRedirect(route('admin.course-categories.index'));
        
        expect(CourseCategory::where('name', 'New Test Category')->exists())->toBeTrue();
    });
    
    it('validates required fields', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        $response = $this->actingAs($adminUser)
                         ->post(route('admin.course-categories.store'), []);
        
        $response->assertSessionHasErrors(['name']);
    });
    
    it('validates unique name constraint', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        CourseCategory::factory()->create(['name' => 'Existing Category']);
        
        $response = $this->actingAs($adminUser)
                         ->post(route('admin.course-categories.store'), [
                             'name' => 'Existing Category'
                         ]);
        
        $response->assertSessionHasErrors(['name']);
    });
    
    it('validates level field values', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        $validLevels = ['pemula', 'menengah', 'lanjut'];
        
        foreach ($validLevels as $level) {
            $response = $this->actingAs($adminUser)
                             ->post(route('admin.course-categories.store'), [
                                 'name' => "Category $level",
                                 'level' => $level
                             ]);
            
            $response->assertRedirect(route('admin.course-categories.index'));
            expect(CourseCategory::where('name', "Category $level")->first()->level)->toBe($level);
        }
    });
    
    it('rejects invalid level values', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        $response = $this->actingAs($adminUser)
                         ->post(route('admin.course-categories.store'), [
                             'name' => 'Invalid Level Category',
                             'level' => 'invalid_level'
                         ]);
        
        $response->assertSessionHasErrors(['level']);
    });
});

describe('CourseCategoryController Show Method', function () {
    it('can display category details with courses', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        $category = CourseCategory::factory()->create();
        Course::factory()->count(3)->create(['category_id' => $category->id]);
        
        $response = $this->actingAs($adminUser)
                         ->get(route('admin.course-categories.show', $category));
        
        $response->assertStatus(200)
                 ->assertViewIs('admin.course-categories.show')
                 ->assertViewHas('courseCategory');
        
        $viewCategory = $response->viewData('courseCategory');
        expect($viewCategory->courses)->toHaveCount(3);
    });
});

describe('CourseCategoryController Edit Method', function () {
    it('can display edit form with existing data', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        $category = CourseCategory::factory()->create([
            'name' => 'Edit Test Category',
            'description' => 'Original description'
        ]);
        
        $response = $this->actingAs($adminUser)
                         ->get(route('admin.course-categories.edit', $category));
        
        $response->assertStatus(200)
                 ->assertViewIs('admin.course-categories.edit')
                 ->assertViewHas('courseCategory')
                 ->assertSee('Edit Test Category')
                 ->assertSee('Original description');
    });
});

describe('CourseCategoryController Update Method', function () {
    it('can update category with valid data', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        $category = CourseCategory::factory()->create([
            'name' => 'Original Name',
            'description' => 'Original description',
            'level' => 'pemula'
        ]);
        
        $updateData = [
            'name' => 'Updated Name',
            'description' => 'Updated description',
            'level' => 'menengah'
        ];
        
        $response = $this->actingAs($adminUser)
                         ->put(route('admin.course-categories.update', $category), $updateData);
        
        $response->assertRedirect(route('admin.course-categories.index'));
        
        $updatedCategory = CourseCategory::find($category->id);
        expect($updatedCategory->name)->toBe('Updated Name');
        expect($updatedCategory->description)->toBe('Updated description');
        expect($updatedCategory->level)->toBe('menengah');
    });
    
    it('validates unique name constraint on update', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        $category1 = CourseCategory::factory()->create(['name' => 'Category 1']);
        $category2 = CourseCategory::factory()->create(['name' => 'Category 2']);
        
        $response = $this->actingAs($adminUser)
                         ->put(route('admin.course-categories.update', $category2), [
                             'name' => 'Category 1'
                         ]);
        
        $response->assertSessionHasErrors(['name']);
    });
    
    it('allows keeping same name on update', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        $category = CourseCategory::factory()->create(['name' => 'Same Name']);
        
        $response = $this->actingAs($adminUser)
                         ->put(route('admin.course-categories.update', $category), [
                             'name' => 'Same Name',
                             'description' => 'Updated description',
                             'level' => 'lanjut'
                         ]);
        
        $response->assertRedirect(route('admin.course-categories.index'));
        
        $updatedCategory = CourseCategory::find($category->id);
        expect($updatedCategory->name)->toBe('Same Name');
        expect($updatedCategory->description)->toBe('Updated description');
        expect($updatedCategory->level)->toBe('lanjut');
    });
});

describe('CourseCategoryController Destroy Method', function () {
    it('can delete category without courses', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        $category = CourseCategory::factory()->create();
        $categoryId = $category->id;
        
        $response = $this->actingAs($adminUser)
                         ->delete(route('admin.course-categories.destroy', $category));
        
        $response->assertRedirect(route('admin.course-categories.index'));
        
        expect(CourseCategory::find($categoryId))->toBeNull();
    });
    
    it('prevents deletion of category with courses', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        $category = CourseCategory::factory()->create();
        Course::factory()->create(['category_id' => $category->id]);
        
        $response = $this->actingAs($adminUser)
                         ->delete(route('admin.course-categories.destroy', $category));
        
        $response->assertRedirect(route('admin.course-categories.index'));
        
        expect(CourseCategory::find($category->id))->not->toBeNull();
    });
});

describe('CourseCategoryController Courses Method', function () {
    it('redirects to courses index with category filter', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        $category = CourseCategory::factory()->create();
        
        $response = $this->actingAs($adminUser)
                         ->get(route('admin.course-categories.courses', $category));
        
        $response->assertRedirect(route('admin.courses.index', ['category' => $category->id]));
    });
});

describe('CourseCategoryController Access Control', function () {
    it('requires authentication for all routes', function () {
        $category = CourseCategory::factory()->create();
        
        $routes = [
            ['get', route('admin.course-categories.index')],
            ['get', route('admin.course-categories.create')],
            ['post', route('admin.course-categories.store')],
            ['get', route('admin.course-categories.show', $category)],
            ['get', route('admin.course-categories.edit', $category)],
            ['put', route('admin.course-categories.update', $category)],
            ['delete', route('admin.course-categories.destroy', $category)],
        ];
        
        foreach ($routes as [$method, $url]) {
            $response = $this->$method($url);
            $response->assertRedirect(route('login'));
        }
    });
    
    it('requires admin role for all routes', function () {
        $regularUser = User::factory()->create();
        $category = CourseCategory::factory()->create();
        
        $routes = [
            ['get', route('admin.course-categories.index')],
            ['get', route('admin.course-categories.create')],
            ['post', route('admin.course-categories.store')],
            ['get', route('admin.course-categories.show', $category)],
            ['get', route('admin.course-categories.edit', $category)],
            ['put', route('admin.course-categories.update', $category)],
            ['delete', route('admin.course-categories.destroy', $category)],
        ];
        
        foreach ($routes as [$method, $url]) {
            $response = $this->actingAs($regularUser)->$method($url);
            $response->assertStatus(403);
        }
    });
});

describe('CourseCategoryController Edge Cases', function () {
    it('handles empty categories list', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        $response = $this->actingAs($adminUser)
                         ->get(route('admin.course-categories.index'));
        
        $response->assertStatus(200);
        
        $categories = $response->viewData('categories');
        expect($categories)->toHaveCount(0);
    });
    
    it('handles category with many courses', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        $category = CourseCategory::factory()->create();
        Course::factory()->count(100)->create(['category_id' => $category->id]);
        
        $response = $this->actingAs($adminUser)
                         ->get(route('admin.course-categories.show', $category));
        
        $response->assertStatus(200);
        
        $viewCategory = $response->viewData('courseCategory');
        expect($viewCategory->courses)->toHaveCount(100);
    });
});
