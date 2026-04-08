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
        data-url-month="{{ route('admin.dashboard.month-detail') }}">
    </div>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- TABS --}}
        <div x-data="{ tab: 'resumen' }">

            <div class="flex border-b border-gray-200 mb-6">
                <button @click="tab = 'resumen'"
                    :class="tab === 'resumen' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="px-6 py-3 text-sm font-medium border-b-2 transition">
                    Resumen
                </button>
                <button @click="tab = 'entrenadores'"
                    :class="tab === 'entrenadores' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="px-6 py-3 text-sm font-medium border-b-2 transition">
                    Entrenadores
                </button>
                <button @click="tab = 'exportar'"
                    :class="tab === 'exportar' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="px-6 py-3 text-sm font-medium border-b-2 transition">
                    Exportar
                </button>
            </div>

            {{-- TAB RESUMEN --}}
            <div x-show="tab === 'resumen'" class="space-y-6">

                {{-- TARJETAS RESUMEN --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div class="bg-white shadow rounded-lg p-6">
                        <p class="text-sm text-gray-500 mb-1">Reservas totales</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $totalReservations }}</p>
                    </div>
                    <div class="bg-white shadow rounded-lg p-6">
                        <p class="text-sm text-gray-500 mb-1">Ingresos totales</p>
                        <p class="text-3xl font-bold text-green-600">{{ number_format($totalRevenue, 2) }}€</p>
                    </div>
                    <div class="bg-white shadow rounded-lg p-6">
                        <p class="text-sm text-gray-500 mb-1">Jugadores registrados</p>
                        <p class="text-3xl font-bold text-purple-600">{{ $totalPlayers }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $activePlayersCount }} activos últimos 30 días</p>
                    </div>
                </div>

                {{-- GRÁFICO OCUPACIÓN --}}
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="font-semibold text-gray-700 mb-1">Ocupación de pistas (últimas 8 semanas)</h3>
                    <p class="text-xs text-gray-400 mb-4">Pulsa en un punto para ver el detalle de esa semana</p>
                    <div id="chart-occupancy" style="height: 300px;"></div>
                </div>

                {{-- GRÁFICO INGRESOS --}}
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="font-semibold text-gray-700 mb-1">Ingresos por mes (últimos 6 meses)</h3>
                    <p class="text-xs text-gray-400 mb-4">Pulsa en una barra para ver el detalle de ese mes</p>
                    <div id="chart-revenue" style="height: 300px;"></div>
                </div>

            </div>

            {{-- TAB ENTRENADORES --}}
            <div x-show="tab === 'entrenadores'" class="space-y-6">

                {{-- REGISTRO DE ENTRENADORES --}}
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="font-semibold text-gray-700 mb-4">Entrenadores y clases activas</h3>

                    @if($coaches->isEmpty())
                    <p class="text-gray-400 text-sm">No hay entrenadores registrados.</p>
                    @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($coaches as $coach)
                        <div class="border border-gray-200 rounded-lg p-4">

                            {{-- CABECERA COACH --}}
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $coach->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $coach->email }}</p>
                                </div>
                                <span class="px-2 py-1 bg-purple-100 text-purple-700 text-xs rounded-full">
                                    {{ $coach->classesByCoach->count() }} clases
                                </span>
                            </div>

                            {{-- CLASES ACTIVAS --}}
                            @if($coach->classesByCoach->isEmpty())
                            <p class="text-xs text-gray-400">Sin clases programadas.</p>
                            @else
                            <div class="space-y-2">
                                @foreach($coach->classesByCoach as $class)
                                <div class="bg-gray-50 rounded p-2 text-xs">
                                    <div class="flex justify-between items-center">
                                        <p class="font-medium text-gray-700">{{ $class->title }}</p>
                                        <span class="text-gray-400">
                                            {{ $class->registered->count() }}/{{ $class->max_players }}
                                        </span>
                                    </div>
                                    <p class="text-gray-400 mt-0.5">
                                        {{ \Carbon\Carbon::parse($class->date)->format('d/m/Y') }}
                                    </p>
                                    <p class="text-gray-400 mt-0.5">
                                        {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }}
                                        - {{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}
                                    </p>
                                    <p class="text-gray-400">
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
                            <div class="mt-3 pt-3 border-t border-gray-100">
                                <a href="{{ route('admin.users.edit', $coach) }}"
                                    class="text-xs text-blue-600 hover:underline">
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
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="font-semibold text-gray-700 mb-4">Exportar informes</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                        {{-- EXPORTAR RESERVAS --}}
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-700 mb-1">Informe de reservas</h4>
                            <p class="text-xs text-gray-400 mb-4">Exporta el listado completo de reservas con jugador, pista, horario y precio.</p>
                            <form action="{{ route('admin.export.reservations') }}" method="GET">
                                <div class="grid grid-cols-2 gap-2 mb-3">
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Desde</label>
                                        <input type="date" name="start_date"
                                            class="w-full border-gray-300 rounded-lg text-sm shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Hasta</label>
                                        <input type="date" name="end_date"
                                            class="w-full border-gray-300 rounded-lg text-sm shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                                <button type="submit"
                                    class="w-full bg-blue-600 text-white text-sm py-2 rounded-lg hover:bg-blue-700">
                                    Descargar reservas (.xlsx)
                                </button>
                            </form>
                        </div>

                        {{-- EXPORTAR INGRESOS --}}
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-700 mb-1">Informe de ingresos</h4>
                            <p class="text-xs text-gray-400 mb-4">Exporta el resumen de ingresos de un mes concreto.</p>
                            <form action="{{ route('admin.export.revenue') }}" method="GET">
                                <div class="mb-3">
                                    <label class="block text-xs text-gray-500 mb-1">Mes</label>
                                    <input type="month" name="month"
                                        class="w-full border-gray-300 rounded-lg text-sm shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <button type="submit"
                                    class="w-full bg-green-600 text-white text-sm py-2 rounded-lg hover:bg-green-700">
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
            <div style="display:flex; justify-content:space-between; align-items:center; padding:20px 24px; border-bottom:1px solid #e5e7eb;">
                <h3 id="modal-title" style="font-weight:600; font-size:1.1rem; color:#1f2937;"></h3>
                <button onclick="closeModal()"
                    style="color:#9ca3af; font-size:1.5rem; line-height:1; background:none; border:none; cursor:pointer;">
                    &times;
                </button>
            </div>

            {{-- CONTENIDO MODAL --}}
            <div id="modal-body" style="overflow-y:auto; padding:24px; flex:1;">
                <div id="modal-loading" style="text-align:center; color:#9ca3af; padding:40px 0;">Cargando...</div>
                <div id="modal-content" style="display:none;"></div>
            </div>

        </div>
    </div>

    <script>
        // // EVITAR QUE HAYA REEDECLARACIONES AL NAVEGAR CON LIVEWIRE
        // if (typeof occupancyLabels === 'undefined') {
        //     // DATOS DESDE PHP
        //     var occupancyLabels = @js($occupancyLabels);
        //     var occupancyData = @js($occupancyData);
        //     var revenueLabels = @js($revenueLabels);
        //     var revenueData = @js($revenueData);
        //     var weekData = @js($weekData);
        //     var monthData = @js($monthData);

        //     // URLS
        //     var urlWeek = "{{ route('admin.dashboard.week-detail') }}";
        //     var urlMonth = "{{ route('admin.dashboard.month-detail') }}";
        // }

        /**
         * FUNCIÓN QUE FORMA LA TABLA QUE CONTIENE LA INFORMACIÓN DE LAS RESERVAS
         * 
         * @param reservations 
         * 
         * */
        function reservationsTable(reservations) {
            if (!reservations.length) return '<p class="text-red-500 text-sm">No hay reservas.</p>';
            return `
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jugador</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Pista</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Horario</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Precio</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        ${reservations.map(r => `
                            <tr>
                                <td class="px-4 py-2">${r.date}</td>
                                <td class="px-4 py-2">${r.player}</td>
                                <td class="px-4 py-2">${r.court}</td>
                                <td class="px-4 py-2">${r.time}</td>
                                <td class="px-4 py-2 font-medium text-green-600">${r.price}</td>
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
        <div style="display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid #f3f4f6;">
            <span style="font-weight:500; color:#374151;">${c.court}</span>
            <span style="color:#6b7280; font-size:0.875rem;">${c.count} reservas</span>
            <span style="font-weight:600; color:#16a34a;">${c.total}</span>
        </div>`).join('');

            content.innerHTML = `
        <div style="margin-bottom:24px;">
            <h4 style="font-weight:600; color:#374151; margin-bottom:12px;">Desglose por pista</h4>
            <div style="background:#f9fafb; border-radius:8px; padding:16px;">
                ${byCourt || '<p style="color:#9ca3af; font-size:0.875rem;">Sin datos.</p>'}
            </div>
        </div>
        <div>
            <h4 style="font-weight:600; color:#374151; margin-bottom:12px;">Listado de reservas</h4>
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

            // SELECTORES DE LOS GRÁFICAS EN HTML
            const occupancy = document.getElementById('chart-occupancy');
            const revenue = document.getElementById('chart-revenue');

            if (!occupancy || !revenue) return;

            // LIMPIAR INSTANCIAS ANTERIORES
            const existingOccupancy = echarts.getInstanceByDom(occupancy);
            const existingRevenue = echarts.getInstanceByDom(revenue);
            if (existingOccupancy) existingOccupancy.dispose();
            if (existingRevenue) existingRevenue.dispose();

            // INICIALIZAR GRÁFICOS
            const chartOccupancy = echarts.init(occupancy);
            const chartRevenue = echarts.init(revenue);

            // GRÁFICO OCUPACIÓN -> LÍNEAS
            chartOccupancy.setOption({
                tooltip: {
                    trigger: 'axis',
                    formatter: (params) => {
                        const meta = weekData[params[0].dataIndex];
                        return `<b>${params[0].name}</b><br/>
                            Reservas: <b>${params[0].value}</b><br/>
                            <span style="font-size:11px;color:#9ca3af">Pulsa para ver el detalle</span>`;
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
                        color: '#3b82f6'
                    },
                    areaStyle: {
                        color: 'rgba(59,130,246,0.1)'
                    },
                    emphasis: {
                        itemStyle: {
                            borderWidth: 3,
                            borderColor: '#1d4ed8'
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
                            <span style="font-size:11px;color:#9ca3af">Pulsa para ver el detalle</span>`;
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
                        color: '#22c55e',
                        borderRadius: [6, 6, 0, 0]
                    },
                    emphasis: {
                        itemStyle: {
                            color: '#16a34a'
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

            // HACEMOS LOS GRÁFICOS RESPONSIVE
            window.addEventListener('resize', () => {
                chartOccupancy.resize();
                chartRevenue.resize();
            });
        });
    </script>

</x-app-layout>