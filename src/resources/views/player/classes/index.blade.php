<x-app-layout>
    <x-slot name="header">
        <h2 style="font-size: 20px; font-weight: 600; color: #2d3b2d; margin: 0;">
            Clases
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- MENSAJES --}}
        @if(session('success'))
        <div style="margin-bottom: 20px; padding: 14px 18px; background: #e8f0e8; color: #4a6b4a; border-radius: 8px; font-size: 14px; border-left: 3px solid #6b8f6b;">
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div style="margin-bottom: 20px; padding: 14px 18px; background: #fce8e8; color: #9b4444; border-radius: 8px; font-size: 14px; border-left: 3px solid #c0625e;">
            {{ session('error') }}
        </div>
        @endif

        <div x-data="{ tab: 'mis-clases' }">

            {{-- TABS --}}
            <div style="display: flex; gap: 15px; border-bottom: 0.5px solid #d4d9cc;font-weight: 500; background: none; border-top: none; border-left: none; border-right: none; cursor: pointer; margin-bottom: 24px;">
                <button @click="tab = 'mis-clases'"
                    :style="tab === 'mis-clases' ? 'border-bottom: 2px solid #6b8f6b; color: #4a6b4a;' : 'border-bottom: 2px solid transparent; color: #7a8a7a;'"
                    style="display: inline-flex; align-items: center; gap: 8px; padding: 14px 40px; font-size: 14px; font-weight: 500; background: none; border-top: none; border-left: none; border-right: none; cursor: pointer; margin-bottom: -1px;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:15px;height:15px;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                    Mis clases
                </button>
                <button @click="tab = 'disponibles'"
                    :style="tab === 'disponibles' ? 'border-bottom: 2px solid #6b8f6b; color: #4a6b4a;' : 'border-bottom: 2px solid transparent; color: #7a8a7a;'"
                    style="display: inline-flex; align-items: center; gap: 8px; padding: 14px 40px; font-size: 14px; font-weight: 500; background: none; border-top: none; border-left: none; border-right: none; cursor: pointer; margin-bottom: -1px;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:15px;height:15px;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="2" y1="12" x2="22" y2="12" />
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                    </svg>
                    Clases disponibles
                </button>
            </div>

            {{-- TAB: MIS CLASES --}}
            <div x-show="tab === 'mis-clases'" class="space-y-8">

                @if($myClasses->isEmpty())
                <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 60px; text-align: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:40px;height:40px;stroke:#d4d9cc;margin: 0 auto 12px;" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                    <p style="font-size: 14px; color: #9aaa9a; margin: 0;">No estás inscrito en ninguna clase todavía.</p>
                </div>
                @else

                {{-- CLASES PRIVADAS --}}
                @php $privateClasses = $myClasses->where('visibility', 'private'); @endphp
                @if($privateClasses->isNotEmpty())
                <div>
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <svg xmlns="http://www.w3.org/2000/svg" style="width:15px;height:15px;stroke:#7a8a7a;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                            <h3 style="font-size: 14px; font-weight: 600; color: #5a6b5a; margin: 0; text-transform: uppercase; letter-spacing: 0.05em;">Privadas</h3>
                        </div>
                        <span style="padding: 2px 8px; background: #f0f3ee; color: #7a8a7a; border-radius: 20px; font-size: 11px; font-weight: 600;">
                            {{ $privateClasses->count() }}
                        </span>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px;">
                        @foreach($privateClasses as $class)
                        @include('player.classes._card', ['class' => $class])
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- CLASES PÚBLICAS --}}
                @php $publicClasses = $myClasses->where('visibility', 'public'); @endphp
                @if($publicClasses->isNotEmpty())
                <div>
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <svg xmlns="http://www.w3.org/2000/svg" style="width:15px;height:15px;stroke:#6b8f6b;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="2" y1="12" x2="22" y2="12" />
                                <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                            </svg>
                            <h3 style="font-size: 14px; font-weight: 600; color: #4a6b4a; margin: 0; text-transform: uppercase; letter-spacing: 0.05em;">Públicas</h3>
                        </div>
                        <span style="padding: 2px 8px; background: #e8f0e8; color: #4a6b4a; border-radius: 20px; font-size: 11px; font-weight: 600;">
                            {{ $publicClasses->count() }}
                        </span>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px;">
                        @foreach($publicClasses as $class)
                        @include('player.classes._card', ['class' => $class])
                        @endforeach
                    </div>
                </div>
                @endif

                @endif
            </div>

            {{-- TAB: CLASES DISPONIBLES --}}
            <div x-show="tab === 'disponibles'">

                @if($availableClasses->isEmpty())
                <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 60px; text-align: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:40px;height:40px;stroke:#d4d9cc;margin: 0 auto 12px;" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="2" y1="12" x2="22" y2="12" />
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                    </svg>
                    <p style="font-size: 14px; color: #9aaa9a; margin: 0;">No hay clases públicas disponibles en este momento.</p>
                </div>
                @else
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px;">
                    @foreach($availableClasses as $class)
                    <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 20px; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <h4 style="font-size: 15px; font-weight: 600; color: #2d3b2d; margin: 0 0 4px;">{{ $class->title }}</h4>

                            <p style="font-size: 12px; color: #7a8a7a; margin: 0 0 14px; text-transform: capitalize;">
                                {{ $class->type }} ·
                                {{ match($class->level) {
                                            'initiation'   => 'Iniciación',
                                            'intermediate' => 'Intermedio',
                                            'advanced'     => 'Avanzado',
                                            default        => $class->level
                                        } }}
                            </p>

                            <div style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 14px;">
                                <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #5a6b5a;">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px;stroke:#6b8f6b;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2" />
                                        <line x1="16" y1="2" x2="16" y2="6" />
                                        <line x1="8" y1="2" x2="8" y2="6" />
                                        <line x1="3" y1="10" x2="21" y2="10" />
                                    </svg>
                                    {{ \Carbon\Carbon::parse($class->date)->format('d/m/Y') }}
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #5a6b5a;">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px;stroke:#6b8f6b;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                                        <circle cx="12" cy="12" r="10" />
                                        <polyline points="12 6 12 12 16 14" />
                                    </svg>
                                    {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }} — {{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #5a6b5a;">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px;stroke:#6b8f6b;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                    </svg>
                                    {{ $class->court->name }}
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #5a6b5a;">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px;stroke:#6b8f6b;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                        <circle cx="12" cy="7" r="4" />
                                    </svg>
                                    {{ $class->coach->name }}
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #5a6b5a;">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px;stroke:#6b8f6b;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="2">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                        <circle cx="9" cy="7" r="4" />
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                    </svg>
                                    {{ $class->enrolled_count }}/{{ $class->max_players }} plazas
                                </div>
                            </div>

                            <p style="font-size: 18px; font-weight: 600; color: #6b8f6b; margin: 0;">
                                {{ number_format($class->price, 2) }}€
                            </p>
                        </div>

                        <form action="{{ route('player.classes.register', $class) }}"
                            method="POST" style="margin-top: 16px; padding-top: 16px; border-top: 0.5px solid #f0f3ee;">
                            @csrf
                            <button type="submit"
                                style="width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: 8px; background: #6b8f6b; color: #fff; font-size: 14px; font-weight: 500; padding: 10px; border-radius: 8px; border: none; cursor: pointer;"
                                onmouseover="this.style.background='#4a6b4a'"
                                onmouseout="this.style.background='#6b8f6b'">
                                <svg xmlns="http://www.w3.org/2000/svg" style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                Inscribirme
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
                @endif

            </div>

        </div>
    </div>

    {{-- PARTIAL: TARJETA DE CLASE (MIS CLASES) --}}
    @once
    @push('partials')
    @endpush
    @endonce

</x-app-layout>