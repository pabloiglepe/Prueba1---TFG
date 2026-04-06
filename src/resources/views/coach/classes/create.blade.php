<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Nueva Clase
            </h2>
            <a href="{{ route('coach.classes.index') }}"
                class="text-sm text-gray-600 hover:underline">
                ← Volver al listado
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6">

            <form action="{{ route('coach.classes.store') }}" method="POST">
                @csrf

                {{-- TÍTULO --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título de la clase</label>
                    <input type="text" name="title" value="{{ old('title') }}"
                        placeholder="Ej: Clase de iniciación jueves"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('title')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- TIPO Y NIVEL --}}
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                        <select name="type"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            x-data x-on:change="
                                    const max = document.getElementById('max_players');
                                    max.value = $event.target.value === 'individual' ? 1 : max.value;
                                    max.disabled = $event.target.value === 'individual';
                                ">
                            <option value="individual" {{ old('type') == 'individual' ? 'selected' : '' }}>Individual</option>
                            <option value="grupal" {{ old('type') == 'grupal'     ? 'selected' : '' }}>Grupal</option>
                        </select>
                        @error('type')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nivel</label>
                        <select name="level"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="initiation" {{ old('level') == 'initiation'   ? 'selected' : '' }}>Iniciación</option>
                            <option value="intermediate" {{ old('level') == 'intermediate' ? 'selected' : '' }}>Intermedio</option>
                            <option value="advanced" {{ old('level') == 'advanced'     ? 'selected' : '' }}>Avanzado</option>
                        </select>
                        @error('level')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- VISIBILIDAD Y PLAZAS --}}
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Visibilidad</label>
                        <select name="visibility"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            x-data x-on:change="
                                    document.getElementById('players-section').style.display =
                                        $event.target.value === 'private' ? 'block' : 'none';
                                ">
                            <option value="public" {{ old('visibility') == 'public'  ? 'selected' : '' }}>Pública</option>
                            <option value="private" {{ old('visibility') == 'private' ? 'selected' : '' }}>Privada</option>
                        </select>
                        @error('visibility')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Plazas máximas</label>
                        <input type="number" name="max_players" id="max_players"
                            value="{{ old('max_players', 4) }}" min="1" max="4"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('max_players')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- PISTA --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pista</label>
                    <select name="court_id"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @foreach($courts as $court)
                        <option value="{{ $court->id }}" {{ old('court_id') == $court->id ? 'selected' : '' }}>
                            {{ $court->name }} ({{ $court->type }} · {{ $court->surface }})
                        </option>
                        @endforeach
                    </select>
                    @error('court_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- FECHA Y HORARIO --}}
                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                        <input type="date" name="date" value="{{ old('date') }}"
                            min="{{ date('Y-m-d') }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hora inicio</label>
                        <input type="time" name="start_time" value="{{ old('start_time') }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('start_time')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hora fin</label>
                        <input type="time" name="end_time" value="{{ old('end_time') }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('end_time')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- PRECIO --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Precio por alumno (€)</label>
                    <input type="number" name="price" value="{{ old('price', 15) }}"
                        min="0" step="0.50"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('price')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ALUMNOS (SOLO VISIBLE EN CLASES PRIVADAS) --}}
                <div id="players-section" style="display:{{ old('visibility') == 'private' ? 'block' : 'none' }}"
                    class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Alumnos a inscribir
                    </label>
                    <p class="text-xs text-gray-400 mb-3">
                        Recibirán una notificación al ser inscritos.
                    </p>
                    <div class="grid grid-cols-2 gap-2 max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-3">
                        @foreach($players as $player)
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                            <input type="checkbox" name="players[]" value="{{ $player->id }}"
                                {{ in_array($player->id, old('players', [])) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600">
                            {{ $player->name }}
                        </label>
                        @endforeach
                    </div>
                    @error('players')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                        Crear Clase
                    </button>
                </div>

            </form>
        </div>
    </div>
</x-app-layout>