# Fix: Null Safety untuk CourseCategory Relationships

## 🐛 Problem
Error terjadi pada nested resource Course Management dengan pesan:
```
ErrorException - Internal Server Error
Attempt to read property "name" on null
```

## 🔍 Root Cause
Error terjadi karena ada `Course` records di database yang tidak memiliki `category_id` (null), sehingga ketika mencoba mengakses `$course->courseCategory->name` akan menghasilkan null pointer error.

## ✅ Solution Applied

### Files Fixed:

1. **CourseModulesTable.php**
   - ✅ Added null safety check pada filter options
   - ✅ Added placeholder untuk kolom kategori

2. **CourseModuleLessonsTable.php**
   - ✅ Added null safety check pada filter options
   - ✅ Added placeholder untuk kolom kategori

3. **CourseModuleForm.php**
   - ✅ Added null safety check pada select options

4. **CourseModuleLessonForm.php**
   - ✅ Added null safety check pada select options

### Changes Made:

#### Before:
```php
->options(Course::with('courseCategory')->get()->mapWithKeys(function ($course) {
    return [$course->id => $course->courseCategory->name . ' - ' . $course->title];
}))
```

#### After:
```php
->options(Course::with('courseCategory')->get()->mapWithKeys(function ($course) {
    $categoryName = $course->courseCategory ? $course->courseCategory->name : 'Tanpa Kategori';
    return [$course->id => $categoryName . ' - ' . $course->title];
}))
```

#### Column Placeholder:
```php
TextColumn::make('course.courseCategory.name')
    ->label('Kategori')
    ->placeholder('Tanpa Kategori')  // ← Added this
    ->searchable()
    ->sortable(),
```

## 🎯 Result
- ✅ Error "Attempt to read property 'name' on null" fixed
- ✅ Courses without category now display as "Tanpa Kategori"
- ✅ All dropdowns and tables handle null categories gracefully
- ✅ No breaking changes to existing functionality

## 📝 Notes
- Courses dengan `category_id = null` sekarang ditampilkan dengan label "Tanpa Kategori"
- Semua relationship access sekarang null-safe
- Performance tidak terpengaruh karena eager loading tetap digunakan

## 🧪 Testing
Setelah fix ini, endpoint `/admin/course/course-modules` dan semua related resources dapat diakses tanpa error.

---
**Date**: November 3, 2025  
**Status**: ✅ **RESOLVED**