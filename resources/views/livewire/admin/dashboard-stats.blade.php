<div class="p-4 sm:p-6 lg:p-8">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl"> {{-- Aumenté el gap un poco --}}

        {{-- Sección de Estadísticas --}}
        <div class="grid auto-rows-min gap-4 md:gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
            {{-- Tarjeta Total Elecciones --}}
            <div class="relative p-6 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-800 shadow-sm">
                <h3 class="text-sm font-medium text-gray-500 dark:text-zinc-400 truncate">Total Elecciones</h3>
                <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $totalElections }}</p>
            </div>

            {{-- Tarjeta Elecciones Activas --}}
            <div class="relative p-6 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-800 shadow-sm">
                <h3 class="text-sm font-medium text-green-600 dark:text-green-400 truncate">Elecciones Activas</h3>
                <p class="mt-1 text-3xl font-semibold text-green-700 dark:text-green-300">{{ $activeElections }}</p>
            </div>

            {{-- Tarjeta Elecciones Completadas --}}
            <div class="relative p-6 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-800 shadow-sm">
                <h3 class="text-sm font-medium text-blue-600 dark:text-blue-400 truncate">Elecciones Completadas</h3>
                <p class="mt-1 text-3xl font-semibold text-blue-700 dark:text-blue-300">{{ $completedElections }}</p>
            </div>
            
            {{-- Tarjeta Total Estudiantes --}}
            <div class="relative p-6 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-800 shadow-sm">
                <h3 class="text-sm font-medium text-gray-500 dark:text-zinc-400 truncate">Estudiantes Activos</h3>
                <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $totalStudents }}</p>
            </div>

            {{-- Tarjeta Total Candidatos --}}
            <div class="relative p-6 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-800 shadow-sm">
                <h3 class="text-sm font-medium text-gray-500 dark:text-zinc-400 truncate">Candidatos Únicos</h3>
                <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $totalCandidates }}</p>
            </div>
        </div>

        {{-- Sección de Enlaces Rápidos --}}
        <div class="bg-white dark:bg-zinc-800 shadow-sm rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Accesos Rápidos</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="{{ route('elections.index') }}"
                   class="block p-6 bg-indigo-500 hover:bg-indigo-600 dark:bg-indigo-600 dark:hover:bg-indigo-700 rounded-lg shadow-md transition-colors">
                    <h3 class="text-lg font-semibold text-white">Gestionar Elecciones</h3>
                    <p class="mt-1 text-sm text-indigo-100 dark:text-indigo-200">Crear, ver y editar elecciones.</p>
                </a>
                <a href="{{ route('students.index') }}"
                   class="block p-6 bg-sky-500 hover:bg-sky-600 dark:bg-sky-600 dark:hover:bg-sky-700 rounded-lg shadow-md transition-colors">
                    <h3 class="text-lg font-semibold text-white">Gestionar Estudiantes</h3>
                    <p class="mt-1 text-sm text-sky-100 dark:text-sky-200">Administrar la lista de votantes.</p>
                </a>
                <a href="{{ route('candidates.index') }}"
                   class="block p-6 bg-emerald-500 hover:bg-emerald-600 dark:bg-emerald-600 dark:hover:bg-emerald-700 rounded-lg shadow-md transition-colors">
                    <h3 class="text-lg font-semibold text-white">Gestionar Candidatos</h3>
                    <p class="mt-1 text-sm text-emerald-100 dark:text-emerald-200">Registrar y editar candidaturas.</p>
                </a>
                <a href="{{ route('settings.index') }}"
                   class="block p-6 bg-slate-500 hover:bg-slate-600 dark:bg-slate-600 dark:hover:bg-slate-700 rounded-lg shadow-md transition-colors">
                    <h3 class="text-lg font-semibold text-white">Configuraciones</h3>
                    <p class="mt-1 text-sm text-slate-100 dark:text-slate-200">Ajustes generales del sistema.</p>
                </a>
            </div>
        </div>

        {{-- Espacio para futuras gráficas o información más detallada --}}
        {{-- <div class="relative h-80 flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-800 shadow-sm">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Actividad Reciente (Próximamente)</h3>
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/10 dark:stroke-neutral-100/10 opacity-20 -z-10" />
                <p class="mt-2 text-sm text-gray-500 dark:text-zinc-400">Aquí se mostrará un resumen de la actividad.</p>
            </div>
        </div> --}}
    </div>
</div>