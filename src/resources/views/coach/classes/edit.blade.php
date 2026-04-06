<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Editar Clase: {{ $class->title }}
            </h2>
            <a href="{{ route('coach.classes.index') }}"
                class="text-sm text-gray-600 hover:underline">
                ← Volver al listado
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6">

            <form action="{{ route('coach.classes.update', $class) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- TÍTULO --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título de la clase</label>
                    <input type="text" name="title" value="{{ old('title', $class->title) }}"
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
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="individual" {{ old('type', $class->type) == 'individual' ? 'selected' : '' }}>Individual</option>
                            <option value="grupal" {{ old('type', $class->type) == 'grupal'     ? 'selected' : '' }}>Grupal</option>
                        </select>
                        @error('type')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nivel</label>
                        <select name="level"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="initiation" {{ old('level', $class->level) == 'initiation'   ? 'selected' : '' }}>Iniciación</option>
                            <option value="intermediate" {{ old('level', $class->level) == 'intermediate' ? 'selected' : '' }}>Intermedio</option>
                            <option value="advanced" {{ old('level', $class->level) == 'advanced'     ? 'selected' : '' }}>Avanzado</option>
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
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="public" {{ old('visibility', $class->visibility) == 'public'  ? 'selected' : '' }}>Pública</option>
                            <option value="private" {{ old('visibility', $class->visibility) == 'private' ? 'selected' : '' }}>Privada</option>
                        </select>
                        @error('visibility')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Plazas máximas</label>
                        <input type="number" name="max_players"
                            value="{{ old('max_players', $class->max_players) }}"
                            min="1" max="4"
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
                        <option value="{{ $court->id }}"
                            {{ old('court_id', $class->court_id) == $court->id ? 'selected' : '' }}>
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
                        <input type="date" name="date"
                            value="{{ old('date', $class->date) }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hora inicio</label>
                        <input type="time" name="start_time"
                            value="{{ old('start_time', \Carbon\Carbon::parse($class->start_time)->format('H:i')) }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('start_time')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hora fin</label>
                        <input type="time" name="end_time"
                            value="{{ old('end_time', \Carbon\Carbon::parse($class->end_time)->format('H:i')) }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('end_time')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- PRECIO --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Precio por alumno (€)</label>
                    <input type="number" name="price"
                        value="{{ old('price', $class->price) }}"
                        min="0" step="0.50"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('price')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ESTADO --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select name="status"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="registered" {{ old('status', $class->status) == 'registered'  ? 'selected' : '' }}>Programada</option>
                        <option value="completed" {{ old('status', $class->status) == 'completed'  ? 'selected' : '' }}>Completada</option>
                        <option value="cancelled" {{ old('status', $class->status) == 'cancelled'  ? 'selected' : '' }}>Cancelada</option>
                    </select>
                    @error('status')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ALUMNOS INSCRITOS --}}
                @if($class->registered->where('status', 'registered')->count() > 0)
                <div class="mb-6 bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Alumnos inscritos</h4>
                    <div class="space-y-2">
                        @foreach($class->registered->where('status', 'registered') as $enrollment)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-700">{{ $enrollment->user->name }}</span>
                            <span class="text-gray-400">{{ $enrollment->user->email }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="flex justify-end">
                    <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                        Guardar cambios
                    </button>
                </div>

            </form>
        </div>
    </div>
</x-app-layout>