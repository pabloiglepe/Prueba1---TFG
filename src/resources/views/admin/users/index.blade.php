<x-app-layout>
    <x-slot name="header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 20px; font-weight: 600; color: #2d3b2d; margin: 0;">
                Gestión de Usuarios
            </h2>
            <a href="{{ route('admin.users.create') }}"
               style="display: inline-flex; align-items: center; gap: 8px; background: #6b8f6b; color: #fff; font-size: 14px; font-weight: 500; padding: 9px 18px; border-radius: 8px; text-decoration: none;">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <line x1="19" y1="8" x2="19" y2="14"/>
                    <line x1="22" y1="11" x2="16" y2="11"/>
                </svg>
                Nuevo Usuario
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

        {{-- MENSAJES --}}
        @if(session('success'))
        <div style="padding: 14px 18px; background: #e8f0e8; color: #4a6b4a; border-radius: 8px; font-size: 14px; border-left: 3px solid #6b8f6b;">
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div style="padding: 14px 18px; background: #fce8e8; color: #9b4444; border-radius: 8px; font-size: 14px; border-left: 3px solid #c0625e;">
            {{ session('error') }}
        </div>
        @endif

        {{-- BUSCADOR + TABS --}}
        <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; overflow: hidden;">

            {{-- BUSCADOR --}}
            <div style="padding: 16px 20px; border-bottom: 0.5px solid #f0f3ee;">
                <form method="GET" action="{{ route('admin.users.index') }}" style="display: flex; gap: 10px;">
                    <input type="hidden" name="role" value="{{ $role }}">
                    <div style="flex: 1; position: relative;">
                        <svg xmlns="http://www.w3.org/2000/svg" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);width:16px;height:16px;stroke:#9aaa9a;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input type="text" name="search" value="{{ $search }}"
                               placeholder="Buscar por nombre o email..."
                               style="width: 100%; padding: 9px 12px 9px 38px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; box-sizing: border-box;">
                    </div>
                    <button type="submit"
                            style="display: inline-flex; align-items: center; gap: 6px; background: #6b8f6b; color: #fff; font-size: 14px; font-weight: 500; padding: 9px 18px; border-radius: 8px; border: none; cursor: pointer;">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        Buscar
                    </button>
                    @if($search)
                        <a href="{{ route('admin.users.index', ['role' => $role]) }}"
                           style="display: inline-flex; align-items: center; font-size: 13px; color: #9aaa9a; text-decoration: none; padding: 0 8px;">
                            Limpiar
                        </a>
                    @endif
                </form>
            </div>

            {{-- TABS --}}
            <div style="display: flex; border-bottom: 0.5px solid #d4d9cc; padding: 0 20px;">
                <a href="{{ route('admin.users.index', ['role' => 'player']) }}"
                   style="display: inline-flex; align-items: center; gap: 8px; padding: 14px 16px; font-size: 14px; font-weight: 500; text-decoration: none; border-bottom: 2px solid {{ $role === 'player' ? '#6b8f6b' : 'transparent' }}; color: {{ $role === 'player' ? '#4a6b4a' : '#7a8a7a' }}; margin-bottom: -1px;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    </svg>
                    Jugadores
                    <span style="padding: 2px 8px; background: {{ $role === 'player' ? '#e8f0e8' : '#f7f8f5' }}; color: {{ $role === 'player' ? '#4a6b4a' : '#7a8a7a' }}; border-radius: 20px; font-size: 12px;">
                        {{ $totalPlayers }}
                    </span>
                </a>
                <a href="{{ route('admin.users.index', ['role' => 'coach']) }}"
                   style="display: inline-flex; align-items: center; gap: 8px; padding: 14px 16px; font-size: 14px; font-weight: 500; text-decoration: none; border-bottom: 2px solid {{ $role === 'coach' ? '#6b8f6b' : 'transparent' }}; color: {{ $role === 'coach' ? '#4a6b4a' : '#7a8a7a' }}; margin-bottom: -1px;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    Entrenadores
                    <span style="padding: 2px 8px; background: {{ $role === 'coach' ? '#e8f0e8' : '#f7f8f5' }}; color: {{ $role === 'coach' ? '#4a6b4a' : '#7a8a7a' }}; border-radius: 20px; font-size: 12px;">
                        {{ $totalCoaches }}
                    </span>
                </a>
            </div>

            {{-- TABLA --}}
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f7f8f5;">
                        <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Usuario</th>
                        <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Email</th>
                        <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Teléfono</th>
                        <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Rol</th>
                        <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Registro</th>
                        <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        @if($user->role->name !== 'admin')
                        <tr style="border-top: 0.5px solid #f0f3ee;" onmouseover="this.style.background='#fafbf9'" onmouseout="this.style.background='#fff'">

                            {{-- AVATAR + NOMBRE --}}
                            <td style="padding: 14px 20px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 34px; height: 34px; border-radius: 50%; background: #e8f0e8; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 600; color: #4a6b4a; flex-shrink: 0;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <span style="font-size: 14px; font-weight: 500; color: #2d3b2d;">{{ $user->name }}</span>
                                </div>
                            </td>

                            <td style="padding: 14px 20px; font-size: 14px; color: #5a6b5a;">{{ $user->email }}</td>
                            <td style="padding: 14px 20px; font-size: 14px; color: #5a6b5a;">{{ $user->phone_number }}</td>

                            <td style="padding: 14px 20px;">
                                @if($user->role->name === 'coach')
                                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #f0eaf8; color: #6b4a8f; border-radius: 20px; font-size: 12px; font-weight: 500;">
                                        <svg xmlns="http://www.w3.org/2000/svg" style="width:10px;height:10px;fill:#6b4a8f;" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>
                                        Entrenador
                                    </span>
                                @else
                                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #e8f0e8; color: #4a6b4a; border-radius: 20px; font-size: 12px; font-weight: 500;">
                                        <svg xmlns="http://www.w3.org/2000/svg" style="width:10px;height:10px;fill:#4a6b4a;" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>
                                        Jugador
                                    </span>
                                @endif
                            </td>

                            <td style="padding: 14px 20px; font-size: 14px; color: #7a8a7a;">
                                {{ $user->created_at->format('d/m/Y') }}
                            </td>

                            <td style="padding: 14px 20px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       style="display: inline-flex; align-items: center; gap: 5px; font-size: 13px; color: #6b8f6b; font-weight: 500; text-decoration: none;"
                                       onmouseover="this.style.color='#4a6b4a'"
                                       onmouseout="this.style.color='#6b8f6b'">
                                        <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                        </svg>
                                        Editar
                                    </a>
                                    <form action="{{ route('admin.users.destroy', $user) }}"
                                          method="POST" class="inline"
                                          onsubmit="return confirm('¿Eliminar este usuario?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                style="display: inline-flex; align-items: center; gap: 5px; font-size: 13px; color: #c0625e; font-weight: 500; background: none; border: none; cursor: pointer; padding: 0;"
                                                onmouseover="this.style.color='#9b4444'"
                                                onmouseout="this.style.color='#c0625e'">
                                            <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <polyline points="3 6 5 6 21 6"/>
                                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                                <path d="M10 11v6M14 11v6"/>
                                                <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                            </svg>
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 48px 20px; text-align: center; font-size: 14px; color: #9aaa9a;">
                                No hay usuarios registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>