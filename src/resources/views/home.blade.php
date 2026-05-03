<x-app-layout>
    <x-slot name="header">
        <h2 style="font-size: 20px; font-weight: 600; color: #2d3b2d; margin: 0;">Inicio</h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- CARRUSEL --}}
        @php
        $role = $user->role->name;
        $name = explode(' ', $user->name)[0];
        $hour = now()->hour;
        $greeting = $hour < 12 ? 'Buenos días' : ($hour < 20 ? 'Buenas tardes' : 'Buenas noches' );

            if ($role==='player' ) {
            $slides=[
            [ 'tag'=> $greeting,
            'title' => '¡Hola, ' . $name . '! Bienvenido al club.',
            'sub' => 'Reserva tu pista, únete a una clase o consulta tu actividad.',
            'btn' => 'Reservar pista',
            'url' => route('player.reservations.create'),
            'img' => 'Tfg-padel1.png',
            ],
            [
            'tag' => 'Nuestras pistas',
            'title' => 'Pistas disponibles para ti',
            'sub' => 'Pistas de cristal y muro con césped artificial y cemento.',
            'btn' => 'Ver mis reservas',
            'url' => route('player.reservations.index'),
            'img' => 'Tfg-padel4.png',
            ],
            [
            'tag' => 'Academia',
            'title' => 'Mejora tu juego con nuestros entrenadores',
            'sub' => 'Clases para todos los niveles. Iniciación, intermedio y avanzado.',
            'btn' => 'Ver clases',
            'url' => route('player.classes.index'),
            'img' => 'Tfg-padel5.png',
            ],
            [
            'tag' => 'Club PadelSync',
            'title' => 'Tu club, siempre en tu mano',
            'sub' => 'Gestiona tus reservas, clases y perfil desde cualquier dispositivo.',
            'btn' => 'Mi perfil',
            'url' => route('profile'),
            'img' => 'Tfg-padel7.png',
            ],
            ];
            } elseif ($role === 'coach') {
            $slides = [
            [
            'tag' => $greeting,
            'title' => '¡Hola, ' . $name . '! Listo para entrenar.',
            'sub' => 'Gestiona tus clases, inscribe alumnos y consulta tu actividad.',
            'btn' => 'Ver mis clases',
            'url' => route('coach.classes.index'),
            'img' => 'Tfg-padel6.png',
            ],
            [
            'tag' => 'Academia',
            'title' => 'Crea y gestiona tus clases',
            'sub' => 'Clases individuales y grupales, públicas o privadas.',
            'btn' => 'Nueva clase',
            'url' => route('coach.classes.create'),
            'img' => 'Tfg-padel2.png',
            ],
            [
            'tag' => 'Tu perfil',
            'title' => 'Consulta tus estadísticas',
            'sub' => 'Clases impartidas, alumnos totales e ingresos generados.',
            'btn' => 'Ver perfil',
            'url' => route('profile'),
            'img' => 'Tfg-padel7.png',
            ],
            ];
            } else {
            $slides = [
            [
            'tag' => $greeting,
            'title' => '¡Hola, ' . $name . '! Panel de administración.',
            'sub' => 'Gestiona pistas, usuarios y consulta las analíticas del club.',
            'btn' => 'Ir al dashboard',
            'url' => route('admin.dashboard'),
            'img' => 'Tfg-padel3.png',
            ],
            [
            'tag' => 'Pistas',
            'title' => 'Gestiona las pistas del club',
            'sub' => 'Crea, edita y activa o desactiva pistas según la demanda.',
            'btn' => 'Ver pistas',
            'url' => route('admin.courts.index'),
            'img' => 'Tfg-padel4.png',
            ],
            [
            'tag' => 'Usuarios',
            'title' => 'Jugadores y entrenadores',
            'sub' => 'Gestiona los usuarios del club y consulta sus estadísticas.',
            'btn' => 'Ver usuarios',
            'url' => route('admin.users.index'),
            'img' => 'Tfg-padel7.png',
            ],
            ];
            }
            @endphp

            <div x-data="{
                    current: 0,
                    slides: {{ json_encode($slides) }},
                    init() { setInterval(() => { this.current = (this.current + 1) % this.slides.length }, 4000) }
                }"
                style="background: #f7f8f5; border-radius: 12px; border: 0.5px solid #d4d9cc; overflow: hidden; position: relative; height: 480px;">

                {{-- SLIDE DE FOTOSs --}}
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 32px; padding: 48px 64px 56px; height: 480px;">
                    <div style="flex: 1; min-width: 0;">
                        <p x-text="slides[current].tag"
                            style="font-size: 16px; font-weight: 600; color: #6b8f6b; text-transform: uppercase; letter-spacing: 0.08em; margin: 0 0 12px;"></p>
                        <h2 x-text="slides[current].title"
                            style="font-size: 34px; font-weight: 600; color: #2d3b2d; line-height: 1.2; margin: 0 0 14px;"></h2>
                        <p x-text="slides[current].sub"
                            style="font-size: 16px; color: #7a8a7a; line-height: 1.6; margin: 0 0 24px;"></p>
                        <a :href="slides[current].url"
                            x-text="slides[current].btn"
                            style="display: inline-flex; align-items: center; background: #6b8f6b; color: #fff; font-size: 14px; font-weight: 500; padding: 10px 20px; border-radius: 8px; text-decoration: none;"
                            onmouseover="this.style.background='#4a6b4a'"
                            onmouseout="this.style.background='#6b8f6b'"></a>
                    </div>
                    <img :src="'/images/padel/' + slides[current].img"
                        style="height: 380px; width: 380px; object-fit: contain; flex-shrink: 0; opacity: 0.9; transition: opacity 0.3s ease;">
                </div>

                {{-- FLECHA IZQUIERDA --}}
                <button @click="current = (current + slides.length - 1) % slides.length"
                    style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); background: #fff; border: 0.5px solid #d4d9cc; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10; font-size: 16px; color: #5a6b5a;"
                    onmouseover="this.style.background='#f0f3ee'"
                    onmouseout="this.style.background='#fff'">‹</button>

                {{-- FLECHA DERECHA --}}
                <button @click="current = (current + 1) % slides.length"
                    style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: #fff; border: 0.5px solid #d4d9cc; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10; font-size: 16px; color: #5a6b5a;"
                    onmouseover="this.style.background='#f0f3ee'"
                    onmouseout="this.style.background='#fff'">›</button>

                {{-- DOTS --}}
                <div style="position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); display: flex; gap: 8px; z-index: 10;">
                    <template x-for="(slide, i) in slides" :key="i">
                        <div @click="current = i"
                            :style="current === i ? 'background: #2d3b2d; transform: scale(1.3);' : 'background: #b8c9b8;'"
                            style="width: 8px; height: 8px; border-radius: 50%; cursor: pointer; transition: all 0.3s;">
                        </div>
                    </template>
                </div>
            </div>

            {{-- ACCESOS RÁPIDOS SEGÚN ROL --}}
            @if($user->role->name === 'player')
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px;">
                <a href="{{ route('player.reservations.create') }}" wire:navigate style="text-decoration: none;">
                    <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 20px;"
                        onmouseover="this.style.borderColor='#6b8f6b'"
                        onmouseout="this.style.borderColor='#d4d9cc'">
                        <div style="width: 36px; height: 36px; border-radius: 9px; background: #e8f0e8; display: flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                            <!-- <svg xmlns="http://www.w3.org/2000/svg" style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="#6b8f6b" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" />
                                <line x1="16" y1="2" x2="16" y2="6" />
                                <line x1="8" y1="2" x2="8" y2="6" />
                                <line x1="3" y1="10" x2="21" y2="10" />
                            </svg> -->
                            <iconify-icon icon="ph:calendar-check-light" style="font-size: 28px; color:var(--sage);"></iconify-icon>
                        </div>
                        <p style="font-size: 14px; font-weight: 500; color: #2d3b2d; margin: 0 0 4px;">Reservar pista</p>
                        <p style="font-size: 15px; color: #7a8a7a; margin: 0 0 12px; line-height: 1.5;">Consulta disponibilidad y reserva tu pista favorita</p>
                        <p style="font-size: 15px; color: #6b8f6b; font-weight: 500; margin: 0;">Ir a reservas →</p>
                    </div>
                </a>
                <a href="{{ route('player.classes.index') }}" wire:navigate style="text-decoration: none;">
                    <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 20px;"
                        onmouseover="this.style.borderColor='#6b8f6b'"
                        onmouseout="this.style.borderColor='#d4d9cc'">
                        <div style="width: 36px; height: 36px; border-radius: 9px; background: #e8f0e8; display: flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                            <!-- <svg xmlns="http://www.w3.org/2000/svg" style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="#6b8f6b" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg> -->
                            <iconify-icon icon="ph:person-simple-throw-light" style="font-size: 28px; color:var(--sage);"></iconify-icon>
                        </div>
                        <p style="font-size: 14px; font-weight: 500; color: #2d3b2d; margin: 0 0 4px;">Clases disponibles</p>
                        <p style="font-size: 15px; color: #7a8a7a; margin: 0 0 12px; line-height: 1.5;">Descubre las clases públicas con plazas libres</p>
                        <p style="font-size: 15px; color: #6b8f6b; font-weight: 500; margin: 0;">Ver clases →</p>
                    </div>
                </a>
                <a href="{{ route('profile') }}" wire:navigate style="text-decoration: none;">
                    <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 20px;"
                        onmouseover="this.style.borderColor='#6b8f6b'"
                        onmouseout="this.style.borderColor='#d4d9cc'">
                        <div style="width: 36px; height: 36px; border-radius: 9px; background: #e8f0e8; display: flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                            <!-- <svg xmlns="http://www.w3.org/2000/svg" style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="#6b8f6b" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg> -->
                            <!-- <iconify-icon icon="ph:person-simple-light" style="font-size: 28px; color:var(--sage);"></iconify-icon> -->
                            <iconify-icon icon="ph:user-circle-light" style="font-size: 28px; color:var(--sage);"></iconify-icon>
                        </div>
                        <p style="font-size: 14px; font-weight: 500; color: #2d3b2d; margin: 0 0 4px;">Mi perfil</p>
                        <p style="font-size: 15px; color: #7a8a7a; margin: 0 0 12px; line-height: 1.5;">Consulta tu historial y gestiona tu cuenta</p>
                        <p style="font-size: 15px; color: #6b8f6b; font-weight: 500; margin: 0;">Ver perfil →</p>
                    </div>
                </a>
            </div>

            @elseif($user->role->name === 'coach')
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px;">
                <a href="{{ route('coach.classes.index') }}" wire:navigate style="text-decoration: none;">
                    <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 20px;"
                        onmouseover="this.style.borderColor='#6b8f6b'"
                        onmouseout="this.style.borderColor='#d4d9cc'">
                        <div style="width: 36px; height: 36px; border-radius: 9px; background: #e8f0e8; display: flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                            <!-- <svg xmlns="http://www.w3.org/2000/svg" style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="#6b8f6b" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                            </svg> -->
                            <iconify-icon icon="ph:clock-user-light" style="font-size: 28px; color:var(--sage);"></iconify-icon>
                        </div>
                        <p style="font-size: 14px; font-weight: 500; color: #2d3b2d; margin: 0 0 4px;">Mis clases</p>
                        <p style="font-size: 15px; color: #7a8a7a; margin: 0 0 12px; line-height: 1.5;">Gestiona tus clases y alumnos inscritos</p>
                        <p style="font-size: 15px; color: #6b8f6b; font-weight: 500; margin: 0;">Ver clases →</p>
                    </div>
                </a>
                <a href="{{ route('profile') }}" wire:navigate style="text-decoration: none;">
                    <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 20px;"
                        onmouseover="this.style.borderColor='#6b8f6b'"
                        onmouseout="this.style.borderColor='#d4d9cc'">
                        <div style="width: 36px; height: 36px; border-radius: 9px; background: #e8f0e8; display: flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                            <!-- <svg xmlns="http://www.w3.org/2000/svg" style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="#6b8f6b" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg> -->
                            <iconify-icon icon="ph:user-circle-light" style="font-size: 28px; color:var(--sage);"></iconify-icon>
                        </div>
                        <p style="font-size: 14px; font-weight: 500; color: #2d3b2d; margin: 0 0 4px;">Mi perfil</p>
                        <p style="font-size: 15px; color: #7a8a7a; margin: 0 0 12px; line-height: 1.5;">Consulta tus estadísticas y gestiona tu cuenta</p>
                        <p style="font-size: 15px; color: #6b8f6b; font-weight: 500; margin: 0;">Ver perfil →</p>
                    </div>
                </a>
            </div>

            @else
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px;">
                <a href="{{ route('admin.dashboard') }}" wire:navigate style="text-decoration: none;">
                    <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 20px;"
                        onmouseover="this.style.borderColor='#6b8f6b'"
                        onmouseout="this.style.borderColor='#d4d9cc'">
                        <div style="width: 36px; height: 36px; border-radius: 9px; background: #e8f0e8; display: flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                            <!-- <svg xmlns="http://www.w3.org/2000/svg" style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="#6b8f6b" stroke-width="2">
                                <line x1="18" y1="20" x2="18" y2="10" />
                                <line x1="12" y1="20" x2="12" y2="4" />
                                <line x1="6" y1="20" x2="6" y2="14" />
                            </svg> -->
                            <iconify-icon icon="ph:chart-line-up-light" style="font-size: 28px; color: var(--sage);"></iconify-icon>
                        </div>
                        <p style="font-size: 14px; font-weight: 500; color: #2d3b2d; margin: 0 0 4px;">Dashboard</p>
                        <p style="font-size: 15px; color: #7a8a7a; margin: 0 0 12px; line-height: 1.5;">KPIs, gráficos y exportación de datos</p>
                        <p style="font-size: 15px; color: #6b8f6b; font-weight: 500; margin: 0;">Ir al dashboard →</p>
                    </div>
                </a>
                <a href="{{ route('admin.courts.index') }}" wire:navigate style="text-decoration: none;">
                    <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 20px;"
                        onmouseover="this.style.borderColor='#6b8f6b'"
                        onmouseout="this.style.borderColor='#d4d9cc'">
                        <div style="width: 36px; height: 36px; border-radius: 9px; background: #e8f0e8; display: flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                            <!-- <svg xmlns="http://www.w3.org/2000/svg" style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="#6b8f6b" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2" />
                                <line x1="3" y1="12" x2="21" y2="12" />
                                <line x1="12" y1="3" x2="12" y2="21" />
                            </svg> -->
                            <!-- <iconify-icon icon="ph:court-basketball-light" style="font-size: 28px; color: var(--sage);"></iconify-icon> -->
                            <iconify-icon icon="ph:squares-four-light" style="font-size: 28px; color: var(--sage);"></iconify-icon>
                        </div>
                        <p style="font-size: 14px; font-weight: 500; color: #2d3b2d; margin: 0 0 4px;">Pistas</p>
                        <p style="font-size: 15px; color: #7a8a7a; margin: 0 0 12px; line-height: 1.5;">Gestiona las pistas del club</p>
                        <p style="font-size: 15px; color: #6b8f6b; font-weight: 500; margin: 0;">Ver pistas →</p>
                    </div>
                </a>
                <a href="{{ route('admin.users.index') }}" wire:navigate style="text-decoration: none;">
                    <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 20px;"
                        onmouseover="this.style.borderColor='#6b8f6b'"
                        onmouseout="this.style.borderColor='#d4d9cc'">
                        <div style="width: 36px; height: 36px; border-radius: 9px; background: #e8f0e8; display: flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                            <!-- <svg xmlns="http://www.w3.org/2000/svg" style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="#6b8f6b" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg> -->
                            <iconify-icon icon="ph:users-three-light" style="font-size: 28px; color: var(--sage);"></iconify-icon>
                        </div>
                        <p style="font-size: 14px; font-weight: 500; color: #2d3b2d; margin: 0 0 4px;">Usuarios</p>
                        <p style="font-size: 15px; color: #7a8a7a; margin: 0 0 12px; line-height: 1.5;">Gestiona jugadores y entrenadores</p>
                        <p style="font-size: 15px; color: #6b8f6b; font-weight: 500; margin: 0;">Ver usuarios →</p>
                    </div>
                </a>
            </div>
            @endif

    </div>
</x-app-layout>