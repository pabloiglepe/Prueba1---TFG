<x-app-layout>
    <x-slot name="header">
        <h2 style="font-size: 20px; font-weight: 600; color: #2d3b2d; margin: 0;">
            Configuración del Club
        </h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">

        @if(session('success'))
        <div style="padding: 12px 16px; background: #e8f5e8; border: 0.5px solid #b8d4b8; border-radius: 10px; font-size: 14px; color: #3a6b3a; display: flex; align-items: center; gap: 10px;">
            <iconify-icon icon="ph:check-circle" style="font-size: 18px; flex-shrink: 0;"></iconify-icon>
            {{ session('success') }}
        </div>
        @endif

        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            @method('PATCH')

            {{-- HORARIO --}}
            <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 24px; margin-bottom: 16px;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                    <iconify-icon icon="ph:clock" style="font-size: 20px; color: #6b8f6b;"></iconify-icon>
                    <h3 style="font-size: 15px; font-weight: 600; color: #2d3b2d; margin: 0;">Horario de apertura</h3>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">
                            Hora de apertura
                        </label>
                        <input type="time" name="opening_time"
                               value="{{ old('opening_time', $settings->get('opening_time', '09:00')) }}"
                               style="width: 100%; padding: 9px 12px; border: 0.5px solid {{ $errors->has('opening_time') ? '#c0625e' : '#d4d9cc' }}; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; box-sizing: border-box;"
                               onfocus="this.style.borderColor='#6b8f6b'"
                               onblur="this.style.borderColor='{{ $errors->has('opening_time') ? '#c0625e' : '#d4d9cc' }}'">
                        @error('opening_time')
                            <p style="color: #c0625e; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">
                            Hora de cierre
                        </label>
                        <input type="time" name="closing_time"
                               value="{{ old('closing_time', $settings->get('closing_time', '22:00')) }}"
                               style="width: 100%; padding: 9px 12px; border: 0.5px solid {{ $errors->has('closing_time') ? '#c0625e' : '#d4d9cc' }}; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; box-sizing: border-box;"
                               onfocus="this.style.borderColor='#6b8f6b'"
                               onblur="this.style.borderColor='{{ $errors->has('closing_time') ? '#c0625e' : '#d4d9cc' }}'">
                        @error('closing_time')
                            <p style="color: #c0625e; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">
                            Intervalo entre franjas
                        </label>
                        <select name="slot_interval"
                                style="width: 100%; padding: 9px 12px; border: 0.5px solid {{ $errors->has('slot_interval') ? '#c0625e' : '#d4d9cc' }}; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; background: #fff; box-sizing: border-box;"
                                onfocus="this.style.borderColor='#6b8f6b'"
                                onblur="this.style.borderColor='{{ $errors->has('slot_interval') ? '#c0625e' : '#d4d9cc' }}'">
                            @foreach([15 => '15 minutos', 30 => '30 minutos', 60 => '60 minutos'] as $val => $label)
                                <option value="{{ $val }}" {{ (int) old('slot_interval', $settings->get('slot_interval', 30)) === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('slot_interval')
                            <p style="color: #c0625e; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">
                            Duración de la reserva
                        </label>
                        <select name="reservation_duration"
                                style="width: 100%; padding: 9px 12px; border: 0.5px solid {{ $errors->has('reservation_duration') ? '#c0625e' : '#d4d9cc' }}; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; background: #fff; box-sizing: border-box;"
                                onfocus="this.style.borderColor='#6b8f6b'"
                                onblur="this.style.borderColor='{{ $errors->has('reservation_duration') ? '#c0625e' : '#d4d9cc' }}'">
                            @foreach([60 => '60 minutos (1h)', 90 => '90 minutos (1h 30min)', 120 => '120 minutos (2h)'] as $val => $label)
                                <option value="{{ $val }}" {{ (int) old('reservation_duration', $settings->get('reservation_duration', 90)) === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('reservation_duration')
                            <p style="color: #c0625e; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- TARIFAS --}}
            <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 24px; margin-bottom: 24px;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                    <iconify-icon icon="ph:currency-eur" style="font-size: 20px; color: #6b8f6b;"></iconify-icon>
                    <h3 style="font-size: 15px; font-weight: 600; color: #2d3b2d; margin: 0;">Tarifas</h3>
                </div>

                <div style="background: #f7f8f5; border-radius: 8px; padding: 12px 14px; margin-bottom: 16px; font-size: 13px; color: #5a6b5a; display: flex; align-items: flex-start; gap: 8px;">
                    <iconify-icon icon="ph:info" style="font-size: 16px; flex-shrink: 0; margin-top: 1px;"></iconify-icon>
                    La tarifa nocturna se aplica a partir del ocaso. El precio mostrado aquí es por reserva completa.
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">
                            <iconify-icon icon="ph:sun" style="font-size: 14px; color: #6b8f6b; vertical-align: middle;"></iconify-icon>
                            Tarifa diurna (€)
                        </label>
                        <input type="number" name="price_day" step="0.01" min="0"
                               value="{{ old('price_day', $settings->get('price_day', '12.00')) }}"
                               style="width: 100%; padding: 9px 12px; border: 0.5px solid {{ $errors->has('price_day') ? '#c0625e' : '#d4d9cc' }}; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; box-sizing: border-box;"
                               onfocus="this.style.borderColor='#6b8f6b'"
                               onblur="this.style.borderColor='{{ $errors->has('price_day') ? '#c0625e' : '#d4d9cc' }}'">
                        @error('price_day')
                            <p style="color: #c0625e; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">
                            <iconify-icon icon="ph:moon" style="font-size: 14px; color: #6b8f6b; vertical-align: middle;"></iconify-icon>
                            Tarifa nocturna (€)
                        </label>
                        <input type="number" name="price_night" step="0.01" min="0"
                               value="{{ old('price_night', $settings->get('price_night', '16.00')) }}"
                               style="width: 100%; padding: 9px 12px; border: 0.5px solid {{ $errors->has('price_night') ? '#c0625e' : '#d4d9cc' }}; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; box-sizing: border-box;"
                               onfocus="this.style.borderColor='#6b8f6b'"
                               onblur="this.style.borderColor='{{ $errors->has('price_night') ? '#c0625e' : '#d4d9cc' }}'">
                        @error('price_night')
                            <p style="color: #c0625e; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end;">
                <button type="submit"
                        style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 24px; background: #6b8f6b; color: #fff; border: none; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer;"
                        onmouseover="this.style.background='#5a7a5a'"
                        onmouseout="this.style.background='#6b8f6b'">
                    <iconify-icon icon="ph:floppy-disk" style="font-size: 16px;"></iconify-icon>
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
