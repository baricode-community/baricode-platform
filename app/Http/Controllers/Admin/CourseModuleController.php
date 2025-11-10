<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Learning\CourseModule;
use App\Models\Learning\Course;
use Illuminate\Http\Request;

class CourseModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CourseModule::with('course.courseCategory');
        
        // Filter by course if provided
        if ($request->has('course') && $request->course) {
            $query->where('course_id', $request->course);
        }
        
        $modules = $query->orderBy('order')->paginate(10);
        $courses = Course::with('courseCategory')->orderBy('title')->get();
        $selectedCourse = $request->course ? Course::with('courseCategory')->find($request->course) : null;
        
        return view('admin.course-modules.index', compact('modules', 'courses', 'selectedCourse'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $courses = Course::with('courseCategory')->where('is_active', 1)->orderBy('title')->get();
        $selectedCourseId = $request->get('course');
        
        // Get next order number for the selected course
        $nextOrder = 1;
        if ($selectedCourseId) {
            $lastModule = CourseModule::where('course_id', $selectedCourseId)->orderBy('order', 'desc')->first();
            $nextOrder = $lastModule ? $lastModule->order + 1 : 1;
        }
        
        return view('admin.course-modules.create', compact('courses', 'selectedCourseId', 'nextOrder'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_id' => 'required|exists:courses,id',
            'order' => 'required|integer|min:1',
            'duration_minutes' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        // Check if order already exists for this course
        $existingModule = CourseModule::where('course_id', $request->course_id)
            ->where('order', $request->order)
            ->first();
            
        if ($existingModule) {
            return back()->withErrors(['order' => 'Urutan ini sudah digunakan untuk kursus ini.'])
                ->withInput();
        }

        CourseModule::create([
            'title' => $request->title,
            'description' => $request->description,
            'course_id' => $request->course_id,
            'order' => $request->order,
            'duration_minutes' => $request->duration_minutes,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('admin.course-modules.index', ['course' => $request->course_id])
            ->with('success', 'Modul kursus berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CourseModule $courseModule)
    {
        $courseModule->load(['course.courseCategory', 'courseModuleLessons']);
        
        return view('admin.course-modules.show', compact('courseModule'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CourseModule $courseModule)
    {
        $courses = Course::with('courseCategory')->where('is_active', 1)->orderBy('title')->get();
        
        return view('admin.course-modules.edit', compact('courseModule', 'courses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CourseModule $courseModule)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_id' => 'required|exists:courses,id',
            'order' => 'required|integer|min:1',
            'duration_minutes' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        // Check if order already exists for this course (excluding current module)
        $existingModule = CourseModule::where('course_id', $request->course_id)
            ->where('order', $request->order)
            ->where('id', '!=', $courseModule->id)
            ->first();
            
        if ($existingModule) {
            return back()->withErrors(['order' => 'Urutan ini sudah digunakan untuk kursus ini.'])
                ->withInput();
        }

        $courseModule->update([
            'title' => $request->title,
            'description' => $request->description,
            'course_id' => $request->course_id,
            'order' => $request->order,
            'duration_minutes' => $request->duration_minutes,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('admin.course-modules.index', ['course' => $courseModule->course_id])
            ->with('success', 'Modul kursus berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseModule $courseModule)
    {
        // Check if module has lessons
        if ($courseModule->courseModuleLessons()->count() > 0) {
            return redirect()->route('admin.course-modules.index')
                ->with('error', 'Tidak dapat menghapus modul yang masih memiliki pelajaran.');
        }

        $courseId = $courseModule->course_id;
        $courseModule->delete();

        return redirect()->route('admin.course-modules.index', ['course' => $courseId])
            ->with('success', 'Modul kursus berhasil dihapus.');
    }

    /**
     * Navigate to module lessons management for this module
     */
    public function lessons(CourseModule $courseModule)
    {
        return redirect()->route('admin.course-module-lessons.index', ['module' => $courseModule->id]);
    }

    /**
     * Reorder modules for a specific course
     */
    public function reorder(Request $request, Course $course)
    {
        $request->validate([
            'modules' => 'required|array',
            'modules.*.id' => 'required|exists:course_modules,id',
            'modules.*.order' => 'required|integer|min:1',
        ]);

        foreach ($request->modules as $moduleData) {
            CourseModule::where('id', $moduleData['id'])
                ->where('course_id', $course->id)
                ->update(['order' => $moduleData['order']]);
        }

        return response()->json(['success' => true, 'message' => 'Urutan modul berhasil diperbarui.']);
    }
}
