<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course\CourseCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourseCategoryController extends Controller
{
    public function __construct()
    {
        // Middleware akan diatur di routes
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = CourseCategory::withCount('courses')->orderBy('name')->paginate(10);
        
        return view('admin.course-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.course-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:course_categories,name',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        CourseCategory::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('admin.course-categories.index')
            ->with('success', 'Kategori kursus berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CourseCategory $courseCategory)
    {
        $courseCategory->load('courses');
        
        return view('admin.course-categories.show', compact('courseCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CourseCategory $courseCategory)
    {
        return view('admin.course-categories.edit', compact('courseCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CourseCategory $courseCategory)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('course_categories')->ignore($courseCategory->id)],
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $courseCategory->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('admin.course-categories.index')
            ->with('success', 'Kategori kursus berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseCategory $courseCategory)
    {
        // Check if category has courses
        if ($courseCategory->courses()->count() > 0) {
            return redirect()->route('admin.course-categories.index')
                ->with('error', 'Tidak dapat menghapus kategori yang masih memiliki kursus.');
        }

        $courseCategory->delete();

        return redirect()->route('admin.course-categories.index')
            ->with('success', 'Kategori kursus berhasil dihapus.');
    }

    /**
     * Navigate to courses management for this category
     */
    public function courses(CourseCategory $courseCategory)
    {
        return redirect()->route('admin.courses.index', ['category' => $courseCategory->id]);
    }
}
