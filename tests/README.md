# Database Relationship Tests

Comprehensive test suite untuk menguji semua relasi database di aplikasi Baricode Community Platform.

## Struktur Test

### Unit Tests (`tests/Unit/Models/`)
- `UserTest.php` - Test relasi User dengan enrollments, notes, dan permissions
- `CourseTest.php` - Test relasi Course dengan categories, modules, dan enrollments
- `CourseCategoryTest.php` - Test relasi CourseCategory dengan courses
- `CourseModuleTest.php` - Test relasi CourseModule dengan courses dan lessons
- `CourseModuleLessonTest.php` - Test relasi CourseModuleLesson dengan modules dan notes
- `EnrollmentTest.php` - Test relasi Enrollment dengan users, courses, dan modules
- `EnrollmentModuleTest.php` - Test relasi EnrollmentModule dengan enrollments dan lessons
- `EnrollmentLessonTest.php` - Test relasi EnrollmentLesson dengan modules dan lessons
- `UserNoteTest.php` - Test relasi UserNote dengan users dan lessons

### Feature Tests (`tests/Feature/`)
- `DatabaseRelationshipIntegrationTest.php` - Test integrasi kompleks antar model
- `DatabaseTestRunner.php` - Helper untuk menjalankan semua test

## Relasi yang Ditest

### 1. User Relationships
- `hasMany` Enrollment (courseEnrollments)
- `hasMany` UserNote (userNotes)
- `belongsToMany` Role (via Spatie Permission)
- `belongsToMany` Permission (via Spatie Permission)

### 2. Course Relationships
- `belongsTo` CourseCategory (courseCategory)
- `hasMany` CourseModule (courseModules)
- `hasMany` Enrollment (enrollments)

### 3. CourseCategory Relationships
- `hasMany` Course (courses)

### 4. CourseModule Relationships
- `belongsTo` Course (course)
- `hasMany` CourseModuleLesson (courseModuleLessons)
- `hasMany` ModuleProgress (moduleProgresses)

### 5. CourseModuleLesson Relationships
- `belongsTo` CourseModule (courseModule)
- `hasMany` UserNote (userNotes)

### 6. Enrollment Relationships
- `belongsTo` User (user)
- `belongsTo` Course (course)
- `hasMany` EnrollmentModule (enrollmentModules)

### 7. EnrollmentModule Relationships
- `belongsTo` Enrollment (enrollment)
- `belongsTo` CourseModule (via module_id)
- `hasMany` EnrollmentLesson (enrollmentLessons)

### 8. EnrollmentLesson Relationships
- `belongsTo` EnrollmentModule (enrollmentModule)
- `belongsTo` CourseModuleLesson (via lesson_id)

### 9. UserNote Relationships
- `belongsTo` User (user)
- `belongsTo` CourseModuleLesson (via lesson_id)

## Fitur yang Ditest

### Model Events
- Auto-creation of EnrollmentModules saat Enrollment dibuat
- Cascading deletes untuk menjaga data integrity

### Data Validation
- Foreign key constraints
- Required fields
- Enum validations (level fields)
- Unique constraints (course slugs, etc.)

### Business Logic
- Enrollment approval workflow
- Course completion tracking
- Permission-based access control
- Ordering dalam modules dan lessons

### Integration Scenarios
- Complete course creation workflow
- Student enrollment dan progress tracking
- Multi-user note management
- Role-based permission system
- Cascading delete scenarios

## Menjalankan Tests

### Semua Tests
```bash
php artisan test
```

### Unit Tests Saja
```bash
php artisan test tests/Unit/
```

### Feature Tests Saja
```bash
php artisan test tests/Feature/
```

### Test Spesifik Model
```bash
php artisan test tests/Unit/Models/UserTest.php
php artisan test tests/Unit/Models/CourseTest.php
```

### Test dengan Coverage
```bash
php artisan test --coverage
```

## Prerequisites

Test ini memerlukan:
- Laravel 11.x
- SQLite (untuk testing)
- Spatie Permission package
- PHPUnit/Pest
- Factory classes untuk semua models

## Database Test Configuration

Tests menggunakan konfigurasi database terpisah dengan SQLite in-memory untuk performa optimal. Semua migrations dijalankan ulang untuk setiap test untuk memastikan clean state.

## Notes

- Tests menggunakan `RefreshDatabase` trait untuk memastikan setiap test berjalan dengan database yang bersih
- Foreign key constraints di-disable sementara untuk SQLite selama testing setup
- Semua relationships ditest baik dari segi structure maupun data integrity
- Integration tests mensimulasikan real-world scenarios

## Troubleshooting

### Jika ada error terkait factory:
Pastikan semua factory classes sudah dibuat dan sesuai dengan namespace model.

### Jika ada error foreign key:
Cek migration files untuk memastikan foreign key constraints sudah benar.

### Jika test hang atau lambat:
Gunakan `--parallel` flag atau periksa N+1 query issues dalam relationships.
