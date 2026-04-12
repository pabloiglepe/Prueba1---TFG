<x-app-layout>
    <x-slot name="header">
        <h2 style="font-size: 20px; font-weight: 600; color: #2d3b2d; margin: 0;">
            Clases
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

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

        {{-- MIS CLASES --}}
        <div>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
                <h3 style="font-size: 16px; font-weight: 600; color: #2d3b2d; margin: 0;">Mis clases</h3>
                @if(!$myClasses->isEmpty())
                    <span style="padding: 2px 10px; background: #e8f0e8; color: #4a6b4a; border-radius: 20px; font-size: 12px; font-weight: 500;">
                        {{ $myClasses->count() }}
                    </span>
                @endif
            </div>

            @if($myClasses->isEmpty())
                <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 40px; text-align: center; color: #9aaa9a; font-size: 14px;">
                    No estás inscrito en ninguna clase todavía.
                </div>
            @else
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px;">
                    @foreach($myClasses as $class)
                        <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 20px; display: flex; flex-direction: column; justify-content: space-between;">
                            <div>
                                {{-- CABECERA --}}
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                                    <h4 style="font-size: 15px; font-weight: 600; color: #2d3b2d; margin: 0;">{{ $class->title }}</h4>
                                    <span style="padding: 3px 8px; background: {{ $class->visibility === 'public' ? '#e8f0e8' : '#f0f3ee' }}; color: {{ $class->visibility === 'public' ? '#4a6b4a' : '#7a8a7a' }}; border-radius: 20px; font-size: 11px; font-weight: 500; white-space: nowrap; margin-left: 8px;">
                                        {{ $class->visibility === 'public' ? 'Pública' : 'Privada' }}
                                    </span>
                                </div>

                                {{-- NIVEL Y TIPO --}}
                                <p style="font-size: 12px; color: #7a8a7a; margin: 0 0 12px; text-transform: capitalize;">
                                    {{ $class->type }} ·
                                    {{ match($class->level) {
                                        'initiation'   => 'Iniciación',
                                        'intermediate' => 'Intermedio',
                                        'advanced'     => 'Avanzado',
                                        default        => $class->level
                                    } }}
                                </p>

                                {{-- DETALLES --}}
                                <div style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 14px;">
                                    <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #5a6b5a;">
                                        <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px;stroke:#6b8f6b;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                                            <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                                        </svg>
                                        {{ \Carbon\Carbon::parse($class->date)->format('d/m/Y') }}
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #5a6b5a;">
                                        <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px;stroke:#6b8f6b;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                        </svg>
                                        {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }} — {{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #5a6b5a;">
                                        <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px;stroke:#6b8f6b;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                        </svg>
                                        {{ $class->court->name }}
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #5a6b5a;">
                                        <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px;stroke:#6b8f6b;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                                        </svg>
                                        {{ $class->coach->name }}
                                    </div>
                                </div>

                                <p style="font-size: 18px; font-weight: 600; color: #6b8f6b; margin: 0;">
                                    {{ number_format($class->price, 2) }}€
                                </p>
                            </div>

                            {{-- CANCELAR INSCRIPCIÓN --}}
                            @if($class->date >= today()->format('Y-m-d'))
                                <form action="{{ route('player.classes.cancel', $class) }}"
                                      method="POST" style="margin-top: 16px; padding-top: 16px; border-top: 0.5px solid #f0f3ee;"
                                      onsubmit="return confirm('¿Cancelar tu inscripción?')">
                                    @csrf
                                    <button type="submit"
                                            style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: #c0625e; font-weight: 500; background: none; border: none; cursor: pointer; padding: 0;"
                                            onmouseover="this.style.color='#9b4444'"
                                            onmouseout="this.style.color='#c0625e'">
                                        <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                                        </svg>
                                        Cancelar inscripción
                                    </button>
                                </form>
                            @else
                                <p style="margin-top: 16px; padding-top: 16px; border-top: 0.5px solid #f0f3ee; font-size: 12px; color: #9aaa9a; text-align: center;">
                                    Clase finalizada
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- CLASES DISPONIBLES --}}
        <div>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
                <h3 style="font-size: 16px; font-weight: 600; color: #2d3b2d; margin: 0;">Clases disponibles</h3>
                @if(!$availableClasses->isEmpty())
                    <span style="padding: 2px 10px; background: #e8f0e8; color: #4a6b4a; border-radius: 20px; font-size: 12px; font-weight: 500;">
                        {{ $availableClasses->count() }}
                    </span>
                @endif
            </div>

            @if($availableClasses->isEmpty())
                <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 40px; text-align: center; color: #9aaa9a; font-size: 14px;">
                    No hay clases públicas disponibles en este momento.
                </div>
            @else
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px;">
                    @foreach($availableClasses as $class)
                        <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; border-left: 3px solid #6b8f6b; padding: 20px; display: flex; flex-direction: column; justify-content: space-between;">
                            <div>
                                <h4 style="font-size: 15px; font-weight: 600; color: #2d3b2d; margin: 0 0 6px;">{{ $class->title }}</h4>

                                <p style="font-size: 12px; color: #7a8a7a; margin: 0 0 12px; text-transform: capitalize;">
                                    {{ $class->type }} ·
                                    {{ match($class->level) {
                                        'initiation'   => 'Iniciación',
                                        'intermediate' => 'Intermedio',
                                        'advanced'     => 'Avanzado',
                                        default        => $class->level
                                    } }}
                                </p>

                                <div style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 14px;">
                                    <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #5a6b5a;">
                                        <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px;stroke:#6b8f6b;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                                            <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                                        </svg>
                                        {{ \Carbon\Carbon::parse($class->date)->format('d/m/Y') }}
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #5a6b5a;">
                                        <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px;stroke:#6b8f6b;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                        </svg>
                                        {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }} — {{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #5a6b5a;">
                                        <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px;stroke:#6b8f6b;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                        </svg>
                                        {{ $class->court->name }}
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #5a6b5a;">
                                        <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px;stroke:#6b8f6b;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                                        </svg>
                                        {{ $class->coach->name }}
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #5a6b5a;">
                                        <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px;stroke:#6b8f6b;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                        </svg>
                                        {{ $class->enrolled_count }}/{{ $class->max_players }} plazas
                                    </div>
                                </div>

                                <p style="font-size: 18px; font-weight: 600; color: #6b8f6b; margin: 0;">
                                    {{ number_format($class->price, 2) }}€
                                </p>
                            </div>

                            {{-- INSCRIBIRSE --}}
                            <form action="{{ route('player.classes.register', $class) }}"
                                  method="POST" style="margin-top: 16px; padding-top: 16px; border-top: 0.5px solid #f0f3ee;">
                                @csrf
                                <button type="submit"
                                        style="width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: 8px; background: #6b8f6b; color: #fff; font-size: 14px; font-weight: 500; padding: 10px; border-radius: 8px; border: none; cursor: pointer;"
                                        onmouseover="this.style.background='#4a6b4a'"
                                        onmouseout="this.style.background='#6b8f6b'">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Inscribirme
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</x-app-layout>