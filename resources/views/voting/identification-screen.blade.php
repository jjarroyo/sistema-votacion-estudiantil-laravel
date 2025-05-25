<x-layouts.guest>    
    @if (session()->has('ballot_error'))
        <div class="mb-6 p-4 rounded-md bg-red-100 dark:bg-red-700 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-100" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    {{-- Icono de error (opcional) --}}
                    <svg class="h-5 w-5 " xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">
                        {{ session('ballot_error') }}
                    </p>
                </div>
            </div>
        </div>
    @endif
    @livewire('voting.voter-identification', ['electionId' => $election->id, 'electionName' => $election->name])

</x-layouts-guest>