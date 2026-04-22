<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PadelSync — Gestión de clubes de pádel</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --sage: #6b8f6b;
            --sage-light: #e8f0e8;
            --sage-dark: #4a6b4a;
            --gray-warm: #f7f8f5;
            --gray-border: #d4d9cc;
            --text-dark: #2d3b2d;
            --text-mid: #5a6b5a;
            --text-light: #7a8a7a;
        }
    </style>
</head>

<body class="font-sans antialiased" style="background: var(--gray-warm); margin: 0;">

    {{-- NAVBAR --}}
    <nav style="background: #fff; border-bottom: 0.5px solid var(--gray-border); padding: 0 2rem; display: flex; justify-content: space-between; align-items: center; height: 64px;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <x-application-logo style="height: 36px; width: auto; fill: var(--sage);" />
            <span style="font-size: 17px; font-weight: 600; color: var(--text-dark);">PadelSync</span>
        </div>
        <div style="display: flex; gap: 12px; align-items: center;">
            <a href="{{ route('login') }}"
                style="font-size: 14px; color: var(--text-mid); text-decoration: none; font-weight: 500;">
                Iniciar sesión
            </a>
            <a href="{{ route('register') }}"
                style="background: var(--sage); color: #fff; font-size: 14px; padding: 8px 20px; border-radius: 8px; text-decoration: none; font-weight: 500;">
                Registrarse
            </a>
        </div>
    </nav>

    {{-- HERO --}}
    <div style="text-align: center; padding: 80px 2rem 60px;">
        <div style="width: 80px; height: 80px; background: var(--sage-light); border-radius: 50%; margin: 0 auto 28px; display: flex; align-items: center; justify-content: center;">
            <x-application-logo style="height: 44px; width: auto; fill: var(--sage);" />
        </div>
        <h1 style="font-size: 42px; font-weight: 600; color: var(--text-dark); margin: 0 0 18px; line-height: 1.25;">
            La plataforma de gestión<br>para tu club de pádel
        </h1>
        <p style="font-size: 17px; color: var(--text-light); max-width: 500px; margin: 0 auto 36px; line-height: 1.7;">
            Reserva pistas, gestiona clases y controla el rendimiento de tu club desde un solo lugar. Diseñado para administradores, entrenadores y jugadores.
        </p>
        <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
            <a href="{{ route('register') }}"
                style="background: var(--sage); color: #fff; font-size: 15px; padding: 13px 32px; border-radius: 8px; text-decoration: none; font-weight: 500;">
                Empieza ahora
            </a>
            <a href="{{ route('login') }}"
                style="background: #fff; color: var(--sage-dark); font-size: 15px; padding: 13px 32px; border-radius: 8px; border: 0.5px solid var(--gray-border); text-decoration: none; font-weight: 500;">
                Iniciar sesión
            </a>
        </div>
    </div>

    {{-- FEATURES --}}
    <div style="background: #fff; border-top: 0.5px solid var(--gray-border); border-bottom: 0.5px solid var(--gray-border); padding: 60px 2rem;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 32px; max-width: 860px; margin: 0 auto;">

            {{-- FEATURE: RESERVA DE PISTAS --}}
            <div style="text-align: center; padding: 16px;">
                <div style="width: 56px; height: 56px; background: var(--sage-light); border-radius: 14px; margin: 0 auto 18px; display: flex; align-items: center; justify-content: center;">
                    <iconify-icon icon="ph:calendar-light" style="font-size: 28px; color: var(--sage);"></iconify-icon>
                    <!-- <iconify-icon icon="ph:clock-user-light" style="font-size: 28px; color: var(--sage);"></iconify-icon> -->
                </div>
                <p style="font-size: 15px; font-weight: 600; color: var(--text-dark); margin: 0 0 10px;">Reserva de pistas</p>
                <p style="font-size: 13px; color: var(--text-light); line-height: 1.7; margin: 0;">
                    Consulta la disponibilidad en tiempo real y reserva tu pista en segundos con tarifa dinámica según el horario.
                </p>
            </div>

            {{-- FEATURE: GESTIÓN DE CLASES --}}
            <div style="text-align: center; padding: 16px;">
                <div style="width: 56px; height: 56px; background: var(--sage-light); border-radius: 14px; margin: 0 auto 18px; display: flex; align-items: center; justify-content: center;">
                    <iconify-icon icon="ph:users-three-light" style="font-size: 28px; color: var(--sage);"></iconify-icon>
                </div>
                <p style="font-size: 15px; font-weight: 600; color: var(--text-dark); margin: 0 0 10px;">Gestión de clases</p>
                <p style="font-size: 13px; color: var(--text-light); line-height: 1.7; margin: 0;">
                    Los entrenadores crean clases individuales o grupales. Los jugadores se inscriben según su nivel de juego.
                </p>
            </div>

            {{-- FEATURE: DASHBOARD ANALÍTICO --}}
            <div style="text-align: center; padding: 16px;">
                <div style="width: 56px; height: 56px; background: var(--sage-light); border-radius: 14px; margin: 0 auto 18px; display: flex; align-items: center; justify-content: center;">
                    <iconify-icon icon="ph:chart-line-up-light" style="font-size: 28px; color: var(--sage);"></iconify-icon>
                </div>
                <p style="font-size: 15px; font-weight: 600; color: var(--text-dark); margin: 0 0 10px;">Dashboard analítico</p>
                <p style="font-size: 13px; color: var(--text-light); line-height: 1.7; margin: 0;">
                    Monitoriza ocupación, ingresos y alumnos activos con gráficos interactivos y exportación de informes.
                </p>
            </div>

        </div>
    </div>

    {{-- FOOTER --}}
    <footer style="padding: 28px; text-align: center;">
        <span style="font-size: 13px; color: var(--text-light);">
            © {{ date('Y') }} PadelSync · Desarrollado por <strong style="color: var(--text-mid); font-weight: 500;">Pablo Iglesias Peral</strong>
        </span>
    </footer>

    {{-- CDN DE ICONIFY --}}
    <!-- <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script> -->

</body>

</html>