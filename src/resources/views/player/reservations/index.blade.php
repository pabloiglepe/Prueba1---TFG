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
                <svg xmlns="http://www.w3.org/2000/svg" style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                    <line x1="12" y1="14" x2="12" y2="18"/>
                    <line x1="10" y1="16" x2="14" y2="16"/>
                </svg>
                Nueva Reserva
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- MENSAJE DE ÉXITO --}}
        @if(session('success'))
        <div style="margin-bottom: 16px; padding: 14px 18px; background: #e8f0e8; color: #4a6b4a; border-radius: 8px; font-size: 14px; border-left: 3px solid #6b8f6b;">
            {{ session('success') }}
        </div>
        @endif

        {{-- TABLA DE RESERVAS --}}
        <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f7f8f5; border-bottom: 0.5px solid #d4d9cc;">
                        <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Fecha</th>
                        <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Horario</th>
                        <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Pista</th>
                        <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Precio</th>
                        <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Estado</th>
                        <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservations as $reservation)
                    <tr style="border-top: 0.5px solid #f0f3ee;" onmouseover="this.style.background='#fafbf9'" onmouseout="this.style.background='#fff'">
                        <td style="padding: 14px 20px; font-size: 14px; font-weight: 500; color: #2d3b2d;">
                            {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d/m/Y') }}
                        </td>
                        <td style="padding: 14px 20px; font-size: 14px; color: #5a6b5a;">
                            {{ \Carbon\Carbon::parse($reservation->start_time)->format('H:i') }} —
                            {{ \Carbon\Carbon::parse($reservation->end_time)->format('H:i') }}
                        </td>
                        <td style="padding: 14px 20px; font-size: 14px; color: #2d3b2d;">
                            {{ $reservation->court->name }}
                        </td>
                        <td style="padding: 14px 20px; font-size: 14px; font-weight: 500; color: #6b8f6b;">
                            {{ number_format($reservation->total_price, 2) }}€
                        </td>
                        <td style="padding: 14px 20px;">
                            @if($reservation->status === 'pending')
                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #fef9e8; color: #92650a; border-radius: 20px; font-size: 12px; font-weight: 500;">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width:10px;height:10px;fill:#92650a;" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>
                                    Pendiente
                                </span>
                            @elseif($reservation->status === 'paid')
                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #e8f0e8; color: #4a6b4a; border-radius: 20px; font-size: 12px; font-weight: 500;">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width:10px;height:10px;fill:#4a6b4a;" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>
                                    Pagada
                                </span>
                            @elseif($reservation->status === 'cancelled')
                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #fce8e8; color: #9b4444; border-radius: 20px; font-size: 12px; font-weight: 500;">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width:10px;height:10px;fill:#9b4444;" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>
                                    Cancelada
                                </span>
                            @endif
                        </td>
                        <td style="padding: 14px 20px;">
                            @if($reservation->status !== 'cancelled')
                                <form action="{{ route('player.reservations.destroy', $reservation) }}"
                                      method="POST" class="inline"
                                      onsubmit="return confirm('¿Cancelar esta reserva?')">
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
                            @else
                                <span style="color: #c8d4c8; font-size: 14px;">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="padding: 48px 20px; text-align: center; font-size: 14px; color: #9aaa9a;">
                            No tienes reservas todavía.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>