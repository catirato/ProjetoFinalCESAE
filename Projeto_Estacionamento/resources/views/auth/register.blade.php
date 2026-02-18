@extends('layouts.master')

@section('title', 'Registar')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                🚗 Estacionamento Cesae Digital
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Crie a sua conta
            </p>
        </div>
        
        <form class="mt-8 space-y-6" action="{{ url('/register') }}" method="POST">
            @csrf
            
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
                           placeholder="Bruno Santos"
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
                           placeholder="bruno.santos@cesae.pt"
                           value="{{ old('email') }}">
                    <p class="mt-1 text-xs text-gray-500">Use o seu email corporativo</p>
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Password
                    </label>
                    <input id="password" 
                           name="password" 
                           type="password" 
                           required 
                           class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                           placeholder="••••••••">
                    <p class="mt-1 text-xs text-gray-500">Mínimo 8 caracteres</p>
                </div>
                
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                        Confirmar Password
                    </label>
                    <input id="password_confirmation" 
                           name="password_confirmation" 
                           type="password" 
                           required 
                           class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                           placeholder="••••••••">
                </div>
            </div>

            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="terms" 
                           name="terms" 
                           type="checkbox" 
                           required
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                </div>
                <div class="ml-3 text-sm">
                    <label for="terms" class="font-medium text-gray-700">
                        Aceito os termos e condições
                    </label>
                    <p class="text-gray-500">
                        Concordo com as regras de utilização do sistema de estacionamento
                    </p>
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Criar Conta (Ganhe 30 Pontos! ⭐)
                </button>
            </div>
            
            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Já tem conta? 
                    <a href="{{ url('/login') }}" class="font-medium text-blue-600 hover:text-blue-500">
                        Entrar aqui
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection