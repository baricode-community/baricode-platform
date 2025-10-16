# Task Management System - Quick Start Guide

## Sistem Baru (Updated: Oct 17, 2025)

Sistem task telah dirombak dengan workflow baru:
- ✅ Admin membuat task via Filament
- ✅ Admin assign task ke multiple users
- ✅ **User dapat menerima MULTIPLE assignments untuk task yang sama**
- ✅ User dapat submit task berkali-kali (sesuai limit per assignment)
- ✅ Setiap submission harus di-approve oleh admin

---

## Setup

### 1. Jalankan Migrasi
```bash
php artisan migrate
```

### 2. Link Storage (jika belum)
```bash
php artisan storage:link
```

---

## Admin: Cara Membuat & Assign Task

1. **Login ke Admin Panel** (`/admin`)

2. **Buat Task Baru:**
   - Klik menu "Tasks"
   - Click "Create"
   - Isi form:
     - Title (wajib)
     - Description
     - Content (detail tugas)
     - Instructions (cara pengerjaan)
     - Upload attachments (opsional)
     - Set "Is Active" = Yes
     - Set "Max Submissions per User" (berapa kali user bisa submit)
   - Save

3. **Assign ke Users:**
   - Edit task yang baru dibuat
   - Klik tab "Assignments"
   - Click "Create"
   - **Pilih Users** (bisa pilih 1 atau lebih users sekaligus)
   - Isi Title, Description, Deadline, Notes (akan sama untuk semua users yang dipilih)
   - Save
   
   **💡 Cara Kerja:**
   - **Pilih 1 user**: Assignment akan dibuat untuk user tersebut
   - **Pilih multiple users**: Assignment yang sama akan dibuat untuk semua users yang dipilih
   - **User yang sama bisa dipilih berkali-kali**: Buat assignment lagi dengan klik Create, pilih user yang sama, beri title/description berbeda
   
   **💡 Use Cases:**
   - **Tugas Kelas**: Pilih semua siswa (10-30 users) → semua dapat assignment yang sama
   - **Revisi Bertahap**: 
     - Create #1: Pilih user A, Title "Draft Awal"
     - Create #2: Pilih user A lagi, Title "Revision 1"
     - Create #3: Pilih user A lagi, Title "Final Version"
   - **Level Tasks**:
     - Create #1: Pilih user B, Title "Beginner Level"
     - Create #2: Pilih user B lagi, Title "Advanced Level"

4. **Review Submissions:**
   - Klik menu "Review Submissions"
   - Filter: Status = "Pending"
   - Pilih submission
   - Option 1: Click tombol "Setujui" atau "Tolak" langsung dari table
   - Option 2: Click "Edit" untuk detailed review:
     - Ubah status
     - Beri skor (0-100)
     - Tambah review notes
     - Save

---

## User: Cara Mengerjakan Task

1. **Login** ke aplikasi

2. **Lihat Task yang Di-assign:**
   - Navigate ke `/tasks`
   - Lihat daftar **assignments** yang didelegasikan ke Anda
   - Jika ada multiple assignments untuk task yang sama, akan tampil sebagai card terpisah
   - Setiap assignment menunjukkan title (badge ungu) dan description (teks biru italic)
   - Klik "Lihat Detail" pada assignment yang ingin dikerjakan

3. **Pilih Assignment (jika ada multiple):**
   - Di halaman task detail, jika Anda punya multiple assignments untuk task yang sama
   - Gunakan **Assignment Selector** di atas form
   - Klik assignment yang ingin dikerjakan (akan ter-highlight biru)
   - Info assignment (title, description, notes, deadline) akan update otomatis
   - Submission count akan menunjukkan berapa kali sudah submit untuk assignment tersebut

4. **Submit Pengerjaan:**
   - Baca task detail, instruksi, download lampiran jika ada
   - Scroll ke bagian "Submit Pengerjaan"
   - Tulis hasil pengerjaan di textarea
   - Upload file jika diperlukan (max 10 files, 20MB each)
   - Klik "Submit Pengerjaan"
   - ⚠️ **Penting:** Submission akan tercatat untuk assignment yang sedang aktif/dipilih

5. **Cek Status Submission:**
   - Klik "Riwayat Submission" di header
   - Lihat semua submission Anda (untuk semua assignments)
   - Cek status: Pending/Approved/Rejected/Revision Requested
   - Klik "View Detail" untuk lihat feedback dari reviewer

6. **Submit Ulang (jika masih bisa):**
   - Jika belum mencapai batas max submission **per assignment**
   - Kembali ke task detail
   - Pilih assignment yang ingin di-submit ulang (jika multiple)
   - Submit lagi dengan perbaikan

7. **Kerjakan Assignment Lain:**
   - Jika punya assignment lain untuk task yang sama
   - Gunakan assignment selector untuk switch
   - Setiap assignment punya submission limit terpisah
   - Bisa kerjakan secara paralel atau bertahap

---

## Database Tables

### tasks
- Menyimpan informasi task
- Key fields: title, description, content, instructions, is_active, max_submissions_per_user

### task_assignments (UPDATED: Oct 17, 2025)
- Mendelegasikan task ke users
- **NO unique constraint** - same user dapat di-assign berkali-kali ke task yang sama
- New fields: **title**, **description** (untuk membedakan assignments)
- Key fields: task_id, user_id, assigned_by, due_date, status, notes, title, description

### task_submissions
- Menyimpan pengerjaan dari users
- Setiap submission terikat ke specific **assignment_id**
- Key fields: task_id, user_id, **assignment_id**, submission_content, files, status, reviewed_by, review_notes, score

---

## Routes

```
GET  /tasks                           - Daftar assignments assigned ke user
GET  /tasks/{id}/{assignmentId?}      - Detail task & form submit (optional assignmentId)
POST /tasks/{id}/submit               - Submit pengerjaan (requires assignment_id in request)
GET  /tasks/submissions               - History semua submissions
GET  /tasks/submission/{id}           - Detail satu submission
```

---

## Status Options

### Assignment Status:
- `pending` - Belum dikerjakan
- `in_progress` - Sedang dikerjakan
- `completed` - Selesai
- `cancelled` - Dibatalkan

### Submission Status:
- `pending` - Menunggu review
- `approved` - Disetujui
- `rejected` - Ditolak
- `revision_requested` - Perlu revisi

---

## File Uploads

### Task Attachments (by Admin):
- Location: `storage/app/public/task-attachments/`
- Max: 5 files @ 10MB each

### Submission Files (by User):
- Location: `storage/app/public/task-submissions/`
- Max: 10 files @ 20MB each
- Allowed: .pdf, .doc, .docx, .jpg, .jpeg, .png, .zip

---

## Troubleshooting

**Problem:** Files not showing
**Solution:** 
```bash
php artisan storage:link
```

**Problem:** Can't submit task
**Possible causes:**
- Task is not active (is_active = false)
- Not assigned to you
- Reached max submission limit **for that specific assignment**
- No assignment_id provided in request
- Check console for validation errors

**Problem:** Assignment selector not showing
**Causes:**
- You only have 1 assignment for that task (selector only shows when multiple exist)
- Expected behavior - selector appears only when needed

**Problem:** Can't see Filament admin menus
**Solution:** Make sure your user has admin role/permission

**Problem:** Submission went to wrong assignment
**Cause:** Assignment selector not used before submitting
**Solution:** Always check which assignment is highlighted/active before submitting

---

## New Features (Oct 17, 2025)

### Multiple Assignments Per User Per Task
- ✅ Same user dapat di-assign berkali-kali ke task yang sama
- ✅ Each assignment dapat punya title dan description unik
- ✅ Submission limit tracked per assignment (bukan per task)
- ✅ User bisa switch between assignments via selector UI
- ✅ Admin bisa track submission per assignment

### Use Cases:
1. **Revisi Bertahap**: "Initial Submission" → "Revision 1" → "Revision 2"
2. **Level Difficulty**: "Beginner Level" → "Intermediate Level" → "Advanced Level"
3. **Variasi Tugas**: "Version A" → "Version B" → "Experimental Version"
4. **Bonus Tasks**: "Main Assignment" → "Bonus Challenge" → "Extra Credit"

---

Untuk dokumentasi lengkap, lihat: **TASK_MANAGEMENT_DOCUMENTATION.md**
