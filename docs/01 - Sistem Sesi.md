## Konsep Pengembangan Sistem Absensi Otomatis

**Deskripsi:**

Sistem absensi otomatis untuk kursus berbasis waktu sesi (dengan pengingat/reminder). Sistem akan melakukan pengecekan dan pembuatan absensi secara periodik.

**Proses Utama:**

1. **Pencarian Sesi Aktif:** Setiap periode waktu tertentu (misalnya, setiap menit), sistem mencari semua `CourseRecordSession` yang memiliki status `is_completed: false`.
2. **Iterasi Sesi:** Untuk setiap `CourseRecordSession` yang ditemukan:
    * Ambil informasi `day_of_week` (hari pelaksanaan) dan daftar waktu pengingat (`reminder_1`, `reminder_2`, `reminder_3`).
    * Periksa apakah hari ini sesuai dengan `day_of_week` dari sesi.
    * **Iterasi Pengingat:** Untuk setiap waktu pengingat (yang berjumlah 3 itu):
        * Jika reminder itu kosong maka skip
        * Cek apakah waktu saat ini itu ada pada menit yang sama dengan reminder itu (contoh 22:00)
            * Cek dulu apakah ada `CourseAttendance` dengan `crated_at` nya sama dengan reminder saat ini, jika tidak:
                * Buat catatan `CourseAttendance` baru dengan informasi berikut:
                    * `status`: `'Belum'` (atau status default lainnya)
                    * `course_record_session_id`: ID dari `CourseRecordSession` yang sedang diproses
                    * `student_id`: ID siswa
                    * `waktu_absensi`: Waktu pengingat yang memicu pembuatan absensi
                * Kirim notifikasi whatsapp