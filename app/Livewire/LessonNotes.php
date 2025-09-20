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

    public $noteTitle;
    public $noteContent;

    public function createNote()
    {
        logger()->info('Creating note', ['title' => $this->noteTitle, 'content' => $this->noteContent]);

        $this->validate([
            'noteTitle' => 'required|string|max:255',
            'noteContent' => 'required|string',
        ]);
        logger()->info('Note created successfully', ['title' => $this->noteTitle, 'content' => $this->noteContent]);

        $this->lesson->notes()->create([
            'title' => $this->noteTitle,
            'note' => $this->noteContent,
            'user_id' => auth()->id(),
            'lesson_id' => $this->lesson->id,
        ]);
        logger()->info('Note created in database', ['lesson_id' => $this->lesson->id]);

        $this->reset(['noteTitle', 'noteContent']);
    }

    public function deleteNote($noteId)
    {
        $note = $this->lesson->notes()->where('id', $noteId)->first();

        if ($note) {
            $note->delete();
        }
    }

    public function render()
    {
        return view('livewire.lesson-notes', [
            'notes' => $this->lesson->notes,
        ]);
    }
}
