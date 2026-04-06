<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Editar Pista: {{ $court->name }}
            </h2>
            <a href="{{ route('admin.courts.index') }}"
               class="text-sm text-gray-600 hover:underline">
                ← Volver al listado
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6">

            <form action="{{ route('admin.courts.update', $court) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- NOMBRE --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nombre de la pista
                    </label>
                    <input type="text" name="name"
                           value="{{ old('name', $court->name) }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- TIPO --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tipo
                    </label>
                    <select name="type"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="cristal" {{ old('type', $court->type) == 'cristal' ? 'selected' : '' }}>Cristal</option>
                        <option value="muro"    {{ old('type', $court->type) == 'muro'    ? 'selected' : '' }}>Muro</option>
                    </select>
                    @error('type')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- SUPERFICIE --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Superficie
                    </label>
                    <select name="surface"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="cesped"  {{ old('surface', $court->surface) == 'cesped'  ? 'selected' : '' }}>Césped</option>
                        <option value="cemento" {{ old('surface', $court->surface) == 'cemento' ? 'selected' : '' }}>Cemento</option>
                    </select>
                    @error('surface')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ESTADO --}}
                <div class="mb-6">
                    <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', $court->is_active) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600">
                        Pista activa
                    </label>
                </div>

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