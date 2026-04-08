<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }

    public function markAsRead(string $id): void
    {
        Auth::user()->notifications()->findOrFail($id)->markAsRead();
    }

    public function markAllAsRead(): void
    {
        Auth::user()->unreadNotifications->markAsRead();
    }
}; ?>

<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">

                {{-- LOGO --}}
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate>
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                {{-- ENLACES SEGÚN ROL --}}
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @php $role = Auth::user()->role->name; @endphp

                    @if($role === 'admin')
                    <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" wire:navigate>
                        Dashboard
                    </x-nav-link>
                    <x-nav-link :href="route('admin.courts.index')" :active="request()->routeIs('admin.courts.*')" wire:navigate>
                        Pistas
                    </x-nav-link>
                    <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" wire:navigate>
                        Usuarios
                    </x-nav-link>
                    @elseif($role === 'coach')
                    <x-nav-link :href="route('coach.classes.index')" :active="request()->routeIs('coach.classes.*')" wire:navigate>
                        Mis Clases
                    </x-nav-link>
                    @elseif($role === 'player')
                    <x-nav-link :href="route('player.reservations.index')" :active="request()->routeIs('player.reservations.*')" wire:navigate>
                        Mis Reservas
                    </x-nav-link>
                    <x-nav-link :href="route('player.classes.index')" :active="request()->routeIs('player.classes.*')" wire:navigate>
                        Clases
                    </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-4">

                {{-- CAMPANA DE NOTIFICACIONES --}}
                @php $notifications = Auth::user()->unreadNotifications; @endphp
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open"
                        class="relative p-2 text-gray-500 hover:text-gray-700 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @if($notifications->count() > 0)
                        <span class="absolute top-1 right-1 inline-flex items-center justify-center w-4 h-4 text-xs font-bold text-white bg-red-500 rounded-full">
                            {{ $notifications->count() }}
                        </span>
                        @endif
                    </button>

                    {{-- DROPDOWN NOTIFICACIONES --}}
                    <div x-show="open" x-transition
                        class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50">

                        <div class="flex justify-between items-center px-4 py-3 border-b">
                            <h3 class="font-semibold text-gray-700 text-sm">Notificaciones</h3>
                            @if($notifications->count() > 0)
                            <button wire:click="markAllAsRead"
                                class="text-xs text-blue-600 hover:underline">
                                Marcar todas como leídas
                            </button>
                            @endif
                        </div>

                        <div class="max-h-80 overflow-y-auto divide-y divide-gray-100">
                            @forelse($notifications as $notification)
                            <div class="px-4 py-3 hover:bg-gray-50 flex justify-between items-start gap-2">
                                <div>
                                    <p class="text-sm font-medium text-gray-800">
                                        {{ $notification->data['title'] }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        {{ $notification->data['message'] }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <button wire:click="markAsRead('{{ $notification->id }}')"
                                    class="text-gray-300 hover:text-gray-500 shrink-0 mt-1"
                                    title="Marcar como leída">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                            </div>
                            @empty
                            <div class="px-4 py-6 text-center text-sm text-gray-400">
                                No tienes notificaciones nuevas
                            </div>
                            @endforelse
                        </div>

                    </div>
                </div>

                {{-- DROPDOWN USUARIO --}}
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div x-data="{{ json_encode(['name' => Auth::user()->name]) }}"
                                x-text="name"
                                x-on:profile-updated.window="name = $event.detail.name"></div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile')" wire:navigate>
                            Perfil
                        </x-dropdown-link>
                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link>
                                Cerrar sesión
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>

            </div>

            {{-- HAMBURGER --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- MENÚ RESPONSIVE --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if($role === 'admin')
            <x-responsive-nav-link :href="route('admin.dashboard')" wire:navigate>Dashboard</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.courts.index')" wire:navigate>Pistas</x-responsive-nav-link>
            @elseif($role === 'coach')
            <x-responsive-nav-link :href="route('coach.classes.index')" wire:navigate>Mis Clases</x-responsive-nav-link>
            @elseif($role === 'player')
            <x-responsive-nav-link :href="route('player.reservations.index')" wire:navigate>Mis Reservas</x-responsive-nav-link>
            @endif
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800"
                    x-data="{{ json_encode(['name' => Auth::user()->name]) }}"
                    x-text="name"
                    x-on:profile-updated.window="name = $event.detail.name"></div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile')" wire:navigate>Perfil</x-responsive-nav-link>
                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link>Cerrar sesión</x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>