<x-layouts.app.sidebar :title="$title ?? null">
    <flux:main class="py-12 min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100 dark:bg-gradient-to-br dark:from-indigo-900 dark:via-slate-900 dark:to-indigo-950">
        @if (!isset($slot))
            @yield('content')
        @else
            {{ $slot }}
        @endif
    </flux:main>
</x-layouts.app.sidebar>
