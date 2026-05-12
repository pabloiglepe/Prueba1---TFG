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
                    <a href="{{ route('admin.backups.index') }}" wire:navigate
                        style="font-size: 14px; font-weight: 500; padding: 6px 14px; border-radius: 8px; text-decoration: none; transition: background 0.15s;
                           {{ request()->routeIs('admin.backups.*') ? 'background: #e8f0e8; color: #4a6b4a;' : 'color: #5a6b5a;' }}"
                        onmouseover="if(!{{ request()->routeIs('admin.backups.*') ? 'true' : 'false' }})this.style.background='#f7f8f5'"
                        onmouseout="if(!{{ request()->routeIs('admin.backups.*') ? 'true' : 'false' }})this.style.background='transparent'">
                        Backups
                    </a>
                    <a href="{{ route('admin.settings.index') }}" wire:navigate
                        style="font-size: 14px; font-weight: 500; padding: 6px 14px; border-radius: 8px; text-decoration: none; transition: background 0.15s;
                           {{ request()->routeIs('admin.settings.*') ? 'background: #e8f0e8; color: #4a6b4a;' : 'color: #5a6b5a;' }}"
                        onmouseover="if(!{{ request()->routeIs('admin.settings.*') ? 'true' : 'false' }})this.style.background='#f7f8f5'"
                        onmouseout="if(!{{ request()->routeIs('admin.settings.*') ? 'true' : 'false' }})this.style.background='transparent'">
                        Ajustes
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
                        <iconify-icon icon="ph:bell" style="font-size: 22px;"></iconify-icon>
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
                                    <iconify-icon icon="ph:check-light" style="font-size: 16px;"></iconify-icon>
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
                            <iconify-icon icon="ph:caret-down" style="font-size: 14px; color: #5a6b5a;"></iconify-icon>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile')" wire:navigate>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <iconify-icon icon="ph:user-circle" style="font-size: 15px; color: #5a6b5a; flex-shrink: 0;"></iconify-icon>

                                Perfil
                            </div>
                        </x-dropdown-link>
                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <iconify-icon icon="ph:sign-out" style="font-size: 15px; color: #5a6b5a; flex-shrink: 0;"></iconify-icon>
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
                    <iconify-icon x-show="!open" icon="ph:list-light" style="font-size: 24px;"></iconify-icon>
                    <iconify-icon x-show="open" icon="ph:x-light" style="font-size: 24px;"></iconify-icon>
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
            <a href="{{ route('admin.backups.index') }}" wire:navigate
                style="display: block; padding: 10px 12px; font-size: 14px; color: #2d3b2d; text-decoration: none; border-radius: 8px;">Backups</a>
            <a href="{{ route('admin.settings.index') }}" wire:navigate
                style="display: block; padding: 10px 12px; font-size: 14px; color: #2d3b2d; text-decoration: none; border-radius: 8px;">Ajustes</a>
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