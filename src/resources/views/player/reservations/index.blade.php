<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Mis Reservas
            </h2>
            <a href="{{ route('player.reservations.create') }}"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                + Nueva Reserva
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

        {{-- TABLA DE RESERVAS --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Horario</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pista</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Precio</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($reservations as $reservation)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($reservation->start_time)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($reservation->end_time)->format('H:i') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $reservation->court->name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ number_format($reservation->total_price, 2) }}€
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($reservation->status === 'pending')
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Pendiente</span>
                            @elseif($reservation->status === 'paid')
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Pagada</span>
                            @elseif($reservation->status === 'cancelled')
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Cancelada</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($reservation->status !== 'cancelled')
                            <form action="{{ route('player.reservations.destroy', $reservation) }}"
                                method="POST" class="inline"
                                onsubmit="return confirm('¿Cancelar esta reserva?')">
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
                        <td colspan="6" class="px-6 py-4 text-center text-gray-400">
                            No tienes reservas todavía.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>