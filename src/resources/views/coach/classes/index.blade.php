<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Mis Clases
            </h2>
            <a href="{{ route('coach.classes.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                + Nueva Clase
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Clase</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Horario</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pista</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plazas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Visibilidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($classes as $class)
                        <tr>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-gray-900">{{ $class->title }}</p>
                                <p class="text-xs text-gray-400 capitalize">{{ $class->type }} · {{ $class->level }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($class->date)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $class->court->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $class->enrollments->where('status', 'enrolled')->count() }}/{{ $class->max_players }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($class->visibility === 'public')
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Pública</span>
                                @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Privada</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($class->status === 'scheduled')
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Programada</span>
                                @elseif($class->status === 'completed')
                                    <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs">Completada</span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Cancelada</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm space-x-2">
                                @if($class->status === 'scheduled')
                                    <a href="{{ route('coach.classes.edit', $class) }}"
                                       class="text-blue-600 hover:underline">Editar</a>

                                    <form action="{{ route('coach.classes.destroy', $class) }}"
                                          method="POST" class="inline"
                                          onsubmit="return confirm('¿Cancelar esta clase?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">
                                            Cancelar
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-400">
                                No has creado ninguna clase todavía.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>