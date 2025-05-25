<x-layouts.app.sidebar :title="$title ?? null">
    <flux:main>
        @if (isset($header))
            <header class="bg-white dark:bg-zinc-800 shadow text-center">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif
        {{ $slot }}
    </flux:main>
</x-layouts.app.sidebar>
