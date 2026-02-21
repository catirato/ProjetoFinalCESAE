@extends('layouts.master')

@section('title', 'Meu Perfil')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">{{ $isOwnProfile ? 'Meu Perfil' : 'Perfil de Utilizador' }}</h1>
        <p class="text-gray-600 mt-1">
            @if($isOwnProfile)
                Atualize os seus dados pessoais e a sua senha.
            @else
                Visualização de perfil em modo administrador.
            @endif
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Foto de Perfil</h2>

                <div class="w-48 h-48 mx-auto border-2 border-dashed border-gray-300 rounded-xl overflow-hidden bg-gray-50 flex items-center justify-center">
                    @if(!empty($user->foto_perfil_path))
                        <img src="{{ asset('storage/' . $user->foto_perfil_path) }}" alt="Foto de perfil" class="w-full h-full object-cover">
                    @else
                        <span class="text-gray-400 text-sm text-center px-4">Sem foto<br>de perfil</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Dados Pessoais</h2>

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
                        <input id="foto_perfil" name="foto_perfil" type="file" accept=".jpg,.jpeg,.png,.webp"
                               {{ $isOwnProfile ? '' : 'disabled' }}
                               class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white focus:ring-blue-500 focus:border-blue-500">
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
</div>
@endsection
