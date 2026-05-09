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
                    <iconify-icon icon="ph:user" style="font-size: 15px; flex-shrink: 0;"></iconify-icon>
                    Mis clases
                </button>
                <button @click="tab = 'disponibles'"
                    :style="tab === 'disponibles' ? 'border-bottom: 2px solid #6b8f6b; color: #4a6b4a;' : 'border-bottom: 2px solid transparent; color: #7a8a7a;'"
                    style="display: inline-flex; align-items: center; gap: 8px; padding: 14px 40px; font-size: 14px; font-weight: 500; background: none; border-top: none; border-left: none; border-right: none; cursor: pointer; margin-bottom: -1px;">
                    <iconify-icon icon="ph:globe" style="font-size: 15px; flex-shrink: 0;"></iconify-icon>
                    Clases disponibles
                </button>
            </div>

            {{-- TAB: MIS CLASES --}}
            <div x-show="tab === 'mis-clases'" class="space-y-8">

                @if($myClasses->isEmpty())
                <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 60px; text-align: center;">
                    <iconify-icon icon="ph:user" style="font-size: 40px; color: #d4d9cc; display: block; margin: 0 auto 12px;"></iconify-icon>
                    <p style="font-size: 14px; color: #9aaa9a; margin: 0;">No estás inscrito en ninguna clase todavía.</p>
                </div>
                @else

                {{-- CLASES PRIVADAS --}}
                @php $privateClasses = $myClasses->where('visibility', 'private'); @endphp
                @if($privateClasses->isNotEmpty())
                <div>
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <iconify-icon icon="ph:lock-simple" style="font-size: 15px; color: #7a8a7a;"></iconify-icon>
                            <h3 style="font-size: 14px; font-weight: 600; color: #5a6b5a; margin: 0; text-transform: uppercase; letter-spacing: 0.05em;">Privadas</h3>
                        </div>
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
                            <iconify-icon icon="ph:globe" style="font-size: 15px; color: #6b8f6b;"></iconify-icon>
                            <h3 style="font-size: 14px; font-weight: 600; color: #4a6b4a; margin: 0; text-transform: uppercase; letter-spacing: 0.05em;">Públicas</h3>
                        </div>
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
                    <iconify-icon icon="ph:globe" style="font-size: 40px; color: #d4d9cc; display: block; margin: 0 auto 12px;"></iconify-icon>
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
                                    <iconify-icon icon="ph:calendar" style="font-size: 14px; color: #6b8f6b; flex-shrink: 0;"></iconify-icon>
                                    {{ \Carbon\Carbon::parse($class->date)->format('d/m/Y') }}
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #5a6b5a;">
                                    <iconify-icon icon="ph:clock" style="font-size: 14px; color: #6b8f6b; flex-shrink: 0;"></iconify-icon>
                                    {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }} — {{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #5a6b5a;">
                                    <iconify-icon icon="ph:house" style="font-size: 14px; color: #6b8f6b; flex-shrink: 0;"></iconify-icon>
                                    {{ $class->court->name }}
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #5a6b5a;">
                                    <iconify-icon icon="ph:user" style="font-size: 14px; color: #6b8f6b; flex-shrink: 0;"></iconify-icon>
                                    {{ $class->coach->name }}
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #5a6b5a;">
                                    <iconify-icon icon="ph:users" style="font-size: 14px; color: #6b8f6b; flex-shrink: 0;"></iconify-icon>
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
                                <iconify-icon icon="ph:check" style="font-size: 15px;"></iconify-icon>
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