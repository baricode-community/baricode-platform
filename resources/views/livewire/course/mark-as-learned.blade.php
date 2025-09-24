<?php

use Livewire\Volt\Component;
use App\Services\CourseService;
use App\Models\LessonProgress;

new class extends Component {
    public LessonProgress $lessonProgress;
    public $courseRecordId;
    public $isLoading = false;

    public function mount(LessonProgress $lessonProgress, $courseRecordId)
    {
        if (app()->environment('local')) {
            logger()->debug('Mounting mark-as-learned component for lesson: ' . $lessonProgress->title . ', courseRecordId: ' . $courseRecordId, [
                'lesson_id' => $lessonProgress->id,
                'course_record_id' => $courseRecordId,
                'user_id' => auth()->id(),
                'context' => [
                    'lessonProgress' => $lessonProgress->toArray(),
                ],
            ]);
        } else {
            logger()->info('Mounting mark-as-learned component for lesson: ' . $lessonProgress->title . ', courseRecordId: ' . $courseRecordId);
        }
        $this->lessonProgress = $lessonProgress;
        $this->courseRecordId = $courseRecordId;
    }

    public function markAsLearned()
{
    $this->isLoading = true;
    if (!$this->lessonProgress->moduleProgress->courseEnrollment->isWaktunyaBelajar()) {
        logger()->info('Saat ini bukan waktunya belajar. Silakan cek jadwal Anda.');
        flash()->warning('Saat ini bukan waktunya belajar. Silakan cek jadwal Anda.');
        return;
    }

    try {
        $courseService = app(CourseService::class);
        $userId = auth()->id();

        logger()->info('Marking lesson as learned: ' . $this->lessonProgress->lessonDetail->title);

        $courseService->markLessonAsLearned($this->lessonProgress->lessonDetail, $userId);

        // Update the lesson record status
        $this->lessonProgress->update(['is_completed' => true]);

        // Refresh the component state
        $this->lessonProgress->refresh();

        // Show success message
        session()->flash('success', 'Pelajaran telah ditandai sebagai sudah dipelajari!');

        // Dispatch browser event for any additional handling
        $this->dispatch('lesson-marked-as-learned', lessonId: $this->lessonProgress->lessonDetail->id);
    } catch (\Exception $e) {
        logger()->error('Error marking lesson as learned: ' . $e->getMessage());
        session()->flash('error', 'Terjadi kesalahan saat menandai pelajaran. Silakan coba lagi.');
    } finally {
        $this->isLoading = false;
    }
}
}; ?>

<div>

    @if ($lessonProgress->is_completed)
        <button type="button" disabled
            class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-gray-400 to-gray-600 text-white rounded-lg shadow font-semibold opacity-70 cursor-not-allowed">
            <span class="mr-2 text-lg">✅</span> {{ __('Sudah Dipelajari') }}
        </button>
    @else
        <button type="button" wire:click="markAsLearned" wire:loading.attr="disabled"
            wire:loading.class="opacity-50 cursor-not-allowed" {{ $isLoading ? 'disabled' : '' }}
            class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-green-400 to-green-600 text-white rounded-lg shadow hover:scale-105 hover:from-green-500 hover:to-green-700 transition-all font-semibold disabled:hover:scale-100">
            <span class="mr-2 text-lg" wire:loading.remove wire:target="markAsLearned">✔️</span>
            <span class="mr-2 text-lg" wire:loading wire:target="markAsLearned">
                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
            </span>
            <span wire:loading.remove wire:target="markAsLearned">{{ __('Tandai sebagai Sudah Dipelajari') }}</span>
            <span wire:loading wire:target="markAsLearned">{{ __('Memproses...') }}</span>
        </button>
    @endif
</div>
