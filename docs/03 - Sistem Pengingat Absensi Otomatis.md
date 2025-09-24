# Sistem Pengingat Absensi Otomatis

## Ringkasan Implementasi

Sistem pengingat absensi otomatis telah berhasil diimplementasikan dengan fitur-fitur berikut:

## Fitur-Fitur Utama

### 1. **Pencarian Sesi Aktif Otomatis**
- Sistem berjalan setiap menit melalui command `attendance:check-sessions`
- Mencari semua `CourseRecordSession` dengan status `is_completed: false`
- Memeriksa apakah hari ini sesuai dengan `day_of_week` dari sesi

### 2. **Sistem Pengingat Berbasis Waktu**
- Memeriksa 3 waktu pengingat (`reminder_1`, `reminder_2`, `reminder_3`)
- Jika reminder kosong, sistem akan skip
- Mengecek apakah waktu saat ini berada dalam rentang waktu reminder hingga 5 menit kemudian
- Contoh: jika reminder_1 = "14:00", sistem akan aktif dari 14:00 - 14:05

### 3. **Pembuatan Absensi Otomatis**
- Sebelum membuat absensi baru, sistem mengecek apakah sudah ada `CourseAttendance` dengan:
  - `course_record_session_id` yang sama
  - `student_id` yang sama  
  - `waktu_absensi` yang sama dengan reminder
- Jika belum ada, sistem membuat catatan absensi baru dengan:
  - `status`: 'Belum'
  - `course_record_session_id`: ID dari session yang diproses
  - `student_id`: ID siswa dari enrollment
  - `waktu_absensi`: Waktu pengingat yang memicu pembuatan
  - `created_at`: Waktu reminder (bukan waktu sistem saat itu)
  - `absent_date`: Tanggal hari ini
  - `course_id`: ID course dari enrollment

### 4. **Notifikasi WhatsApp**
- Setiap kali absensi dibuat, sistem mengirim notifikasi WhatsApp ke siswa
- Format notifikasi yang user-friendly dengan emoji dan informasi lengkap
- Nomor telepon otomatis diformat ke format internasional (+62)
- Menggunakan WhatsAppService yang sudah terintegrasi dengan API

## File-File yang Dimodifikasi/Dibuat

### 1. **Database Migration**
```php
// 2025_09_24_092700_add_waktu_absensi_to_course_attendances_table.php
$table->time('waktu_absensi')->nullable()->comment('Waktu pengingat yang memicu pembuatan absensi');
```

### 2. **Model Updates**
- `CourseAttendance`: Menambahkan fillable fields dan relasi ke CourseRecordSession
- Menambahkan field `waktu_absensi` untuk tracking waktu reminder

### 3. **Business Logic**
- `CourseRecordSessionTrait`: Logic utama untuk checking dan pembuatan absensi
- Implementasi yang presisi sesuai dengan requirement

### 4. **Notification Service**
- `WhatsAppService`: Method baru `sendAttendanceReminder()` untuk notifikasi
- Format pesan yang menarik dan informatif

### 5. **Console Command**
- `CheckSessionAttendance`: Command yang berjalan setiap menit
- Logging yang comprehensive untuk monitoring

## Cara Kerja Sistem

1. **Setiap menit**, command `attendance:check-sessions` dijalankan oleh Laravel scheduler
2. **Sistem mengambil** semua CourseRecordSession yang belum completed (`is_completed = false`)
3. **Untuk setiap session**, sistem mengecek:
   - Apakah hari ini sesuai dengan `day_of_week` session
   - Apakah waktu saat ini dalam rentang reminder (reminder_time sampai +5 menit)
4. **Jika kondisi terpenuhi**, sistem:
   - Mengecek apakah absensi dengan waktu reminder yang sama sudah dibuat
   - Jika belum, membuat record absensi baru
   - Mengirim notifikasi WhatsApp ke siswa
5. **Logging** semua aktivitas untuk monitoring dan debugging

## Konfigurasi Schedule

```php
// routes/console.php
Schedule::command(CheckSessionAttendance::class)->everyMinute();
```

## Contoh Log Output

```
[2025-09-24 09:27:00] INFO: Checked 5 incomplete sessions for attendance creation
[2025-09-24 09:27:00] INFO: Created attendance for student ID: 123 in session ID: 45 for reminder time: 14:00:00
[2025-09-24 09:27:00] INFO: WhatsApp notification sent successfully to 6281234567890 for student ID: 123
```

## Testing

Sistem telah diuji dan berjalan tanpa error. Command dapat dijalankan manual untuk testing:

```bash
php artisan attendance:check-sessions
```

## Environment Variables Required

Pastikan `WHATSAPP_API_KEY` sudah dikonfigurasi di file `.env` untuk notifikasi WhatsApp.

## Kesimpulan

Sistem pengingat absensi otomatis telah berhasil diimplementasikan sesuai dengan requirement yang diminta. Sistem akan:

✅ Berjalan otomatis setiap menit  
✅ Hanya membuat absensi pada waktu reminder yang tepat  
✅ Menghindari duplikasi absensi untuk reminder yang sama  
✅ Mengirim notifikasi WhatsApp yang informatif  
✅ Mencatat semua aktivitas untuk monitoring  

Sistem siap untuk production use!
