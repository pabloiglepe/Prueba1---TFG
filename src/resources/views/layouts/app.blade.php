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
                confirmButtonColor: '#4f46e5',
            });

        });
    </script>
    @endif -->

    <title>{{ config('app.name', 'Padel Sync') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <livewire:layout.navigation />

        <!-- Page Heading -->
        @if (isset($header))
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endif

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>


    @if (session('swal'))
    <div
        x-data="{}"
        x-init="
            Swal.fire({
                icon: '{{ session('swal')['icon'] }}',
                title: '{{ session('swal')['title'] }}',
                text: '{{ session('swal')['text'] }}',
                timer: 2000,
                showConfirmButton: false,
                timerProgressBar: true,
            })
        ">
    </div>
    @endif
</body>

</html>