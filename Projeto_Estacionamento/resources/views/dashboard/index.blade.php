@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- Welcome Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
        {{-- {{ auth()->user()->nome }} --}}
            {{-- Olá, {{ auth('utilizador')->user()->nome }}! 👋 --}}
        </h1>
        <p class="text-gray-600 mt-1">Bem-vindo ao seu painel de controlo</p>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Pontos -->
        <div class="bg-gradient-to-br from-yellow-400 to-yellow-500 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm font-medium">Meus Pontos</p>
                    {{-- <p class="text-3xl font-bold mt-2">{{ auth('utilizador')->user()->pontos }}</p> --}}
                    <p class="text-3xl font-bold mt-2">NOME</p>
                </div>
                <div class="text-5xl opacity-80">⭐</div>
            </div>
        </div>
        
        <!-- Reservas Ativas -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Reservas Ativas</p>
                    <p class="text-3xl font-bold mt-2">{{ $reservasAtivas ?? 0 }}</p>
                </div>
                <div class="text-5xl opacity-80">🅿️</div>
            </div>
        </div>
        
        <!-- Lista de Espera -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Na Lista Espera</p>
                    <p class="text-3xl font-bold mt-2">{{ $listaEsperaAtiva ?? 0 }}</p>
                </div>
                <div class="text-5xl opacity-80">📋</div>
            </div>
        </div>
        
        <!-- Lugares Disponíveis -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Vagas Hoje</p>
                    <p class="text-3xl font-bold mt-2">{{ $vagasDisponiveis ?? 0 }}/7</p>
                </div>
                <div class="text-5xl opacity-80">✅</div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Ações Rápidas</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ url('/reservas/criar') }}" 
               class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                <div class="bg-blue-600 text-white p-3 rounded-lg mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-900">Nova Reserva</p>
                    <p class="text-sm text-gray-600">Reserve uma vaga</p>
                </div>
            </a>
            
            <a href="{{ url('/lista-espera/adicionar') }}" 
               class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition">
                <div class="bg-purple-600 text-white p-3 rounded-lg mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-900">Lista de Espera</p>
                    <p class="text-sm text-gray-600">Entrar na fila</p>
                </div>
            </a>
            
            <a href="{{ url('/pontos') }}" 
               class="flex items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition">
                <div class="bg-yellow-600 text-white p-3 rounded-lg mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-900">Ver Pontos</p>
                    <p class="text-sm text-gray-600">Histórico completo</p>
                </div>
            </a>
        </div>
    </div>
    
    <!-- Two Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Próximas Reservas -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Próximas Reservas</h2>
            
            @if(isset($proximasReservas) && $proximasReservas->count() > 0)
                <div class="space-y-4">
                    @foreach($proximasReservas as $reserva)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-semibold text-gray-900">
                                        Lugar {{ $reserva->lugar->numero }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($reserva->data)->format('d/m/Y') }}
                                    </p>
                                    <span class="inline-block mt-2 px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                        {{ $reserva->estado }}
                                    </span>
                                </div>
                                <a href="{{ url('/reservas/' . $reserva->id) }}" 
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Ver detalhes →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <a href="{{ url('/reservas') }}" 
                   class="block mt-4 text-center text-blue-600 hover:text-blue-800 font-medium">
                    Ver todas as reservas →
                </a>
            @else
                <div class="text-center py-8 text-gray-500">
                    <p class="text-4xl mb-2">🅿️</p>
                    <p>Não tem reservas ativas</p>
                    <a href="{{ url('/reservas/criar') }}" 
                       class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Fazer Reserva
                    </a>
                </div>
            @endif
        </div>
        
        <!-- Histórico Recente -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Atividade Recente</h2>
            
            @if(isset($historico) && $historico->count() > 0)
                <div class="space-y-3">
                    @foreach($historico as $evento)
                        <div class="flex items-start border-l-4 border-gray-300 pl-4 py-2">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $evento->descricao }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ \Carbon\Carbon::parse($evento->created_at)->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <p class="text-4xl mb-2">📋</p>
                    <p>Sem atividade recente</p>
                </div>
            @endif
        </div>
        
    </div>
    
    <!-- Info Banner -->
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-xl p-6">
        <div class="flex items-start">
            <div class="text-3xl mr-4">💡</div>
            <div>
                <h3 class="font-bold text-blue-900 mb-2">Dica do Dia</h3>
                <p class="text-blue-800">
                    Cancele reservas com antecedência para recuperar pontos! Faltas não justificadas resultam em perda de pontos.
                </p>
            </div>
        </div>
    </div>
    
</div>
@endsection