<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Clases
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

        {{-- MENSAJES --}}
        @if(session('success'))
            <div class="p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="p-4 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
        @endif

        {{-- MIS CLASES --}}
        <div>
            <h3 class="font-semibold text-gray-700 text-lg mb-4">Mis clases</h3>

            @if($myClasses->isEmpty())
                <div class="bg-white shadow rounded-lg p-6 text-center text-gray-400">
                    No estás inscrito en ninguna clase todavía.
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($myClasses as $class)
                        <div class="bg-white shadow rounded-lg p-5 flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-semibold text-gray-800">{{ $class->title }}</h4>
                                    <span class="px-2 py-1 text-xs rounded-full
                                        {{ $class->visibility === 'public' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $class->visibility === 'public' ? 'Pública' : 'Privada' }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500 capitalize mb-1">
                                    {{ $class->type }} ·
                                    {{ match($class->level) {
                                        'initiation'   => 'Iniciación',
                                        'intermediate' => 'Intermedio',
                                        'advanced'     => 'Avanzado',
                                        default        => $class->level
                                    } }}
                                </p>
                                <p class="text-sm text-gray-500 mb-1">
                                    📅 {{ \Carbon\Carbon::parse($class->date)->format('d/m/Y') }}
                                </p>
                                <p class="text-sm text-gray-500 mb-1">
                                    🕐 {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }} -
                                       {{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}
                                </p>
                                <p class="text-sm text-gray-500 mb-1">
                                    🎾 {{ $class->court->name }}
                                </p>
                                <p class="text-sm text-gray-500 mb-3">
                                    👤 {{ $class->coach->name }}
                                </p>
                                <p class="text-sm font-semibold text-green-600">
                                    {{ number_format($class->price, 2) }}€
                                </p>
                            </div>

                            {{-- CANCELAR INSCRIPCIÓN --}}
                            @if($class->date >= today()->format('Y-m-d'))
                                <form action="{{ route('player.classes.cancel', $class) }}"
                                      method="POST" class="mt-4"
                                      onsubmit="return confirm('¿Cancelar tu inscripción?')">
                                    @csrf
                                    <button type="submit"
                                            class="w-full text-center text-sm text-red-600 hover:underline">
                                        Cancelar inscripción
                                    </button>
                                </form>
                            @else
                                <p class="mt-4 text-xs text-center text-gray-400">Clase finalizada</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- CLASES DISPONIBLES --}}
        <div>
            <h3 class="font-semibold text-gray-700 text-lg mb-4">Clases disponibles</h3>

            @if($availableClasses->isEmpty())
                <div class="bg-white shadow rounded-lg p-6 text-center text-gray-400">
                    No hay clases públicas disponibles en este momento.
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($availableClasses as $class)
                        <div class="bg-white shadow rounded-lg p-5 flex flex-col justify-between border-l-4 border-blue-400">
                            <div>
                                <h4 class="font-semibold text-gray-800 mb-2">{{ $class->title }}</h4>
                                <p class="text-sm text-gray-500 capitalize mb-1">
                                    {{ $class->type }} ·
                                    {{ match($class->level) {
                                        'initiation'   => 'Iniciación',
                                        'intermediate' => 'Intermedio',
                                        'advanced'     => 'Avanzado',
                                        default        => $class->level
                                    } }}
                                </p>
                                <p class="text-sm text-gray-500 mb-1">
                                    📅 {{ \Carbon\Carbon::parse($class->date)->format('d/m/Y') }}
                                </p>
                                <p class="text-sm text-gray-500 mb-1">
                                    🕐 {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }} -
                                       {{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}
                                </p>
                                <p class="text-sm text-gray-500 mb-1">
                                    🎾 {{ $class->court->name }}
                                </p>
                                <p class="text-sm text-gray-500 mb-1">
                                    👤 {{ $class->coach->name }}
                                </p>
                                <p class="text-sm text-gray-500 mb-3">
                                    👥 {{ $class->enrolled_count }}/{{ $class->max_players }} plazas ocupadas
                                </p>
                                <p class="text-sm font-semibold text-green-600">
                                    {{ number_format($class->price, 2) }}€
                                </p>
                            </div>

                            {{-- INSCRIBIRSE --}}
                            <form action="{{ route('player.classes.register', $class) }}"
                                  method="POST" class="mt-4">
                                @csrf
                                <button type="submit"
                                        class="w-full bg-blue-600 text-white text-sm py-2 rounded-lg hover:bg-blue-700">
                                    Inscribirme
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</x-app-layout>