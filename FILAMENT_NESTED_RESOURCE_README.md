# Nested Resource Filament - Quick Start

## 📚 Struktur Hierarki

```
CourseCategory (Kategori Kursus)
    └── Course (Kursus)
        └── CourseModule (Modul)
            └── CourseModuleLesson (Pelajaran)
```

## 🚀 Resources yang Dibuat

### 1. **CourseCategoryResource** 
- **Path**: `/admin/course-categories`
- **Icon**: 🎓 Academic Cap
- **Features**: Kelola kategori kursus dengan level (pemula/menengah/lanjut)
- **Nested**: Tab "Kursus" untuk kelola courses

### 2. **CourseResource**
- **Path**: `/admin/courses`
- **Icon**: 📖 Book Open
- **Features**: Kelola kursus dengan thumbnail, slug, dan status published
- **Nested**: Tab "Course Modules" untuk kelola modules

### 3. **CourseModuleResource**
- **Path**: `/admin/course-modules`
- **Icon**: 📋 Queue List
- **Features**: Kelola modul dengan urutan dan deskripsi
- **Nested**: Tab "Course Module Lessons" untuk kelola lessons

### 4. **CourseModuleLessonResource**
- **Path**: `/admin/course-module-lessons`
- **Icon**: 📄 Document Text
- **Features**: Kelola pelajaran dengan Rich Text Editor

## 🎯 Cara Menggunakan

### Metode 1: Melalui Nested Tabs (Recommended)
1. Buka **Kategori Kursus** → Pilih kategori → Tab **"Kursus"**
2. Pilih kursus → Tab **"Course Modules"**
3. Pilih modul → Tab **"Course Module Lessons"**

### Metode 2: Direct Access
- Akses langsung resource via navigasi sidebar
- Gunakan filter untuk menyaring berdasarkan parent

## ✨ Fitur Utama

### 🔢 Auto Counting
- Setiap parent menampilkan jumlah child records
- Category → courses count
- Course → modules count
- Module → lessons count

### 🖼️ Image Management
- Upload thumbnail untuk courses
- Preview circular di table
- Auto-save ke `storage/app/public/course-thumbnails`

### 🎨 Badge & Icons
- Level kategori dengan badge berwarna
- Status published dengan icon check/x
- Color coding untuk visual feedback

### 📝 Rich Text
- Editor WYSIWYG untuk konten lesson
- Formatting penuh untuk materi pembelajaran

### 🔍 Search & Filter
- Search di semua kolom utama
- Filter berdasarkan parent relationship
- Sort by column dengan toggle visibility

## 📁 Struktur File

```
app/Filament/Resources/
├── CourseCategories/
│   ├── CourseCategoryResource.php
│   ├── RelationManagers/
│   │   └── CoursesRelationManager.php
│   ├── Schemas/CourseCategoryForm.php
│   └── Tables/CourseCategoriesTable.php
│
├── Courses/
│   ├── CourseResource.php
│   ├── RelationManagers/
│   │   └── CourseModulesRelationManager.php
│   ├── Schemas/CourseForm.php
│   └── Tables/CoursesTable.php
│
├── CourseModules/
│   ├── CourseModuleResource.php
│   ├── RelationManagers/
│   │   └── CourseModuleLessonsRelationManager.php
│   ├── Schemas/CourseModuleForm.php
│   └── Tables/CourseModulesTable.php
│
└── CourseModuleLessons/
    ├── CourseModuleLessonResource.php
    ├── Schemas/CourseModuleLessonForm.php
    └── Tables/CourseModuleLessonsTable.php
```

## 🔗 Model Relationships

```php
CourseCategory → hasMany → Course
Course → belongsTo → CourseCategory
Course → hasMany → CourseModule
CourseModule → belongsTo → Course
CourseModule → hasMany → CourseModuleLesson
CourseModuleLesson → belongsTo → CourseModule
```

## 📖 Dokumentasi Lengkap

Lihat file `FILAMENT_NESTED_RESOURCE_DOCUMENTATION.md` untuk:
- Penjelasan detail setiap resource
- Struktur lengkap folder dan file
- Tips development
- Troubleshooting

## 🛠️ Commands

```bash
# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Generate resources (jika perlu tambahan)
php artisan make:filament-resource ResourceName --generate

# Generate relation manager
php artisan make:filament-relation-manager ParentResource relationship titleAttribute
```

## 🎉 Selesai!

Nested resource Filament sudah siap digunakan. Akses Filament admin panel di `/admin` dan mulai kelola konten kursus Anda dengan mudah!

---

**Dibuat**: 23 Oktober 2025  
**Filament Version**: v4.0  
**Laravel Version**: 11.x
