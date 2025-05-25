<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Editar Estudiante: <span class="text-indigo-600 dark:text-indigo-400">{{ $student->full_name }}</span>
        </h2>
    </x-slot>

    <div class="py-4 px-2 md:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto bg-white dark:bg-zinc-700 shadow-lg rounded-lg p-6">
            <form wire:submit.prevent="updateStudent">
                <div class="space-y-6">
                    {{-- Identificador del Estudiante --}}
                    <div>
                        <label for="student_identifier" class="block text-sm font-medium text-gray-700 dark:text-zinc-300">Identificador del Estudiante</label>
                        <input wire:model.lazy="student_identifier" type="text" name="student_identifier" id="student_identifier"
                               class="mt-1 block w-full rounded-md bg-white dark:bg-zinc-800 px-3 py-1.5 text-base text-gray-900 dark:text-gray-100 outline-1 -outline-offset-1 outline-gray-300 dark:outline-zinc-600 placeholder:text-gray-400 dark:placeholder:text-zinc-400 focus:outline-2 focus:-outline-offset-2 focus:outline-gray-400 dark:focus:outline-zinc-500 sm:text-sm/6">
                        @error('student_identifier') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- Nombre Completo --}}
                    <div>
                        <label for="full_name" class="block text-sm font-medium text-gray-700 dark:text-zinc-300">Nombre Completo</label>
                        <input wire:model.lazy="full_name" type="text" name="full_name" id="full_name"
                               class="mt-1 block w-full rounded-md bg-white dark:bg-zinc-800 px-3 py-1.5 text-base text-gray-900 dark:text-gray-100 outline-1 -outline-offset-1 outline-gray-300 dark:outline-zinc-600 placeholder:text-gray-400 dark:placeholder:text-zinc-400 focus:outline-2 focus:-outline-offset-2 focus:outline-gray-400 dark:focus:outline-zinc-500 sm:text-sm/6">
                        @error('full_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- Grado --}}
                    <div>
                        <label for="grade" class="block text-sm font-medium text-gray-700 dark:text-zinc-300">Grado</label>
                        <input wire:model.lazy="grade" type="text" name="grade" id="grade"
                               class="mt-1 block w-full rounded-md bg-white dark:bg-zinc-800 px-3 py-1.5 text-base text-gray-900 dark:text-gray-100 outline-1 -outline-offset-1 outline-gray-300 dark:outline-zinc-600 placeholder:text-gray-400 dark:placeholder:text-zinc-400 focus:outline-2 focus:-outline-offset-2 focus:outline-gray-400 dark:focus:outline-zinc-500 sm:text-sm/6">
                        @error('grade') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- Estado Activo/Inactivo --}}
                    <div class="mt-4">
                        <label for="is_active" class="block text-sm font-medium text-gray-700 dark:text-zinc-300">Estado</label>
                        <select wire:model="is_active" id="is_active" name="is_active"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-zinc-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md bg-white dark:bg-zinc-800 text-gray-900 dark:text-gray-100">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                        @error('is_active') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                </div>

                {{-- Botones de Acción --}}
                <div class="mt-8 pt-5 border-t border-gray-200 dark:border-zinc-600">
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('students.index') }}"
                           class="px-4 py-2 border border-gray-300 dark:border-zinc-500 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-zinc-200 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancelar
                        </a>
                        <button type="submit"
                                class="inline-flex justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Actualizar Estudiante
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>