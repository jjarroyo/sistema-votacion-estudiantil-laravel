<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Gestión de Candidatos
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
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center space-y-3 md:space-y-0">
                        {{-- Filtros --}}
                        <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3 w-full md:w-auto">
                            <input wire:model.debounce.300ms.live="searchStudent" type="text"
                                   class="block w-full sm:w-auto rounded-md bg-white dark:bg-zinc-800 px-3 py-1.5 text-base text-gray-900 dark:text-gray-100 outline-1 -outline-offset-1 outline-gray-300 dark:outline-zinc-600 placeholder:text-gray-400 dark:placeholder:text-zinc-400 focus:outline-2 focus:-outline-offset-2 focus:outline-gray-400 dark:focus:outline-zinc-500 sm:text-sm/6"
                                   placeholder="Buscar por estudiante...">
                            
                            <select wire:model="selectedElectionId"
                                    class="block w-full sm:w-auto rounded-md bg-white dark:bg-zinc-800 px-3 py-1.5 text-base text-gray-900 dark:text-gray-100 outline-1 -outline-offset-1 outline-gray-300 dark:outline-zinc-600 focus:outline-2 focus:-outline-offset-2 focus:outline-gray-400 dark:focus:outline-zinc-500 sm:text-sm/6">
                                <option value="">Todas las Elecciones</option>
                                @foreach($elections as $election)
                                    <option value="{{ $election->id }}">{{ $election->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Botón de Crear --}}
                        <div class="w-full md:w-auto md:text-right">
                            <a href="{{ route('candidates.create') }}" class="inline-flex items-center justify-center w-full md:w-auto px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Registrar Candidato
                            </a>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-600">
                        <thead class="bg-gray-50 dark:bg-zinc-800">
                            <tr>
                                {{-- Nombre Estudiante --}}
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer" wire:click="sortBy('student_name')">
                                    <div class="flex items-center">Estudiante @if ($sortField === 'student_name_placeholder') <x-sort-icon :direction="$sortDirection" /> @endif</div>
                                </th>
                                {{-- Elección --}}
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer" wire:click="sortBy('election_name')">
                                    <div class="flex items-center">Elección @if ($sortField === 'election_name_placeholder') <x-sort-icon :direction="$sortDirection" /> @endif</div>
                                </th>
                                {{-- Número de Lista --}}
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer" wire:click="sortBy('list_number')">
                                    <div class="flex items-center">N° Lista @if ($sortField === 'list_number') <x-sort-icon :direction="$sortDirection" /> @endif</div>
                                </th>
                                {{-- Propuesta --}}
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase tracking-wider">
                                    Propuesta
                                </th>
                                {{-- Registrado --}}
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer" wire:click="sortBy('created_at')">
                                    <div class="flex items-center">Registrado @if ($sortField === 'created_at') <x-sort-icon :direction="$sortDirection" /> @endif</div>
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-zinc-700 divide-y divide-gray-200 dark:divide-zinc-600">
                            @forelse ($candidates as $candidate)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $candidate->student->full_name ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500 dark:text-zinc-400">{{ $candidate->student->student_identifier ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-700 dark:text-zinc-200">{{ $candidate->election->name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-zinc-200">
                                        {{ $candidate->list_number ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-zinc-200">
                                        <span title="{{ $candidate->proposal }}">{{ Str::limit($candidate->proposal, 50) ?? '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-zinc-200">
                                        {{ $candidate->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('candidates.edit', $candidate) }}" 
                                           class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 p-1 rounded hover:bg-indigo-100 dark:hover:bg-zinc-600"
                                           title="Editar Candidatura">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 inline-block">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                            </svg>
                                            <span class="sr-only">Editar Candidatura</span>
                                        </a>
                                         <button wire:click="deleteCandidate({{ $candidate->id }})"
                                                onclick="confirm('¿Estás seguro de que quieres eliminar esta candidatura? Esta acción no se puede deshacer.') || event.stopImmediatePropagation()"
                                                class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 p-1 rounded hover:bg-red-100 dark:hover:bg-zinc-600 inline-block align-middle ml-2"
                                                title="Eliminar Candidatura">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12.56 0c.342.052.682.107 1.022.166m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                            <span class="sr-only">Eliminar Candidatura</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500 dark:text-zinc-400">
                                        No hay candidatos registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($candidates->hasPages())
                <div class="bg-white dark:bg-zinc-700 px-4 py-3 border-t border-gray-200 dark:border-zinc-600 sm:px-6">
                    {{ $candidates->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
