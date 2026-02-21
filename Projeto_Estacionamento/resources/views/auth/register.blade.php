@extends('layouts.master')

@section('title', 'Registar')

@section('content')
<div class="min-h-screen flex items-start justify-center bg-gray-50 pt-4 pb-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                🚗 Estacionamento CESAE Digital
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Registo de novo utilizador (admin)
            </p>
        </div>

        <form class="mt-8 space-y-6" action="{{ url('/register') }}" method="POST">
            @csrf

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative">
                    {{ session('warning') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="rounded-md shadow-sm space-y-4">
                <div>
                    <label for="nome" class="block text-sm font-medium text-gray-700 mb-1">
                        Nome Completo
                    </label>
                    <input id="nome"
                           name="nome"
                           type="text"
                           required
                           class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                           placeholder="Nome Completo"
                           value="{{ old('nome') }}">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email
                    </label>
                    <input id="email"
                           name="email"
                           type="email"
                           required
                           class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                           placeholder="nome.colaborador@cesae.pt"
                           value="{{ old('email') }}">
                    <p class="mt-1 text-xs text-gray-500">Insira o email institucional</p>
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">
                        Perfil (Role)
                    </label>
                    <select id="role"
                            name="role"
                            required
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="COLAB" {{ old('role') === 'COLAB' ? 'selected' : '' }}>Colaborador</option>
                        <option value="SEGURANCA" {{ old('role') === 'SEGURANCA' ? 'selected' : '' }}>Segurança</option>
                        <option value="ADMIN" {{ old('role') === 'ADMIN' ? 'selected' : '' }}>Administrador</option>
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Este perfil só pode ser definido pelo administrador.</p>
                </div>
            </div>

            <div class="rounded-md bg-blue-50 border border-blue-200 p-3">
                <p class="text-sm text-blue-800">
                    Será gerada uma password temporária automática e enviado um email ao utilizador para definir a password no primeiro acesso.
                </p>
            </div>

            <div>
                <button type="submit"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Criar Utilizador
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
