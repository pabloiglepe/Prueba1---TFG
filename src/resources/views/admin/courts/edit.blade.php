<x-app-layout>
    <x-slot name="header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 20px; font-weight: 600; color: #2d3b2d; margin: 0;">
                Editar Pista: <span style="color: #6b8f6b;">{{ $court->name }}</span>
            </h2>
            <a href="{{ route('admin.courts.index') }}"
                style="display: inline-flex; align-items: center; gap: 6px; font-size: 14px; color: #5a6b5a; text-decoration: none;"
                onmouseover="this.style.color='#2d3b2d'"
                onmouseout="this.style.color='#5a6b5a'">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <line x1="19" y1="12" x2="5" y2="12" />
                    <polyline points="12 19 5 12 12 5" />
                </svg>
                Volver al listado
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 28px;">

            <form action="{{ route('admin.courts.update', $court) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- NOMBRE --}}
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">
                        Nombre de la pista
                    </label>
                    <input type="text" name="name"
                        value="{{ old('name', $court->name) }}"
                        style="width: 100%; padding: 9px 12px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; box-sizing: border-box;"
                        onfocus="this.style.borderColor='#6b8f6b'"
                        onblur="this.style.borderColor='#d4d9cc'">
                    @error('name')
                    <p style="color: #c0625e; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- TIPO Y SUPERFICIE EN GRID --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">

                    {{-- TIPO --}}
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">
                            Tipo
                        </label>
                        <select name="type"
                            style="width: 100%; padding: 9px 12px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; background: #fff; box-sizing: border-box;"
                            onfocus="this.style.borderColor='#6b8f6b'"
                            onblur="this.style.borderColor='#d4d9cc'">
                            <option value="cristal" {{ old('type', $court->type) == 'cristal' ? 'selected' : '' }}>Cristal</option>
                            <option value="muro" {{ old('type', $court->type) == 'muro'    ? 'selected' : '' }}>Muro</option>
                        </select>
                        @error('type')
                        <p style="color: #c0625e; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- SUPERFICIE --}}
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">
                            Superficie
                        </label>
                        <select name="surface"
                            style="width: 100%; padding: 9px 12px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; background: #fff; box-sizing: border-box;"
                            onfocus="this.style.borderColor='#6b8f6b'"
                            onblur="this.style.borderColor='#d4d9cc'">
                            <option value="cesped" {{ old('surface', $court->surface) == 'cesped'  ? 'selected' : '' }}>Césped</option>
                            <option value="cemento" {{ old('surface', $court->surface) == 'cemento' ? 'selected' : '' }}>Cemento</option>
                        </select>
                        @error('surface')
                        <p style="color: #c0625e; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- ESTADÍSTICAS DE LA PISTA --}}
                <div style="background: #f7f8f5; border-radius: 10px; border: 0.5px solid #d4d9cc; padding: 18px; margin-bottom: 20px;">
                    <p style="font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 14px;">Estadísticas de la pista</p>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">
                        <div style="background: #fff; border-radius: 8px; padding: 14px; border: 0.5px solid #e8ede8;">
                            <p style="font-size: 12px; color: #7a8a7a; margin: 0 0 4px;">Reservas totales</p>
                            <p style="font-size: 22px; font-weight: 600; color: #2d3b2d; margin: 0;">
                                {{ $court->reservations->where('status', '!=', 'cancelled')->count() }}
                            </p>
                        </div>
                        <div style="background: #fff; border-radius: 8px; padding: 14px; border: 0.5px solid #e8ede8;">
                            <p style="font-size: 12px; color: #7a8a7a; margin: 0 0 4px;">Ingresos generados</p>
                            <p style="font-size: 22px; font-weight: 600; color: #6b8f6b; margin: 0;">
                                {{ number_format($court->reservations->where('status', '!=', 'cancelled')->sum('total_price'), 2) }}€
                            </p>
                        </div>
                        <div style="background: #fff; border-radius: 8px; padding: 14px; border: 0.5px solid #e8ede8;">
                            <p style="font-size: 12px; color: #7a8a7a; margin: 0 0 4px;">Última reserva</p>
                            <p style="font-size: 15px; font-weight: 600; color: #2d3b2d; margin: 0;">
                                {{ $court->reservations->where('status', '!=', 'cancelled')->sortByDesc('reservation_date')->first()?->reservation_date
                    ? \Carbon\Carbon::parse($court->reservations->where('status', '!=', 'cancelled')->sortByDesc('reservation_date')->first()->reservation_date)->format('d/m/Y')
                    : '—' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- ESTADO --}}
                {{-- ESTADO --}}
                <div style="margin-bottom: 28px; padding: 16px; background: {{ $futureReservations ? '#fdf6e8' : '#f7f8f5' }}; border-radius: 8px; border: 0.5px solid {{ $futureReservations ? '#e8d4a0' : '#d4d9cc' }};">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: {{ $futureReservations ? 'not-allowed' : 'pointer' }};">
                        <input type="hidden" name="is_active" value="{{ $futureReservations ? '1' : '0' }}">
                        <input type="checkbox" name="{{ $futureReservations ? '_is_active_disabled' : 'is_active' }}" value="1"
                            {{ old('is_active', $court->is_active) ? 'checked' : '' }}
                            {{ $futureReservations ? 'disabled' : '' }}
                            style="width: 18px; height: 18px; accent-color: #6b8f6b; cursor: {{ $futureReservations ? 'not-allowed' : 'pointer' }};">
                        <div>
                            <p style="font-size: 14px; font-weight: 500; color: #2d3b2d; margin: 0;">Pista activa</p>
                            @if($futureReservations)
                            <p style="font-size: 12px; color: #b8860b; margin: 4px 0 0;">
                                Esta pista tiene reservas pendientes y por ello no puede desactivarse.
                            </p>
                            @else
                            <p style="font-size: 12px; color: #7a8a7a; margin: 2px 0 0;">
                                La pista está desactivada por tanto no aparecerá disponible para reservas.
                            </p>
                            @endif
                        </div>
                    </label>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <a href="{{ route('admin.courts.index') }}"
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
</x-app-layout>