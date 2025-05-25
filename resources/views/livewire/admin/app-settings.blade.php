<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Configuraciones Generales de Votación
        </h2>
    </x-slot>

    <div class="py-4 px-2 md:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto">
            {{-- Área para mensajes Flash --}}
            @if (session()->has('message'))
                <div class="mb-6 {{ session('message_type', 'success') == 'success' ? 'bg-green-100 dark:bg-green-700 border-green-400 dark:border-green-600 text-green-700 dark:text-green-100' : 'bg-red-100 dark:bg-red-700 border-red-400 dark:border-red-600 text-red-700 dark:text-red-100' }} border px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">{{ session('message_type', 'success') == 'success' ? '¡Éxito!' : 'Error' }}</strong>
                    <span class="block sm:inline">{{ session('message') }}</span>
                </div>
            @endif

            <div class="bg-white dark:bg-zinc-700 shadow-lg rounded-lg p-6">
                <form wire:submit.prevent="saveSettings">
                    <div class="space-y-6">
                        {{-- Intervalo de Actualización de Resultados --}}
                        <div>
                            <label for="resultsRefreshInterval" class="block text-sm font-medium text-gray-700 dark:text-zinc-300">Intervalo de Actualización de Resultados (segundos)</label>
                            <input wire:model.lazy="resultsRefreshInterval" type="number" name="resultsRefreshInterval" id="resultsRefreshInterval"
                                   class="mt-1 block w-full rounded-md bg-white dark:bg-zinc-800 px-3 py-1.5 text-base text-gray-900 dark:text-gray-100 outline-1 -outline-offset-1 outline-gray-300 dark:outline-zinc-600 placeholder:text-gray-400 dark:placeholder:text-zinc-400 focus:outline-2 focus:-outline-offset-2 focus:outline-gray-400 dark:focus:outline-zinc-500 sm:text-sm/6"
                                   placeholder="Ej: 60">
                            <p class="mt-1 text-xs text-gray-500 dark:text-zinc-400">Cada cuántos segundos se intentará refrescar la vista de resultados parciales.</p>
                            @error('resultsRefreshInterval') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>

                        {{-- Umbral Mínimo de Votos para Mostrar Resultados --}}
                        <div>
                            <label for="resultsMinVotesThreshold" class="block text-sm font-medium text-gray-700 dark:text-zinc-300">Mínimo de Votos para Mostrar/Actualizar Resultados</label>
                            <input wire:model.lazy="resultsMinVotesThreshold" type="number" name="resultsMinVotesThreshold" id="resultsMinVotesThreshold"
                                   class="mt-1 block w-full rounded-md bg-white dark:bg-zinc-800 px-3 py-1.5 text-base text-gray-900 dark:text-gray-100 outline-1 -outline-offset-1 outline-gray-300 dark:outline-zinc-600 placeholder:text-gray-400 dark:placeholder:text-zinc-400 focus:outline-2 focus:-outline-offset-2 focus:outline-gray-400 dark:focus:outline-zinc-500 sm:text-sm/6"
                                   placeholder="Ej: 10">
                            <p class="mt-1 text-xs text-gray-500 dark:text-zinc-400">Número de votos que deben registrarse antes de que los resultados sean visibles o se actualicen, para proteger el anonimato.</p>
                            @error('resultsMinVotesThreshold') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Botón de Guardar --}}
                    <div class="mt-8 pt-5 border-t border-gray-200 dark:border-zinc-600">
                        <div class="flex justify-end">
                            <button type="submit"
                                    class="inline-flex justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Guardar Configuraciones
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>