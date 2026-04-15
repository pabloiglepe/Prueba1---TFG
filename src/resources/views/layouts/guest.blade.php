<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- CDN DE SWEETALERT -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <title>{{ config('app.name', 'Padel Sync') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
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

        /* OCULTAR ELEMENTOS ALPINE ANTES DE INICIALIZAR */
        [x-cloak] { display: none !important; }

        /* ANIMACIÓN SPINNER BOTONES */
        @keyframes padel-spin {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }
        .padel-spin {
            display: inline-block;
            animation: padel-spin 0.75s linear infinite;
        }
    </style>
</head>

<body class="font-sans text-gray-900 antialiased">

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0" style="background: #f7f8f5;">
        <div>
            <a href="/" wire:navigate class="flex items-center gap-2">
                <x-application-logo style="height: 40px; width: auto; fill: #6b8f6b;" />
                <span style="font-size: 20px; font-weight: 600; color: #2d3b2d;">PadelSync</span>
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            {{ $slot }}
        </div>
    </div>

</body>

</html>