<?php

namespace App\Livewire;

use Livewire\Component;

class LessonNotes extends Component
{
    public $lesson;

    public function mount($lesson)
    {
        $this->lesson = $lesson;
    }

    public function render()
    {
        return view('livewire.lesson-notes', [
            'notes' => $this->lesson->notes,
        ]);
    }
}
