<div wire:poll.{{ $refreshIntervalMs }}ms="loadResults">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Resultados: {{ $election->name }}
            <span class="text-sm font-normal px-2 py-1 rounded-full ml-2
                @switch($election->status)
                    @case('scheduled') bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100 @break
                    @case('active') bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100 @break
                    @case('completed') bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-100 @break
                    @case('archived') bg-gray-100 text-gray-800 dark:bg-zinc-600 dark:text-zinc-100 @break
                    @default bg-gray-200 text-gray-700 dark:bg-zinc-500 dark:text-zinc-200
                @endswitch">
                {{ Str::ucfirst($election->status) }}
            </span>
        </h2>
    </x-slot>

    <div class="py-4 px-2 md:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            @if (!$showResults && $election->status !== 'completed') {{-- Mensaje de umbral para no completadas --}}
                {{-- ... (código del mensaje de umbral como estaba) ... --}}
            @else
                <div class="bg-white dark:bg-zinc-700 shadow-lg rounded-lg overflow-hidden">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-zinc-600">
                        <h3 class="text-2xl leading-7 font-semibold text-gray-900 dark:text-gray-100">
                            {{ $election->name }}
                        </h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-zinc-400">
                            Total de votos registrados: {{ $grandTotalVotesInDb }}
                        </p>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        {{-- LÓGICA PARA ELECCIONES COMPLETADAS Y CON GANADORES --}}
                        @if ($election->status === 'completed' && $showResults)
                            @if (!empty($winners))
                                <div class="mb-10">
                                    <h4 class="text-2xl md:text-3xl font-bold text-center text-green-600 dark:text-green-400 mb-2">
                                        🎉 ¡GANADOR<span class="text-2xl md:text-3xl font-bold text-center text-green-600 dark:text-green-400 mb-2">{{ count($winners) > 1 ? 'ES' : '' }}</span>! 🎉
                                    </h4>
                                    <div class="grid grid-cols-1 {{ count($winners) > 1 ? 'md:grid-cols-' . min(count($winners), 2) : '' }} gap-6">
                                        @foreach ($winners as $winner)
                                            <div class="p-6 bg-green-50 dark:bg-green-900/40 rounded-xl shadow-2xl text-center border-2 border-green-500 dark:border-green-600 transform scale-105">
                                                @if($winner['photo_path'])
                                                    <img src="{{ Storage::url($winner['photo_path']) }}" alt="Foto de {{ $winner['candidate_name'] }}" class="w-48 h-48 lg:w-56 lg:h-56 object-cover rounded-full mx-auto mb-4 shadow-lg border-4 border-white dark:border-zinc-600">
                                                @else
                                                    <div class="w-48 h-48 lg:w-56 lg:h-56 rounded-full bg-gray-200 dark:bg-zinc-600 flex items-center justify-center mx-auto mb-4 shadow-lg border-4 border-white dark:border-zinc-600">
                                                        <svg class="w-24 h-24 text-gray-400 dark:text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                    </div>
                                                @endif
                                                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $winner['candidate_name'] }}</h3>
                                                @if($winner['list_number'])
                                                    <p class="text-md text-gray-600 dark:text-zinc-400">(N° {{ $winner['list_number'] }})</p>
                                                @endif
                                                <p class="text-3xl font-semibold text-green-700 dark:text-green-300 mt-3">{{ $winner['votes'] }} votos</p>
                                                <p class="text-xl text-green-600 dark:text-green-400">{{ $winner['percentage'] }}%</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                @if (!empty($otherParticipants) || $blankVotesCount > 0)
                                    <hr class="my-8 border-gray-300 dark:border-zinc-600">
                                    <h4 class="text-lg font-semibold text-gray-700 dark:text-zinc-300 mb-4">Resultados de Otros Participantes:</h4>
                                @endif
                            @elseif ($totalVotesProcessed > 0)
                                <p class="text-center text-gray-700 dark:text-zinc-300 my-6 text-lg">La elección ha finalizado. No se declararon ganadores con los votos actuales o todos los candidatos obtuvieron cero votos.</p>
                            @endif
                            
                            {{-- Mostrar Otros Participantes (si hay ganadores y otros, o si no hay ganadores pero sí otros) --}}
                            @if (!empty($otherParticipants))
                            <ul role="list" class="space-y-3">
                                @foreach ($otherParticipants as $participant)
                                    {{-- Estructura <li> para otros participantes (más pequeña) --}}
                                    <li class="bg-gray-100 dark:bg-zinc-800 px-4 py-3 shadow-sm sm:rounded-md sm:px-6">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">
                                                    {{ $participant['candidate_name'] }}
                                                    @if($participant['list_number'])
                                                        <span class="text-xs text-gray-500 dark:text-zinc-400">(N° {{ $participant['list_number'] }})</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="ml-4 flex items-baseline">
                                                <span class="text-md font-semibold text-gray-700 dark:text-gray-300">{{ $participant['votes'] }}</span>
                                                <span class="ml-2 text-xs text-gray-500 dark:text-zinc-400">votos</span>
                                                <span class="ml-3 px-2 py-0.5 rounded-full text-xs font-medium {{ $participant['percentage'] > 0 ? 'bg-blue-100 dark:bg-blue-800/50 text-blue-800 dark:text-blue-200' : 'bg-gray-200 dark:bg-zinc-600 text-gray-700 dark:text-zinc-300' }}">
                                                    {{ $participant['percentage'] }}%
                                                </span>
                                            </div>
                                        </div>
                                        <div class="mt-2 w-full bg-gray-200 dark:bg-zinc-600 rounded-full h-1.5">
                                            <div class="bg-blue-500 dark:bg-blue-400 h-1.5 rounded-full" style="width: {{ $participant['percentage'] }}%"></div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                            @endif

                        {{-- SI LA ELECCIÓN ESTÁ ACTIVA O PROGRAMADA, MOSTRAR RESULTADOS PARCIALES NORMALES --}}
                        @else
                            @if(empty($results) && $blankVotesCount == 0 && $totalVotesProcessed > 0 && $showResults)
                                <p class="text-center text-gray-500 dark:text-zinc-400">Aún no hay votos registrados para candidatos o en blanco (pero se superó el umbral).</p>
                            @elseif(empty($results) && $blankVotesCount == 0 && $totalVotesProcessed == 0 && $showResults)
                                <p class="text-center text-gray-500 dark:text-zinc-400">Aún no se han registrado votos.</p>
                            @elseif($showResults) {{-- $showResults ya implica que se superó el umbral --}}
                                <ul role="list" class="space-y-3">
                                    @foreach ($results as $result)
                                        {{-- Estructura <li> para resultados parciales (como estaba antes) --}}
                                        <li class="bg-gray-50 dark:bg-zinc-800 px-4 py-3 shadow sm:rounded-md sm:px-6">
                                            {{-- ... (copiar de la versión anterior) ... --}}
                                             <div class="flex items-center justify-between">
                                                <div class="flex items-center">
                                                    <div class="text-sm font-medium text-indigo-600 dark:text-indigo-400 truncate">
                                                        {{ $result['candidate_name'] }}
                                                        @if($result['list_number'])
                                                            <span class="text-xs text-gray-500 dark:text-zinc-400">(N° {{ $result['list_number'] }})</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="ml-4 flex items-baseline">
                                                    <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $result['votes'] }}</span>
                                                    <span class="ml-2 text-sm text-gray-500 dark:text-zinc-400">votos</span>
                                                    <span class="ml-3 px-2 py-0.5 rounded-full text-xs font-medium {{ $result['percentage'] > 0 ? 'bg-green-100 dark:bg-green-700 text-green-800 dark:text-green-100' : 'bg-gray-100 dark:bg-zinc-600 text-gray-800 dark:text-zinc-200' }}">
                                                        {{ $result['percentage'] }}%
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="mt-2 w-full bg-gray-200 dark:bg-zinc-600 rounded-full h-2.5">
                                                <div class="bg-indigo-600 dark:bg-indigo-500 h-2.5 rounded-full" style="width: {{ $result['percentage'] }}%"></div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        @endif

                        {{-- Votos en Blanco (se muestra si hay resultados o si la elección está completada y hubo votos) --}}
                        @if ($showResults && ($blankVotesCount > 0 || $election->status === 'completed'))
                            <div class="mt-6 pt-4 {{ (!empty($winners) || !empty($otherParticipants) || !empty($results)) ? 'border-t border-gray-200 dark:border-zinc-600' : '' }}">
                                <div class="bg-gray-100 dark:bg-zinc-800/70 px-4 py-3 shadow-sm sm:rounded-md sm:px-6">
                                    {{-- ... (código para mostrar Votos en Blanco como estaba antes) ... --}}
                                    <div class="flex items-center justify-between">
                                        <div class="text-sm font-medium text-gray-700 dark:text-zinc-300 truncate">
                                            Votos en Blanco
                                        </div>
                                        <div class="ml-4 flex items-baseline">
                                            <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $blankVotesCount }}</span>
                                            <span class="ml-2 text-sm text-gray-500 dark:text-zinc-400">votos</span>
                                            @if($totalVotesProcessed > 0)
                                            <span class="ml-3 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-200 dark:bg-zinc-600 text-gray-700 dark:text-zinc-300">
                                                {{ round(($blankVotesCount / $totalVotesProcessed) * 100, 1) }}%
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($totalVotesProcessed > 0)
                                    <div class="mt-2 w-full bg-gray-200 dark:bg-zinc-600 rounded-full h-2.5">
                                        <div class="bg-gray-400 dark:bg-zinc-500 h-2.5 rounded-full" style="width: {{ $totalVotesProcessed > 0 ? round(($blankVotesCount / $totalVotesProcessed) * 100, 1) : 0 }}%"></div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        @if($lastUpdatedAt && $showResults)
                        <div class="mt-6 text-xs text-right text-gray-500 dark:text-zinc-400 px-4 sm:px-0 pb-1">
                            Última actualización: {{ $lastUpdatedAt->format('d/m/Y H:i:s') }}
                        </div>
                        @endif
                    </div>
                </div>
            @endif {{-- Cierre del @if (!$showResults && $election->status !== 'completed') --}}
        </div>
    </div>
</div>