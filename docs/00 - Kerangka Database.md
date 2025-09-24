## Dokumentasi Kerangka & Struktur Database

Berikut adalah dokumentasi lengkap kerangka dan struktur database dari proyek ini beserta penjelasan detailnya:

---

### 1. Tabel Utama & Relasi

#### 1.1. courses
- **Kolom:** id, title, slug, description, thumbnail, is_published, timestamps
- **Deskripsi:** Menyimpan data kursus utama. Setiap kursus memiliki judul, deskripsi, thumbnail, dan status publikasi.
- **Relasi:** Memiliki relasi ke `course_categories` melalui kolom `category_id`.

#### 1.2. course_categories
- **Kolom:** id, name, level (pemula/menengah/lanjut), description, timestamps
- **Deskripsi:** Kategori untuk mengelompokkan kursus berdasarkan level dan tema.
- **Relasi:** Satu kategori dapat memiliki banyak kursus.

#### 1.3. course_modules
- **Kolom:** id, course_id, name, description, order, timestamps
- **Deskripsi:** Modul pembelajaran dalam sebuah kursus. Setiap modul memiliki urutan unik dalam kursus.
- **Relasi:** Satu kursus memiliki banyak modul.

#### 1.4. lesson_details
- **Kolom:** id, module_id, title, content, order, timestamps
- **Deskripsi:** Detail materi pelajaran dalam modul.
- **Relasi:** Satu modul memiliki banyak lesson.

#### 1.5. course_enrollments
- **Kolom:** id, is_approved, approved_by, approved_at, approval_notes, course_id, user_id, timestamps
- **Deskripsi:** Data pendaftaran user ke kursus, termasuk status persetujuan.
- **Relasi:** Relasi ke `courses` dan `users`.

#### 1.6. course_record_sessions
- **Kolom:** id, course_enrollment_id, day_of_week, reminder_1, reminder_2, reminder_3, timestamps
- **Deskripsi:** Jadwal sesi pembelajaran dan pengingat absensi.
- **Relasi:** Relasi ke `course_enrollments`.

#### 1.7. course_attendances
- **Kolom:** id, course_record_session_id, student_id, status (Masuk/Bolos/Izin/Belum), notes, timestamps
- **Deskripsi:** Data absensi siswa pada setiap sesi.
- **Relasi:** Relasi ke `course_record_sessions` dan `users`.

#### 1.8. module_progresses
- **Kolom:** id, course_enrollment_id, module_id, is_completed, is_approved, approved_by, approved_at, approval_notes, timestamps
- **Deskripsi:** Progress siswa pada modul tertentu.
- **Relasi:** Relasi ke `course_enrollments`, `course_modules`, dan `users`.

#### 1.9. lesson_progresses
- **Kolom:** id, module_progress_id, lesson_id, is_completed, timestamps
- **Deskripsi:** Progress siswa pada materi pelajaran tertentu.
- **Relasi:** Relasi ke `module_progresses` dan `lesson_details`.

#### 1.10. student_notes
- **Kolom:** id, user_id, lesson_id, title, note, timestamps
- **Deskripsi:** Catatan pribadi siswa pada materi pelajaran.
- **Relasi:** Relasi ke `users` dan `lesson_details`.

---

### 2. Penjelasan Relasi

- **User** dapat mendaftar ke banyak kursus (`course_enrollments`), dan setiap pendaftaran dapat memiliki banyak sesi (`course_record_sessions`).
- **Absensi** dicatat per sesi dan per siswa (`course_attendances`).
- **Progress** siswa pada modul dan lesson dicatat di `module_progresses` dan `lesson_progresses`.
- **Catatan siswa** pada materi pelajaran disimpan di `student_notes`.

---

### 3. Diagram Relasi (ERD) Sederhana

```
users --< course_enrollments >-- courses --< course_modules >-- lesson_details
		 |                        |                          |
		 |                        |                          |
		 v                        v                          v
course_record_sessions      module_progresses         lesson_progresses
		 |                        |                          |
		 v                        v                          v
course_attendances         student_notes
```

---

### 4. Penjelasan Singkat Setiap Tabel

- **users:** Data user/siswa/pengajar.
- **courses:** Data kursus utama.
- **course_categories:** Kategori kursus.
- **course_modules:** Modul dalam kursus.
- **lesson_details:** Materi pelajaran dalam modul.
- **course_enrollments:** Pendaftaran user ke kursus.
- **course_record_sessions:** Jadwal sesi pembelajaran.
- **course_attendances:** Absensi siswa per sesi.
- **module_progresses:** Progress siswa pada modul.
- **lesson_progresses:** Progress siswa pada materi pelajaran.
- **student_notes:** Catatan siswa pada materi pelajaran.

---

### 5. Fitur & Fungsionalitas Database

- Mendukung sistem kursus, modul, dan materi pelajaran yang terstruktur.
- Mendukung pendaftaran, persetujuan, dan absensi otomatis.
- Mendukung tracking progress siswa secara detail.
- Mendukung pencatatan catatan pribadi siswa.

---

Jika ingin penjelasan lebih detail per kolom atau ingin diagram visual, silakan informasikan! Dokumentasi ini sudah mencakup seluruh kerangka dan relasi utama database proyek.
