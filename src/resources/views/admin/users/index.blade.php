<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Gestión de Usuarios
            </h2>
            <a href="{{ route('admin.users.create') }}"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                + Nuevo Usuario
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- MENSAJES --}}
        @if(session('success'))
        <div class="p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="p-4 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
        @endif

        {{-- BUSCADOR --}}
        <div class="bg-white shadow rounded-lg p-4">
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex gap-4">
                <input type="hidden" name="role" value="{{ $role }}">
                <input type="text" name="search" value="{{ $search }}"
                    placeholder="Buscar por nombre o email..."
                    class="flex-1 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Buscar
                </button>
                @if($search)
                <a href="{{ route('admin.users.index', ['role' => $role]) }}"
                    class="text-sm text-gray-500 hover:underline self-center">
                    Limpiar
                </a>
                @endif
            </form>
        </div>

        {{-- TABS --}}
        <div x-data="{ tab: '{{ $role === 'coach' ? 'coach' : 'player' }}' }">

            <div class="flex border-b border-gray-200 mb-4">
                <a href="{{ route('admin.users.index', ['role' => 'player']) }}"
                    class="px-6 py-3 text-sm font-medium border-b-2 transition
                   {{ $role === 'player' || $role === 'all'
                       ? 'border-blue-600 text-blue-600'
                       : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Jugadores
                    <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-700">
                        {{ $totalPlayers }}
                    </span>
                </a>
                <a href="{{ route('admin.users.index', ['role' => 'coach']) }}"
                    class="px-6 py-3 text-sm font-medium border-b-2 transition
                   {{ $role === 'coach'
                       ? 'border-blue-600 text-blue-600'
                       : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Entrenadores
                    <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-purple-100 text-purple-700">
                        {{ $totalCoaches }}
                    </span>
                </a>
            </div>

            {{-- TABLA --}}
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teléfono</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rol</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Registro</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($users as $user)
                        @if($user->role->name !== 'admin')
                        <tr>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $user->email }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $user->phone_number }}</td>
                            <td class="px-6 py-4">
                                @if($user->role->name === 'coach')
                                <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs">Entrenador</span>
                                @else
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Jugador</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $user->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm space-x-2">
                                <a href="{{ route('admin.users.edit', $user) }}"
                                    class="text-blue-600 hover:underline">Editar</a>
                                <form action="{{ route('admin.users.destroy', $user) }}"
                                    method="POST" class="inline"
                                    onsubmit="return confirm('¿Eliminar este usuario?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endif
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-400">
                                No hay usuarios registrados.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>