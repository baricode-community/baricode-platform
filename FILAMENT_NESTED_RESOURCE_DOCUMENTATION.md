# Nested Resource Filament - Dokumentasi

## Overview
Dokumentasi ini menjelaskan struktur nested resource Filament yang telah dibuat untuk sistem manajemen kursus dengan hierarki: **CourseCategory → Course → CourseModule → CourseModuleLesson**

## Struktur Resource

### 1. Course Category Resource
**Path**: `app/Filament/Resources/CourseCategories/`

**Model**: `App\Models\Course\CourseCategory`

**Struktur Folder**:
```
CourseCategories/
├── CourseCategoryResource.php
├── Pages/
│   ├── CreateCourseCategory.php
│   ├── EditCourseCategory.php
│   └── ListCourseCategories.php
├── RelationManagers/
│   └── CoursesRelationManager.php
├── Schemas/
│   └── CourseCategoryForm.php
└── Tables/
    └── CourseCategoriesTable.php
```

**Form Fields**:
- Nama Kategori (text, required)
- Level (select: pemula/menengah/lanjut)
- Deskripsi (textarea)

**Table Columns**:
- Nama Kategori
- Level (dengan badge berwarna)
- Jumlah Kursus (counter)
- Created/Updated timestamps

**Navigation**:
- Label: "Kategori Kursus"
- Icon: `heroicon-o-academic-cap`
- Sort: 1

---

### 2. Course Resource
**Path**: `app/Filament/Resources/Courses/`

**Model**: `App\Models\Course\Course`

**Struktur Folder**:
```
Courses/
├── CourseResource.php
├── Pages/
│   ├── CreateCourse.php
│   ├── EditCourse.php
│   └── ListCourses.php
├── RelationManagers/
│   └── CourseModulesRelationManager.php
├── Schemas/
│   └── CourseForm.php
└── Tables/
    └── CoursesTable.php
```

**Form Fields**:
- Kategori (select relationship ke CourseCategory)
- Judul (text, required)
- Slug (text, required, unique)
- Deskripsi (textarea)
- Thumbnail (file upload, image)
- Dipublikasikan (toggle)

**Table Columns**:
- Kategori (relation)
- Thumbnail (image, circular)
- Judul
- Slug (hidden by default)
- Status (icon: published/draft)
- Jumlah Modul (counter)
- Created/Updated timestamps

**Navigation**:
- Label: "Kursus"
- Icon: `heroicon-o-book-open`
- Sort: 2

---

### 3. Course Module Resource
**Path**: `app/Filament/Resources/CourseModules/`

**Model**: `App\Models\Course\CourseModule`

**Struktur Folder**:
```
CourseModules/
├── CourseModuleResource.php
├── Pages/
│   ├── CreateCourseModule.php
│   ├── EditCourseModule.php
│   └── ListCourseModules.php
├── RelationManagers/
│   └── CourseModuleLessonsRelationManager.php
├── Schemas/
│   └── CourseModuleForm.php
└── Tables/
    └── CourseModulesTable.php
```

**Form Fields**:
- Kursus (select relationship ke Course)
- Nama Modul (text, required)
- Deskripsi (textarea)
- Urutan (number, required, min: 1)

**Table Columns**:
- Kursus (relation)
- Nama Modul
- Urutan
- Jumlah Pelajaran (counter)
- Created/Updated timestamps

**Navigation**:
- Label: "Modul"
- Icon: `heroicon-o-queue-list`
- Sort: 3

---

### 4. Course Module Lesson Resource
**Path**: `app/Filament/Resources/CourseModuleLessons/`

**Model**: `App\Models\Course\CourseModuleLesson`

**Struktur Folder**:
```
CourseModuleLessons/
├── CourseModuleLessonResource.php
├── Pages/
│   ├── CreateCourseModuleLesson.php
│   ├── EditCourseModuleLesson.php
│   └── ListCourseModuleLessons.php
├── Schemas/
│   └── CourseModuleLessonForm.php
└── Tables/
    └── CourseModuleLessonsTable.php
```

**Form Fields**:
- Modul (select relationship ke CourseModule)
- Judul Pelajaran (text, required)
- Konten (rich editor)
- Urutan (number, required, min: 1)

**Table Columns**:
- Modul (relation)
- Kursus (nested relation: module.course)
- Judul Pelajaran
- Urutan
- Created/Updated timestamps

**Navigation**:
- Label: "Pelajaran"
- Icon: `heroicon-o-document-text`
- Sort: 4

---

## Relation Managers

### 1. CoursesRelationManager
**Parent**: CourseCategory  
**Relationship**: `courses` (hasMany)  
**Location**: `app/Filament/Resources/CourseCategories/RelationManagers/`

**Features**:
- Menampilkan semua kursus dalam kategori
- Membuat kursus baru langsung dari kategori
- Edit dan hapus kursus
- Menampilkan jumlah modul per kursus

**Table Columns**:
- Thumbnail
- Judul
- Slug
- Status (published/draft)
- Jumlah Modul

---

### 2. CourseModulesRelationManager
**Parent**: Course  
**Relationship**: `courseModules` (hasMany)  
**Location**: `app/Filament/Resources/Courses/RelationManagers/`

**Features**:
- Menampilkan semua modul dalam kursus
- Membuat modul baru langsung dari kursus
- Edit dan hapus modul
- Menampilkan jumlah pelajaran per modul
- Default sort by order (urutan)

**Table Columns**:
- Nama Modul
- Urutan
- Jumlah Pelajaran
- Dibuat

---

### 3. CourseModuleLessonsRelationManager
**Parent**: CourseModule  
**Relationship**: `courseModuleLessons` (hasMany)  
**Location**: `app/Filament/Resources/CourseModules/RelationManagers/`

**Features**:
- Menampilkan semua pelajaran dalam modul
- Membuat pelajaran baru langsung dari modul
- Edit dan hapus pelajaran
- Default sort by order (urutan)

**Table Columns**:
- Judul Pelajaran
- Urutan
- Dibuat

---

## Cara Penggunaan

### Navigasi Hierarkis

1. **Dari Course Category**:
   - Buka halaman "Kategori Kursus"
   - Pilih kategori yang ingin dikelola
   - Tab "Kursus" akan muncul untuk mengelola kursus dalam kategori tersebut

2. **Dari Course**:
   - Buka halaman "Kursus"
   - Pilih kursus yang ingin dikelola
   - Tab "Course Modules" akan muncul untuk mengelola modul dalam kursus tersebut

3. **Dari Course Module**:
   - Buka halaman "Modul"
   - Pilih modul yang ingin dikelola
   - Tab "Course Module Lessons" akan muncul untuk mengelola pelajaran dalam modul tersebut

### Filter dan Search

Setiap resource dilengkapi dengan:
- **Search**: Pencarian berdasarkan nama/judul
- **Filter**: Filter berdasarkan parent relationship (kategori, kursus, modul)
- **Sort**: Sorting berdasarkan berbagai kolom
- **Toggle Columns**: Sembunyikan/tampilkan kolom tertentu

---

## Model Relationships

```php
// CourseCategory Model
public function courses()
{
    return $this->hasMany(Course::class, 'category_id', 'id');
}

// Course Model
public function courseCategory()
{
    return $this->belongsTo(CourseCategory::class, 'category_id');
}

public function courseModules()
{
    return $this->hasMany(CourseModule::class, 'course_id', 'id')->orderBy('order');
}

// CourseModule Model
public function course()
{
    return $this->belongsTo(Course::class, 'course_id', 'id');
}

public function courseModuleLessons()
{
    return $this->hasMany(CourseModuleLesson::class, 'module_id', 'id')->orderBy('order');
}

// CourseModuleLesson Model
public function courseModule()
{
    return $this->belongsTo(CourseModule::class, 'module_id', 'id');
}
```

---

## Fitur Tambahan

### 1. Counters
Setiap resource menampilkan jumlah child records:
- Category → jumlah courses
- Course → jumlah modules
- Module → jumlah lessons

### 2. Image Upload
Course resource memiliki fitur upload thumbnail dengan:
- Validasi image only
- Auto-save ke directory `course-thumbnails`
- Preview circular di table

### 3. Rich Text Editor
Course Module Lesson menggunakan RichEditor untuk konten yang lebih kaya.

### 4. Status Management
Course memiliki toggle published/unpublished dengan:
- Icon indicator di table
- Warna: hijau (published), merah (draft)

### 5. Badge untuk Level
Course Category menampilkan level dengan badge berwarna:
- Pemula: Success (hijau)
- Menengah: Warning (kuning)
- Lanjut: Danger (merah)

---

## File-File Penting

### Resource Files
```
app/Filament/Resources/
├── CourseCategories/
│   └── CourseCategoryResource.php
├── Courses/
│   └── CourseResource.php
├── CourseModules/
│   └── CourseModuleResource.php
└── CourseModuleLessons/
    └── CourseModuleLessonResource.php
```

### Relation Manager Files
```
app/Filament/Resources/
├── CourseCategories/RelationManagers/
│   └── CoursesRelationManager.php
├── Courses/RelationManagers/
│   └── CourseModulesRelationManager.php
└── CourseModules/RelationManagers/
    └── CourseModuleLessonsRelationManager.php
```

---

## Tips Pengembangan

1. **Menambah Field Baru**: Edit file di folder `Schemas/` untuk form dan `Tables/` untuk table columns
2. **Custom Actions**: Tambahkan actions di relation manager untuk operasi khusus
3. **Validation**: Tambahkan validation rules di form components
4. **Filters**: Tambahkan filter tambahan di method `filters()` pada table
5. **Bulk Actions**: Tambahkan bulk actions untuk operasi massal

---

## Troubleshooting

### Issue: Relation tidak muncul
**Solusi**: Pastikan method `getRelations()` di resource mengembalikan array relation manager class.

### Issue: Form tidak menyimpan
**Solusi**: Periksa fillable/guarded di model dan validation rules di form.

### Issue: Counter tidak akurat
**Solusi**: Pastikan relationship name di `counts()` sesuai dengan method relationship di model.

---

Dibuat pada: 23 Oktober 2025
Versi Filament: v4.0
Framework: Laravel 11.x
