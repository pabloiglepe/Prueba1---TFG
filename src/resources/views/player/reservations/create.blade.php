<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Reservar Pista
            </h2>
            <a href="{{ route('player.reservations.index') }}"
               class="text-sm text-gray-600 hover:underline">
                ← Mis Reservas
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- PASO 1: ELEGIR FECHA --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="font-semibold text-gray-700 mb-4">1. Elige una fecha</h3>
            <form method="GET" action="{{ route('player.reservations.create') }}">
                <div class="flex gap-4 items-end">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                        <input type="date" name="date"
                               value="{{ request('date') }}"
                               min="{{ date('Y-m-d') }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                        Buscar franjas
                    </button>
                </div>
            </form>
        </div>

        {{-- PASO 2: ELEGIR FRANJA HORARIA --}}
        @if(request('date') && $slots->isNotEmpty())
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="font-semibold text-gray-700 mb-2">2. Elige una franja horaria</h3>
            <p class="text-sm text-gray-500 mb-4">
                Tarifa diurna: <strong>12€</strong> · 
                Tarifa nocturna (desde {{ $nightStartTime }}): <strong>16€</strong> · 
                Duración: <strong>1h 30min</strong>
            </p>
            <form method="GET" action="{{ route('player.reservations.create') }}">
                <input type="hidden" name="date" value="{{ request('date') }}">
                <div class="grid grid-cols-4 sm:grid-cols-6 gap-2 mb-4">
                    @foreach($slots as $slot)
                        <button type="submit" name="start_time" value="{{ $slot }}"
                                class="py-2 px-3 rounded-lg text-sm font-medium border transition
                                {{ request('start_time') == $slot
                                    ? 'bg-blue-600 text-white border-blue-600'
                                    : 'bg-white text-gray-700 border-gray-300 hover:border-blue-400' }}">
                            {{ $slot }}
                        </button>
                    @endforeach
                </div>
            </form>
        </div>
        @endif

        {{-- PASO 3: ELEGIR PISTA --}}
        @if(request('start_time'))
            @if($courts->isEmpty())
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-lg p-4">
                    No hay pistas disponibles para ese horario. Prueba otra franja.
                </div>
            @else
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="font-semibold text-gray-700 mb-4">3. Elige una pista disponible</h3>
                <form action="{{ route('player.reservations.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="date"       value="{{ request('date') }}">
                    <input type="hidden" name="start_time" value="{{ request('start_time') }}">

                    @error('court_id')
                        <p class="text-red-600 text-sm mb-3">{{ $message }}</p>
                    @enderror

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        @foreach($courts as $court)
                            <label class="flex items-center gap-4 border rounded-lg p-4 cursor-pointer hover:border-blue-400 transition">
                                <input type="radio" name="court_id" value="{{ $court->id }}" class="text-blue-600" required>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $court->name }}</p>
                                    <p class="text-sm text-gray-500 capitalize">{{ $court->type }} · {{ $court->surface }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    {{-- RESUMEN DE LA RESERVA --}}
                    <div class="bg-gray-50 rounded-lg p-4 mb-6 text-sm text-gray-600">
                        <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse(request('date'))->format('d/m/Y') }}</p>
                        <p><strong>Horario:</strong> {{ request('start_time') }} - 
                            {{ \Carbon\Carbon::createFromFormat('H:i', request('start_time'))->addMinutes(90)->format('H:i') }}
                        </p>
                        <p><strong>Precio:</strong> 
                            {{ \Carbon\Carbon::createFromFormat('H:i', request('start_time'))->gte(\Carbon\Carbon::createFromFormat('H:i', $nightStartTime)) ? '16€' : '12€' }}
                        </p>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                            Confirmar Reserva
                        </button>
                    </div>
                </form>
            </div>
            @endif
        @endif

    </div>
</x-app-layout>