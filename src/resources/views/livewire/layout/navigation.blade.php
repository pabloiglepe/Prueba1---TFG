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

<nav x-data="{ open: false }" style="background: #fff; border-bottom: 0.5px solid #d4d9cc;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">

                {{-- LOGO --}}
                <div class="shrink-0 flex items-center gap-2">
                    <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2">
                        <x-application-logo style="height: 36px; width: auto; fill: #6b8f6b;" />
                        <span style="font-size: 16px; font-weight: 600; color: #2d3b2d;">PadelSync</span>
                    </a>
                </div>

                {{-- ENLACES SEGÚN ROL --}}
                <div class="hidden sm:flex sm:items-center sm:ms-10 sm:gap-1">
                    @php $role = Auth::user()->role->name; @endphp

                    @if($role === 'admin')
                    <a href="{{ route('home') }}" wire:navigate
                        style="font-size: 14px; font-weight: 500; padding: 6px 14px; border-radius: 8px; text-decoration: none;
                        {{ request()->routeIs('home') ? 'background: #e8f0e8; color: #4a6b4a;' : 'color: #5a6b5a;' }}">
                        Inicio
                    </a>
                    <a href="{{ route('admin.dashboard') }}" wire:navigate
                        style="font-size: 14px; font-weight: 500; padding: 6px 14px; border-radius: 8px; text-decoration: none; transition: background 0.15s;
                           {{ request()->routeIs('admin.dashboard') ? 'background: #e8f0e8; color: #4a6b4a;' : 'color: #5a6b5a;' }}"
                        onmouseover="if(!{{ request()->routeIs('admin.dashboard') ? 'true' : 'false' }})this.style.background='#f7f8f5'"
                        onmouseout="if(!{{ request()->routeIs('admin.dashboard') ? 'true' : 'false' }})this.style.background='transparent'">
                        Dashboard
                    </a>
                    <a href="{{ route('admin.courts.index') }}" wire:navigate
                        style="font-size: 14px; font-weight: 500; padding: 6px 14px; border-radius: 8px; text-decoration: none; transition: background 0.15s;
                           {{ request()->routeIs('admin.courts.*') ? 'background: #e8f0e8; color: #4a6b4a;' : 'color: #5a6b5a;' }}"
                        onmouseover="if(!{{ request()->routeIs('admin.courts.*') ? 'true' : 'false' }})this.style.background='#f7f8f5'"
                        onmouseout="if(!{{ request()->routeIs('admin.courts.*') ? 'true' : 'false' }})this.style.background='transparent'">
                        Pistas
                    </a>
                    <a href="{{ route('admin.users.index') }}" wire:navigate
                        style="font-size: 14px; font-weight: 500; padding: 6px 14px; border-radius: 8px; text-decoration: none; transition: background 0.15s;
                           {{ request()->routeIs('admin.users.*') ? 'background: #e8f0e8; color: #4a6b4a;' : 'color: #5a6b5a;' }}"
                        onmouseover="if(!{{ request()->routeIs('admin.users.*') ? 'true' : 'false' }})this.style.background='#f7f8f5'"
                        onmouseout="if(!{{ request()->routeIs('admin.users.*') ? 'true' : 'false' }})this.style.background='transparent'">
                        Usuarios
                    </a>
                    @elseif($role === 'coach')
                    <a href="{{ route('home') }}" wire:navigate
                        style="font-size: 14px; font-weight: 500; padding: 6px 14px; border-radius: 8px; text-decoration: none;
                        {{ request()->routeIs('home') ? 'background: #e8f0e8; color: #4a6b4a;' : 'color: #5a6b5a;' }}">
                        Inicio
                    </a>
                    <a href="{{ route('coach.classes.index') }}" wire:navigate
                        style="font-size: 14px; font-weight: 500; padding: 6px 14px; border-radius: 8px; text-decoration: none;
                           {{ request()->routeIs('coach.classes.*') ? 'background: #e8f0e8; color: #4a6b4a;' : 'color: #5a6b5a;' }}">
                        Mis Clases
                    </a>
                    @elseif($role === 'player')
                    <a href="{{ route('home') }}" wire:navigate
                        style="font-size: 14px; font-weight: 500; padding: 6px 14px; border-radius: 8px; text-decoration: none;
                        {{ request()->routeIs('home') ? 'background: #e8f0e8; color: #4a6b4a;' : 'color: #5a6b5a;' }}">
                        Inicio
                    </a>
                    <a href="{{ route('player.reservations.index') }}" wire:navigate
                        style="font-size: 14px; font-weight: 500; padding: 6px 14px; border-radius: 8px; text-decoration: none;
                           {{ request()->routeIs('player.reservations.*') ? 'background: #e8f0e8; color: #4a6b4a;' : 'color: #5a6b5a;' }}">
                        Mis Reservas
                    </a>
                    <a href="{{ route('player.classes.index') }}" wire:navigate
                        style="font-size: 14px; font-weight: 500; padding: 6px 14px; border-radius: 8px; text-decoration: none;
                           {{ request()->routeIs('player.classes.*') ? 'background: #e8f0e8; color: #4a6b4a;' : 'color: #5a6b5a;' }}">
                        Clases
                    </a>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">

                {{-- CAMPANA DE NOTIFICACIONES --}}
                @php $notifications = Auth::user()->unreadNotifications; @endphp
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open"
                        style="position: relative; padding: 8px; color: #5a6b5a; background: none; border: none; cursor: pointer; border-radius: 8px;"
                        onmouseover="this.style.background='#f7f8f5'"
                        onmouseout="this.style.background='none'">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width:22px;height:22px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @if($notifications->count() > 0)
                        <span style="position: absolute; top: 4px; right: 4px; width: 16px; height: 16px; background: #e05c5c; color: #fff; font-size: 10px; font-weight: 700; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            {{ $notifications->count() }}
                        </span>
                        @endif
                    </button>

                    {{-- DROPDOWN NOTIFICACIONES --}}
                    <div x-show="open" x-transition
                        style="position: absolute; right: 0; top: calc(100% + 8px); width: 320px; background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; box-shadow: 0 8px 24px rgba(0,0,0,0.08); z-index: 50;">

                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px 16px; border-bottom: 0.5px solid #d4d9cc;">
                            <h3 style="font-size: 14px; font-weight: 600; color: #2d3b2d; margin: 0;">Notificaciones</h3>
                            @if($notifications->count() > 0)
                            <button wire:click="markAllAsRead"
                                style="font-size: 12px; color: #6b8f6b; background: none; border: none; cursor: pointer;">
                                Marcar todas como leídas
                            </button>
                            @endif
                        </div>

                        <div style="max-height: 320px; overflow-y: auto;">
                            @forelse($notifications as $notification)
                            <div style="padding: 12px 16px; display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; border-bottom: 0.5px solid #f0f3ee;">
                                <div>
                                    <p style="font-size: 13px; font-weight: 500; color: #2d3b2d; margin: 0 0 3px;">
                                        {{ $notification->data['title'] }}
                                    </p>
                                    <p style="font-size: 12px; color: #5a6b5a; margin: 0 0 3px;">
                                        {{ $notification->data['message'] }}
                                    </p>
                                    <p style="font-size: 11px; color: #9aaa9a; margin: 0;">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <button wire:click="markAsRead('{{ $notification->id }}')"
                                    style="color: #b8c9b8; background: none; border: none; cursor: pointer; flex-shrink: 0; padding: 2px;"
                                    title="Marcar como leída">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                            </div>
                            @empty
                            <div style="padding: 32px 16px; text-align: center; font-size: 13px; color: #9aaa9a;">
                                No tienes notificaciones nuevas
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- DROPDOWN USUARIO --}}
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 8px; border: 0.5px solid #d4d9cc; background: #fff; font-size: 14px; color: #2d3b2d; cursor: pointer; font-weight: 500;"
                            onmouseover="this.style.background='#f7f8f5'"
                            onmouseout="this.style.background='#fff'">
                            <div x-data="{{ json_encode(['name' => Auth::user()->name]) }}"
                                x-text="name"
                                x-on:profile-updated.window="name = $event.detail.name"></div>
                            <svg style="width:14px;height:14px;fill:#5a6b5a;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile')" wire:navigate>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <svg xmlns="http://www.w3.org/2000/svg" style="width:15px;height:15px;stroke:#5a6b5a;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>

                                Perfil
                            </div>
                        </x-dropdown-link>
                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width:15px;height:15px;stroke:#5a6b5a;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                        <polyline points="16 17 21 12 16 7" />
                                        <line x1="21" y1="12" x2="9" y2="12" />
                                    </svg>
                                    Cerrar sesión
                                </div>
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>

            </div>

            {{-- HAMBURGER --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    style="padding: 8px; border-radius: 8px; border: none; background: none; cursor: pointer; color: #5a6b5a;">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- MENÚ RESPONSIVE --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden"
        style="border-top: 0.5px solid #d4d9cc;">
        <div style="padding: 8px 12px;">
            @if($role === 'admin')
            <a href="{{ route('admin.dashboard') }}" wire:navigate
                style="display: block; padding: 10px 12px; font-size: 14px; color: #2d3b2d; text-decoration: none; border-radius: 8px;">Dashboard</a>
            <a href="{{ route('admin.courts.index') }}" wire:navigate
                style="display: block; padding: 10px 12px; font-size: 14px; color: #2d3b2d; text-decoration: none; border-radius: 8px;">Pistas</a>
            <a href="{{ route('admin.users.index') }}" wire:navigate
                style="display: block; padding: 10px 12px; font-size: 14px; color: #2d3b2d; text-decoration: none; border-radius: 8px;">Usuarios</a>
            @elseif($role === 'coach')
            <a href="{{ route('coach.classes.index') }}" wire:navigate
                style="display: block; padding: 10px 12px; font-size: 14px; color: #2d3b2d; text-decoration: none; border-radius: 8px;">Mis Clases</a>
            @elseif($role === 'player')
            <a href="{{ route('player.reservations.index') }}" wire:navigate
                style="display: block; padding: 10px 12px; font-size: 14px; color: #2d3b2d; text-decoration: none; border-radius: 8px;">Mis Reservas</a>
            <a href="{{ route('player.classes.index') }}" wire:navigate
                style="display: block; padding: 10px 12px; font-size: 14px; color: #2d3b2d; text-decoration: none; border-radius: 8px;">Clases</a>
            @endif
        </div>

        <div style="padding: 12px; border-top: 0.5px solid #d4d9cc;">
            <div style="padding: 0 12px 8px;">
                <div style="font-size: 15px; font-weight: 500; color: #2d3b2d;"
                    x-data="{{ json_encode(['name' => Auth::user()->name]) }}"
                    x-text="name"
                    x-on:profile-updated.window="name = $event.detail.name"></div>
                <div style="font-size: 13px; color: #5a6b5a;">{{ Auth::user()->email }}</div>
            </div>
            <a href="{{ route('profile') }}" wire:navigate
                style="display: block; padding: 10px 12px; font-size: 14px; color: #2d3b2d; text-decoration: none; border-radius: 8px;">Perfil</a>
            <button wire:click="logout"
                style="display: block; width: 100%; text-align: left; padding: 10px 12px; font-size: 14px; color: #2d3b2d; background: none; border: none; cursor: pointer; border-radius: 8px;">
                Cerrar sesión
            </button>
        </div>
    </div>
</nav>