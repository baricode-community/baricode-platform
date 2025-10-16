# Task Management System - Documentation

## Overview
Sistem Task Management yang telah dirombak memungkinkan admin untuk membuat task dan mendelegasikannya ke multiple users. **Setiap user dapat menerima MULTIPLE assignments untuk task yang sama**, dan setiap assignment dapat dikerjakan berkali-kali (sesuai limit). Setiap submission harus di-approve oleh admin.

### Key Features:
- ✅ Admin dapat assign task yang sama ke user yang sama berkali-kali
- ✅ Setiap assignment bisa punya title dan description sendiri
- ✅ User bisa pilih assignment mana yang mau dikerjakan
- ✅ Tracking submission per assignment
- ✅ Flexible workflow untuk berbagai skenario pengerjaan

---

## Database Structure

### 1. **tasks** table
Tabel utama untuk menyimpan informasi task.

**Kolom:**
- `id` - Primary key
- `title` - Judul task
- `description` - Deskripsi singkat task
- `content` - Detail konten task (rich text)
- `instructions` - Instruksi pengerjaan (rich text)
- `user_id` - Foreign key ke user (creator/admin)
- `is_active` - Boolean, apakah task aktif
- `max_submissions_per_user` - Maksimal berapa kali user bisa submit (default: 1)
- `attachments` - JSON array untuk file lampiran
- `created_at`, `updated_at`

### 2. **task_assignments** table
Tabel untuk mendelegasikan task ke users. **User yang sama bisa menerima multiple assignments untuk task yang sama.**

**Kolom:**
- `id` - Primary key
- `task_id` - Foreign key ke tasks
- `user_id` - Foreign key ke users (yang menerima assignment)
- `title` - Judul assignment (nullable) - untuk membedakan assignment satu dengan yang lain
- `description` - Deskripsi spesifik assignment (nullable) - instruksi khusus untuk assignment ini
- `assigned_by` - Foreign key ke users (admin yang assign)
- `assigned_at` - Timestamp kapan di-assign
- `due_date` - Deadline (nullable)
- `status` - Enum: pending, in_progress, completed, cancelled
- `notes` - Catatan dari admin untuk user
- `created_at`, `updated_at`

**Note:** Unique constraint (task_id, user_id) telah dihapus untuk memungkinkan multiple assignments.

### 3. **task_submissions** table
Tabel untuk menyimpan pengerjaan/submission dari users.

**Kolom:**
- `id` - Primary key
- `task_id` - Foreign key ke tasks
- `user_id` - Foreign key ke users (yang submit)
- `assignment_id` - Foreign key ke task_assignments
- `submission_content` - Isi pengerjaan (text)
- `files` - JSON array untuk file yang di-upload
- `submitted_at` - Timestamp submission
- `status` - Enum: pending, approved, rejected, revision_requested
- `reviewed_by` - Foreign key ke users (admin reviewer) - nullable
- `reviewed_at` - Timestamp review - nullable
- `review_notes` - Catatan/feedback dari reviewer
- `score` - Skor 0-100 (opsional)
- `created_at`, `updated_at`

---

## Models & Relationships

### Task Model
```php
// Relasi
- user() - belongsTo User (creator)
- assignments() - hasMany TaskAssignment
- assignedUsers() - belongsToMany User through task_assignments
- submissions() - hasMany TaskSubmission
- approvedSubmissions() - hasMany TaskSubmission where status='approved'
- pendingSubmissions() - hasMany TaskSubmission where status='pending'

// Methods
- isAssignedTo(User $user): bool
- userAssignments(User $user) - Get all assignments for a user on this task
- userSubmissions(User $user)
- userCanSubmit(User $user): bool - Check if user can submit (considers all assignments)
- assignmentCanSubmit(TaskAssignment $assignment): bool - Check if specific assignment can submit
```

### TaskAssignment Model
```php
// Relasi
- task() - belongsTo Task
- user() - belongsTo User (assignee)
- assignedBy() - belongsTo User (admin)
- submissions() - hasMany TaskSubmission
- latestSubmission() - hasOne TaskSubmission (latest)

// Methods
- isOverdue(): bool

// Scopes
- scopePending($query)
- scopeInProgress($query)
- scopeCompleted($query)
```

### TaskSubmission Model
```php
// Relasi
- task() - belongsTo Task
- user() - belongsTo User (submitter)
- assignment() - belongsTo TaskAssignment
- reviewer() - belongsTo User (reviewer)

// Methods
- isPending(): bool
- isApproved(): bool
- isRejected(): bool
- isRevisionRequested(): bool

// Accessors
- getStatusColorAttribute(): string
- getStatusLabelAttribute(): string

// Scopes
- scopePending($query)
- scopeApproved($query)
- scopeRejected($query)
- scopeRevisionRequested($query)
```

---

## Filament Admin Panel

### Task Resource
**Path:** `app/Filament/Resources/Tasks/TaskResource.php`

**Features:**
- Create dan edit tasks
- Form fields:
  - Title (required)
  - Description
  - Content (Rich Editor)
  - Instructions (Rich Editor)
  - Is Active (toggle)
  - Max Submissions per User (1-10)
  - Attachments (multiple files)

**Relation Manager - Assignments:**
- Assign task ke users (user yang sama bisa di-assign berkali-kali)
- Set assignment title (untuk membedakan assignment)
- Set assignment description (instruksi khusus)
- Set deadline
- Set status (pending/in_progress/completed/cancelled)
- Add notes untuk user
- View submissions count per assignment

### TaskSubmission Resource
**Path:** `app/Filament/Resources/TaskSubmissions/TaskSubmissionResource.php`

**Features:**
- View all submissions
- Filter by status, task, user
- Table columns:
  - Task title
  - User name
  - Status badge
  - Score
  - Submit & review timestamps
  - Reviewer name
- Quick actions:
  - Approve button (untuk pending submissions)
  - Reject button (untuk pending submissions)
- Edit untuk detailed review:
  - Change status
  - Add score
  - Add review notes

---

## User-Side Views & Routes

### Routes
```php
Route::prefix('tasks')->group(function () {
    Route::get('/', 'index')->name('tasks.index');
    Route::get('/submissions', 'submissions')->name('tasks.submissions');
    Route::get('/{id}/{assignmentId?}', 'show')->name('tasks.show');
    Route::post('/{id}/submit', 'submit')->name('tasks.submit');
    Route::get('/submission/{id}', 'viewSubmission')->name('tasks.submission.view');
});
```

**Note:** Route `tasks.show` sekarang menerima optional `assignmentId` parameter untuk memilih assignment spesifik.

### Views

#### 1. `resources/views/pages/tasks/index.blade.php`
Halaman daftar task yang di-assign ke user.

**Features:**
- Card-based layout
- Shows ALL assignments (termasuk multiple assignments untuk task yang sama)
- Each card shows:
  - Assignment title (jika ada)
  - Assignment description (jika ada)
  - Task title
  - Status badge (pending/in_progress/completed)
  - Submission count vs max limit per assignment
  - Due date display
  - Admin notes
- Link to submissions history
- Direct link ke specific assignment

#### 2. `resources/views/pages/tasks/show.blade.php`
Detail task dan form submit.

**Features:**
- **Assignment Selector** - Jika user punya multiple assignments untuk task yang sama:
  - Menampilkan semua assignments dalam list
  - Highlight assignment yang sedang aktif
  - Show submission count per assignment
  - Quick switch between assignments
- Task header dengan status
- Assignment info (title, description, assigned date, deadline, submissions count)
- Admin notes display
- Task content & instructions (rich text)
- Task attachments download
- Submit form:
  - Hidden field untuk assignment_id
  - Info assignment yang sedang di-submit
  - Textarea for content
  - Multiple file upload
  - Validation
- Previous submissions list untuk assignment yang aktif dengan status & feedback

#### 3. `resources/views/pages/tasks/submissions.blade.php`
History semua submissions user.

**Features:**
- Stats cards (total, pending, approved, rejected)
- Table view semua submissions
- Filter & search
- Status badges
- Score display
- Link to detail

#### 4. `resources/views/pages/tasks/submission-detail.blade.php`
Detail satu submission.

**Features:**
- Submission header dengan status
- Submit & review timestamps
- Reviewer name
- Score display (with color coding)
- Full submission content
- File attachments
- Reviewer feedback/notes
- Link back to task

---

## Workflow

### Admin Workflow:
1. **Create Task**
   - Login ke Filament admin panel
   - Navigate ke Tasks menu
   - Click Create
   - Fill form (title, description, content, instructions, attachments, settings)
   - Save

2. **Assign Task to Users**
   - Edit task yang sudah dibuat
   - Go to "Assignments" tab
   - Click "Create"
   
   **Field Users (Multiple Select):**
   - Pilih satu atau lebih users dari dropdown
   - Gunakan search untuk cari user dengan cepat
   - Bisa pilih 1 user, 5 users, 10 users, bahkan 50 users sekaligus
   
   **Other Fields:**
   - **Title** (opsional): Judul assignment untuk identifikasi (misal: "Draft Awal", "Beginner Level")
   - **Description** (opsional): Deskripsi khusus untuk assignment ini
   - **Deadline** (opsional): Kapan harus selesai
   - **Status**: Pending / In Progress / Completed / Cancelled
   - **Notes** (opsional): Catatan atau instruksi tambahan
   
   **Behavior:**
   - Jika pilih 1 user → 1 assignment dibuat
   - Jika pilih 10 users → 10 assignments dibuat (semua identik)
   - Jika ingin assign user yang sama berkali-kali → klik Create lagi, pilih user yang sama, ganti title/description
   
   **Use Cases:**
   
   a) **Tugas Kelas (Bulk Assignment)**
   - Create → Pilih 30 siswa sekaligus
   - Title: "Homework Chapter 5"
   - Deadline: 1 minggu
   - Save → Semua 30 siswa dapat assignment yang sama
   
   b) **Revisi Bertahap (Same User, Multiple Assignments)**
   - Create #1: User A, Title "Initial Draft", Deadline: Week 1
   - Create #2: User A, Title "Revision 1", Deadline: Week 2
   - Create #3: User A, Title "Final Version", Deadline: Week 3
   - User A sekarang punya 3 assignments untuk task yang sama
   
   c) **Level-based Tasks**
   - Create #1: User B, Title "Beginner Level", Notes "Basic exercises"
   - Create #2: User B, Title "Advanced Level", Notes "Complex problems"
   - User B bisa kerjakan level by level

3. **Review Submissions**
   - Navigate ke "Review Submissions" menu
   - Filter by status: Pending
   - Click submission untuk view detail atau Edit
   - Option 1: Quick approve/reject dari table
   - Option 2: Edit untuk detailed review
     - Change status (approved/rejected/revision_requested)
     - Add score (0-100)
     - Add review notes
     - Save

### User Workflow:
1. **View Assigned Tasks**
   - Login
   - Navigate ke /tasks
   - See all assignments (including multiple assignments for the same task)
   - Each assignment shown as separate card
   - Click "Lihat Detail" pada assignment yang ingin dikerjakan

2. **Submit Task**
   - Open task detail (akan otomatis ke assignment yang dipilih)
   - **Jika ada multiple assignments**: Gunakan assignment selector di atas untuk switch assignment
   - Read content, instructions, download attachments
   - Lihat assignment-specific info (title, description, notes)
   - Fill submission form:
     - System otomatis track assignment_id yang dipilih
     - Write your work in textarea
     - Upload files (optional)
   - Click "Submit Pengerjaan"
   - Redirected back dengan success message

3. **Check Submission Status**
   - Navigate ke "Riwayat Submission"
   - View all your submissions
   - Click "View Detail" untuk specific submission
   - See status, score, reviewer feedback

4. **Work on Different Assignments**
   - Jika punya multiple assignments untuk task yang sama
   - Gunakan assignment selector untuk switch
   - Each assignment dapat di-submit terpisah sesuai limit
   - Track progress per assignment

4. **Resubmit (if allowed)**
   - Check if you can still submit (based on max_submissions_per_user)
   - If yes, return to task detail
   - Submit again with improvements

---

## File Storage

### Task Attachments
- **Directory:** `storage/app/public/task-attachments/`
- **Format:** Any file type
- **Max size:** 10MB per file
- **Max files:** 5 files per task

### Submission Files
- **Directory:** `storage/app/public/task-submissions/`
- **Format:** .pdf, .doc, .docx, .jpg, .jpeg, .png, .zip
- **Max size:** 20MB per file
- **Max files:** 10 files per submission

**Note:** Pastikan symbolic link sudah dibuat:
```bash
php artisan storage:link
```

---

## Notifications (Future Enhancement)

Potential notifications to implement:
- [ ] User: When assigned to new task
- [ ] User: When submission is reviewed
- [ ] Admin: When new submission received
- [ ] User: When approaching deadline
- [ ] Admin: Daily digest of pending submissions

---

## Permissions & Security

- All routes under `/tasks` require authentication (`auth` middleware)
- Users can only view/submit tasks assigned to them
- Users can only view their own submissions
- Admin panel requires admin role/permission
- File uploads are validated for type and size
- CSRF protection on all POST requests

---

## Testing Checklist

### Admin Side:
- [ ] Create task
- [ ] Edit task
- [ ] Assign task to single user
- [ ] Assign task to multiple users
- [ ] **Assign same task to same user multiple times** (with different title/description)
- [ ] Set deadline for assignment
- [ ] Add notes for user
- [ ] Add title and description to assignment
- [ ] View all submissions
- [ ] Approve submission
- [ ] Reject submission
- [ ] Request revision
- [ ] Add score and feedback
- [ ] Filter submissions by status
- [ ] Track which assignment each submission belongs to

### User Side:
- [ ] View assigned tasks (including multiple assignments for same task)
- [ ] Open task detail
- [ ] **Switch between assignments** using assignment selector (when multiple exist)
- [ ] Read task content
- [ ] Download task attachments
- [ ] See assignment-specific info (title, description, notes)
- [ ] Submit task for specific assignment
- [ ] Upload files with submission
- [ ] View submission history (grouped by assignment)
- [ ] View submission detail
- [ ] See reviewer feedback
- [ ] Resubmit task (if allowed per assignment)
- [ ] Cannot submit if assignment limit reached
- [ ] Work on different assignments for same task independently

---

## Migration Commands

```bash
# Run migrations
php artisan migrate

# Rollback (if needed)
php artisan migrate:rollback --step=3

# Fresh migration (WARNING: will delete all data)
php artisan migrate:fresh
```

---

## Customization Tips

### Change max submissions limit:
Edit migration or update via Filament when creating/editing task.

### Add new submission status:
1. Update enum in migration
2. Update model accessor `getStatusLabelAttribute()`
3. Update Filament form options
4. Update blade views badge colors

### Customize notification:
Create notification class and dispatch on submission/review events.

### Add scoring rubric:
Add JSON field to tasks table for rubric criteria.

### Customize assignment workflow:
- **Make title/description required**: Edit AssignmentsRelationManager form, add `->required()` to fields
- **Add assignment priority**: Add priority column to task_assignments table, update forms and sorting
- **Auto-generate assignment titles**: Create naming convention in controller (e.g., "Revision 1", "Attempt 2")
- **Add assignment types**: Add enum field (original, revision, bonus, extra_credit) to differentiate purposes
- **Limit assignments per user**: Add validation in AssignmentsRelationManager to cap how many times user can be assigned

### UI Customization:
- **Assignment badges**: Edit index.blade.php to change colors/styles of title/description badges
- **Assignment selector**: Edit show.blade.php to customize selector appearance (currently uses blue highlight)
- **Hide assignment info**: Remove title/description display if you want assignments to be invisible to users
- **Group submissions by assignment**: Modify submissions.blade.php to group by assignment_id

---

## Support

For issues or questions, contact the development team or check the repository documentation.

**Last Updated:** October 16, 2025
