<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Gestión de Elecciones
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

            @if (session()->has('generated_link_message'))
                <div class="mb-4 p-4 border rounded-md bg-cyan-50 dark:bg-cyan-900/30 border-cyan-200 dark:border-cyan-700">
                    <p class="font-semibold text-cyan-800 dark:text-cyan-200">{{ session('generated_link_message') }}</p>
                    <p class="text-sm text-cyan-700 dark:text-cyan-300 mt-1 break-all">
                        <strong><code>{{ session('generated_link') }}</code></strong>
                    </p>
                    <p class="text-xs text-gray-500 dark:text-zinc-400 mt-2">
                        Este enlace es para la estación de votación. Al acceder desde un navegador "limpio" (sin sesión de admin), llevará a la pantalla de identificación del votante.
                    </p>
                </div>
            @endif

            <div class="bg-white dark:bg-zinc-700 shadow-lg rounded-lg overflow-hidden">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-zinc-600">
                    <div class="flex flex-col sm:flex-row justify-between items-center">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 sr-only"> 
                            {{-- El título ya está en el header del layout --}}
                            Elecciones
                        </h3>
                        {{-- Contenedor para botones --}}
                        <div class="flex-grow"> {{-- Opcional: para empujar el input de búsqueda si quieres --}}
                            <input wire:model.debounce.300ms.live="search" type="search"
                                   class="block w-full sm:w-2/5 rounded-md bg-white dark:bg-zinc-800 px-3 py-1.5 text-base text-gray-900 dark:text-gray-100 outline-1 -outline-offset-1 outline-gray-300 dark:outline-zinc-600 placeholder:text-gray-400 dark:placeholder:text-zinc-400 focus:outline-2 focus:-outline-offset-2 focus:outline-gray-400 dark:focus:outline-zinc-500 sm:text-sm/6"
                                   placeholder="Buscar por nombre o descripción...">
                        </div>
                        <div class="mt-3 sm:mt-0 sm:ml-3">
                            <a href="{{ route('elections.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Nueva Elección
                            </a>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-600">
                        <thead class="bg-gray-50 dark:bg-zinc-800">
                            <tr>
                                {{-- Nombre --}}
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer" wire:click="sortBy('name')">
                                    <div class="flex items-center">Nombre @if ($sortField === 'name') <x-sort-icon :direction="$sortDirection" /> @endif</div>
                                </th>
                                {{-- Fecha Inicio --}}
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer" wire:click="sortBy('start_time')">
                                    <div class="flex items-center">Inicio @if ($sortField === 'start_time') <x-sort-icon :direction="$sortDirection" /> @endif</div>
                                </th>
                                {{-- Fecha Fin --}}
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer" wire:click="sortBy('end_time')">
                                    <div class="flex items-center">Fin @if ($sortField === 'end_time') <x-sort-icon :direction="$sortDirection" /> @endif</div>
                                </th>
                                {{-- Estado --}}
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer" wire:click="sortBy('status')">
                                    <div class="flex items-center justify-center">Estado @if ($sortField === 'status') <x-sort-icon :direction="$sortDirection" /> @endif</div>
                                </th>
                                {{-- Creado --}}
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer" wire:click="sortBy('created_at')">
                                    <div class="flex items-center">Creado @if ($sortField === 'created_at') <x-sort-icon :direction="$sortDirection" /> @endif</div>
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-zinc-700 divide-y divide-gray-200 dark:divide-zinc-600">
                            @forelse ($elections as $election)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $election->name }}</div>
                                        @if($election->description)
                                        <div class="text-xs text-gray-500 dark:text-zinc-400 truncate w-64" title="{{ $election->description }}">{{ Str::limit($election->description, 80) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-zinc-200">
                                        {{ $election->start_time ? \Carbon\Carbon::parse($election->start_time)->format('d/m/Y H:i') : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-zinc-200">
                                        {{ $election->end_time ? \Carbon\Carbon::parse($election->end_time)->format('d/m/Y H:i') : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @switch($election->status)
                                                @case('scheduled') bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100 @break
                                                @case('active') bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100 @break
                                                @case('completed') bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-100 @break
                                                @case('archived') bg-gray-100 text-gray-800 dark:bg-zinc-600 dark:text-zinc-100 @break
                                                @default bg-gray-200 text-gray-700 dark:bg-zinc-500 dark:text-zinc-200
                                            @endswitch">
                                            {{ Str::ucfirst($election->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-zinc-200">
                                        {{ $election->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('elections.edit', $election) }}" 
                                           class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 p-1 rounded hover:bg-indigo-100 dark:hover:bg-zinc-600"
                                           title="Editar Elección">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 inline-block">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                            </svg>
                                            <span class="sr-only">Editar Elección</span>
                                        </a>
                                        @if(in_array($election->status, ['scheduled', 'active']))
                                            <button wire:click="showGenerateLinkModal({{ $election->id }})"
                                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 p-1 rounded hover:bg-blue-100 dark:hover:bg-zinc-600 inline-block align-middle ml-2"
                                                    title="Generar Enlace de Votación">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                                                </svg>
                                                <span class="sr-only">Generar Enlace</span>
                                            </button>
                                        @endif

                                        @if(in_array($election->status, ['active', 'completed'])) {{-- Solo para activas o completadas --}}
                                            <a href="{{ route('elections.results', $election) }}" 
                                            class="text-teal-600 dark:text-teal-400 hover:text-teal-700 dark:hover:text-teal-300 p-1 rounded hover:bg-teal-100 dark:hover:bg-zinc-600 inline-block align-middle ml-2"
                                            title="Ver Resultados">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A2.25 2.25 0 011.5 18.75v-5.625M3 13.125V3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v9.75M3 13.125H1.5M16.5 13.125h1.5m-4.5-1.5h2.25c.621 0 1.125.504 1.125 1.125v6.75c0 .621-.504 1.125-1.125 1.125h-2.25a2.25 2.25 0 01-2.25-2.25v-5.625c0-.621.504-1.125 1.125-1.125zM16.5 13.125V3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v9.75M16.5 13.125h1.5" />
                                                </svg>
                                                <span class="sr-only">Ver Resultados</span>
                                            </a>
                                        @endif
                                        @if ($election->status === 'active')
                                            <button wire:click="confirmEndElection({{ $election->id }})"
                                                    class="text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 p-1 rounded hover:bg-orange-100 dark:hover:bg-zinc-600 inline-block align-middle ml-2"
                                                    title="Finalizar Elección Manualmente">
                                                {{-- Icono de "stop circle" o similar de Heroicons --}}
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 9.563C9 9.252 9.252 9 9.563 9h4.874c.311 0 .563.252.563.563v4.874c0 .311-.252.563-.563.563H9.564A.562.562 0 019 14.437V9.564z" /> {{-- Un cuadrado dentro del círculo --}}
                                                </svg>
                                                <span class="sr-only">Finalizar Elección</span>
                                            </button>
                                        @endif
                                        {{-- Aquí podríamos añadir un botón para cambiar estado o eliminar --}}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500 dark:text-zinc-400">
                                        No hay elecciones registradas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($elections->hasPages())
                <div class="bg-white dark:bg-zinc-700 px-4 py-3 border-t border-gray-200 dark:border-zinc-600 sm:px-6">
                    {{ $elections->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    @if ($showEndElectionConfirmationModal)
        <div class="fixed z-20 inset-0 overflow-y-auto" aria-labelledby="modal-title-end-election" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 dark:bg-zinc-900 bg-opacity-75 dark:bg-opacity-80 transition-opacity" aria-hidden="true" wire:click="cancelEndElectionConfirmation"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-zinc-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-zinc-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-700 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600 dark:text-red-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title-end-election">Confirmar Finalización</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-600 dark:text-zinc-300">
                                        ¿Estás seguro de que quieres finalizar la elección "{{ $electionToEndName }}"? Esto cambiará su estado a "Completada" y ya no se podrán registrar más votos.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-zinc-800/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="endElection" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm dark:focus:ring-offset-zinc-800">
                            Sí, Finalizar
                        </button>
                        <button wire:click="cancelEndElectionConfirmation" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-zinc-600 shadow-sm px-4 py-2 bg-white dark:bg-zinc-700 text-base font-medium text-gray-700 dark:text-zinc-200 hover:bg-gray-50 dark:hover:bg-zinc-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm dark:focus:ring-offset-zinc-800">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>