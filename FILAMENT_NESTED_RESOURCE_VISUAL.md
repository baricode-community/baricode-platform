# Nested Resource Filament - Visual Structure

## 📊 Hierarki Resource

```
┌─────────────────────────────────────────────────────────────────┐
│                     COURSE CATEGORY                             │
│  📁 app/Filament/Resources/CourseCategories/                    │
│                                                                  │
│  📝 Form Fields:                                                │
│    • Nama Kategori (text)                                       │
│    • Level (select: pemula/menengah/lanjut)                     │
│    • Deskripsi (textarea)                                       │
│                                                                  │
│  📊 Table Columns:                                              │
│    • Nama | Level (badge) | Jumlah Kursus | Timestamps         │
│                                                                  │
│  🔗 Relations:                                                  │
│    └─► CoursesRelationManager                                  │
└──────────────────────┬──────────────────────────────────────────┘
                       │
                       │ hasMany (courses)
                       ▼
┌─────────────────────────────────────────────────────────────────┐
│                         COURSE                                   │
│  📁 app/Filament/Resources/Courses/                             │
│                                                                  │
│  📝 Form Fields:                                                │
│    • Kategori (select relationship)                             │
│    • Judul (text)                                               │
│    • Slug (text, unique)                                        │
│    • Deskripsi (textarea)                                       │
│    • Thumbnail (file upload)                                    │
│    • Dipublikasikan (toggle)                                    │
│                                                                  │
│  📊 Table Columns:                                              │
│    • Thumbnail | Judul | Status | Jumlah Modul | Timestamps    │
│                                                                  │
│  🔗 Relations:                                                  │
│    └─► CourseModulesRelationManager                            │
└──────────────────────┬──────────────────────────────────────────┘
                       │
                       │ hasMany (courseModules)
                       ▼
┌─────────────────────────────────────────────────────────────────┐
│                      COURSE MODULE                               │
│  📁 app/Filament/Resources/CourseModules/                       │
│                                                                  │
│  📝 Form Fields:                                                │
│    • Kursus (select relationship)                               │
│    • Nama Modul (text)                                          │
│    • Deskripsi (textarea)                                       │
│    • Urutan (number)                                            │
│                                                                  │
│  📊 Table Columns:                                              │
│    • Kursus | Nama Modul | Urutan | Jumlah Pelajaran          │
│                                                                  │
│  🔗 Relations:                                                  │
│    └─► CourseModuleLessonsRelationManager                      │
└──────────────────────┬──────────────────────────────────────────┘
                       │
                       │ hasMany (courseModuleLessons)
                       ▼
┌─────────────────────────────────────────────────────────────────┐
│                  COURSE MODULE LESSON                            │
│  📁 app/Filament/Resources/CourseModuleLessons/                 │
│                                                                  │
│  📝 Form Fields:                                                │
│    • Modul (select relationship)                                │
│    • Judul Pelajaran (text)                                     │
│    • Konten (rich editor)                                       │
│    • Urutan (number)                                            │
│                                                                  │
│  📊 Table Columns:                                              │
│    • Modul | Kursus | Judul | Urutan | Timestamps              │
│                                                                  │
│  🔗 Relations: (none - leaf node)                              │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🗂️ File Structure

```
app/Filament/Resources/
│
├── 📁 CourseCategories/
│   ├── 📄 CourseCategoryResource.php         ← Main resource
│   ├── 📁 Pages/
│   │   ├── CreateCourseCategory.php
│   │   ├── EditCourseCategory.php
│   │   └── ListCourseCategories.php
│   ├── 📁 RelationManagers/
│   │   └── CoursesRelationManager.php        ← Nested: courses
│   ├── 📁 Schemas/
│   │   └── CourseCategoryForm.php
│   └── 📁 Tables/
│       └── CourseCategoriesTable.php
│
├── 📁 Courses/
│   ├── 📄 CourseResource.php                 ← Main resource
│   ├── 📁 Pages/
│   │   ├── CreateCourse.php
│   │   ├── EditCourse.php
│   │   └── ListCourses.php
│   ├── 📁 RelationManagers/
│   │   └── CourseModulesRelationManager.php  ← Nested: modules
│   ├── 📁 Schemas/
│   │   └── CourseForm.php
│   └── 📁 Tables/
│       └── CoursesTable.php
│
├── 📁 CourseModules/
│   ├── 📄 CourseModuleResource.php           ← Main resource
│   ├── 📁 Pages/
│   │   ├── CreateCourseModule.php
│   │   ├── EditCourseModule.php
│   │   └── ListCourseModules.php
│   ├── 📁 RelationManagers/
│   │   └── CourseModuleLessonsRelationManager.php ← Nested: lessons
│   ├── 📁 Schemas/
│   │   └── CourseModuleForm.php
│   └── 📁 Tables/
│       └── CourseModulesTable.php
│
└── 📁 CourseModuleLessons/
    ├── 📄 CourseModuleLessonResource.php     ← Main resource
    ├── 📁 Pages/
    │   ├── CreateCourseModuleLesson.php
    │   ├── EditCourseModuleLesson.php
    │   └── ListCourseModuleLessons.php
    ├── 📁 Schemas/
    │   └── CourseModuleLessonForm.php
    └── 📁 Tables/
        └── CourseModuleLessonsTable.php
```

---

## 🎭 Navigation Icons

```
Sidebar Menu:
├── 🎓 Kategori Kursus    (heroicon-o-academic-cap)
├── 📖 Kursus             (heroicon-o-book-open)
├── 📋 Modul              (heroicon-o-queue-list)
└── 📄 Pelajaran          (heroicon-o-document-text)
```

---

## 🔄 Data Flow

### Create Flow (Top-Down)
```
1. Create Category
   ↓
2. Add Courses (via relation manager tab)
   ↓
3. Add Modules (via relation manager tab)
   ↓
4. Add Lessons (via relation manager tab)
```

### Access Flow (Bottom-Up)
```
Lesson ────→ Modul ────→ Kursus ────→ Kategori
  (relation)  (relation)   (relation)
```

---

## 🎨 UI Components Used

### Form Components
```php
TextInput       // Text fields
Textarea        // Long text
Select          // Dropdowns with relationships
FileUpload      // Image upload (thumbnail)
Toggle          // Boolean (is_published)
RichEditor      // WYSIWYG editor (lesson content)
```

### Table Components
```php
TextColumn      // Regular text display
ImageColumn     // Image preview (circular)
IconColumn      // Boolean icons
Badge           // Colored labels (level)
```

### Actions
```php
CreateAction    // Create new record
EditAction      // Edit existing record
DeleteAction    // Delete record
BulkActions     // Multiple selection actions
```

---

## 📈 Relationship Diagram

```
┌──────────────────┐
│  CourseCategory  │
│  (id, name,      │
│   level, desc)   │
└────────┬─────────┘
         │
         │ 1:N
         │
┌────────▼─────────┐
│      Course      │
│  (id, category_  │
│   id, title,     │
│   thumbnail)     │
└────────┬─────────┘
         │
         │ 1:N
         │
┌────────▼─────────┐
│   CourseModule   │
│  (id, course_id, │
│   name, order)   │
└────────┬─────────┘
         │
         │ 1:N
         │
┌────────▼─────────┐
│CourseModuleLesson│
│  (id, module_id, │
│   title, content)│
└──────────────────┘
```

---

## 🚦 Status & Badges

### Course Category Level
- 🟢 **Pemula** (success - green)
- 🟡 **Menengah** (warning - yellow)
- 🔴 **Lanjut** (danger - red)

### Course Published Status
- ✅ **Published** (check-circle - green)
- ❌ **Draft** (x-circle - red)

---

## 🎯 Feature Matrix

| Feature                  | Category | Course | Module | Lesson |
|--------------------------|----------|--------|--------|--------|
| Create                   | ✅       | ✅     | ✅     | ✅     |
| Read/List                | ✅       | ✅     | ✅     | ✅     |
| Update                   | ✅       | ✅     | ✅     | ✅     |
| Delete                   | ✅       | ✅     | ✅     | ✅     |
| Search                   | ✅       | ✅     | ✅     | ✅     |
| Filter                   | ✅       | ✅     | ✅     | ✅     |
| Bulk Actions             | ✅       | ✅     | ✅     | ✅     |
| Relation Manager         | ✅       | ✅     | ✅     | ❌     |
| Image Upload             | ❌       | ✅     | ❌     | ❌     |
| Rich Text Editor         | ❌       | ❌     | ❌     | ✅     |
| Counter (child records)  | ✅       | ✅     | ✅     | ❌     |
| Badge Display            | ✅       | ❌     | ❌     | ❌     |
| Status Toggle            | ❌       | ✅     | ❌     | ❌     |

---

## 💡 Quick Tips

### Navigasi Cepat
1. Gunakan **Sidebar** untuk akses langsung ke resource
2. Gunakan **Relation Tabs** untuk navigasi nested
3. Gunakan **Breadcrumbs** untuk tracking posisi

### Keyboard Shortcuts (Filament default)
- `Cmd/Ctrl + S` - Save form
- `Cmd/Ctrl + K` - Global search
- `Esc` - Close modal

### Best Practices
- Buat **Category** dulu sebelum **Course**
- Atur **Order/Urutan** untuk Module dan Lesson
- Upload **Thumbnail** untuk visual appeal
- Gunakan **Rich Editor** untuk format konten yang baik

---

**Last Updated**: 23 Oktober 2025
**Version**: 1.0
**Filament**: v4.0
