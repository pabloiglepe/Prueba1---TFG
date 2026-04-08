<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mi Perfil
        </h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- MENSAJES --}}
        @if(session('success'))
        <div class="p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif

        <!-- {{-- TARJETAS RESUMEN --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white shadow rounded-lg p-5">
                <p class="text-sm text-gray-500 mb-1">Gasto en reservas</p>
                <p class="text-2xl font-bold text-blue-600">{{ number_format($totalSpentReservations, 2) }}€</p>
            </div>
            <div class="bg-white shadow rounded-lg p-5">
                <p class="text-sm text-gray-500 mb-1">Gasto en clases</p>
                <p class="text-2xl font-bold text-purple-600">{{ number_format($totalSpentClasses, 2) }}€</p>
            </div>
            <div class="bg-white shadow rounded-lg p-5">
                <p class="text-sm text-gray-500 mb-1">Gasto total</p>
                <p class="text-2xl font-bold text-green-600">{{ number_format($totalSpent, 2) }}€</p>
            </div>
        </div> -->

        {{-- TARJETAS RESUMEN SEGÚN ROL --}}
        @if($user->role->name === 'player')
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white shadow rounded-lg p-5">
                <p class="text-sm text-gray-500 mb-1">Gasto en reservas</p>
                <p class="text-2xl font-bold text-blue-600">{{ number_format($totalSpentReservations, 2) }}€</p>
            </div>
            <div class="bg-white shadow rounded-lg p-5">
                <p class="text-sm text-gray-500 mb-1">Gasto en clases</p>
                <p class="text-2xl font-bold text-purple-600">{{ number_format($totalSpentClasses, 2) }}€</p>
            </div>
            <div class="bg-white shadow rounded-lg p-5">
                <p class="text-sm text-gray-500 mb-1">Gasto total</p>
                <p class="text-2xl font-bold text-green-600">{{ number_format($totalSpent, 2) }}€</p>
            </div>
        </div>

        @elseif($user->role->name === 'coach')
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white shadow rounded-lg p-5">
                <p class="text-sm text-gray-500 mb-1">Clases creadas</p>
                <p class="text-2xl font-bold text-blue-600">{{ $coachStats['total_classes'] }}</p>
            </div>
            <div class="bg-white shadow rounded-lg p-5">
                <p class="text-sm text-gray-500 mb-1">Alumnos totales</p>
                <p class="text-2xl font-bold text-purple-600">{{ $coachStats['total_students'] }}</p>
            </div>
            <div class="bg-white shadow rounded-lg p-5">
                <p class="text-sm text-gray-500 mb-1">Ingresos generados</p>
                <p class="text-2xl font-bold text-green-600">{{ number_format($coachStats['total_revenue'], 2) }}€</p>
            </div>
        </div>
        @endif

        {{-- DATOS PERSONALES --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="font-semibold text-gray-700 mb-4">Datos personales</h3>
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('phone_number')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="text" value="{{ $user->email }}" disabled
                        class="w-full border-gray-300 rounded-lg shadow-sm bg-gray-50 text-gray-400 cursor-not-allowed">
                    <p class="text-xs text-gray-400 mt-1">El email no puede modificarse.</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
                    <input type="text" value="{{ ucfirst($user->role->name) }}" disabled
                        class="w-full border-gray-300 rounded-lg shadow-sm bg-gray-50 text-gray-400 cursor-not-allowed">
                </div>

                <div class="flex justify-between items-center">
                    <a href="{{ route('profile.export') }}"
                        class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-800 text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Exportar mis datos
                    </a>
                    <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>

        {{-- CAMBIAR CONTRASEÑA --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="font-semibold text-gray-700 mb-4">Cambiar contraseña</h3>
            <form action="{{ route('profile.password') }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="space-y-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña actual</label>
                        <input type="password" name="current_password"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('current_password')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nueva contraseña</label>
                        <input type="password" name="password"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('password')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar nueva contraseña</label>
                        <input type="password" name="password_confirmation"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                        Cambiar contraseña
                    </button>
                </div>
            </form>
        </div>

        <!-- {{-- HISTORIAL DE RESERVAS (SOLO PLAYER) --}}
        @if($user->role->name === 'player')
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="font-semibold text-gray-700 mb-4">Historial de reservas</h3>
            @if($reservations->isEmpty())
            <p class="text-gray-400 text-sm">No tienes reservas registradas.</p>
            @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Pista</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Horario</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Precio</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($reservations as $reservation)
                        <tr>
                            <td class="px-4 py-2">{{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d/m/Y') }}</td>
                            <td class="px-4 py-2">{{ $reservation->court->name }}</td>
                            <td class="px-4 py-2">
                                {{ \Carbon\Carbon::parse($reservation->start_time)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($reservation->end_time)->format('H:i') }}
                            </td>
                            <td class="px-4 py-2 font-medium text-green-600">{{ number_format($reservation->total_price, 2) }}€</td>
                            <td class="px-4 py-2">
                                @if($reservation->status === 'pending')
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Pendiente</span>
                                @elseif($reservation->status === 'paid')
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Pagada</span>
                                @else
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Cancelada</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        {{-- HISTORIAL DE CLASES COMO ALUMNO (SOLO PLAYER) --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="font-semibold text-gray-700 mb-4">Mis clases</h3>
            @if($classes->isEmpty())
            <p class="text-gray-400 text-sm">No estás inscrito en ninguna clase.</p>
            @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Clase</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Entrenador</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Pista</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Precio</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($classes as $class)
                        <tr>
                            <td class="px-4 py-2 font-medium text-gray-800">{{ $class->title }}</td>
                            <td class="px-4 py-2">{{ \Carbon\Carbon::parse($class->date)->format('d/m/Y') }}</td>
                            <td class="px-4 py-2">{{ $class->coach->name }}</td>
                            <td class="px-4 py-2">{{ $class->court->name }}</td>
                            <td class="px-4 py-2 font-medium text-green-600">{{ number_format($class->price, 2) }}€</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
        @endif

        {{-- CLASES CREADAS (SOLO COACH) --}}
        @if($user->role->name === 'coach')
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="font-semibold text-gray-700 mb-4">Mis clases creadas</h3>
            @if($user->classesByCoach->isEmpty())
            <p class="text-gray-400 text-sm">No has creado ninguna clase todavía.</p>
            @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Clase</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Pista</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Alumnos</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ingresos</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($user->classesByCoach as $class)
                        <tr>
                            <td class="px-4 py-2 font-medium text-gray-800">{{ $class->title }}</td>
                            <td class="px-4 py-2">{{ \Carbon\Carbon::parse($class->date)->format('d/m/Y') }}</td>
                            <td class="px-4 py-2">{{ $class->court->name }}</td>
                            <td class="px-4 py-2">
                                {{ $class->registered->count() }}/{{ $class->max_players }}
                            </td>
                            <td class="px-4 py-2">
                                @if($class->status === 'registered')
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Programada</span>
                                @elseif($class->status === 'completed')
                                <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs">Completada</span>
                                @else
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Cancelada</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 font-medium text-green-600">
                                {{ number_format($class->registered->count() * $class->price, 2) }}€
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
        @endif -->

        <!-- {{-- HISTORIAL DE CLASES --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="font-semibold text-gray-700 mb-4">Mis clases</h3>
            @if($classes->isEmpty())
                <p class="text-gray-400 text-sm">No estás inscrito en ninguna clase.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Clase</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Entrenador</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Pista</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Precio</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($classes as $class)
                                <tr>
                                    <td class="px-4 py-2 font-medium text-gray-800">{{ $class->title }}</td>
                                    <td class="px-4 py-2">{{ \Carbon\Carbon::parse($class->date)->format('d/m/Y') }}</td>
                                    <td class="px-4 py-2">{{ $class->coach->name }}</td>
                                    <td class="px-4 py-2">{{ $class->court->name }}</td>
                                    <td class="px-4 py-2 font-medium text-green-600">{{ number_format($class->price, 2) }}€</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div> -->

        {{-- BORRAR CUENTA --}}
        <div class="bg-white shadow rounded-lg p-6 border border-red-200">
            <h3 class="font-semibold text-red-600 mb-2">Borrar Cuenta</h3>
            <p class="text-sm text-gray-500 mb-4">
                Al eliminar tu cuenta todos tus datos serán borrados permanentemente y tus reservas pendientes serán canceladas. Esta acción no puede deshacerse.
            </p>

            <form action="{{ route('profile.destroy') }}" method="POST"
                onsubmit="return confirm('¿Estás seguro de que quieres eliminar tu cuenta? Esta acción no puede deshacerse.')">
                @csrf
                @method('DELETE')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Confirma tu contraseña para continuar
                    </label>
                    <input type="password" name="password"
                        class="w-full border-red-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500">
                    @error('password')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700">
                    Eliminar mi cuenta
                </button>
            </form>
        </div>

    </div>
</x-app-layout>