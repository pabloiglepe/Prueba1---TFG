<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Editar Usuario: {{ $user->name }}
            </h2>
            <a href="{{ route('admin.users.index') }}"
               class="text-sm text-gray-600 hover:underline">
                ← Volver al listado
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6">
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- NOMBRE --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- EMAIL --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="text" value="{{ $user->email }}" disabled
                           class="w-full border-gray-300 rounded-lg shadow-sm bg-gray-50 text-gray-400 cursor-not-allowed">
                    <p class="text-xs text-gray-400 mt-1">El email no puede modificarse.</p>
                </div>

                {{-- TELÉFONO --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('phone_number')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ROL --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
                    <select name="role_id"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @foreach($roles->where('name', '!=', 'admin') as $role)
                            <option value="{{ $role->id }}"
                                    {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                {{ $role->name === 'coach' ? 'Entrenador' : 'Jugador' }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ESTADÍSTICAS DEL USUARIO --}}
                <div class="mb-6 bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Estadísticas</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">Reservas totales</p>
                            <p class="font-semibold text-gray-800">{{ $user->reservations->count() }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Gasto total</p>
                            <p class="font-semibold text-green-600">
                                {{ number_format($user->reservations->where('status', '!=', 'cancelled')->sum('total_price'), 2) }}€
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500">Miembro desde</p>
                            <p class="font-semibold text-gray-800">{{ $user->created_at->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">RGPD</p>
                            <p class="font-semibold {{ $user->rgpd_consent ? 'text-green-600' : 'text-red-600' }}">
                                {{ $user->rgpd_consent ? 'Aceptado' : 'No aceptado' }}
                            </p>
                        </div>
                    </div>
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