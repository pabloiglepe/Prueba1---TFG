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
                <iconify-icon icon="ph:plus-circle" style="font-size: 16px;"></iconify-icon>
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

        @php
        $scheduled  = $classes->where('status', 'registered');
        $completed  = $classes->where('status', 'completed');
        $cancelled  = $classes->where('status', 'cancelled');
        @endphp

        <div x-data="{ tab: 'scheduled' }">

            {{-- TABS --}}
            <div style="display: flex; gap: 15px; border-bottom: 0.5px solid #d4d9cc; font-weight: 500; background: none; border-top: none; border-left: none; border-right: none; cursor: pointer; margin-bottom: 24px;">
                <button @click="tab = 'scheduled'"
                    :style="tab === 'scheduled' ? 'border-bottom: 2px solid #6b8f6b; color: #4a6b4a;' : 'border-bottom: 2px solid transparent; color: #7a8a7a;'"
                    style="display: inline-flex; align-items: center; gap: 8px; padding: 14px 40px; font-size: 14px; font-weight: 500; background: none; border-top: none; border-left: none; border-right: none; cursor: pointer; margin-bottom: -1px;">
                    <iconify-icon icon="ph:calendar-check" style="font-size: 15px; flex-shrink: 0;"></iconify-icon>
                    Programadas
                </button>
                <button @click="tab = 'completed'"
                    :style="tab === 'completed' ? 'border-bottom: 2px solid #6b8f6b; color: #4a6b4a;' : 'border-bottom: 2px solid transparent; color: #7a8a7a;'"
                    style="display: inline-flex; align-items: center; gap: 8px; padding: 14px 40px; font-size: 14px; font-weight: 500; background: none; border-top: none; border-left: none; border-right: none; cursor: pointer; margin-bottom: -1px;">
                    <iconify-icon icon="ph:check-circle" style="font-size: 15px; flex-shrink: 0;"></iconify-icon>
                    Completadas
                </button>
                <button @click="tab = 'cancelled'"
                    :style="tab === 'cancelled' ? 'border-bottom: 2px solid #6b8f6b; color: #4a6b4a;' : 'border-bottom: 2px solid transparent; color: #7a8a7a;'"
                    style="display: inline-flex; align-items: center; gap: 8px; padding: 14px 40px; font-size: 14px; font-weight: 500; background: none; border-top: none; border-left: none; border-right: none; cursor: pointer; margin-bottom: -1px;">
                    <iconify-icon icon="ph:x-circle" style="font-size: 15px; flex-shrink: 0;"></iconify-icon>
                    Canceladas
                </button>
            </div>

            {{-- TAB: PROGRAMADAS --}}
            <div x-show="tab === 'scheduled'">
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    @forelse($scheduled as $class)
                    @php $enrolled = $class->registered->where('status', 'registered')->count(); @endphp
                    <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 20px 24px; display: flex; align-items: center; gap: 20px;"
                        onmouseover="this.style.borderColor='#b8c9b8'"
                        onmouseout="this.style.borderColor='#d4d9cc'">

                        {{-- BLOQUE FECHA --}}
                        <div style="flex-shrink: 0; width: 52px; text-align: center; background: #f7f8f5; border-radius: 10px; padding: 10px 6px;">
                            <p style="font-size: 22px; font-weight: 700; color: #2d3b2d; margin: 0; line-height: 1;">
                                {{ \Carbon\Carbon::parse($class->date)->format('d') }}
                            </p>
                            <p style="font-size: 11px; font-weight: 600; color: #6b8f6b; text-transform: uppercase; margin: 3px 0 0; letter-spacing: 0.05em;">
                                {{ \Carbon\Carbon::parse($class->date)->translatedFormat('M') }}
                            </p>
                        </div>

                        {{-- INFO CENTRAL --}}
                        <div style="flex: 1; min-width: 0;">
                            <p style="font-size: 15px; font-weight: 600; color: #2d3b2d; margin: 0 0 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $class->title }}
                            </p>
                            <p style="font-size: 12px; color: #7a8a7a; margin: 0 0 9px; text-transform: capitalize;">
                                {{ $class->type }} · {{ match($class->level) {
                                'initiation'   => 'Iniciación',
                                'intermediate' => 'Intermedio',
                                'advanced'     => 'Avanzado',
                                default        => $class->level
                            } }}
                            </p>
                            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                <span style="font-size: 13px; color: #5a6b5a;">{{ $class->court->name }}</span>
                                @if($class->court->is_outdoor)
                                <span style="padding: 2px 7px; background: #e0eef8; color: #2b6691; border-radius: 20px; font-size: 11px; font-weight: 500;">Exterior</span>
                                @else
                                <span style="padding: 2px 7px; background: #f0f0f8; color: #5a5a8a; border-radius: 20px; font-size: 11px; font-weight: 500;">Interior</span>
                                @endif
                                <span style="font-size: 12px; color: #9aaa9a;">·</span>
                                <span style="font-size: 13px; color: #7a8a7a;">
                                    {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }} — {{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}
                                </span>
                            </div>
                        </div>

                        {{-- BADGES, PLAZAS Y ACCIONES --}}
                        <div style="flex-shrink: 0; display: flex; flex-direction: column; align-items: flex-end; gap: 8px;">
                            <div style="display: flex; gap: 6px;">
                                @if($class->visibility === 'public')
                                <span style="padding: 3px 9px; background: #e8f0e8; color: #4a6b4a; border-radius: 20px; font-size: 11px; font-weight: 500;">Pública</span>
                                @else
                                <span style="padding: 3px 9px; background: #f7f8f5; color: #7a8a7a; border-radius: 20px; font-size: 11px; font-weight: 500;">Privada</span>
                                @endif
                                <span style="padding: 3px 9px; background: #e8f0e8; color: #4a6b4a; border-radius: 20px; font-size: 11px; font-weight: 500;">Programada</span>
                            </div>
                            <p style="font-size: 13px; font-weight: 600; color: {{ $enrolled >= $class->max_players ? '#c0625e' : '#5a6b5a' }}; margin: 0;">
                                {{ $enrolled }}/{{ $class->max_players }} plazas
                            </p>
                            <div style="display: flex; gap: 14px; align-items: center;">
                                <a href="{{ route('coach.classes.edit', $class) }}"
                                    style="display: inline-flex; align-items: center; gap: 4px; font-size: 13px; color: #6b8f6b; font-weight: 500; text-decoration: none;"
                                    onmouseover="this.style.color='#4a6b4a'"
                                    onmouseout="this.style.color='#6b8f6b'">
                                    <iconify-icon icon="ph:pencil-simple" style="font-size: 13px;"></iconify-icon>
                                    Editar
                                </a>
                                <form action="{{ route('coach.classes.destroy', $class) }}" method="POST" class="inline"
                                    onsubmit="return confirm('¿Cancelar esta clase?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        style="display: inline-flex; align-items: center; gap: 4px; font-size: 13px; color: #c0625e; font-weight: 500; background: none; border: none; cursor: pointer; padding: 0;"
                                        onmouseover="this.style.color='#9b4444'"
                                        onmouseout="this.style.color='#c0625e'">
                                        <iconify-icon icon="ph:x-circle" style="font-size: 13px;"></iconify-icon>
                                        Cancelar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 48px 20px; text-align: center;">
                        <p style="font-size: 14px; color: #9aaa9a; margin: 0;">No tienes clases programadas.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- TAB: COMPLETADAS --}}
            <div x-show="tab === 'completed'">
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    @forelse($completed as $class)
                    @php $enrolled = $class->registered->where('status', 'registered')->count(); @endphp
                    <div style="background: #fafbf9; border-radius: 12px; border: 0.5px solid #e4e9e0; padding: 20px 24px; display: flex; align-items: center; gap: 20px;">

                        {{-- BLOQUE FECHA --}}
                        <div style="flex-shrink: 0; width: 52px; text-align: center; background: #f0f3ee; border-radius: 10px; padding: 10px 6px;">
                            <p style="font-size: 22px; font-weight: 700; color: #9aaa9a; margin: 0; line-height: 1;">
                                {{ \Carbon\Carbon::parse($class->date)->format('d') }}
                            </p>
                            <p style="font-size: 11px; font-weight: 600; color: #b8c9b8; text-transform: uppercase; margin: 3px 0 0; letter-spacing: 0.05em;">
                                {{ \Carbon\Carbon::parse($class->date)->translatedFormat('M') }}
                            </p>
                        </div>

                        {{-- INFO CENTRAL --}}
                        <div style="flex: 1; min-width: 0;">
                            <p style="font-size: 15px; font-weight: 600; color: #7a8a7a; margin: 0 0 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $class->title }}
                            </p>
                            <p style="font-size: 12px; color: #9aaa9a; margin: 0 0 9px; text-transform: capitalize;">
                                {{ $class->type }} · {{ match($class->level) {
                                'initiation'   => 'Iniciación',
                                'intermediate' => 'Intermedio',
                                'advanced'     => 'Avanzado',
                                default        => $class->level
                            } }}
                            </p>
                            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                <span style="font-size: 13px; color: #9aaa9a;">{{ $class->court->name }}</span>
                                @if($class->court->is_outdoor)
                                <span style="padding: 2px 7px; background: #e0eef8; color: #2b6691; border-radius: 20px; font-size: 11px; font-weight: 500;">Exterior</span>
                                @else
                                <span style="padding: 2px 7px; background: #f0f0f8; color: #5a5a8a; border-radius: 20px; font-size: 11px; font-weight: 500;">Interior</span>
                                @endif
                                <span style="font-size: 12px; color: #c8d4c8;">·</span>
                                <span style="font-size: 13px; color: #9aaa9a;">
                                    {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }} — {{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}
                                </span>
                            </div>
                        </div>

                        {{-- BADGES Y PLAZAS --}}
                        <div style="flex-shrink: 0; display: flex; flex-direction: column; align-items: flex-end; gap: 8px;">
                            <div style="display: flex; gap: 6px;">
                                @if($class->visibility === 'public')
                                <span style="padding: 3px 9px; background: #f0f3ee; color: #9aaa9a; border-radius: 20px; font-size: 11px; font-weight: 500;">Pública</span>
                                @else
                                <span style="padding: 3px 9px; background: #f0f3ee; color: #9aaa9a; border-radius: 20px; font-size: 11px; font-weight: 500;">Privada</span>
                                @endif
                                <span style="padding: 3px 9px; background: #f0eaf8; color: #6b4a8f; border-radius: 20px; font-size: 11px; font-weight: 500;">Completada</span>
                            </div>
                            <p style="font-size: 13px; font-weight: 600; color: #9aaa9a; margin: 0;">
                                {{ $enrolled }}/{{ $class->max_players }} plazas
                            </p>
                            <span style="color: #c8d4c8; font-size: 13px;">—</span>
                        </div>
                    </div>
                    @empty
                    <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 48px 20px; text-align: center;">
                        <p style="font-size: 14px; color: #9aaa9a; margin: 0;">No hay clases completadas todavía.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- TAB: CANCELADAS --}}
            <div x-show="tab === 'cancelled'">
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    @forelse($cancelled as $class)
                    @php $enrolled = $class->registered->where('status', 'registered')->count(); @endphp
                    <div style="background: #fdfafa; border-radius: 12px; border: 0.5px solid #ede0e0; padding: 20px 24px; display: flex; align-items: center; gap: 20px;">

                        {{-- BLOQUE FECHA --}}
                        <div style="flex-shrink: 0; width: 52px; text-align: center; background: #f5eeee; border-radius: 10px; padding: 10px 6px;">
                            <p style="font-size: 22px; font-weight: 700; color: #b89090; margin: 0; line-height: 1;">
                                {{ \Carbon\Carbon::parse($class->date)->format('d') }}
                            </p>
                            <p style="font-size: 11px; font-weight: 600; color: #d4b0b0; text-transform: uppercase; margin: 3px 0 0; letter-spacing: 0.05em;">
                                {{ \Carbon\Carbon::parse($class->date)->translatedFormat('M') }}
                            </p>
                        </div>

                        {{-- INFO CENTRAL --}}
                        <div style="flex: 1; min-width: 0;">
                            <p style="font-size: 15px; font-weight: 600; color: #9a7a7a; margin: 0 0 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $class->title }}
                            </p>
                            <p style="font-size: 12px; color: #b89090; margin: 0 0 9px; text-transform: capitalize;">
                                {{ $class->type }} · {{ match($class->level) {
                                'initiation'   => 'Iniciación',
                                'intermediate' => 'Intermedio',
                                'advanced'     => 'Avanzado',
                                default        => $class->level
                            } }}
                            </p>
                            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                <span style="font-size: 13px; color: #b89090;">{{ $class->court->name }}</span>
                                @if($class->court->is_outdoor)
                                <span style="padding: 2px 7px; background: #e0eef8; color: #2b6691; border-radius: 20px; font-size: 11px; font-weight: 500;">Exterior</span>
                                @else
                                <span style="padding: 2px 7px; background: #f0f0f8; color: #5a5a8a; border-radius: 20px; font-size: 11px; font-weight: 500;">Interior</span>
                                @endif
                                <span style="font-size: 12px; color: #d4b0b0;">·</span>
                                <span style="font-size: 13px; color: #b89090;">
                                    {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }} — {{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}
                                </span>
                            </div>
                        </div>

                        {{-- BADGES Y PLAZAS --}}
                        <div style="flex-shrink: 0; display: flex; flex-direction: column; align-items: flex-end; gap: 8px;">
                            <div style="display: flex; gap: 6px;">
                                @if($class->visibility === 'public')
                                <span style="padding: 3px 9px; background: #f5eeee; color: #b89090; border-radius: 20px; font-size: 11px; font-weight: 500;">Pública</span>
                                @else
                                <span style="padding: 3px 9px; background: #f5eeee; color: #b89090; border-radius: 20px; font-size: 11px; font-weight: 500;">Privada</span>
                                @endif
                                <span style="padding: 3px 9px; background: #fce8e8; color: #9b4444; border-radius: 20px; font-size: 11px; font-weight: 500;">Cancelada</span>
                            </div>
                            <p style="font-size: 13px; font-weight: 600; color: #b89090; margin: 0;">
                                {{ $enrolled }}/{{ $class->max_players }} plazas
                            </p>
                            <span style="color: #d4b0b0; font-size: 13px;">—</span>
                        </div>
                    </div>
                    @empty
                    <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 48px 20px; text-align: center;">
                        <p style="font-size: 14px; color: #9aaa9a; margin: 0;">No hay clases canceladas.</p>
                    </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>