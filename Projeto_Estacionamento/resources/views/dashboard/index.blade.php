@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- Welcome Header -->
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
            Olá, {{ auth('utilizador')->user()->nome }}! 👋
        </h1>
        <p class="text-gray-600 mt-1">Bem-vindo ao seu painel de controlo</p>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Pontos -->
        <div class="bg-gradient-to-br from-yellow-400 to-yellow-500 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-yellow-100 text-sm font-medium">Meus Pontos</p>
                    <p class="text-2xl sm:text-3xl font-bold mt-2">{{ auth('utilizador')->user()->pontos }}</p>
                </div>
                <div class="text-4xl sm:text-5xl opacity-80 shrink-0">⭐</div>
            </div>
        </div>
        
        <!-- Reservas Ativas -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Reservas Ativas</p>
                    <p class="text-2xl sm:text-3xl font-bold mt-2">{{ $reservasAtivas ?? 0 }}</p>
                </div>
                <div class="text-4xl sm:text-5xl opacity-80 shrink-0">🅿️</div>
            </div>
        </div>
        
        <!-- Lista de Espera -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Na Lista Espera</p>
                    <p class="text-2xl sm:text-3xl font-bold mt-2">{{ $listaEsperaAtiva ?? 0 }}</p>
                </div>
                <div class="text-4xl sm:text-5xl opacity-80 shrink-0">📋</div>
            </div>
        </div>
        
        <!-- Lugares Disponíveis -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-green-100 text-sm font-medium">Vagas Hoje</p>
                    <p class="text-2xl sm:text-3xl font-bold mt-2">{{ $vagasDisponiveis ?? 0 }}/7</p>
                </div>
                <div class="text-4xl sm:text-5xl opacity-80 shrink-0">✅</div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Ações Rápidas</h2>
        <div class="grid grid-cols-1 {{ in_array(auth('utilizador')->user()->role, ['COLAB', 'ADMIN']) ? 'md:grid-cols-4' : 'md:grid-cols-3' }} gap-4">
            <a href="{{ url('/reservas/criar') }}" 
               class="flex items-start sm:items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition gap-3">
                <div class="bg-blue-600 text-white p-3 rounded-lg shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-900">Nova Reserva</p>
                    <p class="text-sm text-gray-600">Reserve uma vaga</p>
                </div>
            </a>
            
            <a href="{{ url('/lista-espera') }}" 
               class="flex items-start sm:items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition gap-3">
                <div class="bg-purple-600 text-white p-3 rounded-lg shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.008v.008H3.75V6.75Zm0 5.25h.008v.008H3.75V12Zm0 5.25h.008v.008H3.75v-.008Z"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-900">Lista de Espera</p>
                    <p class="text-sm text-gray-600">Entrar na fila</p>
                </div>
            </a>
            
            @if(in_array(auth('utilizador')->user()->role, ['COLAB', 'ADMIN']))
                <a href="{{ route('reports.create') }}"
                   class="flex items-start sm:items-center p-4 rounded-lg transition gap-3"
                   style="background-color: rgba(224, 231, 255, 0.8);"
                   onmouseover="this.style.backgroundColor='rgba(199, 210, 254, 0.95)'"
                   onmouseout="this.style.backgroundColor='rgba(224, 231, 255, 0.8)'">
                    <div class="text-white p-3 rounded-lg shrink-0" style="background-color: #4f46e5;">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M10.5 1.5a.75.75 0 00-.75.75v.75H8.25A2.25 2.25 0 006 5.25v14.25a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 19.5V5.25A2.25 2.25 0 0015.75 3H14.25v-.75a.75.75 0 00-.75-.75h-3Zm2.25 2.25v-.75h-1.5v.75h1.5ZM8.25 4.5h7.5a.75.75 0 01.75.75v14.25a.75.75 0 01-.75.75h-7.5a.75.75 0 01-.75-.75V5.25a.75.75 0 01.75-.75Zm1.5 4.5a.75.75 0 000 1.5h4.5a.75.75 0 000-1.5h-4.5Zm0 3a.75.75 0 000 1.5h4.5a.75.75 0 000-1.5h-4.5Zm0 3a.75.75 0 000 1.5h3a.75.75 0 000-1.5h-3Z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Submissão de Relatório</p>
                        <p class="text-sm text-gray-600">Reportar ocorrência</p>
                    </div>
                </a>
            @endif

            <a href="{{ url('/pontos') }}" 
               class="flex items-start sm:items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition gap-3">
                <div class="bg-yellow-600 text-white p-3 rounded-lg shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-900">Ver Histórico</p>
                    <p class="text-sm text-gray-600">Histórico completo</p>
                </div>
            </a>

        </div>
    </div>

    @if(auth('utilizador')->user()->role === 'ADMIN')
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Ações Exclusivas</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ url('/register') }}"
                   class="flex items-start sm:items-center p-4 rounded-lg transition gap-3"
                   style="background-color: rgba(16, 185, 129, 0.2);"
                   onmouseover="this.style.backgroundColor='rgba(5, 150, 105, 0.7)'"
                   onmouseout="this.style.backgroundColor='rgba(16, 185, 129, 0.2)'">
                    <div class="bg-emerald-600 text-white p-3 rounded-lg shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v6m3-3h-6M9 7a4 4 0 110-8 4 4 0 010 8zm0 2c-3.314 0-6 2.239-6 5v2h10"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Registar Utilizador</p>
                        <p class="text-sm text-gray-600">Criar nova conta</p>
                    </div>
                </a>

                <a href="{{ url('/reservas') }}"
                   class="flex items-start sm:items-center p-4 rounded-lg transition gap-3"
                   style="background-color: rgba(207, 250, 254, 0.7);"
                   onmouseover="this.style.backgroundColor='rgba(186, 230, 253, 0.95)'"
                   onmouseout="this.style.backgroundColor='rgba(207, 250, 254, 0.7)'">
                    <div class="p-3 rounded-lg shrink-0" style="background-color: #67e8f9;">
                        <svg class="w-6 h-6 text-cyan-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.483 9.246 5 7.5 5S4.168 5.483 3 6.253v13C4.168 18.483 5.754 18 7.5 18s3.332.483 4.5 1.253m0-13C13.168 5.483 14.754 5 16.5 5s3.332.483 4.5 1.253v13C19.832 18.483 18.246 18 16.5 18s-3.332.483-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Gestão de Reservas</p>
                        <p class="text-sm text-gray-600">Histórico global</p>
                    </div>
                </a>

                <a href="{{ url('/admin/relatorios') }}"
                   class="flex items-start sm:items-center p-4 rounded-lg transition gap-3"
                   style="background-color: rgba(224, 231, 255, 0.8);"
                   onmouseover="this.style.backgroundColor='rgba(199, 210, 254, 0.95)'"
                   onmouseout="this.style.backgroundColor='rgba(224, 231, 255, 0.8)'">
                    <div class="text-white p-3 rounded-lg shrink-0" style="background-color: #4f46e5;">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Gestão de Relatórios</p>
                        <p class="text-sm text-gray-600">Aceder aos relatórios</p>
                    </div>
                </a>

                <a href="{{ route('admin.perfis.index') }}"
                   class="flex items-start sm:items-center p-4 rounded-lg transition gap-3"
                   style="background-color: rgba(255, 228, 230, 0.85);"
                   onmouseover="this.style.backgroundColor='rgba(254, 205, 211, 0.95)'"
                   onmouseout="this.style.backgroundColor='rgba(255, 228, 230, 0.85)'">
                    <div class="bg-rose-600 text-white p-3 rounded-lg shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V4H2v16h5m10 0v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2m12 0H7m10-8a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Gestão de Utilizadores</p>
                        <p class="text-sm text-gray-600">Perfis e remoção</p>
                    </div>
                </a>

                <a href="{{ route('admin.pontos.index') }}"
                   class="flex items-start sm:items-center p-4 rounded-lg transition gap-3"
                   style="background-color: rgba(254, 243, 199, 0.75);"
                   onmouseover="this.style.backgroundColor='rgba(253, 230, 138, 0.95)'"
                   onmouseout="this.style.backgroundColor='rgba(254, 243, 199, 0.75)'">
                    <div class="bg-amber-600 text-white p-3 rounded-lg shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.105 0-2 .895-2 2m4 0a2 2 0 00-2-2m0 0V6m0 2v8m0 0v2m0-2a2 2 0 104 0m-4 0a2 2 0 11-4 0"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Gestão de Pontos</p>
                        <p class="text-sm text-gray-600">Ajuste por utilizador</p>
                    </div>
                </a>
            </div>
        </div>
    @endif
    
    <!-- Two Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Próximas Reservas -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Próximas Reservas</h2>
            
            @if(isset($proximasReservas) && $proximasReservas->count() > 0)
                <div class="space-y-4">
                    @foreach($proximasReservas as $reserva)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3">
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
                                    @if(($reserva->modo_reserva ?? 'COLAB') === 'ADMIN')
                                        <span class="inline-block mt-2 ml-1 px-2 py-1 bg-rose-100 text-rose-800 text-xs rounded-full">
                                            Reserva administrativa execional
                                        </span>
                                    @endif
                                </div>
                                <a href="{{ url('/reservas/' . $reserva->id) }}" 
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium self-start">
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
        <div class="flex items-start gap-3">
            <div class="text-3xl shrink-0">💡</div>
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
