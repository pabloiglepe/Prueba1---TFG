<x-app-layout>
    <x-slot name="header">
        <h2 style="font-size: 20px; font-weight: 600; color: #2d3b2d; margin: 0;">
            Backups de Base de Datos
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- AVISO --}}
        <div style="margin-bottom: 20px; padding: 14px 18px; background: #fef9e8; color: #92650a; border-radius: 8px; font-size: 13px; border-left: 3px solid #f0c060; display: flex; align-items: center; gap: 10px;">
            <iconify-icon icon="ph:warning" style="font-size: 18px; flex-shrink: 0;"></iconify-icon>
            <span>Restaurar un backup <strong>reemplaza todos los datos actuales</strong> de la base de datos. Esta acción no se puede deshacer.</span>
        </div>

        {{-- MENSAJES --}}
        @if(session('success'))
        <div style="margin-bottom: 16px; padding: 14px 18px; background: #e8f0e8; color: #4a6b4a; border-radius: 8px; font-size: 14px; border-left: 3px solid #6b8f6b; display: flex; align-items: center; gap: 10px;">
            <iconify-icon icon="ph:check-circle" style="font-size: 16px; flex-shrink: 0;"></iconify-icon>
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div style="margin-bottom: 16px; padding: 14px 18px; background: #fce8e8; color: #9b4444; border-radius: 8px; font-size: 14px; border-left: 3px solid #e05c5c; display: flex; align-items: center; gap: 10px;">
            <iconify-icon icon="ph:x-circle" style="font-size: 16px; flex-shrink: 0;"></iconify-icon>
            {{ session('error') }}
        </div>
        @endif

        {{-- LISTADO DE BACKUPS --}}
        <div style="display: flex; flex-direction: column; gap: 10px;">
            @forelse($backups as $index => $backup)
            <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 20px 24px; display: flex; align-items: center; gap: 20px;"
                 onmouseover="this.style.borderColor='#b8c9b8'"
                 onmouseout="this.style.borderColor='#d4d9cc'">

                {{-- BLOQUE FECHA --}}
                <div style="flex-shrink: 0; width: 52px; text-align: center; background: #f7f8f5; border-radius: 10px; padding: 10px 6px;">
                    @if($backup['date'])
                    <p style="font-size: 22px; font-weight: 700; color: #2d3b2d; margin: 0; line-height: 1;">
                        {{ $backup['date']->format('d') }}
                    </p>
                    <p style="font-size: 11px; font-weight: 600; color: #6b8f6b; text-transform: uppercase; margin: 3px 0 0; letter-spacing: 0.05em;">
                        {{ $backup['date']->translatedFormat('M') }}
                    </p>
                    @else
                    <iconify-icon icon="ph:database" style="font-size: 22px; color: #9aaa9a;"></iconify-icon>
                    @endif
                </div>

                {{-- INFO CENTRAL --}}
                <div style="flex: 1; min-width: 0;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 3px;">
                        <p style="font-size: 15px; font-weight: 600; color: #2d3b2d; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ $backup['name'] }}
                        </p>
                        @if($index === 0)
                        <span style="flex-shrink: 0; padding: 2px 7px; background: #e8f0e8; color: #4a6b4a; border-radius: 20px; font-size: 11px; font-weight: 500;">
                            Más reciente
                        </span>
                        @endif
                    </div>
                    <div style="display: flex; align-items: center; gap: 16px; margin-top: 6px;">
                        @if($backup['date'])
                        <div style="display: flex; align-items: center; gap: 5px;">
                            <iconify-icon icon="ph:calendar" style="font-size: 13px; color: #6b8f6b; flex-shrink: 0;"></iconify-icon>
                            <span style="font-size: 13px; color: #5a6b5a;">
                                {{ $backup['date']->translatedFormat('d \d\e F \d\e Y') }}
                            </span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 5px;">
                            <iconify-icon icon="ph:clock" style="font-size: 13px; color: #6b8f6b; flex-shrink: 0;"></iconify-icon>
                            <span style="font-size: 13px; color: #5a6b5a;">
                                {{ $backup['date']->format('H:i:s') }}
                            </span>
                        </div>
                        @endif
                        <div style="display: flex; align-items: center; gap: 5px;">
                            <iconify-icon icon="ph:file-sql" style="font-size: 13px; color: #6b8f6b; flex-shrink: 0;"></iconify-icon>
                            <span style="font-size: 13px; color: #5a6b5a;">{{ $backup['size'] }}</span>
                        </div>
                    </div>
                </div>

                {{-- ACCIÓN --}}
                <div style="flex-shrink: 0;">
                    <form action="{{ route('admin.backups.restore') }}" method="POST"
                          onsubmit="return confirm('¿Restaurar la base de datos desde este backup?\n\nEsta acción reemplazará TODOS los datos actuales y no se puede deshacer.')">
                        @csrf
                        <input type="hidden" name="file" value="{{ $backup['name'] }}">
                        <button type="submit"
                                style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background: #fce8e8; color: #9b4444; border: 0.5px solid #f0c0c0; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer;"
                                onmouseover="this.style.background='#f8d4d4'; this.style.borderColor='#e09090';"
                                onmouseout="this.style.background='#fce8e8'; this.style.borderColor='#f0c0c0';">
                            <iconify-icon icon="ph:arrow-counter-clockwise" style="font-size: 15px;"></iconify-icon>
                            Restaurar
                        </button>
                    </form>
                </div>

            </div>
            @empty
            <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 48px 20px; text-align: center;">
                <iconify-icon icon="ph:database" style="font-size: 36px; color: #c8d4c8; display: block; margin: 0 auto 12px;"></iconify-icon>
                <p style="font-size: 14px; color: #9aaa9a; margin: 0 0 4px;">No hay backups disponibles.</p>
                <p style="font-size: 13px; color: #b8c9b8; margin: 0;">El backup automático se genera diariamente a las 03:00.</p>
            </div>
            @endforelse
        </div>

    </div>
</x-app-layout>
