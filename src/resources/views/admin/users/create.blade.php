<x-app-layout>
    <x-slot name="header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 20px; font-weight: 600; color: #2d3b2d; margin: 0;">
                Nuevo Usuario
            </h2>
            <a href="{{ route('admin.users.index') }}"
               style="display: inline-flex; align-items: center; gap: 6px; font-size: 14px; color: #5a6b5a; text-decoration: none;"
               onmouseover="this.style.color='#2d3b2d'"
               onmouseout="this.style.color='#5a6b5a'">
                <iconify-icon icon="ph:arrow-left-light" style="font-size: 15px;"></iconify-icon>
                Volver al listado
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div style="background: #fff; border-radius: 12px; border: 0.5px solid #d4d9cc; padding: 28px;">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf

                {{-- NOMBRE Y EMAIL EN GRID --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">Nombre</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               style="width: 100%; padding: 9px 12px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; box-sizing: border-box;"
                               onfocus="this.style.borderColor='#6b8f6b'"
                               onblur="this.style.borderColor='#d4d9cc'">
                        @error('name')
                            <p style="color: #c0625e; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               style="width: 100%; padding: 9px 12px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; box-sizing: border-box;"
                               onfocus="this.style.borderColor='#6b8f6b'"
                               onblur="this.style.borderColor='#d4d9cc'">
                        @error('email')
                            <p style="color: #c0625e; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- TELÉFONO Y ROL EN GRID --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">Teléfono</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number') }}"
                               style="width: 100%; padding: 9px 12px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; box-sizing: border-box;"
                               onfocus="this.style.borderColor='#6b8f6b'"
                               onblur="this.style.borderColor='#d4d9cc'">
                        @error('phone_number')
                            <p style="color: #c0625e; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">Rol</label>
                        <select name="role_id"
                                style="width: 100%; padding: 9px 12px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; background: #fff; box-sizing: border-box;"
                                onfocus="this.style.borderColor='#6b8f6b'"
                                onblur="this.style.borderColor='#d4d9cc'">
                            @foreach($roles->where('name', '!=', 'admin') as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->name === 'coach' ? 'Entrenador' : 'Jugador' }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <p style="color: #c0625e; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- SEPARADOR --}}
                <div style="border-top: 0.5px solid #f0f3ee; margin-bottom: 20px; padding-top: 20px;">
                    <p style="font-size: 12px; font-weight: 600; color: #7a8a7a; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 16px;">Contraseña</p>

                    {{-- CONTRASEÑAS EN GRID --}}
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div>
                            <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">Contraseña</label>
                            <input type="password" name="password"
                                   style="width: 100%; padding: 9px 12px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; box-sizing: border-box;"
                                   onfocus="this.style.borderColor='#6b8f6b'"
                                   onblur="this.style.borderColor='#d4d9cc'">
                            @error('password')
                                <p style="color: #c0625e; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label style="display: block; font-size: 13px; font-weight: 500; color: #2d3b2d; margin-bottom: 6px;">Confirmar contraseña</label>
                            <input type="password" name="password_confirmation"
                                   style="width: 100%; padding: 9px 12px; border: 0.5px solid #d4d9cc; border-radius: 8px; font-size: 14px; color: #2d3b2d; outline: none; box-sizing: border-box;"
                                   onfocus="this.style.borderColor='#6b8f6b'"
                                   onblur="this.style.borderColor='#d4d9cc'">
                        </div>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <a href="{{ route('admin.users.index') }}"
                       style="display: inline-flex; align-items: center; padding: 9px 18px; border-radius: 8px; border: 0.5px solid #d4d9cc; font-size: 14px; color: #5a6b5a; text-decoration: none; font-weight: 500; background: #fff;"
                       onmouseover="this.style.background='#f7f8f5'"
                       onmouseout="this.style.background='#fff'">
                        Cancelar
                    </a>
                    <button type="submit"
                            style="display: inline-flex; align-items: center; gap: 8px; background: #6b8f6b; color: #fff; font-size: 14px; font-weight: 500; padding: 9px 22px; border-radius: 8px; border: none; cursor: pointer;"
                            onmouseover="this.style.background='#4a6b4a'"
                            onmouseout="this.style.background='#6b8f6b'">
                        <iconify-icon icon="ph:user-plus-light" style="font-size: 15px;"></iconify-icon>
                        Crear Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>