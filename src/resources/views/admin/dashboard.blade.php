<x-app-layout>

    <!-- MOVEMOS DATOS PROVENIENTES DE PHP A ATRIBUTOS 'data-' PARA QUE NO DE PROBLEMAS AL GENERAR LOS GRÁFICOS -->
    <div id="dashboard-data"
        data-occupancy-labels="{{ htmlspecialchars(json_encode($occupancyLabels), ENT_NOQUOTES) }}"
        data-occupancy-data="{{ htmlspecialchars(json_encode($occupancyData), ENT_NOQUOTES) }}"
        data-revenue-labels="{{ htmlspecialchars(json_encode($revenueLabels), ENT_NOQUOTES) }}"
        data-revenue-data="{{ htmlspecialchars(json_encode($revenueData), ENT_NOQUOTES) }}"
        data-week-data="{{ htmlspecialchars(json_encode($weekData), ENT_NOQUOTES) }}"
        data-month-data="{{ htmlspecialchars(json_encode($monthData), ENT_NOQUOTES) }}"
        data-url-week="{{ route('admin.dashboard.week-detail') }}"
        data-url-month="{{ route('admin.dashboard.month-detail') }}"
        data-status-paid="{{ $statusPaid }}"
        data-status-pending="{{ $statusPending }}"
        data-status-cancelled="{{ $statusCancelled }}">
    </div>

    <x-slot name="header">
        <h2 style="font-size: 20px; font-weight: 600; color: #2d3b2d; margin: 0;">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- TABS --}}
        <div x-data="{ tab: 'resumen' }">

            <div style="display: inline-flex; align-items: center; gap: 15px; padding: 14px 40px; font-size: 14px; font-weight: 500; background: none; border-top: none; border-left: none; border-right: none; cursor: pointer; margin-bottom: -1px;">
                <button @click="tab = 'resumen'"
                    :style="tab === 'resumen' ? 'border-bottom: 2px solid #6b8f6b; color: #4a6b4a;' : 'border-bottom: 2px solid transparent; color: #7a8a7a;'"
                    style="display: inline-flex; align-items: center; gap: 8px; padding: 14px 28px; font-size: 14px; font-weight: 500; background: none; border-top: none; border-left: none; border-right: none; cursor: pointer; margin-bottom: -1px;">
                    <iconify-icon icon="ph:chart-bar" style="font-size: 15px; flex-shrink: 0;"></iconify-icon>
                    Resumen
                </button>
                <button @click="tab = 'entrenadores'"
                    :style="tab === 'entrenadores' ? 'border-bottom: 2px solid #6b8f6b; color: #4a6b4a;' : 'border-bottom: 2px solid transparent; color: #7a8a7a;'"
                    style="display: inline-flex; align-items: center; gap: 8px; padding: 14px 28px; font-size: 14px; font-weight: 500; background: none; border-top: none; border-left: none; border-right: none; cursor: pointer; margin-bottom: -1px;">
                    <iconify-icon icon="ph:users" style="font-size: 15px; flex-shrink: 0;"></iconify-icon>
                    Entrenadores
                </button>
                <button @click="tab = 'exportar'"
                    :style="tab === 'exportar' ? 'border-bottom: 2px solid #6b8f6b; color: #4a6b4a;' : 'border-bottom: 2px solid transparent; color: #7a8a7a;'"
                    style="display: inline-flex; align-items: center; gap: 8px; padding: 14px 28px; font-size: 14px; font-weight: 500; background: none; border-top: none; border-left: none; border-right: none; cursor: pointer; margin-bottom: -1px;">
                    <iconify-icon icon="ph:download-simple" style="font-size: 15px; flex-shrink: 0;"></iconify-icon>
                    Exportar Informes
                </button>
            </div>

            {{-- TAB RESUMEN --}}
            <div x-show="tab === 'resumen'"
                x-on:click.window="if(tab === 'resumen') { setTimeout(() => { window.dispatchEvent(new Event('resize')); }, 50); }"
                class="space-y-6">

                {{-- PANEL HOY --}}
                <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 24px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                        <p style="font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">
                            Hoy · {{ now()->translatedFormat('l d/m/Y') }}
                        </p>
                        <div style="display: flex; gap: 20px;">
                            <span style="font-size: 13px; color: #5a6b5a;">
                                <b style="color: #2d3b2d;">{{ $todayCount }}</b> reservas
                            </span>
                            <span style="font-size: 13px; color: #6b8f6b; font-weight: 600;">
                                {{ number_format($todayRevenue, 2) }}€
                            </span>
                        </div>
                    </div>
                    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                        @forelse($courtStatusNow as $c)
                        <div style="padding: 8px 14px; border-radius: 8px; font-size: 13px;
                            background: {{ $c['occupied'] ? '#fce8e8' : '#e8f0e8' }};
                            color: {{ $c['occupied'] ? '#9b4444' : '#4a6b4a' }};">
                            <b>{{ $c['name'] }}</b>
                            @if($c['occupied'])
                                · Ocupada hasta {{ $c['until'] }}
                                @if($c['player']) <span style="font-size: 12px; opacity: 0.8;">({{ $c['player'] }})</span> @endif
                            @else
                                · Libre
                            @endif
                        </div>
                        @empty
                        <p style="font-size: 13px; color: #9aaa9a; margin: 0;">No hay pistas activas configuradas.</p>
                        @endforelse
                    </div>
                </div>

                {{-- TARJETAS RESUMEN --}}
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px;">
                    <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 24px;">
                        <p style="font-size: 12px; color: #7a8a7a; margin: 0 0 8px;">Reservas totales</p>
                        <p style="font-size: 32px; font-weight: 600; color: #2d3b2d; margin: 0;">{{ $totalReservations }}</p>
                        @if($reservationsTrend !== null)
                        <p style="font-size: 12px; margin: 6px 0 0; color: {{ $reservationsTrend >= 0 ? '#4a6b4a' : '#9b4444' }};">
                            {{ $reservationsTrend >= 0 ? '▲' : '▼' }} {{ abs($reservationsTrend) }}% vs mes anterior
                        </p>
                        @endif
                    </div>
                    <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 24px;">
                        <p style="font-size: 12px; color: #7a8a7a; margin: 0 0 8px;">Ingresos totales</p>
                        <p style="font-size: 32px; font-weight: 600; color: #6b8f6b; margin: 0;">{{ number_format($totalRevenue, 2) }}€</p>
                        @if($revenueTrend !== null)
                        <p style="font-size: 12px; margin: 6px 0 0; color: {{ $revenueTrend >= 0 ? '#4a6b4a' : '#9b4444' }};">
                            {{ $revenueTrend >= 0 ? '▲' : '▼' }} {{ abs($revenueTrend) }}% vs mes anterior
                        </p>
                        @endif
                    </div>
                    <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 24px;">
                        <p style="font-size: 12px; color: #7a8a7a; margin: 0 0 8px;">Jugadores registrados</p>
                        <p style="font-size: 32px; font-weight: 600; color: #2d3b2d; margin: 0;">{{ $totalPlayers }}</p>
                        <p style="font-size: 12px; color: #9aaa9a; margin: 4px 0 0;">{{ $activePlayersCount }} activos últimos 30 días</p>
                    </div>
                </div>

                {{-- GRÁFICO ESTADO DE RESERVAS --}}
                <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 24px;">
                    <h3 style="font-size: 15px; font-weight: 600; color: #2d3b2d; margin: 0 0 4px;">Estado de las reservas</h3>
                    <p style="font-size: 12px; color: #9aaa9a; margin: 0 0 20px;">Distribución global por estado: completadas, pendientes y canceladas</p>
                    <div id="chart-status" style="height: 280px;"></div>
                </div>

                {{-- GRÁFICO OCUPACIÓN --}}
                <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 24px;">
                    <h3 style="font-size: 15px; font-weight: 600; color: #2d3b2d; margin: 0 0 4px;">Ocupación de pistas (semanas -4 / +4 desde hoy)</h3>
                    <p style="font-size: 12px; color: #9aaa9a; margin: 0 0 20px;">Pulsa en un punto para ver el detalle de esa semana</p>
                    <div id="chart-occupancy" style="height: 300px;"></div>
                </div>

                {{-- GRÁFICO INGRESOS --}}
                <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 24px;">
                    <h3 style="font-size: 15px; font-weight: 600; color: #2d3b2d; margin: 0 0 4px;">Ingresos por mes (últimos 6 meses)</h3>
                    <p style="font-size: 12px; color: #9aaa9a; margin: 0 0 20px;">Pulsa en una barra para ver el detalle de ese mes</p>
                    <div id="chart-revenue" style="height: 300px;"></div>
                </div>

            </div>

            {{-- TAB ENTRENADORES --}}
            <div x-show="tab === 'entrenadores'" class="space-y-6">

                {{-- REGISTRO DE ENTRENADORES --}}
                <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 24px;">
                    <p style="font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 16px;">Entrenadores y clases activas</p>

                    @if($coaches->isEmpty())
                    <p style="font-size: 14px; color: #9aaa9a;">No hay entrenadores registrados.</p>
                    @else
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px;">
                        @foreach($coaches as $coach)
                        <div style="border: 0.5px solid #d4d9cc; border-radius: 10px; padding: 16px;">

                            {{-- CABECERA COACH --}}
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 34px; height: 34px; border-radius: 50%; background: #e8f0e8; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 600; color: #4a6b4a; flex-shrink: 0;">
                                        {{ strtoupper(substr($coach->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p style="font-size: 14px; font-weight: 500; color: #2d3b2d; margin: 0;">{{ $coach->name }}</p>
                                        <p style="font-size: 12px; color: #7a8a7a; margin: 0;">{{ $coach->email }}</p>
                                    </div>
                                </div>
                                <span style="padding: 3px 8px; background: #f0eaf8; color: #6b4a8f; border-radius: 20px; font-size: 11px; font-weight: 500; white-space: nowrap;">
                                    {{ $coach->classesByCoach->count() }} clases
                                </span>
                            </div>

                            {{-- CLASES ACTIVAS --}}
                            @if($coach->classesByCoach->isEmpty())
                            <p style="font-size: 12px; color: #9aaa9a;">Sin clases programadas.</p>
                            @else
                            <div style="display: flex; flex-direction: column; gap: 6px;">
                                @foreach($coach->classesByCoach as $class)
                                <div style="background: #f7f8f5; border-radius: 8px; padding: 10px 12px;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                        <p style="font-size: 13px; font-weight: 500; color: #2d3b2d; margin: 0;">{{ $class->title }}</p>
                                        <span style="font-size: 12px; color: #7a8a7a;">
                                            {{ $class->registered->count() }}/{{ $class->max_players }}
                                        </span>
                                    </div>
                                    <p style="font-size: 12px; color: #7a8a7a; margin: 0;">
                                        {{ \Carbon\Carbon::parse($class->date)->format('d/m/Y') }}
                                        · {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }}
                                        - {{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}
                                    </p>
                                    <p style="font-size: 12px; color: #9aaa9a; margin: 2px 0 0;">
                                        {{ match($class->visibility) {
                                                    'public'  => 'Pública',
                                                    'private' => 'Privada',
                                                    default   => $class->visibility
                                                } }}
                                        ·
                                        {{ match($class->level) {
                                                    'initiation'   => 'Iniciación',
                                                    'intermediate' => 'Intermedio',
                                                    'advanced'     => 'Avanzado',
                                                    default        => $class->level
                                                } }}
                                    </p>
                                </div>
                                @endforeach
                            </div>
                            @endif

                            {{-- ENLACE AL PERFIL --}}
                            <div style="margin-top: 12px; padding-top: 12px; border-top: 0.5px solid #f0f3ee;">
                                <a href="{{ route('admin.users.edit', $coach) }}"
                                    style="font-size: 12px; color: #6b8f6b; text-decoration: none; font-weight: 500;"
                                    onmouseover="this.style.color='#4a6b4a'"
                                    onmouseout="this.style.color='#6b8f6b'">
                                    Ver perfil completo →
                                </a>
                            </div>

                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>

            </div>

            {{-- TAB EXPORTAR --}}
            <div x-show="tab === 'exportar'" class="space-y-6">

                {{-- EXPORTACIÓN DE DATOS --}}
                <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 24px;">
                    <p style="font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 20px;">Exportar informes</p>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">

                        {{-- EXPORTAR RESERVAS --}}
                        <div style="border: 0.5px solid #d4d9cc; border-radius: 10px; padding: 20px;">
                            <p style="font-size: 14px; font-weight: 500; color: #2d3b2d; margin: 0 0 4px;">Informe de reservas</p>
                            <p style="font-size: 12px; color: #7a8a7a; margin: 0 0 16px;">Exporta el listado completo de reservas con jugador, pista, horario y precio.</p>
                            <form action="{{ route('admin.export.reservations') }}" method="GET">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 12px;">
                                    <div>
                                        <label style="display: block; font-size: 12px; color: #7a8a7a; margin-bottom: 5px;">Desde</label>
                                        <input type="date" name="start_date"
                                            style="width: 100%; padding: 8px 10px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 13px; color: #2d3b2d; outline: none; box-sizing: border-box;"
                                            onfocus="this.style.borderColor='#6b8f6b'"
                                            onblur="this.style.borderColor='#d4d9cc'">
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 12px; color: #7a8a7a; margin-bottom: 5px;">Hasta</label>
                                        <input type="date" name="end_date"
                                            style="width: 100%; padding: 8px 10px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 13px; color: #2d3b2d; outline: none; box-sizing: border-box;"
                                            onfocus="this.style.borderColor='#6b8f6b'"
                                            onblur="this.style.borderColor='#d4d9cc'">
                                    </div>
                                </div>
                                <button type="submit"
                                    style="width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: 8px; background: #6b8f6b; color: #fff; font-size: 14px; font-weight: 500; padding: 10px; border-radius: 8px; border: none; cursor: pointer;"
                                    onmouseover="this.style.background='#4a6b4a'"
                                    onmouseout="this.style.background='#6b8f6b'">
                                    <iconify-icon icon="ph:download-simple" style="font-size: 15px;"></iconify-icon>
                                    Descargar reservas (.xlsx)
                                </button>
                            </form>
                        </div>

                        {{-- EXPORTAR INGRESOS --}}
                        <div style="border: 0.5px solid #d4d9cc; border-radius: 10px; padding: 20px;">
                            <p style="font-size: 14px; font-weight: 500; color: #2d3b2d; margin: 0 0 4px;">Informe de ingresos</p>
                            <p style="font-size: 12px; color: #7a8a7a; margin: 0 0 16px;">Exporta el resumen de ingresos de un mes concreto.</p>
                            <form action="{{ route('admin.export.revenue') }}" method="GET">
                                <div style="margin-bottom: 12px;">
                                    <label style="display: block; font-size: 12px; color: #7a8a7a; margin-bottom: 5px;">Mes</label>
                                    <input type="month" name="month"
                                        style="width: 100%; padding: 8px 10px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 13px; color: #2d3b2d; outline: none; box-sizing: border-box;"
                                        onfocus="this.style.borderColor='#6b8f6b'"
                                        onblur="this.style.borderColor='#d4d9cc'">
                                </div>
                                <button type="submit"
                                    style="width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: 8px; background: #6b8f6b; color: #fff; font-size: 14px; font-weight: 500; padding: 10px; border-radius: 8px; border: none; cursor: pointer;"
                                    onmouseover="this.style.background='#4a6b4a'"
                                    onmouseout="this.style.background='#6b8f6b'">
                                    <iconify-icon icon="ph:download-simple" style="font-size: 15px;"></iconify-icon>
                                    Descargar ingresos (.xlsx)
                                </button>
                            </form>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- MODAL --}}
    <div id="modal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.6);"
        class="items-center justify-center">
        <div style="background:white; border-radius:12px; width:90%; max-width:750px; max-height:80vh;
            display:flex; flex-direction:column; margin:7vh; box-shadow: 0 25px 50px rgba(0,0,0,0.3);">

            {{-- CABECERA MODAL --}}
            <div style="display:flex; justify-content:space-between; align-items:center; padding:20px 24px; border-bottom:0.5px solid #d4d9cc;">
                <h3 id="modal-title" style="font-weight:600; font-size:1.1rem; color:#2d3b2d;"></h3>
                <button onclick="closeModal()"
                    style="color:#9aaa9a; font-size:1.5rem; line-height:1; background:none; border:none; cursor:pointer;">
                    &times;
                </button>
            </div>

            {{-- CONTENIDO MODAL --}}
            <div id="modal-body" style="overflow-y:auto; padding:24px; flex:1;">
                <div id="modal-loading" style="text-align:center; color:#9aaa9a; padding:40px 0;">Cargando...</div>
                <div id="modal-content" style="display:none;"></div>
            </div>

        </div>
    </div>

    <script>
        /**
         * FUNCIÓN QUE FORMA LA TABLA QUE CONTIENE LA INFORMACIÓN DE LAS RESERVAS
         * 
         * @param reservations 
         * 
         * */
        function reservationsTable(reservations) {
            if (!reservations.length) return '<p style="color:#c0625e;font-size:14px;">No hay reservas.</p>';
            return `
                <table style="width:100%;border-collapse:collapse;font-size:14px;">
                    <thead>
                        <tr style="background:#f7f8f5;">
                            <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:600;color:#7a8a7a;text-transform:uppercase;letter-spacing:0.05em;">Fecha</th>
                            <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:600;color:#7a8a7a;text-transform:uppercase;letter-spacing:0.05em;">Jugador</th>
                            <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:600;color:#7a8a7a;text-transform:uppercase;letter-spacing:0.05em;">Pista</th>
                            <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:600;color:#7a8a7a;text-transform:uppercase;letter-spacing:0.05em;">Horario</th>
                            <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:600;color:#7a8a7a;text-transform:uppercase;letter-spacing:0.05em;">Precio</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${reservations.map(r => `
                            <tr style="border-top:0.5px solid #f0f3ee;">
                                <td style="padding:12px 16px;color:#2d3b2d;">${r.date}</td>
                                <td style="padding:12px 16px;color:#5a6b5a;">${r.player}</td>
                                <td style="padding:12px 16px;color:#5a6b5a;">${r.court}</td>
                                <td style="padding:12px 16px;color:#5a6b5a;">${r.time}</td>
                                <td style="padding:12px 16px;font-weight:500;color:#6b8f6b;">${r.price}</td>
                            </tr>`).join('')}
                    </tbody>
                </table>`;
        }

        function renderWeekModal(data) {
            document.getElementById('modal-title').textContent = data.label;
            document.getElementById('modal-loading').style.display = 'none';
            const content = document.getElementById('modal-content');
            content.innerHTML = reservationsTable(data.reservations);
            content.style.display = 'block';
        }

        function renderMonthModal(data) {
            document.getElementById('modal-title').textContent = data.label;
            document.getElementById('modal-loading').style.display = 'none';
            const content = document.getElementById('modal-content');
            const byCourt = data.by_court.map(c => `
        <div style="display:flex; justify-content:space-between; padding:10px 0; border-bottom:0.5px solid #f0f3ee;">
            <span style="font-weight:500; color:#2d3b2d;">${c.court}</span>
            <span style="color:#7a8a7a; font-size:0.875rem;">${c.count} reservas</span>
            <span style="font-weight:600; color:#6b8f6b;">${c.total}</span>
        </div>`).join('');

            content.innerHTML = `
        <div style="margin-bottom:24px;">
            <h4 style="font-weight:600; color:#2d3b2d; margin-bottom:12px;">Desglose por pista</h4>
            <div style="background:#f7f8f5; border-radius:8px; padding:16px;">
                ${byCourt || '<p style="color:#9aaa9a; font-size:0.875rem;">Sin datos.</p>'}
            </div>
        </div>
        <div>
            <h4 style="font-weight:600; color:#2d3b2d; margin-bottom:12px;">Listado de reservas</h4>
            ${reservationsTable(data.reservations)}
        </div>`;
            content.style.display = 'block';
        }

        /** 
         * FUNCIÓN PARA ABRIR MODAL 
         * */
        function openModal() {
            const modal = document.getElementById('modal');
            modal.style.display = 'flex';
            document.getElementById('modal-loading').style.display = 'block';
            document.getElementById('modal-content').style.display = 'none';
            document.getElementById('modal-content').innerHTML = '';
            document.body.style.overflow = 'hidden';
        }

        /** 
         * FUNCIÓN PARA CERRAR MODAL
         * */
        function closeModal() {
            document.getElementById('modal').style.display = 'none';
            document.body.style.overflow = '';
        }

        // EVENTO PARA CERRAR EL MODAL AL PULSAR FUERA DE ÉL
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('modal');
            if (modal && e.target === modal) closeModal();
        });

        // SOLO LO RELACIONADO CON LOS GRÁFICOS VAN EN ESTE BLOQUE
        document.addEventListener('livewire:navigated', function() {

            const element = document.getElementById('dashboard-data');
            if (!element) return;

            const occupancyLabels = JSON.parse(element.dataset.occupancyLabels);
            const occupancyData = JSON.parse(element.dataset.occupancyData);
            const revenueLabels = JSON.parse(element.dataset.revenueLabels);
            const revenueData = JSON.parse(element.dataset.revenueData);
            const weekData = JSON.parse(element.dataset.weekData);
            const monthData = JSON.parse(element.dataset.monthData);
            const urlWeek = element.dataset.urlWeek;
            const urlMonth = element.dataset.urlMonth;

            // SELECTORES DE LAS GRÁFICAS EN HTML
            const statusPaid      = parseInt(element.dataset.statusPaid);
            const statusPending   = parseInt(element.dataset.statusPending);
            const statusCancelled = parseInt(element.dataset.statusCancelled);

            // SELECTORES DE LAS GRÁFICAS EN HTML
            const occupancy = document.getElementById('chart-occupancy');
            const revenue   = document.getElementById('chart-revenue');
            const statusEl  = document.getElementById('chart-status');

            if (!occupancy || !revenue || !statusEl) return;

            // LIMPIAR INSTANCIAS ANTERIORES
            const existingOccupancy = echarts.getInstanceByDom(occupancy);
            const existingRevenue   = echarts.getInstanceByDom(revenue);
            if (existingOccupancy) existingOccupancy.dispose();
            if (existingRevenue)   existingRevenue.dispose();

            const chartOccupancy = echarts.init(occupancy);
            const chartRevenue   = echarts.init(revenue);
            const chartStatus    = echarts.init(statusEl);

            // FORZAMOS RESIZE TRAS INICIALIZAR POR SI EL CONTENEDOR TENÍA DIMENSIONES CERO
            setTimeout(() => {
                chartOccupancy.resize();
                chartRevenue.resize();
                chartStatus.resize();
            }, 50);

            // GRÁFICO OCUPACIÓN -> LÍNEAS
            chartOccupancy.setOption({
                tooltip: {
                    trigger: 'axis',
                    formatter: (params) => {
                        const meta = weekData[params[0].dataIndex];
                        return `<b>${params[0].name}</b><br/>
                            Reservas: <b>${params[0].value}</b><br/>
                            <span style="font-size:11px;color:#9aaa9a">Pulsa para ver el detalle</span>`;
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    data: occupancyLabels,
                    boundaryGap: false
                },
                yAxis: {
                    type: 'value',
                    minInterval: 1,
                    name: 'Reservas'
                },
                series: [{
                    name: 'Reservas',
                    type: 'line',
                    smooth: true,
                    data: occupancyData,
                    itemStyle: {
                        color: '#6b8f6b'
                    },
                    areaStyle: {
                        color: 'rgba(107,143,107,0.1)'
                    },
                    emphasis: {
                        itemStyle: {
                            borderWidth: 3,
                            borderColor: '#4a6b4a'
                        }
                    },
                }]
            });

            chartOccupancy.on('click', (params) => {
                const data = weekData[params.dataIndex];
                openModal();
                fetch(`${urlWeek}?week=${data.week}&year=${data.year}`)
                    .then(r => r.json())
                    .then(data => renderWeekModal(data));
            });

            // GRÁFICO INGRESOS -> BARRAS
            chartRevenue.setOption({
                tooltip: {
                    trigger: 'axis',
                    formatter: (params) => {
                        return `<b>${params[0].name}</b><br/>
                            Ingresos: <b>${params[0].value.toFixed(2)}€</b><br/>
                            <span style="font-size:11px;color:#9aaa9a">Pulsa para ver el detalle</span>`;
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    data: revenueLabels
                },
                yAxis: {
                    type: 'value',
                    name: 'Euros (€)'
                },
                series: [{
                    name: 'Ingresos',
                    type: 'bar',
                    data: revenueData,
                    itemStyle: {
                        color: '#6b8f6b',
                        borderRadius: [6, 6, 0, 0]
                    },
                    emphasis: {
                        itemStyle: {
                            color: '#4a6b4a'
                        }
                    },
                }]
            });

            chartRevenue.on('click', (params) => {
                const data = monthData[params.dataIndex];
                openModal();
                fetch(`${urlMonth}?month=${data.month}&year=${data.year}`)
                    .then(r => r.json())
                    .then(data => renderMonthModal(data));
            });

            // GRÁFICO ESTADO DE RESERVAS -> PIE
            chartStatus.setOption({
                tooltip: {
                    trigger: 'item',
                    formatter: '{b}: {c} ({d}%)'
                },
                legend: {
                    orient: 'vertical',
                    right: '5%',
                    top: 'center',
                    textStyle: { color: '#5a6b5a', fontSize: 13 }
                },
                series: [{
                    name: 'Reservas',
                    type: 'pie',
                    radius: ['40%', '70%'],
                    center: ['40%', '50%'],
                    avoidLabelOverlap: false,
                    itemStyle: { borderRadius: 6, borderColor: '#fff', borderWidth: 2 },
                    label: { show: false },
                    emphasis: {
                        label: { show: true, fontSize: 14, fontWeight: 'bold' }
                    },
                    labelLine: { show: false },
                    data: [
                        { value: statusPaid,      name: 'Completadas', itemStyle: { color: '#6b8f6b' } },
                        { value: statusPending,   name: 'Pendientes',  itemStyle: { color: '#f0a840' } },
                        { value: statusCancelled, name: 'Canceladas',  itemStyle: { color: '#c0625e' } }
                    ]
                }]
            });

            // RESIZE AL CAMBIAR DE TAB
            document.querySelectorAll('[\\@click]').forEach(btn => {
                btn.addEventListener('click', () => {
                    setTimeout(() => {
                        chartOccupancy.resize();
                        chartRevenue.resize();
                        chartStatus.resize();
                    }, 50);
                });
            });

            // HACEMOS LOS GRÁFICOS RESPONSIVE
            window.addEventListener('resize', () => {
                chartOccupancy.resize();
                chartRevenue.resize();
                chartStatus.resize();
            });
        });
    </script>

</x-app-layout>