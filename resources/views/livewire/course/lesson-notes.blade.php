<?php

namespace App\Http\Livewire;

use Livewire\Volt\Component;
use App\Models\Course\CourseModuleLesson;

new class extends Component {
    public $lesson;
    public $noteTitle = '';
    public $noteContent = '';
    public $editingNoteId = null;

    protected $rules = [
        'noteTitle' => 'required|string|max:255',
        'noteContent' => 'required|string',
    ];

    public function mount(CourseModuleLesson $courseModuleLesson)
    {
        $this->lesson = $courseModuleLesson;
    }

    public function createNote()
    {
        $this->validate();

        $this->lesson->userNotes()->create([
            'title' => $this->noteTitle,
            'note' => $this->noteContent,
            'user_id' => auth()->id(),
            'lesson_id' => $this->lesson->id,
        ]);

        $this->reset(['noteTitle', 'noteContent']);
    }

    public function editNote($noteId)
    {
        $note = $this->lesson->userNotes()->where('id', $noteId)->first();
        if ($note) {
            $this->editingNoteId = $note->id;
            $this->noteTitle = $note->title;
            $this->noteContent = $note->note;
        }
    }

    public function updateNote()
    {
        $this->validate();

        $note = $this->lesson->userNotes()->where('id', $this->editingNoteId)->first();
        if ($note) {
            $note->update([
                'title' => $this->noteTitle,
                'note' => $this->noteContent,
            ]);
        }

        $this->reset(['noteTitle', 'noteContent', 'editingNoteId']);
    }

    public function cancelEdit()
    {
        $this->reset(['noteTitle', 'noteContent', 'editingNoteId']);
    }

    public function deleteNote($noteId)
    {
        $note = $this->lesson->userNotes()->where('id', $noteId)->first();

        if ($note) {
            $note->delete();
        }
    }

    public function getNotesProperty()
    {
        return $this->lesson->userNotes;
    }
};
?>

<!-- livewire.lesson-notes.blade.php -->
<div class="bg-white rounded-xl shadow-md p-6 mb-6">
    <form wire:submit.prevent="{{ $editingNoteId ? 'updateNote' : 'createNote' }}">
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Judul</label>
            <input type="text" wire:model.defer="noteTitle"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring" placeholder="Judul catatan">
            @error('noteTitle')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Catatan</label>
            <textarea wire:model.defer="noteContent" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring"
                rows="3" placeholder="Tulis catatan Anda di sini..."></textarea>
            @error('noteContent')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>
        <div class="flex space-x-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                {{ $editingNoteId ? 'Update Catatan' : 'Tambah Catatan' }}
            </button>
            @if ($editingNoteId)
                <button type="button" wire:click="cancelEdit"
                    class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">
                    Batal
                </button>
            @endif
        </div>
    </form>

    <div class="bg-white rounded-xl shadow-md p-6">
        <ul class="space-y-3">
            @forelse($this->lesson->userNotes as $note)
                <li class="border border-gray-200 rounded-lg p-4 flex justify-between items-start">
                    <div>
                        <h3 class="font-semibold text-lg mb-2">{{ $note->title }}</h3>
                        <p class="text-gray-700 dark:text-gray-300">{{ $note->note }}</p>
                        <span class="text-xs text-gray-400">{{ $note->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="flex flex-col space-y-1 ml-4">
                        <button wire:click="editNote({{ $note->id }})"
                            class="text-blue-500 hover:text-blue-700 text-sm">
                            Edit
                        </button>
                        <button wire:click="deleteNote({{ $note->id }})"
                            onclick="return confirm('Apakah Anda yakin ingin menghapus catatan ini?')"
                            class="text-red-500 hover:text-red-700 text-sm">
                            Hapus
                        </button>

                    </div>
                </li>
            @empty
                <li class="text-gray-500">Belum ada catatan untuk pelajaran ini.</li>
            @endforelse
        </ul>
    </div>
</div>
