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
                <iconify-icon icon="ph:calendar" style="font-size: 14px; color: #6b8f6b; flex-shrink: 0;"></iconify-icon>
                {{ \Carbon\Carbon::parse($class->date)->format('d/m/Y') }}
            </div>
            <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #5a6b5a;">
                <iconify-icon icon="ph:clock" style="font-size: 14px; color: #6b8f6b; flex-shrink: 0;"></iconify-icon>
                {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }} — {{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}
            </div>
            <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #5a6b5a;">
                <iconify-icon icon="ph:house" style="font-size: 14px; color: #6b8f6b; flex-shrink: 0;"></iconify-icon>
                {{ $class->court->name }}
            </div>
            <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #5a6b5a;">
                <iconify-icon icon="ph:user" style="font-size: 14px; color: #6b8f6b; flex-shrink: 0;"></iconify-icon>
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
                <iconify-icon icon="ph:x-circle" style="font-size: 14px;"></iconify-icon>
                Cancelar inscripción
            </button>
        </form>
    @else
        <p style="margin-top: 16px; padding-top: 16px; border-top: 0.5px solid #f0f3ee; font-size: 12px; color: #9aaa9a; text-align: center;">
            Clase finalizada
        </p>
    @endif
</div>