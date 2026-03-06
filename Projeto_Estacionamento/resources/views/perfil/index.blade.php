@extends('layouts.master')

@section('title', 'Meu Perfil')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8 flex items-start justify-between gap-4">
        <div>
            @if($isAdminView)
                <a href="{{ route('admin.perfis.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium inline-flex items-center mb-3">
                    ← Voltar aos perfis
                </a>
            @endif
            <h1 class="text-3xl font-bold text-gray-900">{{ $isOwnProfile ? 'Meu Perfil' : 'Perfil de Utilizador' }}</h1>
            <p class="text-gray-600 mt-1">
                @if($isOwnProfile)
                    Atualize os seus dados pessoais e a sua senha.
                @else
                    Visualização de perfil em modo administrador.
                @endif
            </p>
        </div>
        @if(($user->role ?? null) !== 'SEGURANCA')
            <a href="{{ route('pontos.index') }}"
               class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition whitespace-nowrap">
                Ver Histórico
            </a>
        @endif
    </div>

    <div class="space-y-8">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Foto de Perfil</h2>

            <div id="foto-perfil-frame" class="w-48 h-48 mx-auto border-2 border-dashed border-gray-300 rounded-xl overflow-hidden bg-gray-50 flex items-center justify-center cursor-grab">
                @if(!empty($user->foto_perfil_path))
                    <img id="foto-perfil-preview"
                         src="{{ asset('storage/' . $user->foto_perfil_path) }}"
                         alt="Foto de perfil"
                         class="w-full h-full object-cover"
                         style="object-position: {{ old('foto_pos_x', $user->foto_pos_x ?? 50) }}% {{ old('foto_pos_y', $user->foto_pos_y ?? 50) }}%;">
                    <span id="foto-perfil-placeholder" class="hidden text-gray-400 text-sm text-center px-4">Sem foto<br>de perfil</span>
                @else
                    <img id="foto-perfil-preview"
                         src=""
                         alt="Foto de perfil"
                         class="hidden w-full h-full object-cover"
                         style="object-position: {{ old('foto_pos_x', $user->foto_pos_x ?? 50) }}% {{ old('foto_pos_y', $user->foto_pos_y ?? 50) }}%;">
                    <span id="foto-perfil-placeholder" class="text-gray-400 text-sm text-center px-4">Sem foto<br>de perfil</span>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Dados Pessoais</h2>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('perfil.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="nome" class="block text-sm font-medium text-gray-700 mb-1">Nome Completo</label>
                    <input id="nome" name="nome" type="text" required
                           value="{{ old('nome', $user->nome) }}"
                           {{ $isOwnProfile ? '' : 'disabled' }}
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input id="email" name="email" type="email" required
                           value="{{ old('email', $user->email) }}"
                           {{ $isOwnProfile ? '' : 'disabled' }}
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="telemovel" class="block text-sm font-medium text-gray-700 mb-1">Telemóvel (opcional)</label>
                    <input id="telemovel" name="telemovel" type="text"
                           value="{{ old('telemovel', $user->telemovel) }}"
                           placeholder="+351 9xx xxx xxx"
                           {{ $isOwnProfile ? '' : 'disabled' }}
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Perfil (Role)</label>
                    <input id="role" type="text"
                           value="{{ $user->role }}"
                           disabled
                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-700">
                    <p class="mt-1 text-xs text-gray-500">Este campo é definido pelo administrador e não pode ser alterado pelo utilizador.</p>
                </div>

                <div>
                    <label for="foto_perfil" class="block text-sm font-medium text-gray-700 mb-1">Upload de Foto</label>
                    <input id="foto_perfil" name="foto_perfil" type="file" accept="image/*,.heic,.heif" capture="environment"
                           {{ $isOwnProfile ? '' : 'disabled' }}
                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-xs text-gray-500">Pode escolher da galeria ou tirar foto com a câmara (até 5MB).</p>
                    <p class="mt-1 text-xs text-gray-500">Clique e arraste a foto no quadrado para ajustar a posição.</p>
                    <input type="hidden" id="foto_pos_x" name="foto_pos_x" value="{{ old('foto_pos_x', $user->foto_pos_x ?? 50) }}">
                    <input type="hidden" id="foto_pos_y" name="foto_pos_y" value="{{ old('foto_pos_y', $user->foto_pos_y ?? 50) }}">
                </div>

                @if($isOwnProfile)
                    <div class="pt-2">
                        <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">
                            Guardar Dados
                        </button>
                    </div>
                @endif
            </form>
        </div>

        @if($isOwnProfile)
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Alterar Senha</h2>

                <form action="{{ route('perfil.password') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Senha Atual</label>
                        <input id="current_password" name="current_password" type="password" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Nova Senha</label>
                        <input id="password" name="password" type="password" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Nova Senha</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="px-5 py-2 bg-emerald-600 text-white rounded-lg font-semibold hover:bg-emerald-700 transition">
                            Alterar Senha
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const fotoInput = document.getElementById('foto_perfil');
    const previewImg = document.getElementById('foto-perfil-preview');
    const placeholder = document.getElementById('foto-perfil-placeholder');
    const frame = document.getElementById('foto-perfil-frame');
    const posXInput = document.getElementById('foto_pos_x');
    const posYInput = document.getElementById('foto_pos_y');
    if (!fotoInput || !previewImg || !placeholder || !frame || !posXInput || !posYInput) return;

    function clamp(value, min, max) {
        return Math.min(max, Math.max(min, value));
    }

    function applyPosition() {
        previewImg.style.objectPosition = `${posXInput.value}% ${posYInput.value}%`;
    }

    fotoInput.addEventListener('change', function (event) {
        const file = event.target.files && event.target.files[0] ? event.target.files[0] : null;
        if (!file) return;

        const imageUrl = URL.createObjectURL(file);
        previewImg.src = imageUrl;
        previewImg.classList.remove('hidden');
        placeholder.classList.add('hidden');
        applyPosition();
    });

    let dragging = false;
    let startClientX = 0;
    let startClientY = 0;
    let startPosX = Number(posXInput.value);
    let startPosY = Number(posYInput.value);

    function startDrag(clientX, clientY) {
        if (previewImg.classList.contains('hidden')) return;
        dragging = true;
        startClientX = clientX;
        startClientY = clientY;
        startPosX = Number(posXInput.value);
        startPosY = Number(posYInput.value);
        frame.classList.remove('cursor-grab');
        frame.classList.add('cursor-grabbing');
    }

    function moveDrag(clientX, clientY) {
        if (!dragging) return;
        const rect = frame.getBoundingClientRect();
        if (!rect.width || !rect.height) return;

        const deltaXPercent = ((clientX - startClientX) / rect.width) * 100;
        const deltaYPercent = ((clientY - startClientY) / rect.height) * 100;

        posXInput.value = String(clamp(startPosX + deltaXPercent, 0, 100));
        posYInput.value = String(clamp(startPosY + deltaYPercent, 0, 100));
        applyPosition();
    }

    function endDrag() {
        dragging = false;
        frame.classList.remove('cursor-grabbing');
        frame.classList.add('cursor-grab');
    }

    frame.addEventListener('mousedown', function (event) {
        event.preventDefault();
        startDrag(event.clientX, event.clientY);
    });

    window.addEventListener('mousemove', function (event) {
        moveDrag(event.clientX, event.clientY);
    });

    window.addEventListener('mouseup', endDrag);

    frame.addEventListener('touchstart', function (event) {
        const touch = event.touches && event.touches[0];
        if (!touch) return;
        startDrag(touch.clientX, touch.clientY);
    }, { passive: true });

    window.addEventListener('touchmove', function (event) {
        const touch = event.touches && event.touches[0];
        if (!touch) return;
        moveDrag(touch.clientX, touch.clientY);
    }, { passive: true });

    window.addEventListener('touchend', endDrag);

    applyPosition();
});
</script>
@endsection
