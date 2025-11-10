<?php

use Livewire\Volt\Component;
use Livewire\Volt\Attributes\Layout;
use App\Models\Projects\Kanboard;
use App\Models\Projects\KanboardCard;
use App\Models\Projects\KanboardTodo;

new #[Layout('layouts.app')] class extends Component {
    public Kanboard $kanboard;
    public $showAddCardModal = false;
    public $showAddTodoModal = false;
    public $showCardDetailModal = false;
    public $showAssignTodoModal = false;
    public $selectedCard = null;
    public $selectedTodo = null;

    // Form properties for new card
    public $newCardTitle = '';
    public $newCardDescription = '';
    public $newCardStatus = 'todo';
    public $newCardColor = '#3B82F6';

    // Form properties for new todo
    public $newTodoTitle = '';
    public $newTodoDescription = '';
    public $newTodoPriority = 'medium';
    public $newTodoDueDate = '';
    public $newTodoAssignedTo = '';
    public $newTodoAssignedUsers = [];
    public $selectedCardForTodo = null;

    public function mount(): void
    {
        $this->authorize('view', $this->kanboard);
    }

    public function with(): array
    {
        return [
            'todoCards' => $this->kanboard
                ->todoCards()
                ->with(['creator', 'assignee', 'todos.assignedUsers'])
                ->get(),
            'doingCards' => $this->kanboard
                ->doingCards()
                ->with(['creator', 'assignee', 'todos.assignedUsers'])
                ->get(),
            'doneCards' => $this->kanboard
                ->doneCards()
                ->with(['creator', 'assignee', 'todos.assignedUsers'])
                ->get(),
            'kanboardUsers' => $this->kanboard->users()->wherePivot('status', 'active')->get(),
            'isManager' => $this->kanboard->isManager(auth()->user()),
            'isAdmin' => $this->kanboard->canManage(auth()->user()),
        ];
    }

    public function addCard(): void
    {
        $this->authorize('view', $this->kanboard);

        $this->validate([
            'newCardTitle' => 'required|string|max:255',
            'newCardDescription' => 'nullable|string|max:1000',
            'newCardStatus' => 'required|in:todo,doing,done',
            'newCardColor' => 'required|string',
        ]);

        $maxOrder = KanboardCard::where('kanboard_id', $this->kanboard->id)->where('status', $this->newCardStatus)->max('order') ?? 0;

        KanboardCard::create([
            'kanboard_id' => $this->kanboard->id,
            'title' => $this->newCardTitle,
            'description' => $this->newCardDescription,
            'status' => $this->newCardStatus,
            'color' => $this->newCardColor,
            'order' => $maxOrder + 1,
            'created_by' => auth()->id(),
        ]);

        $this->reset(['newCardTitle', 'newCardDescription', 'newCardStatus', 'newCardColor', 'showAddCardModal']);
        $this->newCardColor = '#3B82F6';

        session()->flash('message', 'Card berhasil ditambahkan!');
    }

    public function addTodo(): void
    {
        $this->authorize('view', $this->kanboard);

        if (!$this->selectedCardForTodo) {
            $this->addError('selectedCardForTodo', 'Pilih card terlebih dahulu');
            return;
        }

        $this->validate([
            'newTodoTitle' => 'required|string|max:255',
            'newTodoDescription' => 'nullable|string|max:1000',
            'newTodoPriority' => 'required|in:low,medium,high',
            'newTodoDueDate' => 'nullable|date|after:today',
            'newTodoAssignedTo' => 'nullable|exists:users,id',
            'newTodoAssignedUsers' => 'nullable|array',
            'newTodoAssignedUsers.*' => 'exists:users,id',
        ]);

        $card = KanboardCard::findOrFail($this->selectedCardForTodo);

        $maxOrder = KanboardTodo::where('kanboard_card_id', $card->id)->max('order') ?? 0;

        $todo = KanboardTodo::create([
            'kanboard_card_id' => $card->id,
            'title' => $this->newTodoTitle,
            'description' => $this->newTodoDescription,
            'priority' => $this->newTodoPriority,
            'due_date' => $this->newTodoDueDate ? \Carbon\Carbon::parse($this->newTodoDueDate) : null,
            'order' => $maxOrder + 1,
            'created_by' => auth()->id(),
            'assigned_to' => $this->newTodoAssignedTo ?: null,
        ]);

        // Assign multiple users if specified
        if (!empty($this->newTodoAssignedUsers)) {
            $validUserIds = $this->kanboard->users()->where('status', 'active')->whereIn('user_id', $this->newTodoAssignedUsers)->pluck('user_id')->toArray();

            $todo->assignUsers($validUserIds, auth()->user());
        }

        $this->reset(['newTodoTitle', 'newTodoDescription', 'newTodoPriority', 'newTodoDueDate', 'newTodoAssignedTo', 'newTodoAssignedUsers', 'selectedCardForTodo', 'showAddTodoModal']);

        session()->flash('message', 'Todo berhasil ditambahkan!');
    }

    public function toggleTodo($todoId): void
    {
        $todo = KanboardTodo::findOrFail($todoId);

        // Check if user can complete this todo
        if (!$todo->canBeCompletedBy(auth()->user())) {
            session()->flash('error', 'Anda tidak memiliki izin untuk mengubah status todo ini.');
            return;
        }

        if ($todo->is_completed) {
            $todo->markAsIncomplete();
            session()->flash('message', 'Todo berhasil diubah menjadi belum selesai.');
        } else {
            $todo->markAsCompleted(auth()->user(), 'Diselesaikan via kanboard');
            session()->flash('message', 'Todo berhasil diselesaikan!');
        }
    }

    public function assignTodo($todoId, $userId): void
    {
        $this->authorize('view', $this->kanboard);

        if (!$this->kanboard->canManage(auth()->user())) {
            session()->flash('error', 'Anda tidak memiliki izin untuk assign todo.');
            return;
        }

        $todo = KanboardTodo::findOrFail($todoId);

        // Verify the user is part of the kanboard
        if ($userId && !$this->kanboard->users()->where('user_id', $userId)->where('status', 'active')->exists()) {
            session()->flash('error', 'User tidak terdaftar dalam kanboard ini.');
            return;
        }

        if ($userId) {
            $user = \App\Models\User\User::findOrFail($userId);

            if ($todo->isAssignedTo($user)) {
                $todo->unassignUser($user);
                session()->flash('message', "Assignment {$user->name} berhasil dihapus!");
            } else {
                $todo->assignUser($user, auth()->user());
                session()->flash('message', "Todo berhasil di-assign ke {$user->name}!");
            }

            // Force refresh the relationship to see changes
            $todo->refresh();
            $todo->load('assignedUsers');
        }

        // Refresh the selected todo with fresh assignment data
        $this->selectedTodo = KanboardTodo::with(['assignee', 'assignedUsers'])->findOrFail($todoId);

        // Force Livewire to re-render the component
        $this->dispatch('$refresh');
    }

    public function assignMultipleUsers($todoId, array $userIds): void
    {
        $this->authorize('view', $this->kanboard);

        if (!$this->kanboard->canManage(auth()->user())) {
            session()->flash('error', 'Anda tidak memiliki izin untuk assign todo.');
            return;
        }

        $todo = KanboardTodo::findOrFail($todoId);

        // Verify all users are part of the kanboard
        $validUserIds = $this->kanboard->users()->where('status', 'active')->whereIn('user_id', $userIds)->pluck('user_id')->toArray();

        $todo->assignUsers($validUserIds, auth()->user());

        $assignedCount = count($validUserIds);
        session()->flash('message', "Todo berhasil di-assign ke {$assignedCount} user!");

        // Refresh the selected todo with fresh assignment data
        $this->selectedTodo = KanboardTodo::with(['assignee', 'assignedUsers'])->findOrFail($todoId);
    }

    public function openAssignTodoModal($todoId): void
    {
        $this->selectedTodo = KanboardTodo::with(['assignee', 'assignedUsers'])->findOrFail($todoId);
        $this->showAssignTodoModal = true;
    }

    public function closeAssignTodoModal(): void
    {
        $this->selectedTodo = null;
        $this->showAssignTodoModal = false;
    }

    public function moveCard($cardId, $newStatus): void
    {
        $this->authorize('view', $this->kanboard);

        $card = KanboardCard::findOrFail($cardId);

        if ($card->kanboard_id !== $this->kanboard->id) {
            return;
        }

        if ($newStatus === 'done') {
            $this->authorize('update', $this->kanboard);
        }

        $maxOrder = KanboardCard::where('kanboard_id', $this->kanboard->id)->where('status', $newStatus)->max('order') ?? 0;

        $card->update([
            'status' => $newStatus,
            'order' => $maxOrder + 1,
        ]);

        session()->flash('message', 'Card berhasil dipindahkan!');
    }

    public function openCardDetail($cardId): void
    {
        $this->selectedCard = KanboardCard::with(['todos.assignedUsers', 'todos.assignee', 'creator', 'assignee'])->findOrFail($cardId);
        $this->showCardDetailModal = true;
    }

    public function closeCardDetail(): void
    {
        $this->selectedCard = null;
        $this->showCardDetailModal = false;
    }

    public function openAddCardModal($status = 'todo'): void
    {
        $this->newCardStatus = $status;
        $this->showAddCardModal = true;
    }

    public function closeAddCardModal(): void
    {
        $this->showAddCardModal = false;
        $this->reset(['newCardTitle', 'newCardDescription', 'newCardStatus', 'newCardColor']);
        $this->newCardColor = '#3B82F6';
    }

    public function openAddTodoModal($cardId = null): void
    {
        $this->selectedCardForTodo = $cardId;
        $this->showAddTodoModal = true;
    }

    public function closeAddTodoModal(): void
    {
        $this->showAddTodoModal = false;
        $this->reset(['newTodoTitle', 'newTodoDescription', 'newTodoPriority', 'newTodoDueDate', 'newTodoAssignedTo', 'newTodoAssignedUsers', 'selectedCardForTodo']);
    }
};

?>

<div class="py-6">
    <div class="">
        <!-- Header -->
        <header class="flex my-3 justify-between items-center">
            <div>
                <div class="flex items-center space-x-3 overflow-x-auto">
                    <a href="{{ route('kanboard.index') }}" wire:navigate
                        class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $kanboard->title }}</h1>
                    <span
                        class="text-xs px-2 py-1 rounded-full {{ $kanboard->visibility === 'public' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }}">
                        {{ $kanboard->visibility === 'public' ? 'Publik' : 'Privat' }}
                    </span>
                </div>
                @if ($kanboard->description)
                    <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $kanboard->description }}</p>
                @endif
            </div>

            <div class="flex items-center space-x-2">
                @if ($isManager || $isAdmin)
                    <button wire:click="openAddCardModal"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded flex items-center gap-1 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Card
                    </button>
                    <button wire:click="openAddTodoModal"
                        class="bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded flex items-center gap-1 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        Todo
                    </button>
                @endif
                @if ($isAdmin)
                    <a href="{{ route('kanboard.settings', $kanboard) }}" wire:navigate
                        class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </a>
                @endif
            </div>
        </header>

        <!-- Flash Messages -->
        @if (session('message'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('message') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Kanban Board -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Todo Column -->
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white flex items-center">
                        <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                        Todo ({{ $todoCards->count() }})
                    </h3>
                    @if ($isManager || $isAdmin)
                        <button wire:click="openAddCardModal('todo')"
                            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4"></path>
                            </svg>
                        </button>
                    @endif
                </div>

                <div class="space-y-3" id="todo-column">
                    @foreach ($todoCards as $card)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 cursor-pointer hover:shadow-md transition-shadow"
                            wire:click="openCardDetail({{ $card->id }})"
                            style="border-left: 4px solid {{ $card->color }}">
                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">{{ $card->title }}</h4>

                            @if ($card->description)
                                <p class="text-gray-600 dark:text-gray-400 text-sm mb-3 line-clamp-2">
                                    {{ $card->description }}</p>
                            @endif

                            <!-- Todo Progress -->
                            @if ($card->todos->count() > 0)
                                <div class="mb-3">
                                    <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                                        <span>{{ $card->todos->where('is_completed', true)->count() }}/{{ $card->todos->count() }}
                                            todos</span>
                                        <span>{{ $card->todos->count() > 0 ? round(($card->todos->where('is_completed', true)->count() / $card->todos->count()) * 100) : 0 }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full"
                                            style="width: {{ $card->todos->count() > 0 ? ($card->todos->where('is_completed', true)->count() / $card->todos->count()) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="flex justify-between items-center text-xs text-gray-500 dark:text-gray-400">
                                <span>{{ $card->creator->name }}</span>
                                @if ($card->due_date)
                                    <span class="{{ $card->isOverdue() ? 'text-red-500' : '' }}">
                                        {{ $card->due_date->format('M j') }}
                                    </span>
                                @endif
                            </div>

                            @if ($isManager || $isAdmin)
                                <div class="mt-3 flex justify-end space-x-2">
                                    <button wire:click.stop="moveCard({{ $card->id }}, 'doing')"
                                        class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded hover:bg-yellow-200 transition-colors">
                                        → Doing
                                    </button>
                                    @if ($isAdmin)
                                        <button wire:click.stop="moveCard({{ $card->id }}, 'done')"
                                            class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded hover:bg-green-200 transition-colors">
                                            → Done
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Doing Column -->
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white flex items-center">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                        Doing ({{ $doingCards->count() }})
                    </h3>
                    @if ($isManager)
                        <button wire:click="openAddCardModal('doing')"
                            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4"></path>
                            </svg>
                        </button>
                    @endif
                </div>

                <div class="space-y-3" id="doing-column">
                    @foreach ($doingCards as $card)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 cursor-pointer hover:shadow-md transition-shadow"
                            wire:click="openCardDetail({{ $card->id }})"
                            style="border-left: 4px solid {{ $card->color }}">
                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">{{ $card->title }}</h4>

                            @if ($card->description)
                                <p class="text-gray-600 dark:text-gray-400 text-sm mb-3 line-clamp-2">
                                    {{ $card->description }}</p>
                            @endif

                            <!-- Todo Progress -->
                            @if ($card->todos->count() > 0)
                                <div class="mb-3">
                                    <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                                        <span>{{ $card->todos->where('is_completed', true)->count() }}/{{ $card->todos->count() }}
                                            todos</span>
                                        <span>{{ $card->todos->count() > 0 ? round(($card->todos->where('is_completed', true)->count() / $card->todos->count()) * 100) : 0 }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full"
                                            style="width: {{ $card->todos->count() > 0 ? ($card->todos->where('is_completed', true)->count() / $card->todos->count()) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="flex justify-between items-center text-xs text-gray-500 dark:text-gray-400">
                                <span>{{ $card->creator->name }}</span>
                                @if ($card->due_date)
                                    <span class="{{ $card->isOverdue() ? 'text-red-500' : '' }}">
                                        {{ $card->due_date->format('M j') }}
                                    </span>
                                @endif
                            </div>

                            @if ($isManager || $isAdmin)
                                <div class="mt-3 flex justify-end space-x-2">
                                    <button wire:click.stop="moveCard({{ $card->id }}, 'todo')"
                                        class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded hover:bg-red-200 transition-colors">
                                        ← Todo
                                    </button>
                                    @if ($isAdmin)
                                        <button wire:click.stop="moveCard({{ $card->id }}, 'done')"
                                            class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded hover:bg-green-200 transition-colors">
                                            → Done
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Done Column -->
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                        Done ({{ $doneCards->count() }})
                    </h3>
                    @if ($isManager)
                        <button wire:click="openAddCardModal('done')"
                            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4"></path>
                            </svg>
                        </button>
                    @endif
                </div>

                <div class="space-y-3" id="done-column">
                    @foreach ($doneCards as $card)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 cursor-pointer hover:shadow-md transition-shadow opacity-75"
                            wire:click="openCardDetail({{ $card->id }})"
                            style="border-left: 4px solid {{ $card->color }}">
                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">{{ $card->title }}</h4>

                            @if ($card->description)
                                <p class="text-gray-600 dark:text-gray-400 text-sm mb-3 line-clamp-2">
                                    {{ $card->description }}</p>
                            @endif

                            <!-- Todo Progress -->
                            @if ($card->todos->count() > 0)
                                <div class="mb-3">
                                    <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                                        <span>{{ $card->todos->where('is_completed', true)->count() }}/{{ $card->todos->count() }}
                                            todos</span>
                                        <span>{{ $card->todos->count() > 0 ? round(($card->todos->where('is_completed', true)->count() / $card->todos->count()) * 100) : 0 }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full"
                                            style="width: {{ $card->todos->count() > 0 ? ($card->todos->where('is_completed', true)->count() / $card->todos->count()) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="flex justify-between items-center text-xs text-gray-500 dark:text-gray-400">
                                <span>{{ $card->creator->name }}</span>
                                <span class="text-green-600">✓ Selesai</span>
                            </div>

                            @if ($isAdmin)
                                <div class="mt-3 flex justify-end space-x-2">
                                    <button wire:click.stop="moveCard({{ $card->id }}, 'todo')"
                                        class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded hover:bg-red-200 transition-colors">
                                        ← Todo
                                    </button>
                                    <button wire:click.stop="moveCard({{ $card->id }}, 'doing')"
                                        class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded hover:bg-yellow-200 transition-colors">
                                        ← Doing
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Add Card Modal -->
    @if ($showAddCardModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md mx-4">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tambah Card Baru</h3>
                        <button wire:click="closeAddCardModal"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit="addCard">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Judul <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="newCardTitle"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="Masukkan judul card">
                            @error('newCardTitle')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Deskripsi
                            </label>
                            <textarea wire:model="newCardDescription" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="Deskripsi card (opsional)"></textarea>
                            @error('newCardDescription')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Status
                            </label>
                            <select wire:model="newCardStatus"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="todo">Todo</option>
                                <option value="doing">Doing</option>
                                <option value="done">Done</option>
                            </select>
                            @error('newCardStatus')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Warna
                            </label>
                            <div class="flex space-x-2">
                                @foreach (['#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899'] as $color)
                                    <button type="button" wire:click="$set('newCardColor', '{{ $color }}')"
                                        class="w-8 h-8 rounded-full border-2 {{ $newCardColor === $color ? 'border-gray-900 dark:border-white' : 'border-gray-300 dark:border-gray-600' }}"
                                        style="background-color: {{ $color }}"></button>
                                @endforeach
                            </div>
                            @error('newCardColor')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button type="button" wire:click="closeAddCardModal"
                                class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                Tambah Card
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Add Todo Modal -->
    @if ($showAddTodoModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[51]">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-3xl mx-4">
                <div class="p-8">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tambah Todo Baru</h3>
                        <button wire:click="closeAddTodoModal"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit="addTodo">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Pilih Card <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model="selectedCardForTodo"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                        <option value="">Pilih Card</option>
                                        @foreach ($todoCards->concat($doingCards) as $card)
                                            <option value="{{ $card->id }}">{{ $card->title }}
                                                ({{ ucfirst($card->status) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('selectedCardForTodo')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Judul Todo <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" wire:model="newTodoTitle"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                        placeholder="Masukkan judul todo">
                                    @error('newTodoTitle')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Deskripsi
                                    </label>
                                    <textarea wire:model="newTodoDescription" rows="3"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                        placeholder="Deskripsi todo (opsional)"></textarea>
                                    @error('newTodoDescription')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Prioritas
                                    </label>
                                    <select wire:model="newTodoPriority"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                        <option value="low">Rendah</option>
                                        <option value="medium">Sedang</option>
                                        <option value="high">Tinggi</option>
                                    </select>
                                    @error('newTodoPriority')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Assign ke Multiple Users (Baru)
                                    </label>
                                    <div
                                        class="space-y-2 max-h-32 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-md p-2">
                                        @foreach ($kanboardUsers as $user)
                                            <label class="flex items-center space-x-2">
                                                <input type="checkbox" wire:model="newTodoAssignedUsers"
                                                    value="{{ $user->id }}"
                                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                                <span
                                                    class="text-sm text-gray-900 dark:text-white">{{ $user->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('newTodoAssignedUsers')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                    @if (count($newTodoAssignedUsers) > 0)
                                        <p class="text-xs text-blue-600 mt-1">{{ count($newTodoAssignedUsers) }} user
                                            dipilih
                                        </p>
                                    @endif
                                </div>

                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Deadline
                                    </label>
                                    <input type="date" wire:model="newTodoDueDate"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                    @error('newTodoDueDate')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" wire:click="closeAddTodoModal"
                                class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                                Tambah Todo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Card Detail Modal -->
    @if ($showCardDetailModal && $selectedCard)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl overflow-y-auto w-full max-w-2xl max-h-[90vh]">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $selectedCard->title }}
                        </h3>
                        <button wire:click="closeCardDetail"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    @if ($selectedCard->description)
                        <div class="mb-4">
                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">Deskripsi</h4>
                            <p class="text-gray-600 dark:text-gray-400">{{ $selectedCard->description }}</p>
                        </div>
                    @endif

                    <div class="mb-4">
                        <h4 class="font-medium text-gray-900 dark:text-white mb-2">Informasi</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Status:</span>
                                <span
                                    class="ml-2 px-2 py-1 rounded text-xs {{ $selectedCard->status === 'todo' ? 'bg-red-100 text-red-800' : ($selectedCard->status === 'doing' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                    {{ ucfirst($selectedCard->status) }}
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Dibuat oleh:</span>
                                <span class="ml-2">{{ $selectedCard->creator->name }}</span>
                            </div>
                            @if ($selectedCard->assignee)
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Ditugaskan ke:</span>
                                    <span class="ml-2">{{ $selectedCard->assignee->name }}</span>
                                </div>
                            @endif
                            @if ($selectedCard->due_date)
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Deadline:</span>
                                    <span class="ml-2 {{ $selectedCard->isOverdue() ? 'text-red-500' : '' }}">
                                        {{ $selectedCard->due_date->format('d M Y') }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if ($selectedCard->todos->count() > 0)
                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="font-medium text-gray-900 dark:text-white">
                                    Todos
                                    ({{ $selectedCard->todos->where('is_completed', true)->count() }}/{{ $selectedCard->todos->count() }})
                                </h4>
                                @if ($isManager || $isAdmin)
                                    <button wire:click="openAddTodoModal({{ $selectedCard->id }})"
                                        class="text-sm bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition-colors">
                                        Tambah Todo
                                    </button>
                                @endif
                            </div>

                            <div class="space-y-2">
                                @foreach ($selectedCard->todos as $todo)
                                    <div class="border border-gray-300 dark:border-gray-600 rounded-md {{ $todo->is_completed ? 'bg-gray-100 dark:bg-gray-700' : 'bg-white dark:bg-gray-800' }}">
                                        <div class="flex items-center space-x-3 p-3">
                                        <input type="checkbox" {{ $todo->is_completed ? 'checked' : '' }}
                                            @if ($todo->canBeCompletedBy(auth()->user())) wire:click="toggleTodo({{ $todo->id }})"
                                            @else
                                            disabled @endif
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <div class="flex-1">
                                            <h5
                                                class="font-medium text-gray-900 dark:text-white {{ $todo->is_completed ? 'line-through opacity-75' : '' }}">
                                                {{ $todo->title }}
                                            </h5>
                                            @if ($todo->description)
                                                <p
                                                    class="text-sm text-gray-600 dark:text-gray-400 {{ $todo->is_completed ? 'line-through opacity-75' : '' }}">
                                                    {{ $todo->description }}
                                                </p>
                                            @endif
                                            <div
                                                class="flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                <span
                                                    class="px-2 py-1 rounded {{ $todo->priority === 'high' ? 'bg-red-100 text-red-800' : ($todo->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                                    {{ $todo->priority === 'high' ? 'Tinggi' : ($todo->priority === 'medium' ? 'Sedang' : 'Rendah') }}
                                                </span>
                                                @if ($todo->due_date)
                                                    <span class="{{ $todo->isOverdue() ? 'text-red-500' : '' }}">
                                                        Deadline: {{ $todo->due_date->format('d M Y') }}
                                                    </span>
                                                @endif

                                                <!-- Display assigned users -->
                                                @if ($todo->assignedUsers->count() > 0)
                                                    <div class="flex items-center space-x-1">
                                                        <span class="text-gray-400">Assigned:</span>
                                                        <div class="flex space-x-1">
                                                            @foreach ($todo->assignedUsers->take(3) as $assignedUser)
                                                                <span
                                                                    class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">
                                                                    {{ $assignedUser->name }}
                                                                </span>
                                                            @endforeach
                                                            @if ($todo->assignedUsers->count() > 3)
                                                                <span
                                                                    class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs">
                                                                    +{{ $todo->assignedUsers->count() - 3 }} lainnya
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @elseif($todo->assignee)
                                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                                        {{ $todo->assignee->name }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">Belum di-assign</span>
                                                @endif
                                            </div>
                                        </div>

                                        @if ($isManager)
                                            <button wire:click="openAssignTodoModal({{ $todo->id }})"
                                                class="text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400"
                                                title="Assign User">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                    </path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                    <div class="mx-3">
                                        <livewire:todo-messaging :todo="$todo" />
                                    </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="font-medium text-gray-900 dark:text-white">Todos</h4>
                                @if ($isManager)
                                    <button wire:click="openAddTodoModal({{ $selectedCard->id }})"
                                        class="text-sm bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition-colors">
                                        Tambah Todo
                                    </button>
                                @endif
                            </div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">Belum ada todos untuk card ini.</p>
                        </div>
                    @endif

                    <div class="flex justify-end">
                        <button wire:click="closeCardDetail"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Assign Todo Modal -->
    @if ($showAssignTodoModal && $selectedTodo)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md mx-4">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Assign Todo</h3>
                        <button wire:click="closeAssignTodoModal"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="mb-4">
                        <h4 class="font-medium text-gray-900 dark:text-white mb-2">Todo: {{ $selectedTodo->title }}
                        </h4>
                        @if ($selectedTodo->description)
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-3">{{ $selectedTodo->description }}
                            </p>
                        @endif

                        <div class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                            <span class="font-medium">Saat ini di-assign ke:</span>
                            @if ($selectedTodo->assignedUsers->count() > 0)
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach ($selectedTodo->assignedUsers as $assignedUser)
                                        <span
                                            class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs flex items-center space-x-1">
                                            {{ $assignedUser->name }}
                                            <button
                                                wire:click="assignTodo({{ $selectedTodo->id }}, {{ $assignedUser->id }})"
                                                class="ml-1 text-blue-600 hover:text-blue-800"
                                                title="Hapus assignment">
                                                ×
                                            </button>
                                        </span>
                                    @endforeach
                                </div>
                            @elseif($selectedTodo->assignee)
                                <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                                    {{ $selectedTodo->assignee->name }} (Legacy)
                                </span>
                            @else
                                <span class="ml-2 text-gray-400">Belum di-assign</span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            Kelola Assignment (Klik untuk assign/unassign):
                        </label>

                        <div class="space-y-2 max-h-60 overflow-y-auto">
                            <!-- Options untuk setiap user -->
                            @foreach ($kanboardUsers as $user)
                                @php
                                    $isAssigned =
                                        $selectedTodo->assignedUsers->contains('id', $user->id) ||
                                        $selectedTodo->assigned_to == $user->id;
                                @endphp
                                <button wire:click="assignTodo({{ $selectedTodo->id }}, {{ $user->id }})"
                                    class="w-full text-left p-3 rounded-lg border {{ $isAssigned ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700' }} transition-colors">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="w-8 h-8 {{ $isAssigned ? 'bg-blue-500' : 'bg-gray-400' }} rounded-full flex items-center justify-center">
                                                <span class="text-white text-sm font-medium">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $user->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}
                                            </p>
                                        </div>
                                        <div class="ml-auto flex items-center space-x-2">
                                            @if ($isAssigned)
                                                <span class="text-xs text-blue-600 font-medium">Assigned</span>
                                                <svg class="w-5 h-5 text-blue-500" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                            @else
                                                <span class="text-xs text-gray-400">Klik untuk assign</span>
                                            @endif
                                        </div>
                                    </div>
                                </button>
                            @endforeach
                        </div>

                        <!-- Summary -->
                        @if ($selectedTodo->assignedUsers->count() > 0)
                            <div class="mt-3 p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <p class="text-sm text-blue-800 dark:text-blue-200">
                                    <span class="font-medium">{{ $selectedTodo->assignedUsers->count() }} user</span>
                                    saat ini di-assign ke todo ini
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-end">
                        <button wire:click="closeAssignTodoModal"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
