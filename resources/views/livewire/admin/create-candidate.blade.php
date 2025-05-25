@section("styles")
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link
        href="https://cdn.jsdelivr.net/gh/erimicel/select2-tailwindcss-v4-theme@1.1.0/dist/select2-tailwindcss-theme-plain.min.css"
        rel="stylesheet">

@endsection
<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Registrar Nuevo Candidato
        </h2>
    </x-slot>

    <div class="py-4 px-2 md:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto bg-white dark:bg-zinc-700 shadow-lg rounded-lg p-6">
            <form wire:submit.prevent="saveCandidate">
                <div class="space-y-6">
                    {{-- Selección de Elección --}}
                    <div>
                        <label for="election_id" class="block text-sm font-medium text-gray-700 dark:text-zinc-300">Elección</label>
                        <select wire:model.lazy="election_id" id="election_id" name="election_id"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-zinc-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md bg-white dark:bg-zinc-800 text-gray-900 dark:text-gray-100">
                            <option value="">Seleccione una elección...</option>
                            @foreach($elections as $election)
                                <option value="{{ $election->id }}">{{ $election->name }} ({{ $election->status }})</option>
                            @endforeach
                        </select>
                        @error('election_id') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- Selección de Estudiante --}}
                    <div wire:ignore> {{-- <--- Contenedor con wire:ignore --}}
                        <label for="student_id_select2" class="block text-sm font-medium text-gray-700 dark:text-zinc-300">Estudiante</label>
                        <select id="student_id_select2" name="student_id_select2"> {{-- Select2 aplicará sus propios estilos --}}
                            <option value="">Seleccione un estudiante...</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}">{{ $student->full_name }} (ID: {{ $student->student_identifier }})</option>
                            @endforeach
                        </select>
                        {{-- El error se mostrará para la propiedad de Livewire 'student_id' --}}
                    </div>
                    @error('student_id') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-zinc-400">Escriba para buscar un estudiante.</p>


                    {{-- Número de Lista/Tarjetón --}}
                    <div>
                        <label for="list_number" class="block text-sm font-medium text-gray-700 dark:text-zinc-300">Número en Lista/Tarjetón (Opcional)</label>
                        <input wire:model.lazy="list_number" type="text" name="list_number" id="list_number"
                               class="mt-1 block w-full rounded-md bg-white dark:bg-zinc-800 px-3 py-1.5 text-base text-gray-900 dark:text-gray-100 outline-1 -outline-offset-1 outline-gray-300 dark:outline-zinc-600 placeholder:text-gray-400 dark:placeholder:text-zinc-400 focus:outline-2 focus:-outline-offset-2 focus:outline-gray-400 dark:focus:outline-zinc-500 sm:text-sm/6"
                               placeholder="Ej: 01, A2">
                        @error('list_number') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    {{-- Propuesta --}}
                    <div>
                        <label for="proposal" class="block text-sm font-medium text-gray-700 dark:text-zinc-300">Propuesta (Opcional)</label>
                        <textarea wire:model.lazy="proposal" name="proposal" id="proposal" rows="4"
                                  class="mt-1 block w-full rounded-md bg-white dark:bg-zinc-800 px-3 py-1.5 text-base text-gray-900 dark:text-gray-100 outline-1 -outline-offset-1 outline-gray-300 dark:outline-zinc-600 placeholder:text-gray-400 dark:placeholder:text-zinc-400 focus:outline-2 focus:-outline-offset-2 focus:outline-gray-400 dark:focus:outline-zinc-500 sm:text-sm/6"
                                  placeholder="Escriba aquí la propuesta del candidato..."></textarea>
                        @error('proposal') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- Ruta de Foto (Temporalmente como texto) --}}
                    <div>
                        <label for="photo" class="block text-sm font-medium text-gray-700 dark:text-zinc-300">Foto del Candidato (Opcional)</label>
                        <input wire:model="photo" type="file" name="photo" id="photo"
                            class="mt-1 block w-full text-sm text-gray-500 dark:text-zinc-400
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-md file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-indigo-50 dark:file:bg-indigo-800 file:text-indigo-700 dark:file:text-indigo-200
                                    hover:file:bg-indigo-100 dark:hover:file:bg-indigo-700
                                    focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-zinc-800">
                        
                        <div wire:loading wire:target="photo" class="mt-1 text-sm text-gray-500 dark:text-zinc-400">Cargando foto...</div>

                        @error('photo') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror

                        {{-- Previsualización de la Imagen --}}
                        @if ($photo && !$errors->has('photo'))
                            <div class="mt-4">
                                <p class="text-sm text-gray-600 dark:text-zinc-300">Previsualización:</p>
                                <img src="{{ $photo->temporaryUrl() }}" alt="Previsualización de foto del candidato" class="mt-2 h-40 w-40 object-cover rounded-md shadow-md">
                            </div>
                        @endif
                    </div>

                </div>

                {{-- Botones de Acción --}}
                <div class="mt-8 pt-5 border-t border-gray-200 dark:border-zinc-600">
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('candidates.index') }}"
                           class="px-4 py-2 border border-gray-300 dark:border-zinc-500 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-zinc-200 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancelar
                        </a>
                        <button type="submit"
                                class="inline-flex justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Registrar Candidato
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@section("scripts")
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
   
         document.addEventListener('livewire:initialized', () => {
            console.log('Livewire loaded');
           
            $('#student_id_select2').select2({            
                placeholder: 'Seleccione un estudiante',         
                 width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-full') ? '100%' : 'style',
                  theme: 'tailwindcss-4',
            });




            // Cuando Select2 cambia, actualiza la propiedad de Livewire
            $('#student_id_select2').on('change', function (e) {
                var data = $(this).val(); // Obtener el valor seleccionado
                @this.set('student_id', data); // Actualizar la propiedad student_id en Livewire
                
                // Opcional: Si quieres validar en tiempo real después de seleccionar
                // @this.validateOnly('student_id');
            });

            // Escuchar un evento de Livewire si necesitas resetear Select2
            // (por ejemplo, si el formulario se resetea desde el backend)
            Livewire.on('resetStudentSelect', () => {
                $('#student_id_select2').val(null).trigger('change');
            });
        });
    </script>
@endsection