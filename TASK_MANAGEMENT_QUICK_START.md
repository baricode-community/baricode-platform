# Task Management System - Quick Start Guide

## Sistem Baru

Sistem task telah dirombak dengan workflow baru:
- ✅ Admin membuat task via Filament
- ✅ Admin assign task ke multiple users
- ✅ User dapat submit task berkali-kali (sesuai limit)
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
   - Pilih user
   - Set deadline (opsional)
   - Tambah notes untuk user (opsional)
   - Save
   - Ulangi untuk assign ke user lain

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
   - Lihat daftar task yang didelegasikan ke Anda
   - Klik "Lihat Detail" pada task yang ingin dikerjakan

3. **Submit Pengerjaan:**
   - Baca task detail, instruksi, download lampiran jika ada
   - Scroll ke bagian "Submit Pengerjaan"
   - Tulis hasil pengerjaan di textarea
   - Upload file jika diperlukan (max 10 files, 20MB each)
   - Klik "Submit Pengerjaan"

4. **Cek Status Submission:**
   - Klik "Riwayat Submission" di header
   - Lihat semua submission Anda
   - Cek status: Pending/Approved/Rejected/Revision Requested
   - Klik "View Detail" untuk lihat feedback dari reviewer

5. **Submit Ulang (jika masih bisa):**
   - Jika belum mencapai batas max submission
   - Kembali ke task detail
   - Submit lagi dengan perbaikan

---

## Database Tables

### tasks
- Menyimpan informasi task
- Key fields: title, description, content, instructions, is_active, max_submissions_per_user

### task_assignments
- Mendelegasikan task ke users
- Key fields: task_id, user_id, assigned_by, due_date, status, notes

### task_submissions
- Menyimpan pengerjaan dari users
- Key fields: task_id, user_id, submission_content, files, status, reviewed_by, review_notes, score

---

## Routes

```
GET  /tasks                      - Daftar task assigned ke user
GET  /tasks/{id}                 - Detail task & form submit
POST /tasks/{id}/submit          - Submit pengerjaan
GET  /tasks/submissions          - History semua submissions
GET  /tasks/submission/{id}      - Detail satu submission
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
- Reached max submission limit
- Check console for validation errors

**Problem:** Can't see Filament admin menus
**Solution:** Make sure your user has admin role/permission

---

Untuk dokumentasi lengkap, lihat: **TASK_MANAGEMENT_DOCUMENTATION.md**
