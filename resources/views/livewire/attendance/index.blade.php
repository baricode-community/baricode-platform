<?php

use App\Models\CourseAttendance;
use App\Models\CourseEnrollment;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $month;
    public $year;
    public $course_id = '';
    public $status = '';
    
    public $enrolledCourses;
    public $attendanceStats;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->month = request('month', now()->month);
        $this->year = request('year', now()->year);
        $this->course_id = request('course_id', '');
        $this->status = request('status', '');
        
        $this->loadEnrolledCourses();
        $this->loadAttendanceStats();
    }

    /**
     * Load user's enrolled courses
     */
    public function loadEnrolledCourses(): void
    {
        $this->enrolledCourses = CourseEnrollment::where('user_id', Auth::id())
            ->with('course')
            ->get()
            ->pluck('course')
            ->filter();
    }

    /**
     * Load attendance statistics
     */
    public function loadAttendanceStats(): void
    {
        $query = CourseAttendance::where('student_id', Auth::id())
            ->whereYear('absent_date', $this->year)
            ->whereMonth('absent_date', $this->month);

        if ($this->course_id) {
            $query->where('course_id', $this->course_id);
        }

        $totalAttendances = $query->count();
        $presentCount = (clone $query)->where('status', CourseAttendance::STATUS_MASUK)->count();
        $absentCount = (clone $query)->where('status', CourseAttendance::STATUS_BOLOS)->count();
        $excusedCount = (clone $query)->where('status', CourseAttendance::STATUS_IZIN)->count();
        $attendanceRate = $totalAttendances > 0 ? round(($presentCount / $totalAttendances) * 100, 1) : 0;

        $this->attendanceStats = [
            'total' => $totalAttendances,
            'present' => $presentCount,
            'absent' => $absentCount,
            'excused' => $excusedCount,
            'rate' => $attendanceRate
        ];
    }

    /**
     * Get filtered attendances with pagination
     */
    public function getAttendances()
    {
        $query = CourseAttendance::where('student_id', Auth::id())
            ->with(['course.category'])
            ->whereYear('absent_date', $this->year)
            ->whereMonth('absent_date', $this->month);

        if ($this->course_id) {
            $query->where('course_id', $this->course_id);
        }
        
        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query->orderBy('absent_date', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
    }

    /**
     * Apply filters
     */
    public function applyFilters(): void
    {
        $this->loadAttendanceStats();
        $this->resetPage();
    }

    /**
     * Reset filters
     */
    public function resetFilters(): void
    {
        $this->month = now()->month;
        $this->year = now()->year;
        $this->course_id = '';
        $this->status = '';
        
        $this->loadAttendanceStats();
        $this->resetPage();
    }

    /**
     * Updated hook for reactive updates
     */
    public function updated($property): void
    {
        if (in_array($property, ['month', 'year', 'course_id', 'status'])) {
            $this->applyFilters();
        }
    }

    /**
     * Get status options
     */
    public function getStatusOptions(): array
    {
        return CourseAttendance::getStatusOptions();
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass($status): string
    {
        return match($status) {
            CourseAttendance::STATUS_MASUK => 'bg-green-100 text-green-800',
            CourseAttendance::STATUS_BOLOS => 'bg-red-100 text-red-800',
            CourseAttendance::STATUS_IZIN => 'bg-yellow-100 text-yellow-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get status icon
     */
    public function getStatusIcon($status): string
    {
        return match($status) {
            CourseAttendance::STATUS_MASUK => 'check-circle',
            CourseAttendance::STATUS_BOLOS => 'x-circle',
            CourseAttendance::STATUS_IZIN => 'exclamation-triangle',
            default => 'question-mark-circle',
        };
    }
}; ?>

<div>
    <div class="py-12 px-4 md:px-6 lg:px-8 min-h-screen bg-gradient-to-br from-indigo-50 via-white to-indigo-100">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl md:text-4xl font-extrabold mb-2 flex items-center gap-2">
                        <x-heroicon-o-calendar-days class="w-8 h-8 text-indigo-500"/>
                        Riwayat Absensi
                    </h1>
                    <p class="text-gray-600">Lihat dan kelola riwayat kehadiran Anda</p>
                </div>
                <a href="{{ route('attendance.form') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-500 hover:bg-indigo-600 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-1">
                    <x-heroicon-o-plus class="w-5 h-5"/>
                    Absensi Hari Ini
                </a>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-indigo-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Absensi</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $attendanceStats['total'] }}</p>
                        </div>
                        <div class="p-3 bg-indigo-100 rounded-full">
                            <x-heroicon-o-calendar class="w-6 h-6 text-indigo-500"/>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Hadir</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $attendanceStats['present'] }}</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <x-heroicon-o-check-circle class="w-6 h-6 text-green-500"/>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Bolos</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $attendanceStats['absent'] }}</p>
                        </div>
                        <div class="p-3 bg-red-100 rounded-full">
                            <x-heroicon-o-x-circle class="w-6 h-6 text-red-500"/>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Rate Kehadiran</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $attendanceStats['rate'] }}%</p>
                        </div>
                        <div class="p-3 bg-yellow-100 rounded-full">
                            <x-heroicon-o-chart-pie class="w-6 h-6 text-yellow-500"/>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="month" class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                        <select wire:model.live="month" id="month" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}">
                                    {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                        <select wire:model.live="year" id="year" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">Course</label>
                        <select wire:model.live="course_id" id="course_id" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua Course</option>
                            @foreach($enrolledCourses as $course)
                                <option value="{{ $course->id }}">{{ $course->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select wire:model.live="status" id="status" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua Status</option>
                            @foreach($this->getStatusOptions() as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-4 flex gap-3">
                    <button wire:click="resetFilters" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors">
                        <x-heroicon-o-arrow-path class="w-4 h-4"/>
                        Reset Filter
                    </button>
                </div>
            </div>

            <!-- Attendance Records -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Riwayat Absensi - {{ \Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }}
                    </h3>
                </div>

                @php $attendances = $this->getAttendances(); @endphp

                @if($attendances->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Course
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Catatan
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Waktu Input
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($attendances as $attendance)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ \Carbon\Carbon::parse($attendance->absent_date)->format('d M Y') }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($attendance->absent_date)->format('l') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $attendance->course->title }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $attendance->course->category->name ?? 'Tidak ada kategori' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getStatusBadgeClass($attendance->status) }}">
                                                <x-dynamic-component :component="'heroicon-o-' . $this->getStatusIcon($attendance->status)" class="w-4 h-4 mr-1"/>
                                                {{ $attendance->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 max-w-xs truncate">
                                                {{ $attendance->notes ?: '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $attendance->created_at->format('d M Y, H:i') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $attendances->links() }}
                    </div>
                @else
                    <div class="px-6 py-12 text-center">
                        <x-heroicon-o-calendar-days class="mx-auto h-12 w-12 text-gray-400"/>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data absensi</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Belum ada catatan absensi untuk filter yang dipilih.
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('attendance.form') }}" 
                               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white font-medium rounded-lg">
                                <x-heroicon-o-plus class="w-4 h-4"/>
                                Mulai Absensi
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Monthly Summary -->
            @if($attendanceStats['total'] > 0)
                <div class="mt-8 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl shadow-lg text-white p-6">
                    <h3 class="text-lg font-semibold mb-4">Ringkasan Bulan {{ \Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }}</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <p class="text-2xl font-bold">{{ $attendanceStats['total'] }}</p>
                            <p class="text-indigo-100 text-sm">Total Hari</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold">{{ $attendanceStats['present'] }}</p>
                            <p class="text-indigo-100 text-sm">Hadir</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold">{{ $attendanceStats['absent'] }}</p>
                            <p class="text-indigo-100 text-sm">Bolos</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold">{{ $attendanceStats['rate'] }}%</p>
                            <p class="text-indigo-100 text-sm">Tingkat Kehadiran</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
