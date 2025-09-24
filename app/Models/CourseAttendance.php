<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\Course;
use App\Models\User;

class CourseAttendance extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Konstanta untuk status absensi
    public const STATUS_MASUK = 'Masuk';
    public const STATUS_BOLOS = 'Bolos';
    public const STATUS_IZIN = 'Izin';
    public const STATUS_BELUM = 'Belum';

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_MASUK => 'Masuk',
            self::STATUS_BOLOS => 'Bolos',
            self::STATUS_IZIN => 'Izin',
            self::STATUS_BELUM => 'Belum',
        ];
    }

    // Relasi ke Course
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    // Relasi ke Student (User)
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // Scope untuk filter berdasarkan status
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    // Scope untuk filter berdasarkan tanggal
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('absent_date', $date);
    }

    // Scope untuk filter berdasarkan bulan
    public function scopeByMonth($query, int $year, int $month)
    {
        return $query->whereYear('absent_date', $year)
                    ->whereMonth('absent_date', $month);
    }

    // Accessor untuk menampilkan status dengan badge
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            self::STATUS_MASUK => '<span class="badge badge-success">Masuk</span>',
            self::STATUS_BOLOS => '<span class="badge badge-danger">Bolos</span>',
            self::STATUS_IZIN => '<span class="badge badge-warning">Izin</span>',
            self::STATUS_BELUM => '<span class="badge badge-secondary">Belum</span>',
            default => '<span class="badge badge-secondary">Unknown</span>',
        };
    }

    // Method untuk check apakah siswa hadir
    public function isPresent(): bool
    {
        return $this->status === self::STATUS_MASUK;
    }

    // Method untuk check apakah siswa bolos
    public function isAbsent(): bool
    {
        return $this->status === self::STATUS_BOLOS;
    }

    // Method untuk check apakah siswa izin
    public function isExcused(): bool
    {
        return $this->status === self::STATUS_IZIN;
    }

    // Method untuk check apakah siswa belum absen
    public function isNotYet(): bool
    {
        return $this->status === self::STATUS_BELUM;
    }
}