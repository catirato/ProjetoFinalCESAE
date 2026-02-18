@extends('layouts.master')

@section('title', 'Estacionamento Cesae Digital')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Hero Section -->
        <div class="text-center pt-20 pb-16">
            <h1 class="text-5xl font-bold text-gray-900 mb-4">
                🚗 Sistema de Gestão do Estacionamento Cesae Digital
            </h1>
            <p class="text-xl text-gray-600 mb-8">
                Gerir as 7 vagas de estacionamento da empresa de forma eficiente e justa
            </p>
            
            @guest('utilizador')
                <div class="flex justify-center space-x-4">
                    <a href="{{ url('/login') }}" 
                       class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                        Entrar
                    </a>
                    <a href="{{ url('/register') }}" 
                       class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold border-2 border-blue-600 hover:bg-blue-50 transition">
                        Registar
                    </a>
                </div>
            @else
                <a href="{{ url('/dashboard') }}" 
                   class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition inline-block">
                    Ir para Dashboard
                </a>
            @endguest
        </div>
        
        <!-- Features -->
        <div class="grid md:grid-cols-3 gap-8 pb-20">
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <div class="text-4xl mb-4">🎯</div>
                <h3 class="text-xl font-bold mb-2">Sistema de Pontos</h3>
                <p class="text-gray-600">
                    Cada colaborador tem pontos para reservar vagas. Use com sabedoria!
                </p>
            </div>
            
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <div class="text-4xl mb-4">📋</div>
                <h3 class="text-xl font-bold mb-2">Lista de Espera</h3>
                <p class="text-gray-600">
                    Sem vagas disponíveis? Entre na lista de espera e seja notificado.
                </p>
            </div>
            
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <div class="text-4xl mb-4">⚡</div>
                <h3 class="text-xl font-bold mb-2">Reservas Rápidas</h3>
                <p class="text-gray-600">
                    Reserve o seu lugar com apenas alguns cliques. Simples e rápido.
                </p>
            </div>
        </div>
        
        <!-- Info Cards -->
        <div class="bg-white rounded-xl shadow-xl p-8 mb-12">
            <h2 class="text-3xl font-bold text-center mb-8">Como Funciona?</h2>
            
            <div class="space-y-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-xl">
                        1
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-semibold">Crie a sua conta</h4>
                        <p class="text-gray-600">Registe-se com o seu email da empresa e receba 30 pontos iniciais.</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-xl">
                        2
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-semibold">Reserve uma vaga</h4>
                        <p class="text-gray-600">Escolha o dia que precisa e gaste pontos para garantir o seu lugar.</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-xl">
                        3
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-semibold">Estacione com tranquilidade</h4>
                        <p class="text-gray-600">Chegue e estacione no lugar reservado. A segurança validará a sua presença.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Stats -->
        <div class="grid md:grid-cols-3 gap-6 pb-20">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-xl text-center">
                <div class="text-4xl font-bold mb-2">7</div>
                <div class="text-blue-100">Lugares Disponíveis</div>
            </div>
            
            <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-xl text-center">
                <div class="text-4xl font-bold mb-2">25</div>
                <div class="text-green-100">Colaboradores</div>
            </div>
            
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white p-6 rounded-xl text-center">
                <div class="text-4xl font-bold mb-2">30</div>
                <div class="text-purple-100">Pontos Iniciais</div>
            </div>
        </div>
        
    </div>
</div>
@endsection