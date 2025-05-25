 <div>   
     <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Gestión de Estudiantes
        </h2>
    </x-slot>
   <div class="py-4 px-2 md:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            {{-- Área para mensajes Flash --}}
            @if (session()->has('message'))
                <div class="mb-4 {{ session('message_type', 'success') == 'success' ? 'bg-green-100 dark:bg-green-700 border-green-400 dark:border-green-600 text-green-700 dark:text-green-100' : 'bg-red-100 dark:bg-red-700 border-red-400 dark:border-red-600 text-red-700 dark:text-red-100' }} border px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">{{ session('message_type', 'success') == 'success' ? '¡Éxito!' : 'Error' }}</strong>
                    <span class="block sm:inline">{{ session('message') }}</span>
                </div>
            @endif

            <div class="bg-white dark:bg-zinc-700 shadow-lg rounded-lg overflow-hidden">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-zinc-600">
                    <div class="flex flex-col sm:flex-row justify-between items-center">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                           
                        </h3>
                        <div class="mt-3 sm:mt-0 sm:ml-3 flex space-x-3"> {{-- Contenedor para los botones --}}
                            {{-- Botón/Enlace para importar desde Excel --}}
                            <button wire:click="openImportModal" type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-zinc-500 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-zinc-200 bg-white dark:bg-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                title="Importar Estudiantes desde Excel">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2 -ml-1">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                </svg>
                                Importar Excel
                            </button>
                            {{-- Enlace para crear nuevo estudiante --}}
                            <a href="{{ route('students.create') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            title="Añadir Nuevo Estudiante Manualmente">
                                {{-- Podrías añadir un icono de "+" aquí también si quisieras --}}
                                {{-- <svg class="w-5 h-5 mr-2 -ml-1" ...> <path ... /> </svg> --}}
                                Añadir Estudiante
                            </a>
                        </div>
                    </div>
                    <div class="mt-4">
                        <input  wire:model.debounce.300ms.live="search" type="search"
                            class="block w-full sm:w-1/3 rounded-md bg-white dark:bg-zinc-800 px-3 py-1.5 text-base text-gray-900 dark:text-gray-100 outline-1 -outline-offset-1 outline-gray-300 dark:outline-zinc-600 placeholder:text-gray-400 dark:placeholder:text-zinc-400 focus:outline-2 focus:-outline-offset-2 focus:outline-gray-400 dark:focus:outline-zinc-500 sm:text-sm/6"
                            placeholder="Buscar estudiantes...">
                    </div>
                </div>
                <div class="px-4"> {{-- Eliminamos border-t border-gray-200 dark:border-zinc-600 si el card-header ya tiene borde inferior --}}
                    <div class="overflow-y-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-600">
                            <thead class="bg-gray-50 dark:bg-zinc-800">
                                <tr>
                                    {{-- Columna Identificador --}}
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer"
                                        wire:click="sortBy('student_identifier')">
                                        <div class="flex items-center">
                                            Identificador
                                            @if ($sortField === 'student_identifier')
                                                <span class="ml-1">
                                                    @if ($sortDirection === 'asc')
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                                            <path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v10.5a.75.75 0 01-1.5 0V3.75A.75.75 0 0110 3z" clip-rule="evenodd" />
                                                            <path fill-rule="evenodd" d="M6.22 6.22a.75.75 0 011.06 0l2.25 2.25L11.78 6.22a.75.75 0 011.06 1.06l-3 3a.75.75 0 01-1.06 0l-3-3a.75.75 0 010-1.06z" clip-rule="evenodd" /> {{-- Flecha Arriba --}}
                                                        </svg>
                                                    @else
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                                            <path fill-rule="evenodd" d="M10 17a.75.75 0 01-.75-.75V5.75a.75.75 0 011.5 0v10.5A.75.75 0 0110 17z" clip-rule="evenodd" />
                                                            <path fill-rule="evenodd" d="M13.78 13.78a.75.75 0 01-1.06 0l-2.25-2.25L8.22 13.78a.75.75 0 01-1.06-1.06l3-3a.75.75 0 011.06 0l3 3a.75.75 0 010 1.06z" clip-rule="evenodd" /> {{-- Flecha Abajo --}}
                                                        </svg>
                                                    @endif
                                                </span>
                                            @endif
                                        </div>
                                    </th>

                                    {{-- Columna Nombre Completo --}}
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer"
                                        wire:click="sortBy('full_name')">
                                        <div class="flex items-center">
                                            Nombre Completo
                                            @if ($sortField === 'full_name')
                                                <span class="ml-1">
                                                    @if ($sortDirection === 'asc')
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v10.5a.75.75 0 01-1.5 0V3.75A.75.75 0 0110 3z" clip-rule="evenodd" /><path fill-rule="evenodd" d="M6.22 6.22a.75.75 0 011.06 0l2.25 2.25L11.78 6.22a.75.75 0 011.06 1.06l-3 3a.75.75 0 01-1.06 0l-3-3a.75.75 0 010-1.06z" clip-rule="evenodd" /></svg>
                                                    @else
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M10 17a.75.75 0 01-.75-.75V5.75a.75.75 0 011.5 0v10.5A.75.75 0 0110 17z" clip-rule="evenodd" /><path fill-rule="evenodd" d="M13.78 13.78a.75.75 0 01-1.06 0l-2.25-2.25L8.22 13.78a.75.75 0 01-1.06-1.06l3-3a.75.75 0 011.06 0l3 3a.75.75 0 010 1.06z" clip-rule="evenodd" /></svg>
                                                    @endif
                                                </span>
                                            @endif
                                        </div>
                                    </th>

                                    {{-- Columna Grado --}}
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer"
                                        wire:click="sortBy('grade')">
                                        <div class="flex items-center">
                                            Grado
                                            @if ($sortField === 'grade')
                                                <span class="ml-1">
                                                    @if ($sortDirection === 'asc')
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v10.5a.75.75 0 01-1.5 0V3.75A.75.75 0 0110 3z" clip-rule="evenodd" /><path fill-rule="evenodd" d="M6.22 6.22a.75.75 0 011.06 0l2.25 2.25L11.78 6.22a.75.75 0 011.06 1.06l-3 3a.75.75 0 01-1.06 0l-3-3a.75.75 0 010-1.06z" clip-rule="evenodd" /></svg>
                                                    @else
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M10 17a.75.75 0 01-.75-.75V5.75a.75.75 0 011.5 0v10.5A.75.75 0 0110 17z" clip-rule="evenodd" /><path fill-rule="evenodd" d="M13.78 13.78a.75.75 0 01-1.06 0l-2.25-2.25L8.22 13.78a.75.75 0 01-1.06-1.06l3-3a.75.75 0 011.06 0l3 3a.75.75 0 010 1.06z" clip-rule="evenodd" /></svg>
                                                    @endif
                                                </span>
                                            @endif
                                        </div>
                                    </th>

                                    {{-- Columna Estado (is_active) --}}
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer"
                                        wire:click="sortBy('is_active')">
                                        <div class="flex items-center justify-center"> {{-- justify-center para centrar el contenido --}}
                                            Estado
                                            @if ($sortField === 'is_active')
                                                <span class="ml-1">
                                                    @if ($sortDirection === 'asc')
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v10.5a.75.75 0 01-1.5 0V3.75A.75.75 0 0110 3z" clip-rule="evenodd" /><path fill-rule="evenodd" d="M6.22 6.22a.75.75 0 011.06 0l2.25 2.25L11.78 6.22a.75.75 0 011.06 1.06l-3 3a.75.75 0 01-1.06 0l-3-3a.75.75 0 010-1.06z" clip-rule="evenodd" /></svg>
                                                    @else
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M10 17a.75.75 0 01-.75-.75V5.75a.75.75 0 011.5 0v10.5A.75.75 0 0110 17z" clip-rule="evenodd" /><path fill-rule="evenodd" d="M13.78 13.78a.75.75 0 01-1.06 0l-2.25-2.25L8.22 13.78a.75.75 0 01-1.06-1.06l3-3a.75.75 0 011.06 0l3 3a.75.75 0 010 1.06z" clip-rule="evenodd" /></svg>
                                                    @endif
                                                </span>
                                            @endif
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-zinc-700 divide-y divide-gray-200 dark:divide-zinc-600">
                                @forelse ($students as $student)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $student->student_identifier }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">{{ $student->full_name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">{{ $student->grade }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <button wire:click="toggleStudentStatus({{ $student->id }})"
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        {{ $student->is_active 
                                                            ? 'bg-green-100 text-green-800 hover:bg-green-200 dark:bg-green-700 dark:text-green-100 dark:hover:bg-green-600' 
                                                            : 'bg-red-100 text-red-800 hover:bg-red-200 dark:bg-red-700 dark:text-red-100 dark:hover:bg-red-600' }}">
                                                {{ $student->is_active ? 'Activo' : 'Inactivo' }}
                                            </button>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('students.edit', $student) }}" 
                                            class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 p-1 rounded hover:bg-indigo-100 dark:hover:bg-zinc-600"
                                            title="Editar Estudiante"> {{-- El title es útil para el hover en escritorio --}}
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 inline-block">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                                </svg>
                                                <span class="sr-only">Editar Estudiante</span> {{-- Para accesibilidad --}}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500 dark:text-zinc-400">
                                            No hay estudiantes registrados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($students->hasPages())
                <div class="bg-white dark:bg-zinc-700 px-4 py-3 border-t border-gray-200 dark:border-zinc-600 sm:px-6">
                    {{ $students->links() }} {{-- Livewire's Tailwind pagination should adapt if properly configured or you can publish and customize it --}}
                </div>
                @endif
            </div>
        </div>    
        @include('livewire.admin.students._import-modal')
    </div>
 </div>   
 

