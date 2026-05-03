<x-app-layout>
    <x-slot name="header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 20px; font-weight: 600; color: #2d3b2d; margin: 0;">
                Mis Clases
            </h2>
            <a href="{{ route('coach.classes.create') }}"
               style="display: inline-flex; align-items: center; gap: 8px; background: #6b8f6b; color: #fff; font-size: 14px; font-weight: 500; padding: 9px 18px; border-radius: 8px; text-decoration: none;"
               onmouseover="this.style.background='#4a6b4a'"
               onmouseout="this.style.background='#6b8f6b'">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>
                </svg>
                Nueva Clase
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        @if(session('success'))
        <div style="margin-bottom: 16px; padding: 14px 18px; background: #e8f0e8; color: #4a6b4a; border-radius: 8px; font-size: 14px; border-left: 3px solid #6b8f6b;">
            {{ session('success') }}
        </div>
        @endif

        <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f7f8f5; border-bottom: 0.5px solid #d4d9cc;">
                        <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Clase</th>
                        <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Fecha</th>
                        <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Horario</th>
                        <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Pista</th>
                        <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Plazas</th>
                        <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Visibilidad</th>
                        <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Estado</th>
                        <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classes as $class)
                    <tr style="border-top: 0.5px solid #f0f3ee;" onmouseover="this.style.background='#fafbf9'" onmouseout="this.style.background='#fff'">

                        {{-- CLASE --}}
                        <td style="padding: 14px 20px;">
                            <p style="font-size: 14px; font-weight: 500; color: #2d3b2d; margin: 0 0 3px;">{{ $class->title }}</p>
                            <p style="font-size: 12px; color: #7a8a7a; margin: 0; text-transform: capitalize;">
                                {{ $class->type }} ·
                                {{ match($class->level) {
                                    'initiation'   => 'Iniciación',
                                    'intermediate' => 'Intermedio',
                                    'advanced'     => 'Avanzado',
                                    default        => $class->level
                                } }}
                            </p>
                        </td>

                        {{-- FECHA --}}
                        <td style="padding: 14px 20px; font-size: 14px; color: #5a6b5a;">
                            {{ \Carbon\Carbon::parse($class->date)->format('d/m/Y') }}
                        </td>

                        {{-- HORARIO --}}
                        <td style="padding: 14px 20px; font-size: 14px; color: #5a6b5a;">
                            {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }} —
                            {{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}
                        </td>

                        {{-- PISTA CON BADGE INTERIOR/EXTERIOR --}}
                        <td style="padding: 14px 20px;">
                            <p style="font-size: 14px; color: #5a6b5a; margin: 0 0 3px;">{{ $class->court->name }}</p>
                            @if($class->court->is_outdoor)
                                <span style="display: inline-flex; align-items: center; gap: 3px; padding: 2px 7px; background: #e8f4e8; color: #4a6b4a; border-radius: 20px; font-size: 11px; font-weight: 500;">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width:9px;height:9px;stroke:#4a6b4a;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                                        <circle cx="12" cy="12" r="5"/>
                                        <line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
                                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
                                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                                        <line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
                                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
                                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                                    </svg>
                                    Exterior
                                </span>
                            @else
                                <span style="display: inline-flex; align-items: center; gap: 3px; padding: 2px 7px; background: #f0f0f8; color: #5a5a8a; border-radius: 20px; font-size: 11px; font-weight: 500;">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width:9px;height:9px;stroke:#5a5a8a;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                        <polyline points="9 22 9 12 15 12 15 22"/>
                                    </svg>
                                    Interior
                                </span>
                            @endif
                        </td>

                        {{-- PLAZAS --}}
                        <td style="padding: 14px 20px;">
                            @php $enrolled = $class->registered->where('status', 'registered')->count(); @endphp
                            <span style="font-size: 14px; color: {{ $enrolled >= $class->max_players ? '#c0625e' : '#2d3b2d' }}; font-weight: 500;">
                                {{ $enrolled }}/{{ $class->max_players }}
                            </span>
                        </td>

                        {{-- VISIBILIDAD --}}
                        <td style="padding: 14px 20px;">
                            @if($class->visibility === 'public')
                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #e8f0e8; color: #4a6b4a; border-radius: 20px; font-size: 12px; font-weight: 500;">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width:10px;height:10px;" fill="none" viewBox="0 0 24 24" stroke="#4a6b4a" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                                    </svg>
                                    Pública
                                </span>
                            @else
                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #f7f8f5; color: #7a8a7a; border-radius: 20px; font-size: 12px; font-weight: 500;">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width:10px;height:10px;" fill="none" viewBox="0 0 24 24" stroke="#7a8a7a" stroke-width="2">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                    </svg>
                                    Privada
                                </span>
                            @endif
                        </td>

                        {{-- ESTADO --}}
                        <td style="padding: 14px 20px;">
                            @if($class->status === 'registered')
                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #e8f0e8; color: #4a6b4a; border-radius: 20px; font-size: 12px; font-weight: 500;">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width:10px;height:10px;fill:#4a6b4a;" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>
                                    Programada
                                </span>
                            @elseif($class->status === 'completed')
                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #f0eaf8; color: #6b4a8f; border-radius: 20px; font-size: 12px; font-weight: 500;">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width:10px;height:10px;fill:#6b4a8f;" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>
                                    Completada
                                </span>
                            @else
                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #fce8e8; color: #9b4444; border-radius: 20px; font-size: 12px; font-weight: 500;">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width:10px;height:10px;fill:#9b4444;" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>
                                    Cancelada
                                </span>
                            @endif
                        </td>

                        {{-- ACCIONES --}}
                        <td style="padding: 14px 20px;">
                            @if($class->status === 'registered')
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <a href="{{ route('coach.classes.edit', $class) }}"
                                       style="display: inline-flex; align-items: center; gap: 5px; font-size: 13px; color: #6b8f6b; font-weight: 500; text-decoration: none;"
                                       onmouseover="this.style.color='#4a6b4a'"
                                       onmouseout="this.style.color='#6b8f6b'">
                                        <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                        </svg>
                                        Editar
                                    </a>
                                    <form action="{{ route('coach.classes.destroy', $class) }}"
                                          method="POST" class="inline"
                                          onsubmit="return confirm('¿Cancelar esta clase?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                style="display: inline-flex; align-items: center; gap: 5px; font-size: 13px; color: #c0625e; font-weight: 500; background: none; border: none; cursor: pointer; padding: 0;"
                                                onmouseover="this.style.color='#9b4444'"
                                                onmouseout="this.style.color='#c0625e'">
                                            <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"/>
                                                <line x1="15" y1="9" x2="9" y2="15"/>
                                                <line x1="9" y1="9" x2="15" y2="15"/>
                                            </svg>
                                            Cancelar
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span style="color: #c8d4c8; font-size: 14px;">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="padding: 48px 20px; text-align: center; font-size: 14px; color: #9aaa9a;">
                            No has creado ninguna clase todavía.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>