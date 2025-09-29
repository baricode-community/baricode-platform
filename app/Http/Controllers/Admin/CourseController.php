<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course\Course;
use App\Models\Course\CourseCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Course::with('courseCategory');
        
        // Filter by category if provided
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }
        
        $courses = $query->orderBy('title')->paginate(10);
        $categories = CourseCategory::orderBy('name')->get();
        $selectedCategory = $request->category ? CourseCategory::find($request->category) : null;
        
        return view('admin.courses.index', compact('courses', 'categories', 'selectedCategory'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $categories = CourseCategory::orderBy('name')->get();
        $selectedCategoryId = $request->get('category');
        
        return view('admin.courses.create', compact('categories', 'selectedCategoryId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:course_categories,id',
            'level' => 'required|in:beginner,intermediate,advanced',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'duration_hours' => $request->duration_hours,
            'level' => $request->level,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ];

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('course-thumbnails', 'public');
            $data['thumbnail'] = $thumbnailPath;
        }

        Course::create($data);

        return redirect()->route('admin.courses.index', ['category' => $request->category_id])
            ->with('success', 'Kursus berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        $course->load(['courseCategory', 'courseModules']);
        
        return view('admin.courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        $categories = CourseCategory::where('is_active', 1)->orderBy('name')->get();
        
        return view('admin.courses.edit', compact('course', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:course_categories,id',
            'level' => 'required|in:beginner,intermediate,advanced',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'duration_hours' => $request->duration_hours,
            'level' => $request->level,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ];

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail if exists
            if ($course->thumbnail && \Storage::disk('public')->exists($course->thumbnail)) {
                \Storage::disk('public')->delete($course->thumbnail);
            }
            
            $thumbnailPath = $request->file('thumbnail')->store('course-thumbnails', 'public');
            $data['thumbnail'] = $thumbnailPath;
        }

        $course->update($data);

        return redirect()->route('admin.courses.index', ['category' => $course->category_id])
            ->with('success', 'Kursus berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        // Check if course has modules
        if ($course->courseModules()->count() > 0) {
            return redirect()->route('admin.courses.index')
                ->with('error', 'Tidak dapat menghapus kursus yang masih memiliki modul.');
        }

        // Delete thumbnail if exists
        if ($course->thumbnail && \Storage::disk('public')->exists($course->thumbnail)) {
            \Storage::disk('public')->delete($course->thumbnail);
        }

        $categoryId = $course->category_id;
        $course->delete();

        return redirect()->route('admin.courses.index', ['category' => $categoryId])
            ->with('success', 'Kursus berhasil dihapus.');
    }

    /**
     * Navigate to course modules management for this course
     */
    public function modules(Course $course)
    {
        return redirect()->route('admin.course-modules.index', ['course' => $course->id]);
    }
}
