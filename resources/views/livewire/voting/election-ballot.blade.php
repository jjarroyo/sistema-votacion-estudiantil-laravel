<div class="min-h-screen bg-gray-100 dark:bg-zinc-900 flex flex-col items-center justify-center p-4">
    <div class="w-full max-w-2xl lg:max-w-4xl">
        {{-- Título de la Elección --}}
        <div class="text-center mb-8">
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-gray-100">
                {{ $election->name }}
            </h1>
            @if($election->description)
            <p class="mt-2 text-md text-gray-600 dark:text-zinc-400">{{ $election->description }}</p>
            @endif
        </div>

        {{-- Mensaje de Error General --}}
        @if($errorMessage)
            <div class="mb-6 p-4 rounded-md bg-red-50 dark:bg-red-800/30 border border-red-300 dark:border-red-600">
                <p class="text-sm text-red-700 dark:text-red-300">{{ $errorMessage }}</p>
            </div>
        @endif
        @if(session()->has('ballot_error')) {{-- Errores que causan redirección al montar --}}
            <div class="mb-6 p-4 rounded-md bg-red-50 dark:bg-red-800/30 border border-red-300 dark:border-red-600">
                <p class="text-sm text-red-700 dark:text-red-300">{{ session('ballot_error') }}</p>
                <div class="mt-4 text-center">
                     <a href="{{ route('voting.station.login', ['token' => $election->voting_session_token ?? 'default_token_if_error']) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Volver a Identificación
                    </a>
                </div>
            </div>
        @else

        {{-- Lista de Candidatos y Voto en Blanco --}}
        <div class="space-y-6">
            <h2 class="text-xl font-semibold text-gray-700 dark:text-zinc-300 text-center">Seleccione una opción:</h2>
            
            @if($candidates->isEmpty())
                <p class="text-center text-gray-700 dark:text-zinc-300 text-lg my-6">No hay candidatos registrados para esta elección.</p>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                {{-- Iterar sobre los candidatos --}}
                @foreach ($candidates as $candidate)
                    <div wire:click="selectOption('{{ $candidate->id }}')"
                         class="cursor-pointer p-4 border-2 rounded-lg transition-all duration-150 ease-in-out
                                {{ $selectedOption == $candidate->id ? 'border-indigo-600 dark:border-indigo-400 ring-2 ring-indigo-500 dark:ring-indigo-300 bg-indigo-50 dark:bg-indigo-900/30' : 'border-gray-300 dark:border-zinc-600 hover:border-indigo-400 dark:hover:border-indigo-500 hover:shadow-lg' }}
                                bg-white dark:bg-zinc-800 flex flex-col items-center text-center h-full"> {{-- h-full para igualar alturas si es necesario --}}
                        
                        @if($candidate->photo_path)
                            <img src="{{ Storage::url($candidate->photo_path) }}" alt="Foto de {{ $candidate->student->full_name ?? 'Candidato' }}" class="w-32 h-32 object-cover rounded-full mb-3 shadow-md">
                        @else
                            {{-- Placeholder para la foto del candidato --}}
                            <div class="w-32 h-32 rounded-full bg-gray-200 dark:bg-zinc-700 flex items-center justify-center mb-3 shadow-md">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 text-gray-400 dark:text-zinc-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                            </div>
                        @endif
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $candidate->student->full_name ?? 'Nombre no disponible' }}</h3>
                        @if($candidate->list_number)
                            <p class="text-sm text-gray-500 dark:text-zinc-400">N°: {{ $candidate->list_number }}</p>
                        @endif
                        @if($candidate->proposal)
                            <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1 flex-grow w-full truncate hover:whitespace-normal" title="{{ $candidate->proposal }}">{{ Str::limit($candidate->proposal, 50) }}</p>
                        @endif
                    </div>
                @endforeach

                {{-- Tarjeta para VOTO EN BLANCO --}}
                <div wire:click="selectOption('blank_vote')"
                     class="cursor-pointer p-4 border-2 rounded-lg transition-all duration-150 ease-in-out
                            {{ $selectedOption === 'blank_vote' ? 'border-indigo-600 dark:border-indigo-400 ring-2 ring-indigo-500 dark:ring-indigo-300 bg-indigo-50 dark:bg-indigo-900/30' : 'border-gray-300 dark:border-zinc-600 hover:border-indigo-400 dark:hover:border-indigo-500 hover:shadow-lg' }}
                            bg-white dark:bg-zinc-800 flex flex-col items-center justify-center text-center h-full"> {{-- justify-center para centrar verticalmente si el contenido es menos --}}
                    
                    {{-- Placeholder para "Voto en Blanco" - puedes usar un SVG o una imagen --}}
                    <div class="w-32 h-32 rounded-full bg-gray-100 dark:bg-zinc-700 border-2 border-dashed border-gray-300 dark:border-zinc-600 flex items-center justify-center mb-3 shadow-sm">
                        {{-- Icono que sugiere "vacío" o "neutral" --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 text-gray-400 dark:text-zinc-500">
                           <path stroke-linecap="round" stroke-linejoin="round" d="M12 9.75v6.75m0 0l-3-3m3 3l3-3m-8.25 6a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" /> {{-- Este es un cloud, podrías usar algo más neutral --}}
                           {{-- Alternativa: un cuadrado vacío --}}
                           {{-- <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 017.5 5.25h9a2.25 2.25 0 012.25 2.25v9a2.25 2.25 0 01-2.25 2.25h-9a2.25 2.25 0 01-2.25-2.25v-9z" /> --}}
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">VOTO EN BLANCO</h3>
                </div>
            </div>
        </div>
        {{-- Botón de Votar --}}
        <div class="mt-10 text-center">
            <button wire:click="confirmVote"
                    wire:loading.attr="disabled"
                    wire:target="confirmVote, submitVote"
                    @disabled(is_null($selectedOption))
                    class="px-8 py-3 border border-transparent text-xl font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 dark:focus:ring-offset-zinc-900">
                <span wire:loading.remove wire:target="confirmVote, submitVote">VOTAR</span>
                <span wire:loading wire:target="confirmVote, submitVote">Procesando...</span>
            </button>
        </div>
        @endif {{-- Cierre del @if(session()->has('ballot_error')) --}}


        {{-- Modal de Confirmación de Voto --}}
        @if ($showConfirmationModal)
        <div class="fixed z-30 inset-0 overflow-y-auto" aria-labelledby="confirmation-modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 dark:bg-zinc-900 bg-opacity-75 dark:bg-opacity-80 transition-opacity" aria-hidden="true" wire:click="cancelConfirmation"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-zinc-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-zinc-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 dark:bg-yellow-700 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                  </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="confirmation-modal-title">Confirmar Voto</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-600 dark:text-zinc-300">
                                        ¿Estás seguro de que deseas votar por: <strong class="text-indigo-700 dark:text-indigo-400">{{ $selectionMadeForConfirmation }}</strong>?
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1">Esta acción no se puede deshacer.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-zinc-800/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="submitVote" wire:loading.attr="disabled" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm dark:focus:ring-offset-zinc-800">
                            <span wire:loading.remove wire:target="submitVote">Sí, Votar</span>
                            <span wire:loading wire:target="submitVote">Registrando...</span>
                        </button>
                        <button wire:click="cancelConfirmation" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-zinc-600 shadow-sm px-4 py-2 bg-white dark:bg-zinc-700 text-base font-medium text-gray-700 dark:text-zinc-200 hover:bg-gray-50 dark:hover:bg-zinc-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm dark:focus:ring-offset-zinc-800">
                            No, Cambiar Selección
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>