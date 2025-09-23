<?php

namespace App\Observers;

use App\Models\CourseRecordSession;
use Illuminate\Support\Facades\Log;

class CourseRecordSessionObserver
{
    /**
     * Handle the CourseRecordSession "created" event.
     */
    public function created(CourseRecordSession $courseRecordSession): void
    {
        Log::info('CourseRecordSession created', ['id' => $courseRecordSession->id]);
    }

    /**
     * Handle the CourseRecordSession "updated" event.
     */
    public function updated(CourseRecordSession $courseRecordSession): void
    {
        Log::info('CourseRecordSession updated', ['id' => $courseRecordSession->id]);
    }

    /**
     * Handle the CourseRecordSession "deleted" event.
     */
    public function deleted(CourseRecordSession $courseRecordSession): void
    {
        Log::info('CourseRecordSession deleted', ['id' => $courseRecordSession->id]);
    }

    /**
     * Handle the CourseRecordSession "restored" event.
     */
    public function restored(CourseRecordSession $courseRecordSession): void
    {
        Log::info('CourseRecordSession restored', ['id' => $courseRecordSession->id]);
    }

    /**
     * Handle the CourseRecordSession "force deleted" event.
     */
    public function forceDeleted(CourseRecordSession $courseRecordSession): void
    {
        Log::info('CourseRecordSession force deleted', ['id' => $courseRecordSession->id]);
    }
}
