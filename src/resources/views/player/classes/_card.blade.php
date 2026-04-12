<div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 20px; display: flex; flex-direction: column; justify-content: space-between;">
    <div>
        <h4 style="font-size: 15px; font-weight: 600; color: #2d3b2d; margin: 0 0 4px;">{{ $class->title }}</h4>

        <p style="font-size: 12px; color: #7a8a7a; margin: 0 0 14px; text-transform: capitalize;">
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
        </div>

        <p style="font-size: 18px; font-weight: 600; color: #6b8f6b; margin: 0;">
            {{ number_format($class->price, 2) }}€
        </p>
    </div>

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