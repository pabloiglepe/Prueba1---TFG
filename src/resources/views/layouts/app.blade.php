<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- LANZO LA ALERTA AL ENTRAR EN LA PÁGINA -->
    <!-- @if (session('swal'))
    <script>
        document.addEventListener('livewire:navigated', () => {
            Swal.fire({
                icon: "{{ session('swal')['icon'] }}",
                title: "{{ session('swal')['title'] }}",
                text: "{{ session('swal')['text'] }}",
                timer: 3000,
                showConfirmButton: false,
                timerProgressBar: true,
                confirmButtonColor: '#6b8f6b',
            });
        });
    </script>
    @endif -->

    <title>{{ config('app.name', 'PadelSync') }}</title>

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
    </style>
</head>

<body class="font-sans antialiased" style="background: var(--gray-warm);">
    <div class="flex flex-col min-h-screen" style="background: var(--gray-warm);">
        <livewire:layout.navigation />

        <!-- Page Heading -->
        @if (isset($header))
        <header style="background: #fff; border-bottom: 0.5px solid var(--gray-border);">
            <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endif

        <!-- Page Content -->
        <main class="flex-1">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer style="border-top: 0.5px solid var(--gray-border); background: #fff; padding: 20px; text-align: center;">
            <span style="font-size: 13px; color: var(--text-light);">
                © {{ date('Y') }} PadelSync · Desarrollado por
                <span style="font-weight: 500; color: var(--text-mid);">Pablo Iglesias Peral</span>
            </span>
        </footer>
    </div>

    @if (session('swal'))
    <div
        x-data="{}"
        x-init="
            Swal.fire({
                icon: '{{ session('swal')['icon'] }}',
                title: '{{ session('swal')['title'] }}',
                text: '{{ session('swal')['text'] }}',
                timer: 3000,
                showConfirmButton: false,
                timerProgressBar: true,
            })
        ">
    </div>
    @endif

</body>

</html>