<div class="bg-white rounded-xl shadow-md p-6">
    <ul class="space-y-3">
        @forelse($notes as $note)
            <li class="border border-gray-200 rounded-lg p-4">
                <h3 class="font-semibold text-lg mb-2">{{ $note->title }}</h3>
                <p class="text-gray-700 dark:text-gray-300">{{ $note->note }}</p>
                <span class="text-xs text-gray-400">{{ $note->created_at->format('d M Y, H:i') }}</span>
            </li>
        @empty
            <li class="text-gray-500">No notes available for this lesson.</li>
        @endforelse
    </ul>
</div>
