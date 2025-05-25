<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Crear Nueva Elección
        </h2>
    </x-slot>

    <div class="py-4 px-2 md:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto bg-white dark:bg-zinc-700 shadow-lg rounded-lg p-6">
            <form wire:submit.prevent="saveElection">
                <div class="space-y-6">
                    {{-- Nombre de la Elección --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-zinc-300">Nombre de la Elección</label>
                        <input wire:model.lazy="name" type="text" name="name" id="name"
                               class="mt-1 block w-full rounded-md bg-white dark:bg-zinc-800 px-3 py-1.5 text-base text-gray-900 dark:text-gray-100 outline-1 -outline-offset-1 outline-gray-300 dark:outline-zinc-600 placeholder:text-gray-400 dark:placeholder:text-zinc-400 focus:outline-2 focus:-outline-offset-2 focus:outline-gray-400 dark:focus:outline-zinc-500 sm:text-sm/6"
                               placeholder="Ej: Elección Personero 2025">
                        @error('name') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- Descripción --}}
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-zinc-300">Descripción (Opcional)</label>
                        <textarea wire:model.lazy="description" name="description" id="description" rows="3"
                                  class="mt-1 block w-full rounded-md bg-white dark:bg-zinc-800 px-3 py-1.5 text-base text-gray-900 dark:text-gray-100 outline-1 -outline-offset-1 outline-gray-300 dark:outline-zinc-600 placeholder:text-gray-400 dark:placeholder:text-zinc-400 focus:outline-2 focus:-outline-offset-2 focus:outline-gray-400 dark:focus:outline-zinc-500 sm:text-sm/6"
                                  placeholder="Detalles adicionales sobre la elección"></textarea>
                        @error('description') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- Fecha y Hora de Inicio --}}
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-700 dark:text-zinc-300">Fecha y Hora de Inicio</label>
                        <input wire:model.lazy="start_time" type="datetime-local" name="start_time" id="start_time"
                               class="mt-1 block w-full rounded-md bg-white dark:bg-zinc-800 px-3 py-1.5 text-base text-gray-900 dark:text-gray-100 outline-1 -outline-offset-1 outline-gray-300 dark:outline-zinc-600 focus:outline-2 focus:-outline-offset-2 focus:outline-gray-400 dark:focus:outline-zinc-500 sm:text-sm/6">
                        @error('start_time') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- Fecha y Hora de Finalización --}}
                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-700 dark:text-zinc-300">Fecha y Hora de Finalización</label>
                        <input wire:model.lazy="end_time" type="datetime-local" name="end_time" id="end_time"
                               class="mt-1 block w-full rounded-md bg-white dark:bg-zinc-800 px-3 py-1.5 text-base text-gray-900 dark:text-gray-100 outline-1 -outline-offset-1 outline-gray-300 dark:outline-zinc-600 focus:outline-2 focus:-outline-offset-2 focus:outline-gray-400 dark:focus:outline-zinc-500 sm:text-sm/6">
                        @error('end_time') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- Estado --}}
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-zinc-300">Estado</label>
                        <select wire:model.lazy="status" id="status" name="status"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-zinc-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md bg-white dark:bg-zinc-800 text-gray-900 dark:text-gray-100">
                            @foreach($statusOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Botones de Acción --}}
                <div class="mt-8 pt-5 border-t border-gray-200 dark:border-zinc-600">
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('elections.index') }}"
                           class="px-4 py-2 border border-gray-300 dark:border-zinc-500 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-zinc-200 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancelar
                        </a>
                        <button type="submit"
                                class="inline-flex justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Guardar Elección
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>