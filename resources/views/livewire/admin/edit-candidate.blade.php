@section("styles")
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link
        href="https://cdn.jsdelivr.net/gh/erimicel/select2-tailwindcss-v4-theme@1.1.0/dist/select2-tailwindcss-theme-plain.min.css"
        rel="stylesheet">

@endsection

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{-- El título se pasa desde el método render del componente --}}
            {{ $header ?? 'Editar Candidatura' }}
        </h2>
    </x-slot>

    <div class="py-4 px-2 md:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto bg-white dark:bg-zinc-700 shadow-lg rounded-lg p-6">
            <form wire:submit.prevent="updateCandidate">
                <div class="space-y-6">
                    {{-- Selección de Elección --}}
                    <div>
                        <label for="edit_election_id" class="block text-sm font-medium text-gray-700 dark:text-zinc-300">Elección</label>
                        <select wire:model.lazy="election_id" id="edit_election_id" name="election_id"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-zinc-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md bg-white dark:bg-zinc-800 text-gray-900 dark:text-gray-100">
                            <option value="">Seleccione una elección...</option>
                            @foreach($elections as $electionOpt) {{-- Cambiado el nombre de la variable del loop --}}
                                <option value="{{ $electionOpt->id }}">{{ $electionOpt->name }}</option>
                            @endforeach
                        </select>
                        @error('election_id') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- Selección de Estudiante --}}
                    <div wire:ignore>
                        <label for="edit_student_id" class="block text-sm font-medium text-gray-700 dark:text-zinc-300">Estudiante</label>
                        <select id="student_id_select2" name="student_id_select2"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-zinc-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md bg-white dark:bg-zinc-800 text-gray-900 dark:text-gray-100">
                            <option value="">Seleccione un estudiante...</option>
                            @foreach($students as $studentOpt) {{-- Cambiado el nombre de la variable del loop --}}
                                <option value="{{ $studentOpt->id }}">{{ $studentOpt->full_name }} (ID: {{ $studentOpt->student_identifier }})</option>
                            @endforeach
                        </select>
                         @error('student_id') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- Número de Lista/Tarjetón --}}
                    <div>
                        <label for="edit_list_number" class="block text-sm font-medium text-gray-700 dark:text-zinc-300">Número en Lista/Tarjetón (Opcional)</label>
                        <input wire:model.lazy="list_number" type="text" name="list_number" id="edit_list_number"
                               class="mt-1 block w-full rounded-md bg-white dark:bg-zinc-800 px-3 py-1.5 text-base text-gray-900 dark:text-gray-100 outline-1 -outline-offset-1 outline-gray-300 dark:outline-zinc-600 placeholder:text-gray-400 dark:placeholder:text-zinc-400 focus:outline-2 focus:-outline-offset-2 focus:outline-gray-400 dark:focus:outline-zinc-500 sm:text-sm/6">
                        @error('list_number') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    {{-- Propuesta --}}
                    <div>
                        <label for="edit_proposal" class="block text-sm font-medium text-gray-700 dark:text-zinc-300">Propuesta (Opcional)</label>
                        <textarea wire:model.lazy="proposal" name="proposal" id="edit_proposal" rows="4"
                                  class="mt-1 block w-full rounded-md bg-white dark:bg-zinc-800 px-3 py-1.5 text-base text-gray-900 dark:text-gray-100 outline-1 -outline-offset-1 outline-gray-300 dark:outline-zinc-600 placeholder:text-gray-400 dark:placeholder:text-zinc-400 focus:outline-2 focus:-outline-offset-2 focus:outline-gray-400 dark:focus:outline-zinc-500 sm:text-sm/6"></textarea>
                        @error('proposal') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- Ruta de Foto (Temporalmente como texto) --}}
                    <div class="mt-4">
                        <p class="text-sm text-gray-600 dark:text-zinc-300">Foto Actual:</p>
                        @if ($photo && !$errors->has('photo')) {{-- Previsualización de la nueva foto seleccionada --}}
                            <img src="{{ $photo->temporaryUrl() }}" alt="Previsualización nueva foto" class="mt-2 h-40 w-40 object-cover rounded-md shadow-md">
                            <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1">Previsualización de nueva foto. La foto actual se reemplazará al guardar.</p>
                        @elseif ($existing_photo_path) {{-- Muestra la foto existente si no hay una nueva seleccionada --}}
                            <img src="{{ Storage::url($existing_photo_path) }}" alt="Foto actual del candidato" class="mt-2 h-40 w-40 object-cover rounded-md shadow-md">
                        @else
                            <p class="text-sm text-gray-500 dark:text-zinc-400 mt-1">No hay foto asignada.</p>
                        @endif
                    </div>
                    @error('photo') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                    {{-- Input para subir nueva foto --}}
                    <div class="mt-2">
                        <label for="edit_photo" class="block text-sm font-medium text-gray-700 dark:text-zinc-300">Subir nueva foto (opcional):</label>
                        <input wire:model="photo" type="file" name="photo" id="edit_photo"
                            class="mt-1 block w-full text-sm text-gray-500 dark:text-zinc-400
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-md file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-indigo-50 dark:file:bg-indigo-800 file:text-indigo-700 dark:file:text-indigo-200
                                    hover:file:bg-indigo-100 dark:hover:file:bg-indigo-700
                                    focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-zinc-800">
                        <div wire:loading wire:target="photo" class="mt-1 text-sm text-gray-500 dark:text-zinc-400">Cargando foto...</div>
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
                            Actualizar Candidatura
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
            var initialStudentId = @json($student_id);
            $('#student_id_select2').select2({            
                placeholder: 'Seleccione un estudiante',         
                 width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-full') ? '100%' : 'style',
                  theme: 'tailwindcss-4',
            });

            if (initialStudentId) {
                $('#student_id_select2').val(initialStudentId).trigger('change.select2'); // 'change.select2' para que Select2 actualice su UI
            }


            // Cuando Select2 cambia, actualiza la propiedad de Livewire
            $('#student_id_select2').on('change', function (e) {
                var data = $(this).val(); // Obtener el valor seleccionado
                @this.set('student_id', data); // Actualizar la propiedad student_id en Livewire
                
                // Opcional: Si quieres validar en tiempo real después de seleccionar
                // @this.validateOnly('student_id');
            });

            // Escuchar un evento de Livewire si necesitas resetear Select2
            // (por ejemplo, si el formulario se resetea desde el backend)
            Livewire.on('resetEditStudentSelect', () => { // Evento con nombre diferente si es necesario
                var currentStudentId = @json($student_id); // Podrías necesitar re-evaluar el ID aquí
                $('#edit_student_id_select2').val(currentStudentId).trigger('change.select2');
            });
        });
    </script>
@endsection