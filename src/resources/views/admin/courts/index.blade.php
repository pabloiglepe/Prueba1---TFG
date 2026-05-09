<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 style="font-size: 20px; font-weight: 600; color: #2d3b2d; margin: 0;">
                Gestión de Pistas
            </h2>
            <a href="{{ route('admin.courts.create') }}"
               style="display: inline-flex; align-items: center; gap: 8px; background: #6b8f6b; color: #fff; font-size: 14px; font-weight: 500; padding: 9px 18px; border-radius: 8px; text-decoration: none;">
                {{-- ICONO PLUS CIRCLE --}}
                <iconify-icon icon="ph:plus-circle-light" style="font-size: 16px;"></iconify-icon>
                Nueva Pista
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- MENSAJE DE ÉXITO --}}
        @if(session('success'))
        <div style="margin-bottom: 16px; padding: 14px 18px; background: #e8f0e8; color: #4a6b4a; border-radius: 8px; font-size: 14px; border-left: 3px solid #6b8f6b;">
            {{ session('success') }}
        </div>
        @endif

        {{-- TABLA DE PISTAS --}}
        <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f7f8f5; border-bottom: 0.5px solid #d4d9cc;">
                        <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">#</th>
                        <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Nombre</th>
                        <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Tipo</th>
                        <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Superficie</th>
                        <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Ubicación</th>
                        <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Estado</th>
                        <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courts as $court)
                    <tr style="border-bottom: 0.5px solid #f0f3ee;" onmouseover="this.style.background='#fafbf9'" onmouseout="this.style.background='#fff'">
                        <td style="padding: 14px 20px; font-size: 13px; color: #9aaa9a;">{{ $court->id }}</td>
                        <td style="padding: 14px 20px; font-size: 14px; font-weight: 500; color: #2d3b2d;">{{ $court->name }}</td>
                        <td style="padding: 14px 20px; font-size: 14px; color: #5a6b5a; text-transform: capitalize;">{{ $court->type }}</td>
                        <td style="padding: 14px 20px; font-size: 14px; color: #5a6b5a; text-transform: capitalize;">{{ $court->surface }}</td>
                        <td style="padding: 14px 20px;">
                            {{-- BADGE INTERIOR / EXTERIOR --}}
                            @if($court->is_outdoor)
                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #e0eef8; color: #2b6691; border-radius: 20px; font-size: 12px; font-weight: 500;">
                                    <iconify-icon icon="ph:circle-fill" style="font-size: 8px; color: #2b6691;"></iconify-icon>
                                    Exterior
                                </span>
                            @else
                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #f0f0f8; color: #5a5a8a; border-radius: 20px; font-size: 12px; font-weight: 500;">
                                    <iconify-icon icon="ph:circle-fill" style="font-size: 8px; color: #5a5a8a;"></iconify-icon>
                                    Cubiertas
                                </span>
                            @endif
                        </td>
                        <td style="padding: 14px 20px;">
                            @if($court->is_active)
                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #e8f0e8; color: #4a6b4a; border-radius: 20px; font-size: 12px; font-weight: 500;">
                                    <iconify-icon icon="ph:circle-fill" style="font-size: 8px; color: #4a6b4a;"></iconify-icon>
                                    Activa
                                </span>
                            @else
                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #fce8e8; color: #9b4444; border-radius: 20px; font-size: 12px; font-weight: 500;">
                                    <iconify-icon icon="ph:circle-fill" style="font-size: 8px; color: #9b4444;"></iconify-icon>
                                    Inactiva
                                </span>
                            @endif
                        </td>
                        <td style="padding: 14px 20px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                {{-- EDITAR --}}
                                <a href="{{ route('admin.courts.edit', $court) }}"
                                   style="display: inline-flex; align-items: center; gap: 5px; font-size: 13px; color: #6b8f6b; font-weight: 500; text-decoration: none;"
                                   onmouseover="this.style.color='#4a6b4a'"
                                   onmouseout="this.style.color='#6b8f6b'">
                                    <iconify-icon icon="ph:pencil-simple-light" style="font-size: 14px;"></iconify-icon>
                                    Editar
                                </a>

                                {{-- ELIMINAR --}}
                                <form action="{{ route('admin.courts.destroy', $court) }}"
                                      method="POST" class="inline"
                                      onsubmit="return confirm('¿Eliminar esta pista?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            style="display: inline-flex; align-items: center; gap: 5px; font-size: 13px; color: #c0625e; font-weight: 500; background: none; border: none; cursor: pointer; padding: 0;"
                                            onmouseover="this.style.color='#9b4444'"
                                            onmouseout="this.style.color='#c0625e'">
                                        <iconify-icon icon="ph:trash-light" style="font-size: 14px;"></iconify-icon>
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="padding: 48px 20px; text-align: center; font-size: 14px; color: #9aaa9a;">
                            No hay pistas registradas todavía.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>