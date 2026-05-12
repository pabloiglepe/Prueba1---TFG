<x-app-layout>
    <x-slot name="header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 20px; font-weight: 600; color: #2d3b2d; margin: 0;">
                Mis Reservas
            </h2>
            <a href="{{ route('player.reservations.create') }}"
               style="display: inline-flex; align-items: center; gap: 8px; background: #6b8f6b; color: #fff; font-size: 14px; font-weight: 500; padding: 9px 18px; border-radius: 8px; text-decoration: none;"
               onmouseover="this.style.background='#4a6b4a'"
               onmouseout="this.style.background='#6b8f6b'">
                <iconify-icon icon="ph:calendar-plus" style="font-size: 16px;"></iconify-icon>
                Nueva Reserva
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
            $pendingReservations   = $reservations->where('status', 'pending');
            $paidReservations      = $reservations->where('status', 'paid');
            $cancelledReservations = $reservations->where('status', 'cancelled');
        @endphp

        <div x-data="{ tab: 'pending' }">

            {{-- TABS --}}
            <div style="display: flex; gap: 15px; border-bottom: 0.5px solid #d4d9cc; font-weight: 500; background: none; border-top: none; border-left: none; border-right: none; cursor: pointer; margin-bottom: 24px;">
                <button @click="tab = 'pending'"
                    :style="tab === 'pending' ? 'border-bottom: 2px solid #6b8f6b; color: #4a6b4a;' : 'border-bottom: 2px solid transparent; color: #7a8a7a;'"
                    style="display: inline-flex; align-items: center; gap: 8px; padding: 14px 40px; font-size: 14px; font-weight: 500; background: none; border-top: none; border-left: none; border-right: none; cursor: pointer; margin-bottom: -1px;">
                    <iconify-icon icon="ph:hourglass" style="font-size: 15px; flex-shrink: 0;"></iconify-icon>
                    Pendientes
                </button>
                <button @click="tab = 'paid'"
                    :style="tab === 'paid' ? 'border-bottom: 2px solid #6b8f6b; color: #4a6b4a;' : 'border-bottom: 2px solid transparent; color: #7a8a7a;'"
                    style="display: inline-flex; align-items: center; gap: 8px; padding: 14px 40px; font-size: 14px; font-weight: 500; background: none; border-top: none; border-left: none; border-right: none; cursor: pointer; margin-bottom: -1px;">
                    <iconify-icon icon="ph:check-circle" style="font-size: 15px; flex-shrink: 0;"></iconify-icon>
                    Pagadas
                </button>
                <button @click="tab = 'cancelled'"
                    :style="tab === 'cancelled' ? 'border-bottom: 2px solid #6b8f6b; color: #4a6b4a;' : 'border-bottom: 2px solid transparent; color: #7a8a7a;'"
                    style="display: inline-flex; align-items: center; gap: 8px; padding: 14px 40px; font-size: 14px; font-weight: 500; background: none; border-top: none; border-left: none; border-right: none; cursor: pointer; margin-bottom: -1px;">
                    <iconify-icon icon="ph:x-circle" style="font-size: 15px; flex-shrink: 0;"></iconify-icon>
                    Canceladas
                </button>
            </div>

            {{-- TAB: PENDIENTES --}}
            <div x-show="tab === 'pending'">
            <div style="display: flex; flex-direction: column; gap: 10px;">
                @forelse($pendingReservations as $reservation)
                <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 20px 24px; display: flex; align-items: center; gap: 20px;"
                     onmouseover="this.style.borderColor='#b8c9b8'"
                     onmouseout="this.style.borderColor='#d4d9cc'">

                    {{-- BLOQUE FECHA --}}
                    <div style="flex-shrink: 0; width: 52px; text-align: center; background: #f7f8f5; border-radius: 10px; padding: 10px 6px;">
                        <p style="font-size: 22px; font-weight: 700; color: #2d3b2d; margin: 0; line-height: 1;">
                            {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d') }}
                        </p>
                        <p style="font-size: 11px; font-weight: 600; color: #6b8f6b; text-transform: uppercase; margin: 3px 0 0; letter-spacing: 0.05em;">
                            {{ \Carbon\Carbon::parse($reservation->reservation_date)->translatedFormat('M') }}
                        </p>
                    </div>

                    {{-- INFO CENTRAL --}}
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 3px;">
                            <p style="font-size: 15px; font-weight: 600; color: #2d3b2d; margin: 0;">
                                {{ $reservation->court->name }}
                            </p>
                            @if($reservation->court->is_outdoor)
                                <span style="padding: 2px 7px; background: #e0eef8; color: #2b6691; border-radius: 20px; font-size: 11px; font-weight: 500;">Exterior</span>
                            @else
                                <span style="padding: 2px 7px; background: #f0f0f8; color: #5a5a8a; border-radius: 20px; font-size: 11px; font-weight: 500;">Interior</span>
                            @endif
                        </div>
                        <p style="font-size: 12px; color: #7a8a7a; margin: 0 0 9px; text-transform: capitalize;">
                            {{ $reservation->court->type }} · {{ $reservation->court->surface }}
                        </p>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <iconify-icon icon="ph:clock" style="font-size: 13px; color: #6b8f6b; flex-shrink: 0;"></iconify-icon>
                            <span style="font-size: 13px; color: #5a6b5a;">
                                {{ \Carbon\Carbon::parse($reservation->start_time)->format('H:i') }} — {{ \Carbon\Carbon::parse($reservation->end_time)->format('H:i') }}
                            </span>
                        </div>
                    </div>

                    {{-- ESTADO, PRECIO Y ACCIÓN --}}
                    <div style="flex-shrink: 0; display: flex; flex-direction: column; align-items: flex-end; gap: 8px;">
                        <span style="padding: 3px 9px; background: #fef9e8; color: #92650a; border-radius: 20px; font-size: 11px; font-weight: 500;">Pendiente</span>
                        <p style="font-size: 16px; font-weight: 600; color: #6b8f6b; margin: 0;">
                            {{ number_format($reservation->total_price, 2) }}€
                        </p>
                        <form action="{{ route('player.reservations.destroy', $reservation) }}"
                              method="POST" class="inline"
                              onsubmit="return confirm('¿Cancelar esta reserva?')">
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
                @empty
                <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 48px 20px; text-align: center;">
                    <p style="font-size: 14px; color: #9aaa9a; margin: 0;">No tienes reservas pendientes.</p>
                </div>
                @endforelse
            </div>
            </div>

            {{-- TAB: PAGADAS --}}
            <div x-show="tab === 'paid'">
            <div style="display: flex; flex-direction: column; gap: 10px;">
                @forelse($paidReservations as $reservation)
                <div style="background: #f4f8f4; border-radius: 12px; border: 0.5px solid #c8dac8; padding: 20px 24px; display: flex; align-items: center; gap: 20px;"
                     onmouseover="this.style.borderColor='#a8c4a8'"
                     onmouseout="this.style.borderColor='#c8dac8'">

                    {{-- BLOQUE FECHA --}}
                    <div style="flex-shrink: 0; width: 52px; text-align: center; background: #e8f0e8; border-radius: 10px; padding: 10px 6px;">
                        <p style="font-size: 22px; font-weight: 700; color: #2d3b2d; margin: 0; line-height: 1;">
                            {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d') }}
                        </p>
                        <p style="font-size: 11px; font-weight: 600; color: #6b8f6b; text-transform: uppercase; margin: 3px 0 0; letter-spacing: 0.05em;">
                            {{ \Carbon\Carbon::parse($reservation->reservation_date)->translatedFormat('M') }}
                        </p>
                    </div>

                    {{-- INFO CENTRAL --}}
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 3px;">
                            <p style="font-size: 15px; font-weight: 600; color: #2d3b2d; margin: 0;">
                                {{ $reservation->court->name }}
                            </p>
                            @if($reservation->court->is_outdoor)
                                <span style="padding: 2px 7px; background: #e0eef8; color: #2b6691; border-radius: 20px; font-size: 11px; font-weight: 500;">Exterior</span>
                            @else
                                <span style="padding: 2px 7px; background: #f0f0f8; color: #5a5a8a; border-radius: 20px; font-size: 11px; font-weight: 500;">Interior</span>
                            @endif
                        </div>
                        <p style="font-size: 12px; color: #5a6b5a; margin: 0 0 9px; text-transform: capitalize;">
                            {{ $reservation->court->type }} · {{ $reservation->court->surface }}
                        </p>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <iconify-icon icon="ph:clock" style="font-size: 13px; color: #6b8f6b; flex-shrink: 0;"></iconify-icon>
                            <span style="font-size: 13px; color: #4a6b4a;">
                                {{ \Carbon\Carbon::parse($reservation->start_time)->format('H:i') }} — {{ \Carbon\Carbon::parse($reservation->end_time)->format('H:i') }}
                            </span>
                        </div>
                    </div>

                    {{-- ESTADO Y PRECIO --}}
                    <div style="flex-shrink: 0; display: flex; flex-direction: column; align-items: flex-end; gap: 8px;">
                        <span style="padding: 3px 9px; background: #e8f0e8; color: #4a6b4a; border-radius: 20px; font-size: 11px; font-weight: 500;">Pagada</span>
                        <p style="font-size: 16px; font-weight: 600; color: #6b8f6b; margin: 0;">
                            {{ number_format($reservation->total_price, 2) }}€
                        </p>
                        <span style="color: #a8c4a8; font-size: 13px;">—</span>
                    </div>
                </div>
                @empty
                <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 48px 20px; text-align: center;">
                    <p style="font-size: 14px; color: #9aaa9a; margin: 0;">No tienes reservas pagadas.</p>
                </div>
                @endforelse
            </div>
            </div>

            {{-- TAB: CANCELADAS --}}
            <div x-show="tab === 'cancelled'">
            <div style="display: flex; flex-direction: column; gap: 10px;">
                @forelse($cancelledReservations as $reservation)
                <div style="background: #fdfafa; border-radius: 12px; border: 0.5px solid #ede0e0; padding: 20px 24px; display: flex; align-items: center; gap: 20px;">

                    {{-- BLOQUE FECHA --}}
                    <div style="flex-shrink: 0; width: 52px; text-align: center; background: #f5eeee; border-radius: 10px; padding: 10px 6px;">
                        <p style="font-size: 22px; font-weight: 700; color: #b89090; margin: 0; line-height: 1;">
                            {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d') }}
                        </p>
                        <p style="font-size: 11px; font-weight: 600; color: #d4b0b0; text-transform: uppercase; margin: 3px 0 0; letter-spacing: 0.05em;">
                            {{ \Carbon\Carbon::parse($reservation->reservation_date)->translatedFormat('M') }}
                        </p>
                    </div>

                    {{-- INFO CENTRAL --}}
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 3px;">
                            <p style="font-size: 15px; font-weight: 600; color: #9a7a7a; margin: 0;">
                                {{ $reservation->court->name }}
                            </p>
                            @if($reservation->court->is_outdoor)
                                <span style="padding: 2px 7px; background: #e0eef8; color: #2b6691; border-radius: 20px; font-size: 11px; font-weight: 500;">Exterior</span>
                            @else
                                <span style="padding: 2px 7px; background: #f0f0f8; color: #5a5a8a; border-radius: 20px; font-size: 11px; font-weight: 500;">Interior</span>
                            @endif
                        </div>
                        <p style="font-size: 12px; color: #b89090; margin: 0 0 9px; text-transform: capitalize;">
                            {{ $reservation->court->type }} · {{ $reservation->court->surface }}
                        </p>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <iconify-icon icon="ph:clock" style="font-size: 13px; color: #d4b0b0; flex-shrink: 0;"></iconify-icon>
                            <span style="font-size: 13px; color: #b89090;">
                                {{ \Carbon\Carbon::parse($reservation->start_time)->format('H:i') }} — {{ \Carbon\Carbon::parse($reservation->end_time)->format('H:i') }}
                            </span>
                        </div>
                    </div>

                    {{-- ESTADO Y PRECIO --}}
                    <div style="flex-shrink: 0; display: flex; flex-direction: column; align-items: flex-end; gap: 8px;">
                        <span style="padding: 3px 9px; background: #fce8e8; color: #9b4444; border-radius: 20px; font-size: 11px; font-weight: 500;">Cancelada</span>
                        <p style="font-size: 16px; font-weight: 600; color: #b89090; margin: 0;">
                            {{ number_format($reservation->total_price, 2) }}€
                        </p>
                        <span style="color: #d4b0b0; font-size: 13px;">—</span>
                    </div>
                </div>
                @empty
                <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 48px 20px; text-align: center;">
                    <p style="font-size: 14px; color: #9aaa9a; margin: 0;">No tienes reservas canceladas.</p>
                </div>
                @endforelse
            </div>
            </div>

        </div>
    </div>
</x-app-layout>
