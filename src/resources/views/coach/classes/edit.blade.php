<x-app-layout>
    <x-slot name="header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 20px; font-weight: 600; color: #2d3b2d; margin: 0;">
                Editar Clase: <span style="color: #6b8f6b;">{{ $class->title }}</span>
            </h2>
            <a href="{{ route('coach.classes.index') }}"
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

    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">

        {{-- PASO 1: PISTA Y FECHA --}}
        <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 24px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
                <div style="width: 26px; height: 26px; background: #6b8f6b; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; color: #fff; flex-shrink: 0;">1</div>
                <h3 style="font-size: 15px; font-weight: 600; color: #2d3b2d; margin: 0;">Elige una pista y una fecha</h3>
            </div>
            <form method="GET" action="{{ route('coach.classes.edit', $class) }}">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">Pista</label>
                        <select name="court_id"
                            style="width: 100%; padding: 9px 12px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; background: #fff; box-sizing: border-box;"
                            onfocus="this.style.borderColor='#6b8f6b'"
                            onblur="this.style.borderColor='#d4d9cc'">
                            @foreach($courts as $court)
                            <option value="{{ $court->id }}" {{ $courtId == $court->id ? 'selected' : '' }}>
                                {{ $court->name }} ({{ $court->type }} · {{ $court->surface }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">Fecha</label>
                        <input type="date" name="date"
                            value="{{ $date }}"
                            style="width: 100%; padding: 9px 12px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; box-sizing: border-box;"
                            onfocus="this.style.borderColor='#6b8f6b'"
                            onblur="this.style.borderColor='#d4d9cc'">
                    </div>
                </div>
                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit"
                        style="display: inline-flex; align-items: center; gap: 8px; background: #6b8f6b; color: #fff; font-size: 14px; font-weight: 500; padding: 9px 20px; border-radius: 8px; border: none; cursor: pointer;"
                        onmouseover="this.style.background='#4a6b4a'"
                        onmouseout="this.style.background='#6b8f6b'">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                        Buscar franjas
                    </button>
                </div>
            </form>
        </div>

        {{-- PASO 2: FRANJA HORARIA --}}
        @if($slots->isEmpty())
        <div style="padding: 16px 20px; background: #fdf6e8; border: 0.5px solid #e8d4a0; border-radius: 10px; font-size: 14px; color: #92650a; display: flex; align-items: center; gap: 10px;">
            <svg xmlns="http://www.w3.org/2000/svg" style="width:18px;height:18px;stroke:#b8860b;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
            </svg>
            No hay franjas disponibles para esa pista y fecha.
        </div>
        @else
        <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 24px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                <div style="width: 26px; height: 26px; background: #6b8f6b; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; color: #fff; flex-shrink: 0;">2</div>
                <h3 style="font-size: 15px; font-weight: 600; color: #2d3b2d; margin: 0;">Elige una franja horaria</h3>
            </div>

            <div style="display: flex; gap: 16px; margin-bottom: 16px; padding-left: 36px;">
                <span style="display: inline-flex; align-items: center; gap: 5px; font-size: 13px; color: #5a6b5a;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px;stroke:#6b8f6b;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 6 12 12 16 14" />
                    </svg>
                    Duración: <strong style="color: #2d3b2d;">1h 30min</strong>
                </span>
                <span style="display: inline-flex; align-items: center; gap: 5px; font-size: 13px; color: #5a6b5a;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px;stroke:#6b8f6b;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                        <path d="M12 2a10 10 0 1 0 0 20A10 10 0 0 0 12 2z" />
                        <path d="M12 6v6l4 2" />
                    </svg>
                    El precio por alumno se define en el paso siguiente
                </span>
            </div>

            @error('start_time')
            <p style="color: #c0625e; font-size: 12px; margin-bottom: 10px; padding-left: 36px;">{{ $message }}</p>
            @enderror

            <form method="GET" action="{{ route('coach.classes.edit', $class) }}">
                <input type="hidden" name="court_id" value="{{ $courtId }}">
                <input type="hidden" name="date" value="{{ $date }}">
                <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 8px;">
                    @php $currentStart = \Carbon\Carbon::parse($class->start_time)->format('H:i'); @endphp
                    @foreach($slots as $slot)
                    <button type="submit" name="start_time" value="{{ $slot }}"
                        style="padding: 9px 6px; border-radius: 8px; font-size: 13px; font-weight: 500; border: 0.5px solid; cursor: pointer;
                                {{ (request('start_time', $currentStart)) == $slot
                                    ? 'background: #6b8f6b; color: #fff; border-color: #6b8f6b;'
                                    : 'background: #fff; color: #2d3b2d; border-color: #d4d9cc;' }}"
                        onmouseover="if('{{ request('start_time', $currentStart) }}' !== '{{ $slot }}') { this.style.borderColor='#6b8f6b'; this.style.color='#4a6b4a'; }"
                        onmouseout="if('{{ request('start_time', $currentStart) }}' !== '{{ $slot }}') { this.style.borderColor='#d4d9cc'; this.style.color='#2d3b2d'; }">
                        {{ $slot }}
                    </button>
                    @endforeach
                </div>
            </form>
        </div>
        @endif

        {{-- PASO 3: DATOS DE LA CLASE --}}
        @php $selectedStartTime = request('start_time', \Carbon\Carbon::parse($class->start_time)->format('H:i')); @endphp
        @php $currentType = old('type', $class->type); @endphp
        <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 24px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                <div style="width: 26px; height: 26px; background: #6b8f6b; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; color: #fff; flex-shrink: 0;">3</div>
                <h3 style="font-size: 15px; font-weight: 600; color: #2d3b2d; margin: 0;">Datos de la clase</h3>
            </div>

            {{-- RESUMEN --}}
            <div style="background: #f7f8f5; border-radius: 10px; border: 0.5px solid #d4d9cc; padding: 14px; margin-bottom: 20px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">
                    <div>
                        <p style="font-size: 12px; color: #7a8a7a; margin: 0 0 3px;">Pista</p>
                        <p style="font-size: 14px; font-weight: 500; color: #2d3b2d; margin: 0;">{{ $selectedCourt->name }}</p>
                    </div>
                    <div>
                        <p style="font-size: 12px; color: #7a8a7a; margin: 0 0 3px;">Fecha</p>
                        <p style="font-size: 14px; font-weight: 500; color: #2d3b2d; margin: 0;">
                            {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
                        </p>
                    </div>
                    <div>
                        <p style="font-size: 12px; color: #7a8a7a; margin: 0 0 3px;">Horario</p>
                        <p style="font-size: 14px; font-weight: 500; color: #2d3b2d; margin: 0;">
                            {{ $selectedStartTime }} — {{ \Carbon\Carbon::createFromFormat('H:i', $selectedStartTime)->addMinutes(90)->format('H:i') }}
                        </p>
                    </div>
                </div>
            </div>

            <form action="{{ route('coach.classes.update', $class) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="court_id" value="{{ $courtId }}">
                <input type="hidden" name="date" value="{{ $date }}">
                <input type="hidden" name="start_time" value="{{ $selectedStartTime }}">

                {{-- TÍTULO --}}
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">Título de la clase</label>
                    <input type="text" name="title" value="{{ old('title', $class->title) }}"
                        style="width: 100%; padding: 9px 12px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; box-sizing: border-box;"
                        onfocus="this.style.borderColor='#6b8f6b'"
                        onblur="this.style.borderColor='#d4d9cc'">
                    @error('title')
                    <p style="color: #c0625e; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- TIPO Y NIVEL --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">Tipo</label>
                        <select name="type"
                            x-data="{ type: '{{ $currentType }}' }"
                            x-model="type"
                            x-on:change="
                                    const max = document.getElementById('max_players');
                                    const maxHidden = document.getElementById('max_players_hidden');
                                    if (type === 'individual') {
                                        max.value = 1;
                                        max.disabled = true;
                                        max.style.background = '#f7f8f5';
                                        max.style.color = '#9aaa9a';
                                        maxHidden.value = 1;
                                    } else {
                                        max.disabled = false;
                                        max.style.background = '#fff';
                                        max.style.color = '#2d3b2d';
                                        maxHidden.value = max.value;
                                    }
                                "
                            style="width: 100%; padding: 9px 12px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; background: #fff; box-sizing: border-box;"
                            onfocus="this.style.borderColor='#6b8f6b'"
                            onblur="this.style.borderColor='#d4d9cc'">
                            <option value="individual" {{ $currentType == 'individual' ? 'selected' : '' }}>Individual</option>
                            <option value="group" {{ $currentType == 'group'      ? 'selected' : '' }}>Grupal</option>
                        </select>
                        @error('type')
                        <p style="color: #c0625e; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">Nivel</label>
                        <select name="level"
                            style="width: 100%; padding: 9px 12px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; background: #fff; box-sizing: border-box;"
                            onfocus="this.style.borderColor='#6b8f6b'"
                            onblur="this.style.borderColor='#d4d9cc'">
                            <option value="initiation" {{ old('level', $class->level) == 'initiation'   ? 'selected' : '' }}>Iniciación</option>
                            <option value="intermediate" {{ old('level', $class->level) == 'intermediate' ? 'selected' : '' }}>Intermedio</option>
                            <option value="advanced" {{ old('level', $class->level) == 'advanced'     ? 'selected' : '' }}>Avanzado</option>
                        </select>
                        @error('level')
                        <p style="color: #c0625e; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- VISIBILIDAD Y PLAZAS --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">Visibilidad</label>
                        <input type="hidden" name="visibility" value="{{ $class->visibility }}">
                        <div style="padding: 9px 12px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; background: #f7f8f5; color: #9aaa9a; display: flex; align-items: center; gap: 8px;">
                            @if($class->visibility === 'public')
                            <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px;stroke:#9aaa9a;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="2" y1="12" x2="22" y2="12" />
                                <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                            </svg>
                            Pública
                            @else
                            <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px;stroke:#9aaa9a;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                            Privada
                            @endif
                        </div>
                        <p style="font-size: 11px; color: #9aaa9a; margin: 4px 0 0;">La visibilidad no puede modificarse una vez creada la clase.</p>
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">Plazas máximas</label>
                        {{-- HIDDEN QUE SIEMPRE SE ENVÍA --}}
                        <input type="hidden" name="max_players" id="max_players_hidden"
                            value="{{ $currentType === 'individual' ? '1' : old('max_players', $class->max_players) }}">
                        <input type="number" id="max_players"
                            value="{{ old('max_players', $class->max_players) }}" min="1" max="4"
                            {{ $currentType === 'individual' ? 'disabled' : '' }}
                            style="width: 100%; padding: 9px 12px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; outline: none; box-sizing: border-box;
                               {{ $currentType === 'individual' ? 'background: #f7f8f5; color: #9aaa9a;' : 'background: #fff; color: #2d3b2d;' }}"
                            onfocus="this.style.borderColor='#6b8f6b'"
                            onblur="this.style.borderColor='#d4d9cc'"
                            oninput="document.getElementById('max_players_hidden').value = this.value">
                        @error('max_players')
                        <p style="color: #c0625e; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- PRECIO Y ESTADO --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">Precio por alumno (€)</label>
                        <input type="number" name="price" value="{{ old('price', $class->price) }}"
                            min="0" step="0.50"
                            style="width: 100%; padding: 9px 12px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; box-sizing: border-box;"
                            onfocus="this.style.borderColor='#6b8f6b'"
                            onblur="this.style.borderColor='#d4d9cc'">
                        @error('price')
                        <p style="color: #c0625e; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">Estado</label>
                        <select name="status"
                            style="width: 100%; padding: 9px 12px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; background: #fff; box-sizing: border-box;"
                            onfocus="this.style.borderColor='#6b8f6b'"
                            onblur="this.style.borderColor='#d4d9cc'">
                            <option value="registered" {{ old('status', $class->status) == 'registered' ? 'selected' : '' }}>Programada</option>
                            <option value="completed" {{ old('status', $class->status) == 'completed'  ? 'selected' : '' }}>Completada</option>
                            <option value="cancelled" {{ old('status', $class->status) == 'cancelled'  ? 'selected' : '' }}>Cancelada</option>
                        </select>
                        @error('status')
                        <p style="color: #c0625e; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- ALUMNOS INSCRITOS --}}
                @if($class->registered->where('status', 'registered')->count() > 0)
                <div style="background: #f7f8f5; border-radius: 10px; border: 0.5px solid #d4d9cc; padding: 18px; margin-bottom: 24px;">
                    <p style="font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 12px;">
                        Alumnos inscritos ({{ $class->registered->where('status', 'registered')->count() }})
                    </p>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        @foreach($class->registered->where('status', 'registered') as $enrollment)
                        <div style="display: flex; align-items: center; justify-content: space-between; background: #fff; border-radius: 8px; padding: 10px 14px; border: 0.5px solid #e8ede8;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 30px; height: 30px; border-radius: 50%; background: #e8f0e8; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; color: #4a6b4a;">
                                    {{ strtoupper(substr($enrollment->user->name, 0, 1)) }}
                                </div>
                                <span style="font-size: 14px; font-weight: 500; color: #2d3b2d;">{{ $enrollment->user->name }}</span>
                            </div>
                            <span style="font-size: 13px; color: #7a8a7a;">{{ $enrollment->user->email }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <a href="{{ route('coach.classes.index') }}"
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