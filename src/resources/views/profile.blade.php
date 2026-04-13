<x-app-layout>
    <x-slot name="header">
        <h2 style="font-size: 20px; font-weight: 600; color: #2d3b2d; margin: 0;">
            Mi Perfil
        </h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8">

        {{-- MENSAJES --}}
        @if(session('success'))
        <div style="margin-bottom: 20px; padding: 14px 18px; background: #e8f0e8; color: #4a6b4a; border-radius: 8px; font-size: 14px; border-left: 3px solid #6b8f6b;">
            {{ session('success') }}
        </div>
        @endif

        {{-- TABS --}}
        <div x-data="{ tab: 'perfil' }">

            {{-- NAVEGACIÓN DE TABS --}}
            <div style="display: inline-flex; align-items: center; gap: 15px; padding: 14px 40px; font-size: 14px; font-weight: 500; background: none; border-top: none; border-left: none; border-right: none; cursor: pointer; margin-bottom: -1px;">
                <button @click="tab = 'perfil'"
                    :style="tab === 'perfil' ? 'border-bottom: 2px solid #6b8f6b; color: #4a6b4a;' : 'border-bottom: 2px solid transparent; color: #7a8a7a;'"
                    style="display: inline-flex; align-items: center; gap: 8px; padding: 14px 40px; font-size: 14px; font-weight: 500; background: none; border-top: none; border-left: none; border-right: none; cursor: pointer; margin-bottom: -1px;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:15px;height:15px;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                    Perfil
                </button>
                @if($user->role->name === 'player' || $user->role->name === 'coach')
                <button @click="tab = 'historial'"
                    :style="tab === 'historial' ? 'border-bottom: 2px solid #6b8f6b; color: #4a6b4a;' : 'border-bottom: 2px solid transparent; color: #7a8a7a;'"
                    style="display: inline-flex; align-items: center; gap: 8px; padding: 14px 40px; font-size: 14px; font-weight: 500; background: none; border-top: none; border-left: none; border-right: none; cursor: pointer; margin-bottom: -1px;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:15px;height:15px;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 2.994v2.25m10.5-2.25v2.25m-14.252 13.5V7.491a2.25 2.25 0 0 1 2.25-2.25h13.5a2.25 2.25 0 0 1 2.25 2.25v11.251m-18 0a2.25 2.25 0 0 0 2.25 2.25h13.5a2.25 2.25 0 0 0 2.25-2.25m-18 0v-7.5a2.25 2.25 0 0 1 2.25-2.25h13.5a2.25 2.25 0 0 1 2.25 2.25v7.5m-6.75-6h2.25m-9 2.25h4.5m.002-2.25h.005v.006H12v-.006Zm-.001 4.5h.006v.006h-.006v-.005Zm-2.25.001h.005v.006H9.75v-.006Zm-2.25 0h.005v.005h-.006v-.005Zm6.75-2.247h.005v.005h-.005v-.005Zm0 2.247h.006v.006h-.006v-.006Zm2.25-2.248h.006V15H16.5v-.005Z" />
                    </svg>

                    Historial
                </button>
                @endif
                <button @click="tab = 'seguridad'"
                    :style="tab === 'seguridad' ? 'border-bottom: 2px solid #6b8f6b; color: #4a6b4a;' : 'border-bottom: 2px solid transparent; color: #7a8a7a;'"
                    style="display: inline-flex; align-items: center; gap: 8px; padding: 14px 40px; font-size: 14px; font-weight: 500; background: none; border-top: none; border-left: none; border-right: none; cursor: pointer; margin-bottom: -1px;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:15px;height:15px;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                    </svg>
                    Seguridad
                </button>
            </div>

            {{-- TAB 1: PERFIL --}}
            <div x-show="tab === 'perfil'" class="space-y-6">

                {{-- TARJETAS RESUMEN SEGÚN ROL --}}
                @if($user->role->name === 'player')
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
                    <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 20px;">
                        <p style="font-size: 12px; color: #7a8a7a; margin: 0 0 6px;">Gasto en reservas</p>
                        <p style="font-size: 26px; font-weight: 600; color: #2d3b2d; margin: 0;">{{ number_format($totalSpentReservations, 2) }}€</p>
                    </div>
                    <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 20px;">
                        <p style="font-size: 12px; color: #7a8a7a; margin: 0 0 6px;">Gasto en clases</p>
                        <p style="font-size: 26px; font-weight: 600; color: #2d3b2d; margin: 0;">{{ number_format($totalSpentClasses, 2) }}€</p>
                    </div>
                    <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 20px;">
                        <p style="font-size: 12px; color: #7a8a7a; margin: 0 0 6px;">Gasto total</p>
                        <p style="font-size: 26px; font-weight: 600; color: #6b8f6b; margin: 0;">{{ number_format($totalSpent, 2) }}€</p>
                    </div>
                </div>

                @elseif($user->role->name === 'coach')
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
                    <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 20px;">
                        <p style="font-size: 12px; color: #7a8a7a; margin: 0 0 6px;">Clases creadas</p>
                        <p style="font-size: 26px; font-weight: 600; color: #2d3b2d; margin: 0;">{{ $coachStats['total_classes'] }}</p>
                    </div>
                    <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 20px;">
                        <p style="font-size: 12px; color: #7a8a7a; margin: 0 0 6px;">Alumnos totales</p>
                        <p style="font-size: 26px; font-weight: 600; color: #2d3b2d; margin: 0;">{{ $coachStats['total_students'] }}</p>
                    </div>
                    <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 20px;">
                        <p style="font-size: 12px; color: #7a8a7a; margin: 0 0 6px;">Ingresos generados</p>
                        <p style="font-size: 26px; font-weight: 600; color: #6b8f6b; margin: 0;">{{ number_format($coachStats['total_revenue'], 2) }}€</p>
                    </div>
                </div>
                @endif

                {{-- DATOS PERSONALES --}}
                <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 28px;">
                    <p style="font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 20px;">Datos personales</p>
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PATCH')

                        {{-- NOMBRE Y TELÉFONO --}}
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                            <div>
                                <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">Nombre</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                    style="width: 100%; padding: 9px 12px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; box-sizing: border-box;"
                                    onfocus="this.style.borderColor='#6b8f6b'"
                                    onblur="this.style.borderColor='#d4d9cc'">
                                @error('name')
                                <p style="color: #c0625e; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">Teléfono</label>
                                <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}"
                                    style="width: 100%; padding: 9px 12px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; box-sizing: border-box;"
                                    onfocus="this.style.borderColor='#6b8f6b'"
                                    onblur="this.style.borderColor='#d4d9cc'">
                                @error('phone_number')
                                <p style="color: #c0625e; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- EMAIL --}}
                        <div style="margin-bottom: 16px;">
                            <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">Email</label>
                            <input type="text" value="{{ $user->email }}" disabled
                                style="width: 100%; padding: 9px 12px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; color: #9aaa9a; background: #f7f8f5; cursor: not-allowed; box-sizing: border-box;">
                            <p style="font-size: 11px; color: #9aaa9a; margin: 4px 0 0;">El email no puede modificarse.</p>
                        </div>

                        {{-- ROL --}}
                        <div style="margin-bottom: 24px;">
                            <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">Rol</label>
                            <input type="text" value="{{ ucfirst($user->role->name === 'coach' ? 'Entrenador' : ($user->role->name === 'player' ? 'Jugador' : 'Administrador')) }}" disabled
                                style="width: 100%; padding: 9px 12px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; color: #9aaa9a; background: #f7f8f5; cursor: not-allowed; box-sizing: border-box;">
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <a href="{{ route('profile.export') }}"
                                style="display: inline-flex; align-items: center; gap: 8px; background: #6b8f6b; color: #fff; font-size: 14px; font-weight: 500; padding: 9px 18px; border-radius: 8px; text-decoration: none;"
                                onmouseover="this.style.background='#4a6b4a'"
                                onmouseout="this.style.background='#6b8f6b'">
                                <svg xmlns="http://www.w3.org/2000/svg" style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Exportar mis datos de perfil
                            </a>
                            <button type="submit"
                                style="display: inline-flex; align-items: center; gap: 8px; background: #6b8f6b; color: #fff; font-size: 14px; font-weight: 500; padding: 9px 22px; border-radius: 8px; border: none; cursor: pointer;"
                                onmouseover="this.style.background='#4a6b4a'"
                                onmouseout="this.style.background='#6b8f6b'">
                                <svg xmlns="http://www.w3.org/2000/svg" style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                                    <polyline points="17 21 17 13 7 13 7 21" />
                                    <polyline points="7 3 7 8 15 8" />
                                </svg>
                                Guardar cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- TAB 2: HISTORIAL --}}
            <div x-show="tab === 'historial'" class="space-y-6">
                {{-- HISTORIAL DE RESERVAS (SOLO PLAYER) --}}
                @if($user->role->name === 'player')
                <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; overflow: hidden;">
                    <div style="padding: 20px 24px; border-bottom: 0.5px solid #f0f3ee;">
                        <p style="font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">Historial de reservas</p>
                    </div>
                    @if($reservations->isEmpty())
                    <div style="padding: 40px; text-align: center; font-size: 14px; color: #9aaa9a;">
                        No tienes reservas registradas.
                    </div>
                    @else
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f7f8f5;">
                                <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Fecha</th>
                                <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Pista</th>
                                <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Horario</th>
                                <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Precio</th>
                                <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reservations as $reservation)
                            <tr style="border-top: 0.5px solid #f0f3ee;" onmouseover="this.style.background='#fafbf9'" onmouseout="this.style.background='#fff'">
                                <td style="padding: 14px 20px; font-size: 14px; color: #2d3b2d;">
                                    {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d/m/Y') }}
                                </td>
                                <td style="padding: 14px 20px; font-size: 14px; color: #5a6b5a;">{{ $reservation->court->name }}</td>
                                <td style="padding: 14px 20px; font-size: 14px; color: #5a6b5a;">
                                    {{ \Carbon\Carbon::parse($reservation->start_time)->format('H:i') }} —
                                    {{ \Carbon\Carbon::parse($reservation->end_time)->format('H:i') }}
                                </td>
                                <td style="padding: 14px 20px; font-size: 14px; font-weight: 500; color: #6b8f6b;">
                                    {{ number_format($reservation->total_price, 2) }}€
                                </td>
                                <td style="padding: 14px 20px;">
                                    @if($reservation->status === 'pending')
                                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #fef9e8; color: #92650a; border-radius: 20px; font-size: 12px; font-weight: 500;">Pendiente</span>
                                    @elseif($reservation->status === 'paid')
                                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #e8f0e8; color: #4a6b4a; border-radius: 20px; font-size: 12px; font-weight: 500;">Pagada</span>
                                    @else
                                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #fce8e8; color: #9b4444; border-radius: 20px; font-size: 12px; font-weight: 500;">Cancelada</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>

                {{-- HISTORIAL DE CLASES COMO ALUMNO --}}
                <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; overflow: hidden;">
                    <div style="padding: 20px 24px; border-bottom: 0.5px solid #f0f3ee;">
                        <p style="font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">Mis clases</p>
                    </div>
                    @if($classes->isEmpty())
                    <div style="padding: 40px; text-align: center; font-size: 14px; color: #9aaa9a;">
                        No estás inscrito en ninguna clase.
                    </div>
                    @else
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f7f8f5;">
                                <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Clase</th>
                                <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Fecha</th>
                                <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Entrenador</th>
                                <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Pista</th>
                                <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Precio</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($classes as $class)
                            <tr style="border-top: 0.5px solid #f0f3ee;" onmouseover="this.style.background='#fafbf9'" onmouseout="this.style.background='#fff'">
                                <td style="padding: 14px 20px; font-size: 14px; font-weight: 500; color: #2d3b2d;">{{ $class->title }}</td>
                                <td style="padding: 14px 20px; font-size: 14px; color: #5a6b5a;">{{ \Carbon\Carbon::parse($class->date)->format('d/m/Y') }}</td>
                                <td style="padding: 14px 20px; font-size: 14px; color: #5a6b5a;">{{ $class->coach->name }}</td>
                                <td style="padding: 14px 20px; font-size: 14px; color: #5a6b5a;">{{ $class->court->name }}</td>
                                <td style="padding: 14px 20px; font-size: 14px; font-weight: 500; color: #6b8f6b;">{{ number_format($class->price, 2) }}€</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
                @endif

                {{-- CLASES CREADAS (SOLO COACH) --}}
                @if($user->role->name === 'coach')
                <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; overflow: hidden;">
                    <div style="padding: 20px 24px; border-bottom: 0.5px solid #f0f3ee;">
                        <p style="font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">Mis clases creadas</p>
                    </div>
                    @if($user->classesByCoach->isEmpty())
                    <div style="padding: 40px; text-align: center; font-size: 14px; color: #9aaa9a;">
                        No has creado ninguna clase todavía.
                    </div>
                    @else
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f7f8f5;">
                                <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Clase</th>
                                <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Fecha</th>
                                <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Pista</th>
                                <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Alumnos</th>
                                <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Estado</th>
                                <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Ingresos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->classesByCoach as $class)
                            <tr style="border-top: 0.5px solid #f0f3ee;" onmouseover="this.style.background='#fafbf9'" onmouseout="this.style.background='#fff'">
                                <td style="padding: 14px 20px; font-size: 14px; font-weight: 500; color: #2d3b2d;">{{ $class->title }}</td>
                                <td style="padding: 14px 20px; font-size: 14px; color: #5a6b5a;">{{ \Carbon\Carbon::parse($class->date)->format('d/m/Y') }}</td>
                                <td style="padding: 14px 20px; font-size: 14px; color: #5a6b5a;">{{ $class->court->name }}</td>
                                <td style="padding: 14px 20px; font-size: 14px; color: #5a6b5a;">
                                    {{ $class->registered->count() }}/{{ $class->max_players }}
                                </td>
                                <td style="padding: 14px 20px;">
                                    @if($class->status === 'registered')
                                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #e8f0e8; color: #4a6b4a; border-radius: 20px; font-size: 12px; font-weight: 500;">Programada</span>
                                    @elseif($class->status === 'completed')
                                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #f0eaf8; color: #6b4a8f; border-radius: 20px; font-size: 12px; font-weight: 500;">Completada</span>
                                    @else
                                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #fce8e8; color: #9b4444; border-radius: 20px; font-size: 12px; font-weight: 500;">Cancelada</span>
                                    @endif
                                </td>
                                <td style="padding: 14px 20px; font-size: 14px; font-weight: 500; color: #6b8f6b;">
                                    {{ number_format($class->registered->count() * $class->price, 2) }}€
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
                @endif

            </div>

            {{-- TAB 3: SEGURIDAD --}}
            <div x-show="tab === 'seguridad'" class="space-y-6">

                {{-- CAMBIAR CONTRASEÑA --}}
                <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 28px;">
                    <p style="font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 20px;">Cambiar contraseña</p>
                    <form action="{{ route('profile.password') }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 24px;">

                            {{-- CONTRASEÑA ACTUAL --}}
                            <div>
                                <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">Contraseña actual</label>
                                <div x-data="{ show: false }" style="position: relative;">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 15px; height: 15px; stroke: #9aaa9a;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                    </svg>
                                    <input placeholder="*******" :type="show ? 'text' : 'password'" name="current_password"
                                        style="width: 100%; padding: 9px 38px 9px 36px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; box-sizing: border-box;"
                                        onfocus="this.style.borderColor='#6b8f6b'"
                                        onblur="this.style.borderColor='#d4d9cc'">
                                    <button type="button" @click="show = !show"
                                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding: 2px; color: #9aaa9a;"
                                        onmouseover="this.style.color='#5a6b5a'"
                                        onmouseout="this.style.color='#9aaa9a'">
                                        <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                        <svg x-show="show" xmlns="http://www.w3.org/2000/svg" style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" />
                                            <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" />
                                            <line x1="1" y1="1" x2="23" y2="23" />
                                        </svg>
                                    </button>
                                </div>
                                @error('current_password')
                                <p style="color: #c0625e; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- NUEVA CONTRASEÑA --}}
                            <div>
                                <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">Nueva contraseña</label>
                                <div x-data="{ show: false }" style="position: relative;">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 15px; height: 15px; stroke: #9aaa9a;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                    </svg>
                                    <input placeholder="*******" :type="show ? 'text' : 'password'" name="password"
                                        style="width: 100%; padding: 9px 38px 9px 36px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; box-sizing: border-box;"
                                        onfocus="this.style.borderColor='#6b8f6b'"
                                        onblur="this.style.borderColor='#d4d9cc'">
                                    <button type="button" @click="show = !show"
                                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding: 2px; color: #9aaa9a;"
                                        onmouseover="this.style.color='#5a6b5a'"
                                        onmouseout="this.style.color='#9aaa9a'">
                                        <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                        <svg x-show="show" xmlns="http://www.w3.org/2000/svg" style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" />
                                            <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" />
                                            <line x1="1" y1="1" x2="23" y2="23" />
                                        </svg>
                                    </button>
                                </div>
                                @error('password')
                                <p style="color: #c0625e; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- CONFIRMAR NUEVA CONTRASEÑA --}}
                            <div>
                                <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">Confirmar nueva contraseña</label>
                                <div x-data="{ show: false }" style="position: relative;">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 15px; height: 15px; stroke: #9aaa9a;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                    </svg>
                                    <input placeholder="*******" :type="show ? 'text' : 'password'" name="password_confirmation"
                                        style="width: 100%; padding: 9px 38px 9px 36px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; box-sizing: border-box;"
                                        onfocus="this.style.borderColor='#6b8f6b'"
                                        onblur="this.style.borderColor='#d4d9cc'">
                                    <button type="button" @click="show = !show"
                                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding: 2px; color: #9aaa9a;"
                                        onmouseover="this.style.color='#5a6b5a'"
                                        onmouseout="this.style.color='#9aaa9a'">
                                        <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                        <svg x-show="show" xmlns="http://www.w3.org/2000/svg" style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" />
                                            <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" />
                                            <line x1="1" y1="1" x2="23" y2="23" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                        </div>

                        <div style="display: flex; justify-content: flex-end;">
                            <button type="submit"
                                style="display: inline-flex; align-items: center; gap: 8px; background: #6b8f6b; color: #fff; font-size: 14px; font-weight: 500; padding: 9px 22px; border-radius: 8px; border: none; cursor: pointer;"
                                onmouseover="this.style.background='#4a6b4a'"
                                onmouseout="this.style.background='#6b8f6b'">
                                <svg xmlns="http://www.w3.org/2000/svg" style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                </svg>
                                Cambiar contraseña
                            </button>
                        </div>
                    </form>
                </div>

                {{-- BORRAR CUENTA --}}
                <div style="background: #fff; border-radius: 12px; border: 0.5px solid #f0c4c4; padding: 28px;">
                    <p style="font-size: 11px; font-weight: 600; color: #c0625e; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 8px;">Zona de peligro</p>
                    <p style="font-size: 14px; color: #7a8a7a; margin: 0 0 20px; line-height: 1.6;">
                        Al eliminar tu cuenta todos tus datos serán borrados permanentemente y tus reservas pendientes serán canceladas. Esta acción no puede deshacerse.
                    </p>
                    <form action="{{ route('profile.destroy') }}" method="POST"
                        onsubmit="return confirm('¿Estás seguro de que quieres eliminar tu cuenta? Esta acción no puede deshacerse.')">
                        @csrf
                        @method('DELETE')

                        <div style="margin-bottom: 20px; max-width: 360px;">
                            <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">
                                Confirma tu contraseña para continuar
                            </label>
                            <div x-data="{ show: false }" style="position: relative;">
                                <svg xmlns="http://www.w3.org/2000/svg" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 15px; height: 15px; stroke: #9aaa9a;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                </svg>
                                <input type="password" name="password" placeholder="*******"
                                    style="width: 100%; padding: 9px 38px 9px 36px; border: 0.5px solid #f0c4c4; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; box-sizing: border-box;"
                                    onfocus="this.style.borderColor='#c0625e'"
                                    onblur="this.style.borderColor='#f0c4c4'">
                            </div>
                            @error('password')
                            <p style="color: #c0625e; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                            style="display: inline-flex; align-items: center; gap: 8px; background: #c0625e; color: #fff; font-size: 14px; font-weight: 500; padding: 9px 20px; border-radius: 8px; border: none; cursor: pointer;"
                            onmouseover="this.style.background='#9b4444'"
                            onmouseout="this.style.background='#c0625e'">
                            <svg xmlns="http://www.w3.org/2000/svg" style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <polyline points="3 6 5 6 21 6" />
                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                                <path d="M10 11v6M14 11v6" />
                                <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" />
                            </svg>
                            Eliminar mi cuenta
                        </button>
                    </form>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>