
@if ($showImportModal)
<div class="fixed z-20 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        {{-- Background overlay --}}
        <div class="fixed inset-0 bg-gray-500 dark:bg-zinc-900 bg-opacity-25 dark:bg-opacity-10 transition-opacity" aria-hidden="true"></div>

        {{-- Modal panel --}}
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-zinc-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl md:max-w-3xl lg:max-w-4xl sm:w-full">
            
            {{-- Modal Header --}}
            <div class="bg-white dark:bg-zinc-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-200 dark:border-zinc-700">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 dark:bg-indigo-700 sm:mx-0 sm:h-10 sm:w-10">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 text-indigo-600 dark:text-indigo-300">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                            Importar Estudiantes desde Excel
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-zinc-400">
                            Sube un archivo .xlsx o .xls con los datos de los estudiantes.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Modal Body --}}
            <div class="px-4 py-5 sm:p-6 bg-gray-50 dark:bg-zinc-800/50">
                {{-- Section 1: Form --}}
                @if ($currentImportSection == 'form')
                <form wire:submit.prevent="startImportProcess">
                    <div>
                        <div class="flex justify-between items-center mb-2"> {{-- Contenedor para label y botón de plantilla --}}
                            <label for="importFileModal" class="block text-sm font-medium text-gray-700 dark:text-zinc-300">
                                Seleccionar archivo Excel (.xlsx, .xls)
                            </label>
                            <button wire:click="downloadStudentTemplate" type="button"
                                    class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 focus:outline-none focus:underline">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 inline-block mr-1">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /> {{-- Icono de descarga --}}
                                </svg>
                                Descargar Plantilla
                            </button>
                        </div>
                        
                        <div class="mt-1 flex items-center"> {{-- El mt-1 es para el input, el label y botón ya tienen su espacio --}}
                            <input type="file" wire:model="importFile" id="importFileModal" class="block w-full text-sm text-gray-500 dark:text-zinc-400
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-md file:border-0
                                file:text-sm file:font-semibold
                                file:bg-indigo-50 dark:file:bg-indigo-800 file:text-indigo-700 dark:file:text-indigo-200
                                hover:file:bg-indigo-100 dark:hover:file:bg-indigo-700
                                focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-zinc-800"
                                @disabled($currentImportSection !== 'form')>
                            <div wire:loading wire:target="importFile" class="ml-3 text-sm text-gray-500 dark:text-zinc-400">
                                Cargando archivo...
                            </div>
                        </div>
                        @error('importFile') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        <p class="mt-2 text-xs text-gray-500 dark:text-zinc-400">
                            Asegúrate que el archivo tenga columnas como: Identificador, Nombre Completo, Grado. (Como en la plantilla)
                        </p>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button type="button" wire:click="closeImportModal" class="mr-3 inline-flex justify-center py-2 px-4 border border-gray-300 dark:border-zinc-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-zinc-200 bg-white dark:bg-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-zinc-800">
                            Cancelar
                        </button>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                                wire:loading.attr="disabled" wire:target="importFile, startImportProcess">
                            <span wire:loading.remove wire:target="startImportProcess, importFile">Subir y Procesar</span>
                            <span wire:loading wire:target="startImportProcess, importFile">Procesando...
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                  </svg>
                            </span>
                        </button>
                    </div>
                </form>
                @endif

                {{-- Section 2: Progress --}}
                @if ($currentImportSection == 'progress')
                <div class="text-center py-10">
                    <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100">Procesando archivo...</h4>
                    <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Por favor, espera.</p>
                    <div class="mt-4 w-full bg-gray-200 dark:bg-zinc-700 rounded-full h-2.5">
                        <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ $importProgress }}%"></div>
                    </div>
                    <p class="mt-2 text-sm text-indigo-600 dark:text-indigo-400">{{ round($importProgress) }}% completado</p>
                     <div wire:loading class="mt-4">
                        <svg class="animate-spin mx-auto h-8 w-8 text-indigo-600 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                          </svg>
                    </div>
                </div>
                @endif

                {{-- Section 3: Results --}}
                @if ($currentImportSection == 'results')
                <div>
                    <div class="mb-4 p-4 border rounded-md bg-blue-50 dark:bg-blue-900/30 border-blue-200 dark:border-blue-700">
                        <h4 class="font-semibold text-blue-800 dark:text-blue-200">Resumen de la Importación</h4>
                        <ul class="list-disc list-inside text-sm text-blue-700 dark:text-blue-300 mt-1">
                            <li>Total de Filas Procesadas: {{ $importResults['summary']['total_rows'] }}</li>
                            <li>Estudiantes Insertados: {{ $importResults['summary']['inserted_count'] }}</li>
                            <li>Estudiantes Actualizados: {{ $importResults['summary']['updated_count'] }}</li>
                            <li>Errores Encontrados: {{ $importResults['summary']['error_count'] }}</li>
                        </ul>
                    </div>

                    {{-- Tabs --}}
                    <div class="mb-4 border-b border-gray-200 dark:border-zinc-700">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                            <button wire:click="setActiveResultTab('inserted')" type="button" class="{{ $activeResultTab === 'inserted' ? 'border-indigo-500 dark:border-indigo-400 text-indigo-600 dark:text-indigo-300' : 'border-transparent text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-200 hover:border-gray-300 dark:hover:border-zinc-500' }} whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">
                                Insertados ({{ count($importResults['inserted']) }})
                            </button>
                            <button wire:click="setActiveResultTab('updated')" type="button" class="{{ $activeResultTab === 'updated' ? 'border-indigo-500 dark:border-indigo-400 text-indigo-600 dark:text-indigo-300' : 'border-transparent text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-200 hover:border-gray-300 dark:hover:border-zinc-500' }} whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">
                                Actualizados ({{ count($importResults['updated']) }})
                            </button>
                            <button wire:click="setActiveResultTab('errors')" type="button" class="{{ $activeResultTab === 'errors' ? 'border-red-500 dark:border-red-400 text-red-600 dark:text-red-300' : 'border-transparent text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-200 hover:border-gray-300 dark:hover:border-zinc-500' }} whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">
                                Errores ({{ count($importResults['errors']) }})
                            </button>
                        </nav>
                    </div>

                    {{-- Tab Content (tablas idénticas a como estaban antes) --}}
                    <div class="max-h-96 overflow-y-auto">
                        @if ($activeResultTab === 'inserted' && !empty($importResults['inserted']))
                        {{-- Tabla de insertados --}}
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-600">
                            <thead class="bg-gray-100 dark:bg-zinc-700/50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase">Identificador</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase">Nombre</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase">Grado</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase">Detalles</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-zinc-700">
                                @foreach ($importResults['inserted'] as $item)
                                <tr>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-zinc-200">{{ $item['identifier'] }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-zinc-200">{{ $item['name'] }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-zinc-200">{{ $item['grade'] }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-green-600 dark:text-green-400">{{ $item['details'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @elseif ($activeResultTab === 'updated' && !empty($importResults['updated']))
                         {{-- Tabla de actualizados --}}
                         <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-600">
                            <thead class="bg-gray-100 dark:bg-zinc-700/50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase">Identificador</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase">Nombre</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase">Grado</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase">Detalles</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-zinc-700">
                                @foreach ($importResults['updated'] as $item)
                                <tr>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-zinc-200">{{ $item['identifier'] }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-zinc-200">{{ $item['name'] }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-zinc-200">{{ $item['grade'] }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-blue-600 dark:text-blue-400">{{ $item['details'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @elseif ($activeResultTab === 'errors' && !empty($importResults['errors']))
                         {{-- Tabla de errores --}}
                         <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-600">
                            <thead class="bg-gray-100 dark:bg-zinc-700/50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase">Fila #</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase">Identificador Provisto</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase">Error</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-zinc-700">
                                @foreach ($importResults['errors'] as $item)
                                <tr>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-zinc-200">{{ $item['row'] }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-zinc-200">{{ $item['identifier'] ?: 'N/A' }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-red-600 dark:text-red-400">{{ $item['error'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                            <p class="text-center text-gray-500 dark:text-zinc-400 py-4">No hay datos para mostrar en esta sección.</p>
                        @endif
                    </div>
                     <div class="mt-6 flex justify-end">
                        <button type="button" wire:click="closeImportModal" class="mr-3 inline-flex justify-center py-2 px-4 border border-gray-300 dark:border-zinc-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-zinc-200 bg-white dark:bg-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-zinc-800">
                            Cerrar
                        </button>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
@push('scripts')
@parent
<script>
    document.addEventListener('livewire:initialized', () => {
        window.addEventListener('trigger-next-chunk', event => {
            console.log('Evento del navegador: trigger-next-chunk recibido. Llamando a processNextChunk...');
            setTimeout(() => {
                @this.call('processNextChunk');
            }, 250); 
        });
    });
</script>
@endpush