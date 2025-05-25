<x-layouts.guest> {{-- O tu layout para la estación de votación --}}
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-100 dark:bg-zinc-900 text-center px-4">
        <div class="bg-white dark:bg-zinc-800 p-8 sm:p-12 rounded-lg shadow-xl">
            <svg class="w-16 h-16 sm:w-24 sm:h-24 text-green-500 dark:text-green-400 mx-auto mb-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-gray-100 mb-3">
                {{ $message }}
            </h1>
            <p class="text-gray-600 dark:text-zinc-400 mb-8">
                Tu participación es importante.
            </p>
            {{-- Este script redirigirá después de 5 segundos a la pantalla de identificación --}}
            {{-- Es útil para un cubículo de votación multi-usuario --}}
            @if($election_token && $election_token !== 'none')
            <script>
                setTimeout(function() {
                    window.location.href = "{{ route('voting.station.login', ['token' => $election_token]) }}";
                }, 5000); // 5000 milisegundos = 5 segundos
            </script>
            <p class="text-sm text-gray-500 dark:text-zinc-500">
                Serás redirigido a la pantalla de inicio en 5 segundos...
            </p>
            @else
             <a href="/" class="text-indigo-600 dark:text-indigo-400 hover:underline">Volver al inicio</a>
            @endif
        </div>
    </div>
</x-layouts.guest>>