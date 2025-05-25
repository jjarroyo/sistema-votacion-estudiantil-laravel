<div class="min-h-screen flex flex-col items-center justify-center bg-gray-100 dark:bg-zinc-900 px-4">
    <div class="w-full max-w-md p-8 space-y-6 bg-white dark:bg-zinc-800 shadow-xl rounded-lg">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                {{ $electionName }}
            </h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-zinc-400">
                Por favor, ingrese su identificador para votar.
            </p>
        </div>

        <form wire:submit.prevent="verifyStudent" class="space-y-6">
            <div>
                <label for="student_identifier_input" class="sr-only">Identificador de Estudiante</label>
                <input wire:model.defer="student_identifier_input" type="text" id="student_identifier_input"
                       class="appearance-none rounded-md relative block w-full px-4 py-3 border border-gray-300 dark:border-zinc-600 placeholder-gray-500 dark:placeholder-zinc-400 text-gray-900 dark:text-gray-100 bg-white dark:bg-zinc-700 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 text-2xl sm:text-3xl text-center"
                       placeholder="Su Identificador"
                       autofocus>
                @error('student_identifier_input') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- Aquí iría el teclado numérico en pantalla (JavaScript) - Por ahora se omite --}}
            {{-- <div class="grid grid-cols-3 gap-2 my-4"> ... botones del teclado ... </div> --}}


            @if($errorMessage)
                <div class="p-3 rounded-md bg-red-50 dark:bg-red-800/30 border border-red-300 dark:border-red-600">
                    <p class="text-sm text-red-700 dark:text-red-300">{{ $errorMessage }}</p>
                </div>
            @endif
            @if($successMessage) {{-- Aunque aquí no usamos successMessage antes de redirigir --}}
                <div class="p-3 rounded-md bg-green-50 dark:bg-green-800/30">
                    <p class="text-sm text-green-700 dark:text-green-300">{{ $successMessage }}</p>
                </div>
            @endif

            <div>
                <button type="submit"
                        wire:loading.attr="disabled"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-lg font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-zinc-800">
                    <span wire:loading wire:target="verifyStudent" class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-indigo-300 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                    <span wire:loading.remove wire:target="verifyStudent">Ingresar / Consultar</span>
                    <span wire:loading wire:target="verifyStudent">Verificando...</span>
                </button>
            </div>
        </form>
         <p class="mt-4 text-center text-xs text-gray-500 dark:text-zinc-500">
            Utilice el teclado numérico si está disponible o el teclado de su dispositivo.
        </p>
    </div>
</div>