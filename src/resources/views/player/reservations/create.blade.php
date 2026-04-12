<x-app-layout>
    <x-slot name="header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 20px; font-weight: 600; color: #2d3b2d; margin: 0;">
                Reservar Pista
            </h2>
            <a href="{{ route('player.reservations.index') }}"
                style="display: inline-flex; align-items: center; gap: 6px; font-size: 14px; color: #5a6b5a; text-decoration: none;"
                onmouseover="this.style.color='#2d3b2d'"
                onmouseout="this.style.color='#5a6b5a'">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <line x1="19" y1="12" x2="5" y2="12"/>
                    <polyline points="12 19 5 12 12 5"/>
                </svg>
                Mis Reservas
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

        {{-- PASO 1: ELEGIR FECHA --}}
        <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 24px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
                <div style="width: 26px; height: 26px; background: #6b8f6b; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; color: #fff; flex-shrink: 0;">1</div>
                <h3 style="font-size: 15px; font-weight: 600; color: #2d3b2d; margin: 0;">Elige una fecha</h3>
            </div>
            <form method="GET" action="{{ route('player.reservations.create') }}">
                <div style="display: flex; gap: 12px; align-items: flex-end;">
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">Fecha</label>
                        <input type="date" name="date"
                               value="{{ request('date') }}"
                               min="{{ date('Y-m-d') }}"
                               style="width: 100%; padding: 9px 12px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; box-sizing: border-box;"
                               onfocus="this.style.borderColor='#6b8f6b'"
                               onblur="this.style.borderColor='#d4d9cc'">
                    </div>
                    <button type="submit"
                            style="display: inline-flex; align-items: center; gap: 8px; background: #6b8f6b; color: #fff; font-size: 14px; font-weight: 500; padding: 9px 20px; border-radius: 8px; border: none; cursor: pointer; white-space: nowrap;"
                            onmouseover="this.style.background='#4a6b4a'"
                            onmouseout="this.style.background='#6b8f6b'">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        Buscar franjas
                    </button>
                </div>
            </form>
        </div>

        {{-- PASO 2: ELEGIR FRANJA HORARIA --}}
        @if(request('date') && $slots->isNotEmpty())
        <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 24px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                <div style="width: 26px; height: 26px; background: #6b8f6b; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; color: #fff; flex-shrink: 0;">2</div>
                <h3 style="font-size: 15px; font-weight: 600; color: #2d3b2d; margin: 0;">Elige una franja horaria</h3>
            </div>

            {{-- TARIFAS --}}
            <div style="display: flex; gap: 16px; margin-bottom: 16px; padding-left: 36px;">
                <span style="display: inline-flex; align-items: center; gap: 5px; font-size: 13px; color: #5a6b5a;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px;stroke:#6b8f6b;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                        <circle cx="12" cy="12" r="5"/>
                        <line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                        <line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                    </svg>
                    Diurna: <strong style="color: #2d3b2d;">12€</strong>
                </span>
                <span style="display: inline-flex; align-items: center; gap: 5px; font-size: 13px; color: #5a6b5a;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px;stroke:#6b8f6b;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                    </svg>
                    Nocturna (desde {{ $nightStartTime }}): <strong style="color: #2d3b2d;">16€</strong>
                </span>
                <span style="display: inline-flex; align-items: center; gap: 5px; font-size: 13px; color: #5a6b5a;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px;stroke:#6b8f6b;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                    Duración: <strong style="color: #2d3b2d;">1h 30min</strong>
                </span>
            </div>

            {{-- FRANJAS --}}
            <form method="GET" action="{{ route('player.reservations.create') }}">
                <input type="hidden" name="date" value="{{ request('date') }}">
                <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 8px;">
                    @foreach($slots as $slot)
                        <button type="submit" name="start_time" value="{{ $slot }}"
                                style="padding: 9px 6px; border-radius: 8px; font-size: 13px; font-weight: 500; border: 0.5px solid; cursor: pointer;
                                {{ request('start_time') == $slot
                                    ? 'background: #6b8f6b; color: #fff; border-color: #6b8f6b;'
                                    : 'background: #fff; color: #2d3b2d; border-color: #d4d9cc;' }}"
                                onmouseover="if('{{ request('start_time') }}' !== '{{ $slot }}') { this.style.borderColor='#6b8f6b'; this.style.color='#4a6b4a'; }"
                                onmouseout="if('{{ request('start_time') }}' !== '{{ $slot }}') { this.style.borderColor='#d4d9cc'; this.style.color='#2d3b2d'; }">
                            {{ $slot }}
                        </button>
                    @endforeach
                </div>
            </form>
        </div>
        @endif

        {{-- PASO 3: ELEGIR PISTA --}}
        @if(request('start_time'))
            @if($courts->isEmpty())
                <div style="padding: 16px 20px; background: #fdf6e8; border: 0.5px solid #e8d4a0; border-radius: 10px; font-size: 14px; color: #92650a; display: flex; align-items: center; gap: 10px;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:18px;height:18px;stroke:#b8860b;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    </svg>
                    No hay pistas disponibles para ese horario. Prueba otra franja.
                </div>
            @else
                <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 24px;">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
                        <div style="width: 26px; height: 26px; background: #6b8f6b; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; color: #fff; flex-shrink: 0;">3</div>
                        <h3 style="font-size: 15px; font-weight: 600; color: #2d3b2d; margin: 0;">Elige una pista disponible</h3>
                    </div>

                    <form action="{{ route('player.reservations.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="date" value="{{ request('date') }}">
                        <input type="hidden" name="start_time" value="{{ request('start_time') }}">

                        @error('court_id')
                            <p style="color: #c0625e; font-size: 12px; margin-bottom: 12px;">{{ $message }}</p>
                        @enderror

                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 20px;">
                            @foreach($courts as $court)
                                <label style="display: flex; align-items: center; gap: 14px; border: 0.5px solid #d4d9cc; border-radius: 10px; padding: 16px; cursor: pointer;"
                                       onmouseover="this.style.borderColor='#6b8f6b'; this.style.background='#fafbf9';"
                                       onmouseout="this.style.borderColor='#d4d9cc'; this.style.background='#fff';">
                                    <input type="radio" name="court_id" value="{{ $court->id }}" required
                                           style="accent-color: #6b8f6b; width: 16px; height: 16px; flex-shrink: 0;">
                                    <div>
                                        <p style="font-size: 14px; font-weight: 500; color: #2d3b2d; margin: 0 0 3px;">{{ $court->name }}</p>
                                        <p style="font-size: 12px; color: #7a8a7a; margin: 0; text-transform: capitalize;">{{ $court->type }} · {{ $court->surface }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        {{-- RESUMEN --}}
                        <div style="background: #f7f8f5; border-radius: 10px; border: 0.5px solid #d4d9cc; padding: 16px; margin-bottom: 20px;">
                            <p style="font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 12px;">Resumen de la reserva</p>
                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">
                                <div>
                                    <p style="font-size: 12px; color: #7a8a7a; margin: 0 0 3px;">Fecha</p>
                                    <p style="font-size: 14px; font-weight: 500; color: #2d3b2d; margin: 0;">
                                        {{ \Carbon\Carbon::parse(request('date'))->format('d/m/Y') }}
                                    </p>
                                </div>
                                <div>
                                    <p style="font-size: 12px; color: #7a8a7a; margin: 0 0 3px;">Horario</p>
                                    <p style="font-size: 14px; font-weight: 500; color: #2d3b2d; margin: 0;">
                                        {{ request('start_time') }} — {{ \Carbon\Carbon::createFromFormat('H:i', request('start_time'))->addMinutes(90)->format('H:i') }}
                                    </p>
                                </div>
                                <div>
                                    <p style="font-size: 12px; color: #7a8a7a; margin: 0 0 3px;">Precio</p>
                                    <p style="font-size: 18px; font-weight: 600; color: #6b8f6b; margin: 0;">
                                        {{ \Carbon\Carbon::createFromFormat('H:i', request('start_time'))->gte(\Carbon\Carbon::createFromFormat('H:i', $nightStartTime)) ? '16€' : '12€' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div style="display: flex; justify-content: flex-end;">
                            <button type="submit"
                                    style="display: inline-flex; align-items: center; gap: 8px; background: #6b8f6b; color: #fff; font-size: 14px; font-weight: 500; padding: 11px 24px; border-radius: 8px; border: none; cursor: pointer;"
                                    onmouseover="this.style.background='#4a6b4a'"
                                    onmouseout="this.style.background='#6b8f6b'">
                                <svg xmlns="http://www.w3.org/2000/svg" style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                Confirmar Reserva
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        @endif

    </div>
</x-app-layout>