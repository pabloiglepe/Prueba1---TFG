<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Gestión de Pistas
            </h2>
            <a href="{{ route('admin.courts.create') }}"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                + Nueva Pista
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- MENSAJE DE ÉXITO --}}
        @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
        </div>
        @endif

        {{-- TABLA DE PISTAS --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Superficie</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($courts as $court)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $court->id }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $court->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 capitalize">{{ $court->type }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 capitalize">{{ $court->surface }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if($court->is_active)
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Activa</span>
                            @else
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Inactiva</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm space-x-2">
                            <a href="{{ route('admin.courts.edit', $court) }}"
                                class="text-blue-600 hover:underline">Editar</a>

                            <form action="{{ route('admin.courts.destroy', $court) }}"
                                method="POST" class="inline"
                                onsubmit="return confirm('¿Eliminar esta pista?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-400">
                            No hay pistas registradas todavía.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>