<x-app-layout>
    <x-slot name="header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 20px; font-weight: 600; color: #2d3b2d; margin: 0;">
                Gestión de Usuarios
            </h2>
            <a href="{{ route('admin.users.create') }}"
               style="display: inline-flex; align-items: center; gap: 8px; background: #6b8f6b; color: #fff; font-size: 14px; font-weight: 500; padding: 9px 18px; border-radius: 8px; text-decoration: none;">
                <iconify-icon icon="ph:user-plus-light" style="font-size: 16px;"></iconify-icon>
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
                        <iconify-icon icon="ph:magnifying-glass-light" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:16px;color:#9aaa9a;pointer-events:none;"></iconify-icon>
                        <input type="text" name="search" value="{{ $search }}"
                               placeholder="Buscar por nombre o email..."
                               style="width: 100%; padding: 9px 12px 9px 38px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; box-sizing: border-box;">
                    </div>
                    <button type="submit"
                            style="display: inline-flex; align-items: center; gap: 6px; background: #6b8f6b; color: #fff; font-size: 14px; font-weight: 500; padding: 9px 18px; border-radius: 8px; border: none; cursor: pointer;">
                        <iconify-icon icon="ph:magnifying-glass-light" style="font-size: 15px;"></iconify-icon>
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
                    <iconify-icon icon="ph:user-light" style="font-size: 15px; flex-shrink: 0;"></iconify-icon>
                    Jugadores
                    <span style="padding: 2px 8px; background: {{ $role === 'player' ? '#e8f0e8' : '#f7f8f5' }}; color: {{ $role === 'player' ? '#4a6b4a' : '#7a8a7a' }}; border-radius: 20px; font-size: 12px;">
                        {{ $totalPlayers }}
                    </span>
                </a>
                <a href="{{ route('admin.users.index', ['role' => 'coach']) }}"
                   style="display: inline-flex; align-items: center; gap: 8px; padding: 14px 16px; font-size: 14px; font-weight: 500; text-decoration: none; border-bottom: 2px solid {{ $role === 'coach' ? '#6b8f6b' : 'transparent' }}; color: {{ $role === 'coach' ? '#4a6b4a' : '#7a8a7a' }}; margin-bottom: -1px;">
                    <iconify-icon icon="ph:users-light" style="font-size: 15px; flex-shrink: 0;"></iconify-icon>
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
                                        <iconify-icon icon="ph:circle-fill" style="font-size: 8px; color: #6b4a8f;"></iconify-icon>
                                        Entrenador
                                    </span>
                                @else
                                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #e8f0e8; color: #4a6b4a; border-radius: 20px; font-size: 12px; font-weight: 500;">
                                        <iconify-icon icon="ph:circle-fill" style="font-size: 8px; color: #4a6b4a;"></iconify-icon>
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
                                        <iconify-icon icon="ph:pencil-simple-light" style="font-size: 14px;"></iconify-icon>
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
                                            <iconify-icon icon="ph:trash-light" style="font-size: 14px;"></iconify-icon>
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