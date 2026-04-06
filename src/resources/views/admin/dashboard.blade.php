<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

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


    {{-- ECHARTS --}}
    <script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>

    <script>
        // DATOS DESDE PHP
        const occupancyLabels = @js($occupancyLabels);
        const occupancyData = @js($occupancyData);
        const revenueLabels = @js($revenueLabels);
        const revenueData = @js($revenueData);
        const weekData = @js($weekData);
        const monthData = @js($monthData);

        // URLS
        const urlWeek = "{{ route('admin.dashboard.week-detail') }}";
        const urlMonth = "{{ route('admin.dashboard.month-detail') }}";

        // GRÁFICO OCUPACIÓN -> LÍNEAS
        const chartOccupancy = echarts.init(document.getElementById('chart-occupancy'));
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
        const chartRevenue = echarts.init(document.getElementById('chart-revenue'));
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
        document.getElementById('modal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });

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

        // HACEMOS LOS GRÁFICOS RESPONSIVE
        window.addEventListener('resize', () => {
            chartOccupancy.resize();
            chartRevenue.resize();
        });
    </script>

</x-app-layout>