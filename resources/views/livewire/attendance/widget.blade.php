<?php

use App\Models\CourseAttendance;
use App\Models\CourseEnrollment;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Livewire\Volt\Component;

new class extends Component {
    public $todayAttendance;
    public $canMarkAttendance;
    public $pendingCourses;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->loadTodayAttendance();
        $this->checkAttendanceAvailability();
    }

    /**
     * Load today's attendance
     */
    public function loadTodayAttendance(): void
    {
        $this->todayAttendance = CourseAttendance::where('student_id', Auth::id())
            ->whereDate('absent_date', Carbon::today())
            ->with('course')
            ->get();
    }

    /**
     * Check attendance availability
     */
    public function checkAttendanceAvailability(): void
    {
        $enrolledCourses = CourseEnrollment::where('user_id', Auth::id())
            ->with('course')
            ->get()
            ->pluck('course')
            ->filter();
            
        $currentHour = Carbon::now()->hour;
        $isWorkingHours = $currentHour >= 7 && $currentHour <= 17;
        
        if ($isWorkingHours && $enrolledCourses->count() > 0) {
            $attendedCourseIds = $this->todayAttendance->pluck('course_id')->toArray();
            
            $this->pendingCourses = $enrolledCourses->reject(function($course) use ($attendedCourseIds) {
                return in_array($course->id, $attendedCourseIds);
            });
            
            $this->canMarkAttendance = $this->pendingCourses->count() > 0;
        } else {
            $this->canMarkAttendance = false;
            $this->pendingCourses = collect();
        }
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
}; ?>

<div class="bg-white rounded-xl shadow-lg p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
            <x-heroicon-o-calendar-days class="w-5 h-5 text-indigo-500"/>
            Absensi Hari Ini
        </h3>
        <span class="text-sm text-gray-500">{{ Carbon::now()->format('d M Y') }}</span>
    </div>

    @if($todayAttendance->count() > 0)
        <!-- Today's Attendances -->
        <div class="space-y-3 mb-4">
            @foreach($todayAttendance as $attendance)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $attendance->course->title }}</p>
                        <p class="text-xs text-gray-600">{{ $attendance->created_at->format('H:i') }}</p>
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $this->getStatusBadgeClass($attendance->status) }}">
                        {{ $attendance->status }}
                    </span>
                </div>
            @endforeach
        </div>
    @endif

    @if($canMarkAttendance)
        <!-- Pending Attendance -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
            <div class="flex items-center gap-2 text-yellow-800 mb-2">
                <x-heroicon-o-exclamation-triangle class="w-4 h-4"/>
                <span class="text-sm font-medium">Absensi Menunggu</span>
            </div>
            <p class="text-xs text-yellow-700 mb-2">
                {{ $pendingCourses->count() }} course belum diabsen hari ini
            </p>
            <div class="space-y-1">
                @foreach($pendingCourses->take(3) as $course)
                    <p class="text-xs text-yellow-600">• {{ $course->title }}</p>
                @endforeach
                @if($pendingCourses->count() > 3)
                    <p class="text-xs text-yellow-600">• dan {{ $pendingCourses->count() - 3 }} lainnya</p>
                @endif
            </div>
        </div>

        <a href="{{ route('attendance.form') }}" 
           class="block w-full text-center px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white font-medium rounded-lg transition-colors text-sm">
            Lakukan Absensi
        </a>
    @else
        @if($todayAttendance->count() === 0)
            <!-- No Attendance -->
            <div class="text-center py-4">
                <x-heroicon-o-clock class="mx-auto h-8 w-8 text-gray-400 mb-2"/>
                <p class="text-sm text-gray-500 mb-2">Belum ada absensi hari ini</p>
                @php
                    $currentHour = Carbon::now()->hour;
                    $isWorkingHours = $currentHour >= 7 && $currentHour <= 17;
                @endphp
                @if(!$isWorkingHours)
                    <p class="text-xs text-gray-400">Waktu absensi: 07:00 - 17:00</p>
                @else
                    <p class="text-xs text-gray-400">Tidak ada course yang perlu diabsen</p>
                @endif
            </div>
        @else
            <!-- All Attendance Complete -->
            <div class="text-center py-2">
                <x-heroicon-o-check-badge class="mx-auto h-8 w-8 text-green-500 mb-2"/>
                <p class="text-sm text-green-600 font-medium">Absensi Lengkap!</p>
                <p class="text-xs text-gray-500">Semua course sudah diabsen hari ini</p>
            </div>
        @endif
    @endif

    <!-- View All Link -->
    <div class="mt-4 pt-3 border-t border-gray-200">
        <a href="{{ route('attendance.index') }}" 
           class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1">
            <span>Lihat Semua Riwayat</span>
            <x-heroicon-o-arrow-right class="w-4 h-4"/>
        </a>
    </div>
</div>
