# Daily Habit Tracker - Satu Tapak

Daily Habit Tracker adalah fitur lengkap untuk mengelola dan memantau kebiasaan harian secara kolaboratif dengan sistem reminder dan tracking yang canggih.

## 🎯 Fitur Utama

### 1. Manajemen Habit
- **Buat Habit Baru**: User dapat membuat habit dengan ID 5 karakter unik
- **Pengaturan Durasi**: Durasi habit dapat diatur dalam hari (1-365 hari)
- **Lock System**: Setelah habit dikunci, tidak dapat diubah lagi untuk menjaga konsistensi
- **Status Tracking**: Monitor status aktif/non-aktif habit secara real-time

### 2. Sistem Jadwal Fleksibel
- **Multi-Day Schedule**: Satu habit dapat memiliki jadwal di beberapa hari dalam seminggu
- **Waktu Spesifik**: Setiap hari memiliki waktu reminder yang dapat disesuaikan
- **Validasi Jadwal**: Sistem memvalidasi jadwal untuk menghindari konflik

### 3. Sistem Kolaboratif
- **Undang Teman**: Creator habit dapat mengundang user lain untuk bergabung
- **Approval System**: Undangan memerlukan persetujuan dari yang diundang
- **Multi-Participant**: Satu habit dapat diikuti oleh banyak user
- **Role Management**: Creator memiliki kontrol penuh terhadap habit

### 4. Sistem Notifikasi Otomatis
- **Email Reminder**: Notifikasi email otomatis berdasarkan jadwal
- **WhatsApp Integration**: Siap untuk integrasi WhatsApp (placeholder tersedia)
- **Smart Scheduling**: Hanya mengirim reminder jika belum log aktivitas
- **Failure Handling**: Sistem error handling dan retry yang robust

### 5. Activity Logging
- **Daily Log**: User dapat melakukan log aktivitas harian
- **Status Tracking**: Hadir, Terlambat, atau Tidak Hadir
- **Notes System**: Catatan tambahan untuk setiap log aktivitas
- **Duplicate Prevention**: Sistem mencegah log ganda dalam satu hari

### 6. Analytics & Statistics
- **Progress Tracking**: Monitor kemajuan individual dan kelompok
- **Visual Progress**: Progress bar dan statistik visual
- **Performance Metrics**: Analisis kehadiran dan konsistensi
- **Historical Data**: Riwayat lengkap aktivitas semua peserta

## 🗄️ Database Schema

### 1. Tabel `habits`
```sql
- id (string, 5 karakter) - Primary Key
- name (string) - Nama habit
- description (text, nullable) - Deskripsi habit
- user_id (bigint) - Foreign Key ke users
- duration_days (integer) - Durasi dalam hari
- start_date (date) - Tanggal mulai
- end_date (date) - Tanggal selesai
- is_active (boolean) - Status aktif
- is_locked (boolean) - Status dikunci
- settings (json, nullable) - Pengaturan tambahan
- timestamps
```

### 2. Tabel `habit_schedules`
```sql
- id (bigint) - Primary Key
- habit_id (string, 5 karakter) - Foreign Key ke habits
- day_of_week (enum) - Hari dalam seminggu
- scheduled_time (time) - Waktu reminder
- is_active (boolean) - Status aktif jadwal
- timestamps
```

### 3. Tabel `habit_participants`
```sql
- id (bigint) - Primary Key
- habit_id (string, 5 karakter) - Foreign Key ke habits
- user_id (bigint) - Foreign Key ke users
- status (enum) - pending, approved, rejected
- joined_at (timestamp, nullable) - Waktu bergabung
- approved_at (timestamp, nullable) - Waktu disetujui
- approved_by (bigint, nullable) - Foreign Key ke users
- timestamps
- Unique constraint: habit_id, user_id
```

### 4. Tabel `habit_logs`
```sql
- id (bigint) - Primary Key
- habit_id (string, 5 karakter) - Foreign Key ke habits
- user_id (bigint) - Foreign Key ke users
- log_date (date) - Tanggal log
- log_time (time) - Waktu log
- status (enum) - present, absent, late
- notes (text, nullable) - Catatan
- logged_at (timestamp) - Waktu pencatatan
- timestamps
- Unique constraint: habit_id, user_id, log_date
```

### 5. Tabel `habit_invitations`
```sql
- id (bigint) - Primary Key
- habit_id (string, 5 karakter) - Foreign Key ke habits
- inviter_id (bigint) - Foreign Key ke users (pengundang)
- invitee_id (bigint) - Foreign Key ke users (yang diundang)
- token (string, unique) - Token unik untuk link undangan
- status (enum) - pending, accepted, rejected, expired
- expires_at (timestamp) - Waktu kadaluarsa
- responded_at (timestamp, nullable) - Waktu respons
- message (text, nullable) - Pesan dari pengundang
- timestamps
```

## 🛣️ Route Structure

Semua routes menggunakan prefix `satu-tapak` dan middleware `auth`, `verified`:

### Resource Routes
- `GET /satu-tapak/habits` - Index (daftar habit)
- `GET /satu-tapak/habits/create` - Form buat habit baru
- `POST /satu-tapak/habits` - Store habit baru
- `GET /satu-tapak/habits/{habit}` - Detail habit
- `GET /satu-tapak/habits/{habit}/edit` - Form edit habit
- `PUT /satu-tapak/habits/{habit}` - Update habit
- `DELETE /satu-tapak/habits/{habit}` - Hapus habit

### Custom Routes
- `POST /satu-tapak/habits/{habit}/lock` - Kunci habit
- `GET /satu-tapak/habits/{habit}/invite` - Form undang teman
- `POST /satu-tapak/habits/{habit}/invite` - Kirim undangan
- `GET /satu-tapak/invitations` - Daftar undangan yang diterima
- `POST /satu-tapak/invitations/{invitation}/respond` - Respons undangan
- `POST /satu-tapak/habits/{habit}/log` - Log aktivitas
- `GET /satu-tapak/habits/{habit}/statistics` - Statistik habit

## 📱 Halaman UI yang Tersedia

### 1. Dashboard (`/satu-tapak/habits`)
- Tab "Habit Saya": Habit yang dibuat oleh user
- Tab "Ikut Serta": Habit yang diikuti user
- Badge notifikasi untuk undangan pending
- Cards dengan informasi lengkap setiap habit

### 2. Create Habit (`/satu-tapak/habits/create`)
- Form lengkap dengan validasi JavaScript
- Dynamic schedule management
- Real-time form validation
- User-friendly interface

### 3. Habit Detail (`/satu-tapak/habits/{habit}`)
- Informasi lengkap habit
- Form daily logging (jika user adalah peserta)
- Daftar peserta dan progress
- Log aktivitas terbaru
- Action buttons (undang, kunci, edit)

### 4. Invite Friends (`/satu-tapak/habits/{habit}/invite`)
- Daftar user yang dapat diundang
- Checkbox selection dengan "Select All"
- Form pesan undangan
- Informasi habit yang akan dibagikan

### 5. Invitations (`/satu-tapak/invitations`)
- Daftar undangan yang diterima
- Detail lengkap setiap undangan
- Action buttons (terima/tolak)
- Status tracking undangan

### 6. Statistics (`/satu-tapak/habits/{habit}/statistics`)
- Overview metrics dengan cards
- Progress bars per peserta
- Aktivitas terbaru
- Visual analytics

### 7. Edit Habit (`/satu-tapak/habits/{habit}/edit`)
- Form edit informasi dasar
- Read-only display untuk jadwal dan durasi
- Danger zone untuk hapus habit

## ⚙️ Sistem Backend

### 1. Models dengan Relasi Lengkap
- **Habit Model**: Central model dengan relasi ke semua entitas
- **Auto-generating ID**: 5 karakter unik otomatis
- **Eloquent Relations**: Optimized eager loading
- **Business Logic**: Method helper untuk validasi dan status

### 2. Controllers dengan Proper Authorization
- **Policy-based Access Control**: Otomatis melalui route model binding
- **Validation Rules**: Comprehensive input validation
- **Error Handling**: Proper HTTP status codes dan messages
- **Resource Management**: Efficient database queries

### 3. Job System untuk Notifications
- **Queueable Jobs**: Async processing untuk performa optimal
- **Retry Logic**: Built-in retry mechanism untuk failed jobs
- **Logging**: Comprehensive logging untuk debugging
- **Scalable Architecture**: Ready untuk high-volume processing

### 4. Command Line Interface
- **Artisan Commands**: `php artisan habits:send-reminders`
- **Test Mode**: `--test` flag untuk debugging
- **Scheduler Integration**: Otomatis berjalan setiap 5 menit
- **Error Recovery**: Graceful error handling dan reporting

## 🔔 Sistem Notifikasi

### 1. Email Notifications
- **Rich HTML Templates**: Professional email design
- **Personalized Content**: User-specific information
- **Action Links**: Direct links ke habit tracker
- **Responsive Design**: Mobile-friendly emails

### 2. Smart Scheduling
- **Time-based Triggers**: Akurasi dalam 5 menit dari jadwal
- **Duplicate Prevention**: Tidak mengirim jika sudah log hari ini
- **Status Validation**: Hanya untuk habit dan user yang aktif
- **Performance Optimization**: Batch processing untuk efficiency

### 3. WhatsApp Integration (Ready)
- **Placeholder Implementation**: Siap untuk integrasi WhatsApp API
- **Template Messages**: Format pesan sudah disiapkan
- **User Preference**: Optional WhatsApp notifications
- **Fallback Mechanism**: Email sebagai backup

## 🔒 Security Features

### 1. Authorization
- **User Ownership**: Hanya creator yang dapat edit/delete
- **Participant Validation**: Validasi membership sebelum aksi
- **Token-based Invitations**: Secure invitation system
- **CSRF Protection**: Built-in Laravel CSRF

### 2. Data Validation
- **Server-side Validation**: Comprehensive input validation
- **Client-side Validation**: JavaScript real-time validation
- **SQL Injection Prevention**: Eloquent ORM protection
- **XSS Protection**: Blade template escaping

### 3. Rate Limiting
- **API Throttling**: Prevent abuse melalui middleware
- **Queue Management**: Job throttling untuk resource management
- **Database Constraints**: Unique constraints untuk data integrity

## 📊 Performance Optimizations

### 1. Database Optimizations
- **Proper Indexing**: Foreign keys dan unique constraints
- **Eager Loading**: Minimasi N+1 query problems
- **Query Optimization**: Efficient database queries
- **Connection Pooling**: Optimal database connections

### 2. Caching Strategy
- **Model Caching**: Cache expensive queries
- **View Caching**: Cache rendered views untuk performance
- **Session Optimization**: Efficient session management

### 3. Queue System
- **Async Processing**: Background job processing
- **Job Prioritization**: Critical jobs diproses dulu
- **Worker Management**: Scalable worker configuration
- **Memory Management**: Efficient memory usage

## 🚀 Deployment Ready

### 1. Environment Configuration
- **Environment Variables**: Proper .env configuration
- **Queue Workers**: Production-ready queue setup
- **Scheduler**: Cron job configuration
- **Error Monitoring**: Comprehensive error tracking

### 2. Monitoring & Logging
- **Application Logs**: Detailed logging untuk debugging
- **Performance Metrics**: Monitor performance bottlenecks
- **Error Tracking**: Automatic error reporting
- **Health Checks**: System health monitoring

## 📝 Usage Examples

### 1. Membuat Habit Baru
```php
$habit = Habit::create([
    'name' => 'Olahraga Pagi',
    'description' => 'Olahraga ringan setiap pagi',
    'user_id' => auth()->id(),
    'duration_days' => 30,
    'start_date' => now(),
]);
```

### 2. Mengundang User
```php
$invitation = HabitInvitation::create([
    'habit_id' => 'ABC12',
    'inviter_id' => auth()->id(),
    'invitee_id' => $user->id,
    'message' => 'Yuk join habit olahraga!',
]);
```

### 3. Log Aktivitas
```php
HabitLog::create([
    'habit_id' => 'ABC12',
    'user_id' => auth()->id(),
    'log_date' => today(),
    'log_time' => now(),
    'status' => 'present',
    'notes' => 'Jogging 30 menit',
]);
```

### 4. Menjalankan Reminder
```bash
# Test mode
php artisan habits:send-reminders --test

# Production mode
php artisan habits:send-reminders
```

---

## 🎉 Kesimpulan

Daily Habit Tracker "Satu Tapak" adalah solusi lengkap untuk manajemen habit kolaboratif dengan fitur:

✅ **Habit Management** - CRUD lengkap dengan validasi\
✅ **Collaborative System** - Multi-user dengan approval system\
✅ **Smart Notifications** - Email & WhatsApp ready dengan scheduler\
✅ **Activity Logging** - Comprehensive tracking dengan analytics\
✅ **Security & Performance** - Production-ready dengan optimizations\
✅ **User-friendly Interface** - Modern UI dengan responsive design

Fitur ini siap digunakan dan dapat di-scale untuk banyak user dengan performa yang optimal!

🚀 **Happy Habit Tracking!** 🎯