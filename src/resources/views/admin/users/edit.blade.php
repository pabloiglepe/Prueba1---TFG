<x-app-layout>
    <x-slot name="header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 20px; font-weight: 600; color: #2d3b2d; margin: 0;">
                Editar Usuario: <span style="color: #6b8f6b;">{{ $user->name }}</span>
            </h2>
            <a href="{{ route('admin.users.index') }}"
               style="display: inline-flex; align-items: center; gap: 6px; font-size: 14px; color: #5a6b5a; text-decoration: none;"
               onmouseover="this.style.color='#2d3b2d'"
               onmouseout="this.style.color='#5a6b5a'">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <line x1="19" y1="12" x2="5" y2="12"/>
                    <polyline points="12 19 5 12 12 5"/>
                </svg>
                Volver al listado
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 28px;">
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- NOMBRE Y EMAIL EN GRID --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
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
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">Email</label>
                        <input type="text" value="{{ $user->email }}" disabled
                               style="width: 100%; padding: 9px 12px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; color: #9aaa9a; background: #f7f8f5; cursor: not-allowed; box-sizing: border-box;">
                        <p style="font-size: 11px; color: #9aaa9a; margin: 4px 0 0;">El email no puede modificarse.</p>
                    </div>
                </div>

                {{-- TELÉFONO Y ROL EN GRID --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
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
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">Rol</label>
                        <select name="role_id"
                                style="width: 100%; padding: 9px 12px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; background: #fff; box-sizing: border-box;"
                                onfocus="this.style.borderColor='#6b8f6b'"
                                onblur="this.style.borderColor='#d4d9cc'">
                            @foreach($roles->where('name', '!=', 'admin') as $role)
                                <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                    {{ $role->name === 'coach' ? 'Entrenador' : 'Jugador' }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <p style="color: #c0625e; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- ESTADÍSTICAS --}}
                <div style="background: #f7f8f5; border-radius: 10px; border: 0.5px solid #d4d9cc; padding: 18px; margin-bottom: 24px;">
                    <p style="font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 14px;">Estadísticas</p>

                    @if($user->role->name === 'coach')
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div style="background: #fff; border-radius: 8px; padding: 14px; border: 0.5px solid #e8ede8;">
                            <p style="font-size: 12px; color: #7a8a7a; margin: 0 0 4px;">Clases creadas</p>
                            <p style="font-size: 22px; font-weight: 600; color: #2d3b2d; margin: 0;">{{ $user->classesByCoach->count() }}</p>
                        </div>
                        <div style="background: #fff; border-radius: 8px; padding: 14px; border: 0.5px solid #e8ede8;">
                            <p style="font-size: 12px; color: #7a8a7a; margin: 0 0 4px;">Ingresos generados</p>
                            <p style="font-size: 22px; font-weight: 600; color: #6b8f6b; margin: 0;">{{ number_format($user->classesByCoach->sum('price'), 2) }}€</p>
                        </div>
                        <div style="background: #fff; border-radius: 8px; padding: 14px; border: 0.5px solid #e8ede8;">
                            <p style="font-size: 12px; color: #7a8a7a; margin: 0 0 4px;">Miembro desde</p>
                            <p style="font-size: 15px; font-weight: 600; color: #2d3b2d; margin: 0;">{{ $user->created_at->format('d/m/Y') }}</p>
                        </div>
                        <div style="background: #fff; border-radius: 8px; padding: 14px; border: 0.5px solid #e8ede8;">
                            <p style="font-size: 12px; color: #7a8a7a; margin: 0 0 4px;">RGPD</p>
                            <p style="font-size: 15px; font-weight: 600; color: {{ $user->rgpd_consent ? '#6b8f6b' : '#c0625e' }}; margin: 0;">
                                {{ $user->rgpd_consent ? 'Aceptado' : 'No aceptado' }}
                            </p>
                        </div>
                    </div>
                    @else
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div style="background: #fff; border-radius: 8px; padding: 14px; border: 0.5px solid #e8ede8;">
                            <p style="font-size: 12px; color: #7a8a7a; margin: 0 0 4px;">Reservas totales</p>
                            <p style="font-size: 22px; font-weight: 600; color: #2d3b2d; margin: 0;">{{ $user->reservations->count() }}</p>
                        </div>
                        <div style="background: #fff; border-radius: 8px; padding: 14px; border: 0.5px solid #e8ede8;">
                            <p style="font-size: 12px; color: #7a8a7a; margin: 0 0 4px;">Gasto total</p>
                            <p style="font-size: 22px; font-weight: 600; color: #6b8f6b; margin: 0;">{{ number_format($user->reservations->where('status', '!=', 'cancelled')->sum('total_price'), 2) }}€</p>
                        </div>
                        <div style="background: #fff; border-radius: 8px; padding: 14px; border: 0.5px solid #e8ede8;">
                            <p style="font-size: 12px; color: #7a8a7a; margin: 0 0 4px;">Miembro desde</p>
                            <p style="font-size: 15px; font-weight: 600; color: #2d3b2d; margin: 0;">{{ $user->created_at->format('d/m/Y') }}</p>
                        </div>
                        <div style="background: #fff; border-radius: 8px; padding: 14px; border: 0.5px solid #e8ede8;">
                            <p style="font-size: 12px; color: #7a8a7a; margin: 0 0 4px;">RGPD</p>
                            <p style="font-size: 15px; font-weight: 600; color: {{ $user->rgpd_consent ? '#6b8f6b' : '#c0625e' }}; margin: 0;">
                                {{ $user->rgpd_consent ? 'Aceptado' : 'No aceptado' }}
                            </p>
                        </div>
                    </div>
                    @endif
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <a href="{{ route('admin.users.index') }}"
                       style="display: inline-flex; align-items: center; padding: 9px 18px; border-radius: 8px; border: 0.5px solid #d4d9cc; font-size: 14px; color: #5a6b5a; text-decoration: none; font-weight: 500; background: #fff;"
                       onmouseover="this.style.background='#f7f8f5'"
                       onmouseout="this.style.background='#fff'">
                        Cancelar
                    </a>
                    <button type="submit"
                            style="display: inline-flex; align-items: center; gap: 8px; background: #6b8f6b; color: #fff; font-size: 14px; font-weight: 500; padding: 9px 22px; border-radius: 8px; border: none; cursor: pointer;"
                            onmouseover="this.style.background='#4a6b4a'"
                            onmouseout="this.style.background='#6b8f6b'">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                            <polyline points="17 21 17 13 7 13 7 21"/>
                            <polyline points="7 3 7 8 15 8"/>
                        </svg>
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>