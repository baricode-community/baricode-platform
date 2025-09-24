<?php

use App\Models\CourseAttendance;
use App\Models\CourseEnrollment;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Livewire\Volt\Component;

new class extends Component {
    public $course_id = '';
    public $status = '';
    public $notes = '';
    
    public $enrolledCourses;
    public $availableCourses;
    public $canMarkAttendance = false;
    public $currentTime;
    public $todayAttendances;
    
    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->currentTime = Carbon::now();
        $this->loadEnrolledCourses();
        $this->loadTodayAttendances();
        $this->checkAttendanceAvailability();
    }

    /**
     * Load user's enrolled courses
     */
    public function loadEnrolledCourses(): void
    {
        $this->enrolledCourses = CourseEnrollment::where('user_id', Auth::id())
            ->with(['course.category'])
            ->get()
            ->pluck('course')
            ->filter();
    }

    /**
     * Load today's attendances
     */
    public function loadTodayAttendances(): void
    {
        $this->todayAttendances = CourseAttendance::where('student_id', Auth::id())
            ->whereDate('absent_date', Carbon::today())
            ->with('course')
            ->get();
    }

    /**
     * Check if user can mark attendance now
     */
    public function checkAttendanceAvailability(): void
    {
        // Logika: User dapat melakukan absensi jika:
        // 1. Ada course yang enrolled
        // 2. Waktu sekarang dalam jam kerja/belajar (misal: 07:00 - 17:00)
        // 3. Belum melakukan absensi untuk semua course hari ini
        
        $currentHour = Carbon::now()->hour;
        $isWorkingHours = $currentHour >= 7 && $currentHour <= 17;
        
        $this->availableCourses = collect();
        
        if ($isWorkingHours && $this->enrolledCourses->count() > 0) {
            // Filter course yang belum diabsen hari ini
            $attendedCourseIds = $this->todayAttendances->pluck('course_id')->toArray();
            
            $this->availableCourses = $this->enrolledCourses->reject(function($course) use ($attendedCourseIds) {
                return in_array($course->id, $attendedCourseIds);
            });
            
            $this->canMarkAttendance = $this->availableCourses->count() > 0;
        }
    }

    /**
     * Submit attendance
     */
    public function submitAttendance(): void
    {
        $this->validate([
            'course_id' => 'required|exists:courses,id',
            'status' => 'required|in:' . implode(',', array_keys(CourseAttendance::getStatusOptions())),
            'notes' => 'nullable|string|max:500'
        ], [
            'course_id.required' => 'Course harus dipilih.',
            'course_id.exists' => 'Course yang dipilih tidak valid.',
            'status.required' => 'Status kehadiran harus dipilih.',
            'status.in' => 'Status kehadiran tidak valid.',
            'notes.max' => 'Catatan maksimal 500 karakter.'
        ]);

        // Cek apakah user enrolled di course ini
        $enrollment = CourseEnrollment::where('user_id', Auth::id())
            ->where('course_id', $this->course_id)
            ->first();
            
        if (!$enrollment) {
            $this->addError('course_id', 'Anda tidak terdaftar di course yang dipilih.');
            return;
        }

        // Cek apakah sudah absen hari ini untuk course ini
        $existingAttendance = CourseAttendance::where('student_id', Auth::id())
            ->where('course_id', $this->course_id)
            ->whereDate('absent_date', Carbon::today())
            ->first();

        if ($existingAttendance) {
            $this->addError('course_id', 'Anda sudah melakukan absensi untuk course ini hari ini.');
            return;
        }

        // Simpan attendance
        CourseAttendance::create([
            'student_id' => Auth::id(),
            'course_id' => $this->course_id,
            'absent_date' => Carbon::today(),
            'status' => $this->status,
            'notes' => $this->notes
        ]);

        // Reset form
        $this->course_id = '';
        $this->status = '';
        $this->notes = '';

        // Refresh data
        $this->loadTodayAttendances();
        $this->checkAttendanceAvailability();

        // Show success message
        session()->flash('success', 'Absensi berhasil disimpan!');
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
    <!-- Page Header -->
    <div class="py-12 px-4 md:px-6 lg:px-8 min-h-screen bg-gradient-to-br from-indigo-50 via-white to-indigo-100">
        <div class="max-w-4xl mx-auto">
            <div class="mb-8">
                <h1 class="text-3xl md:text-4xl font-extrabold mb-2 flex items-center gap-2">
                    <x-heroicon-o-calendar-days class="w-8 h-8 text-indigo-500"/>
                    Absensi Hari Ini
                </h1>
                <p class="text-gray-600">{{ $currentTime->format('l, d F Y - H:i') }}</p>
            </div>

            <!-- Flash Messages -->
            @if (session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center gap-2 text-green-800">
                        <x-heroicon-o-check-circle class="w-5 h-5"/>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            <!-- Today's Attendances -->
            @if($todayAttendances->count() > 0)
                <div class="mb-8 bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-800">Absensi Hari Ini</h3>
                    </div>
                    
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($todayAttendances as $attendance)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg bg-gray-50">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ $attendance->course->title }}</h4>
                                        <p class="text-sm text-gray-600">{{ $attendance->course->category->name ?? 'Tidak ada kategori' }}</p>
                                        @if($attendance->notes)
                                            <p class="text-sm text-gray-500 mt-1">{{ $attendance->notes }}</p>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getStatusBadgeClass($attendance->status) }}">
                                            <x-dynamic-component :component="'heroicon-o-' . $this->getStatusIcon($attendance->status)" class="w-4 h-4 mr-1"/>
                                            {{ $attendance->status }}
                                        </span>
                                        <span class="text-xs text-gray-400">{{ $attendance->created_at->format('H:i') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Attendance Form -->
            @if($canMarkAttendance)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-indigo-50">
                        <h3 class="text-lg font-semibold text-indigo-800 flex items-center gap-2">
                            <x-heroicon-o-clock class="w-5 h-5"/>
                            Waktu Absensi Tersedia
                        </h3>
                        <p class="text-sm text-indigo-600 mt-1">Silakan lakukan absensi untuk course yang belum diabsen</p>
                    </div>

                    <form wire:submit="submitAttendance" class="p-6 space-y-6">
                        <!-- Course Selection -->
                        <div>
                            <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Course <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="course_id" id="course_id" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('course_id') border-red-300 @enderror">
                                <option value="">Pilih Course</option>
                                @foreach($availableCourses as $course)
                                    <option value="{{ $course->id }}">
                                        {{ $course->title }}
                                        @if($course->category) ({{ $course->category->name }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('course_id') 
                                <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
                                    <x-heroicon-o-exclamation-circle class="w-4 h-4"/>
                                    {{ $message }}
                                </p> 
                            @enderror
                        </div>

                        <!-- Status Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Status Kehadiran <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                @foreach($this->getStatusOptions() as $key => $label)
                                    <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors
                                                  @if($key === App\Models\CourseAttendance::STATUS_MASUK) border-green-200 hover:border-green-300
                                                  @elseif($key === App\Models\CourseAttendance::STATUS_BOLOS) border-red-200 hover:border-red-300
                                                  @else border-yellow-200 hover:border-yellow-300 @endif">
                                        <input type="radio" wire:model="status" value="{{ $key }}" class="sr-only peer">
                                        
                                        <div class="flex items-center gap-3 w-full">
                                            @if($key === App\Models\CourseAttendance::STATUS_MASUK)
                                                <div class="p-2 bg-green-100 rounded-full peer-checked:bg-green-500 transition-colors">
                                                    <x-heroicon-o-check-circle class="w-5 h-5 text-green-600 peer-checked:text-white"/>
                                                </div>
                                            @elseif($key === App\Models\CourseAttendance::STATUS_BOLOS)
                                                <div class="p-2 bg-red-100 rounded-full peer-checked:bg-red-500 transition-colors">
                                                    <x-heroicon-o-x-circle class="w-5 h-5 text-red-600 peer-checked:text-white"/>
                                                </div>
                                            @else
                                                <div class="p-2 bg-yellow-100 rounded-full peer-checked:bg-yellow-500 transition-colors">
                                                    <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-yellow-600 peer-checked:text-white"/>
                                                </div>
                                            @endif
                                            
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $label }}</p>
                                                <p class="text-sm text-gray-500">
                                                    @if($key === App\Models\CourseAttendance::STATUS_MASUK)
                                                        Hadir mengikuti course
                                                    @elseif($key === App\Models\CourseAttendance::STATUS_BOLOS)
                                                        Tidak hadir tanpa keterangan
                                                    @else
                                                        Tidak hadir dengan keterangan
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <!-- Selection indicator -->
                                        <div class="absolute top-2 right-2 w-4 h-4 border-2 rounded-full border-gray-300 peer-checked:border-indigo-500 peer-checked:bg-indigo-500 flex items-center justify-center">
                                            <div class="w-2 h-2 bg-white rounded-full opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('status') 
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <x-heroicon-o-exclamation-circle class="w-4 h-4"/>
                                    {{ $message }}
                                </p> 
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Catatan (Opsional)
                            </label>
                            <textarea wire:model="notes" id="notes" rows="3" placeholder="Tambahkan catatan jika diperlukan..." 
                                      class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('notes') border-red-300 @enderror"></textarea>
                            @error('notes') 
                                <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
                                    <x-heroicon-o-exclamation-circle class="w-4 h-4"/>
                                    {{ $message }}
                                </p> 
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Maksimal 500 karakter</p>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4 border-t border-gray-200">
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-indigo-500 hover:bg-indigo-600 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-1">
                                <x-heroicon-o-check class="w-5 h-5"/>
                                Simpan Absensi
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <!-- No Attendance Available -->
                <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                    <div class="mb-4">
                        @if($enrolledCourses->count() === 0)
                            <x-heroicon-o-book-open class="mx-auto h-16 w-16 text-gray-400"/>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">Tidak Ada Course</h3>
                            <p class="mt-2 text-gray-500">Anda belum terdaftar di course manapun. Silakan daftar course terlebih dahulu.</p>
                        @elseif(!$this->canMarkAttendance && $this->availableCourses->count() === 0 && $todayAttendances->count() > 0)
                            <x-heroicon-o-check-badge class="mx-auto h-16 w-16 text-green-400"/>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">Absensi Sudah Lengkap</h3>
                            <p class="mt-2 text-gray-500">Anda sudah melakukan absensi untuk semua course hari ini.</p>
                        @else
                            <x-heroicon-o-clock class="mx-auto h-16 w-16 text-gray-400"/>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">Waktu Absensi Belum Tersedia</h3>
                            <p class="mt-2 text-gray-500">Absensi hanya dapat dilakukan pada jam 07:00 - 17:00.</p>
                            <p class="text-sm text-gray-400 mt-1">Waktu sekarang: {{ $currentTime->format('H:i') }}</p>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Info Box -->
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-xl p-6">
                <div class="flex items-start gap-3">
                    <x-heroicon-o-information-circle class="w-6 h-6 text-blue-500 mt-0.5 flex-shrink-0"/>
                    <div>
                        <h4 class="font-semibold text-blue-900 mb-2">Informasi Absensi</h4>
                        <ul class="text-sm text-blue-800 space-y-1">
                            <li>• Absensi hanya dapat dilakukan pada jam kerja (07:00 - 17:00)</li>
                            <li>• Setiap course hanya dapat diabsen satu kali per hari</li>
                            <li>• Pilih status sesuai dengan kondisi kehadiran Anda</li>
                            <li>• Catatan bersifat opsional, gunakan untuk informasi tambahan</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
