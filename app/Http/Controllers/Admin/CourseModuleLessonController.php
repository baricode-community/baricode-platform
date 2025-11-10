<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Learning\CourseModuleLesson;
use App\Models\Learning\CourseModule;
use Illuminate\Http\Request;

class CourseModuleLessonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CourseModuleLesson::with('courseModule.course.courseCategory');
        
        // Filter by module if provided
        if ($request->has('module') && $request->module) {
            $query->where('module_id', $request->module);
        }
        
        $lessons = $query->orderBy('order')->paginate(10);
        $modules = CourseModule::with('course.courseCategory')->orderBy('title')->get();
        $selectedModule = $request->module ? CourseModule::with('course.courseCategory')->find($request->module) : null;
        
        return view('admin.course-module-lessons.index', compact('lessons', 'modules', 'selectedModule'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $modules = CourseModule::with('course.courseCategory')
            ->where('is_active', 1)
            ->orderBy('title')
            ->get();
        $selectedModuleId = $request->get('module');
        
        // Get next order number for the selected module
        $nextOrder = 1;
        if ($selectedModuleId) {
            $lastLesson = CourseModuleLesson::where('module_id', $selectedModuleId)->orderBy('order', 'desc')->first();
            $nextOrder = $lastLesson ? $lastLesson->order + 1 : 1;
        }
        
        return view('admin.course-module-lessons.create', compact('modules', 'selectedModuleId', 'nextOrder'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'module_id' => 'required|exists:course_modules,id',
            'order' => 'required|integer|min:1',
            'duration_minutes' => 'nullable|integer|min:1',
            'video_url' => 'nullable|url',
            'type' => 'required|in:video,text,quiz,assignment',
            'is_active' => 'boolean',
            'is_free' => 'boolean',
        ]);

        // Check if order already exists for this module
        $existingLesson = CourseModuleLesson::where('module_id', $request->module_id)
            ->where('order', $request->order)
            ->first();
            
        if ($existingLesson) {
            return back()->withErrors(['order' => 'Urutan ini sudah digunakan untuk modul ini.'])
                ->withInput();
        }

        CourseModuleLesson::create([
            'title' => $request->title,
            'content' => $request->content,
            'module_id' => $request->module_id,
            'order' => $request->order,
            'duration_minutes' => $request->duration_minutes,
            'video_url' => $request->video_url,
            'type' => $request->type,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'is_free' => $request->has('is_free') ? 1 : 0,
        ]);

        return redirect()->route('admin.course-module-lessons.index', ['module' => $request->module_id])
            ->with('success', 'Pelajaran berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CourseModuleLesson $courseModuleLesson)
    {
        $courseModuleLesson->load('courseModule.course.courseCategory');
        
        return view('admin.course-module-lessons.show', compact('courseModuleLesson'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CourseModuleLesson $courseModuleLesson)
    {
        $modules = CourseModule::with('course.courseCategory')
            ->where('is_active', 1)
            ->orderBy('title')
            ->get();
        
        return view('admin.course-module-lessons.edit', compact('courseModuleLesson', 'modules'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CourseModuleLesson $courseModuleLesson)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'module_id' => 'required|exists:course_modules,id',
            'order' => 'required|integer|min:1',
            'duration_minutes' => 'nullable|integer|min:1',
            'video_url' => 'nullable|url',
            'type' => 'required|in:video,text,quiz,assignment',
            'is_active' => 'boolean',
            'is_free' => 'boolean',
        ]);

        // Check if order already exists for this module (excluding current lesson)
        $existingLesson = CourseModuleLesson::where('module_id', $request->module_id)
            ->where('order', $request->order)
            ->where('id', '!=', $courseModuleLesson->id)
            ->first();
            
        if ($existingLesson) {
            return back()->withErrors(['order' => 'Urutan ini sudah digunakan untuk modul ini.'])
                ->withInput();
        }

        $courseModuleLesson->update([
            'title' => $request->title,
            'content' => $request->content,
            'module_id' => $request->module_id,
            'order' => $request->order,
            'duration_minutes' => $request->duration_minutes,
            'video_url' => $request->video_url,
            'type' => $request->type,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'is_free' => $request->has('is_free') ? 1 : 0,
        ]);

        return redirect()->route('admin.course-module-lessons.index', ['module' => $courseModuleLesson->module_id])
            ->with('success', 'Pelajaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseModuleLesson $courseModuleLesson)
    {
        $moduleId = $courseModuleLesson->module_id;
        $courseModuleLesson->delete();

        return redirect()->route('admin.course-module-lessons.index', ['module' => $moduleId])
            ->with('success', 'Pelajaran berhasil dihapus.');
    }

    /**
     * Reorder lessons for a specific module
     */
    public function reorder(Request $request, CourseModule $courseModule)
    {
        $request->validate([
            'lessons' => 'required|array',
            'lessons.*.id' => 'required|exists:course_module_lessons,id',
            'lessons.*.order' => 'required|integer|min:1',
        ]);

        foreach ($request->lessons as $lessonData) {
            CourseModuleLesson::where('id', $lessonData['id'])
                ->where('module_id', $courseModule->id)
                ->update(['order' => $lessonData['order']]);
        }

        return response()->json(['success' => true, 'message' => 'Urutan pelajaran berhasil diperbarui.']);
    }
}
