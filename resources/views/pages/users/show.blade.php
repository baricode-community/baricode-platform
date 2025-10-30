<x-layouts.app :title="__('Profil Pengguna')">
    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg overflow-hidden">
        <div class="flex flex-col md:flex-row items-center p-8">
            <div class="flex-shrink-0 mb-4 md:mb-0 md:mr-8">
                <img class="w-32 h-32 rounded-full object-cover border-4 border-gray-200 dark:border-gray-700"
                    src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}"
                    alt="{{ $user->name }}">
            </div>
            <div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ $user->name }}</h2>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    {{ $user->about ?? '-' }}
                </p>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    <span class="font-semibold">Email:</span> {{ $user->email }}<br>
                    <span class="font-semibold">Bergabung Sejak:</span> {{ $user->created_at->format('d M Y') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Daftar Meet yang Telah Diikuti --}}
    <div class="mt-8">
        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Meet yang Diikuti</h3>
        @if($user->meets && $user->meets->count())
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($user->meets as $meet)
                    <li class="py-4 flex items-center justify-between">
                        <div>
                            <a href="{{ route('meets.show', $meet) }}" class="text-lg font-medium text-blue-600 dark:text-blue-400 hover:underline">
                                {{ $meet->title }}
                            </a>
                        </div>
                        <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded dark:bg-green-900 dark:text-green-200">
                            {{ ucfirst($meet->status) }}
                        </span>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="text-gray-500 dark:text-gray-400">
                Belum pernah mengikuti meet apapun.
            </div>
        @endif
    </div>
</x-layouts.app>
