@extends('layouts.master')

@section('title', 'Detalhes da Reserva')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ url('/reservas') }}" class="text-blue-600 hover:text-blue-800 flex items-center mb-4">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Voltar às reservas
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Detalhes da Reserva</h1>
    </div>
    
    <!-- Main Card -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
        
        <!-- Status Banner -->
        @if(isset($reserva))
            @if($reserva->estado === 'ATIVA')
                <div class="bg-blue-600 text-white px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="text-3xl mr-4">✓</div>
                            <div>
                                <p class="font-bold text-lg">Reserva Ativa</p>
                                <p class="text-blue-100 text-sm">Sua vaga está garantida</p>
                            </div>
                        </div>
                        @if(\Carbon\Carbon::parse($reserva->data)->isToday())
                            <span class="px-4 py-2 bg-yellow-400 text-yellow-900 rounded-full font-bold">
                                🔥 HOJE!
                            </span>
                        @endif
                    </div>
                </div>
            @elseif($reserva->estado === 'PRESENTE')
                <div class="bg-green-600 text-white px-6 py-4">
                    <div class="flex items-center">
                        <div class="text-3xl mr-4">✓</div>
                        <div>
                            <p class="font-bold text-lg">Presença Confirmada</p>
                            <p class="text-green-100 text-sm">Utilizou o estacionamento</p>
                        </div>
                    </div>
                </div>
            @elseif($reserva->estado === 'NAO_COMPARECEU')
                <div class="bg-red-600 text-white px-6 py-4">
                    <div class="flex items-center">
                        <div class="text-3xl mr-4">✗</div>
                        <div>
                            <p class="font-bold text-lg">Não Compareceu</p>
                            <p class="text-red-100 text-sm">Falta não justificada - Penalização aplicada</p>
                        </div>
                    </div>
                </div>
            @elseif($reserva->estado === 'CANCELADA')
                <div class="bg-gray-600 text-white px-6 py-4">
                    <div class="flex items-center">
                        <div class="text-3xl mr-4">⊘</div>
                        <div>
                            <p class="font-bold text-lg">Reserva Cancelada</p>
                            <p class="text-gray-100 text-sm">Esta reserva foi cancelada</p>
                        </div>
                    </div>
                </div>
            @endif
        @endif
        
        <!-- Reservation Details -->
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                
                <!-- Place Info -->
                <div class="bg-gray-50 rounded-lg p-6 text-center">
                    <p class="text-sm text-gray-600 mb-2">Lugar Reservado</p>
                    <div class="text-6xl mb-2">🅿️</div>
                    <p class="text-4xl font-bold text-gray-900">{{ $reserva->lugar->numero ?? 'N/A' }}</p>
                </div>
                
                <!-- Date Info -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-600">Data</p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ \Carbon\Carbon::parse($reserva->data)->format('d/m/Y') }}
                            </p>
                            <p class="text-gray-600">
                                {{ \Carbon\Carbon::parse($reserva->data)->locale('pt')->isoFormat('dddd') }}
                            </p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-600">Reservado há</p>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ \Carbon\Carbon::parse($reserva->created_at)->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </div>
                
            </div>
            
            <!-- User Info -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="font-bold text-gray-900 mb-4">Informações do Utilizador</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Nome</p>
                        <p class="font-semibold text-gray-900">{{ $reserva->utilizador->nome ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Email</p>
                        <p class="font-semibold text-gray-900">{{ $reserva->utilizador->email ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
            
            @if($reserva->validada_por)
                <div class="border-t border-gray-200 pt-6 mt-6">
                    <h3 class="font-bold text-gray-900 mb-4">Validação</h3>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <p class="text-sm text-green-800">
                            Validada por: <span class="font-semibold">{{ $reserva->validadaPor->nome ?? 'Sistema' }}</span>
                        </p>
                    </div>
                </div>
            @endif
            
        </div>
        
        <!-- Actions -->
        @if($reserva->estado === 'ATIVA' && \Carbon\Carbon::parse($reserva->data)->isFuture())
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-600">
                            Pode cancelar esta reserva
                            @if(\Carbon\Carbon::parse($reserva->data)->diffInHours(now()) > 24)
                                e recuperar 3 pontos
                            @else
                                (sem recuperação de pontos)
                            @endif
                        </p>
                    </div>
                    <form action="{{ url('/reservas/' . $reserva->id . '/cancelar') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                onclick="return confirm('Tem certeza que deseja cancelar esta reserva?')"
                                class="px-6 py-2 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition">
                            Cancelar Reserva
                        </button>
                    </form>
                </div>
            </div>
        @endif
        
    </div>
    
    <!-- Points Impact -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <h3 class="font-bold text-gray-900 mb-4">Impacto nos Pontos</h3>
        
        @if(isset($movimentosPontos) && $movimentosPontos->count() > 0)
            <div class="space-y-3">
                @foreach($movimentosPontos as $movimento)
                    <div class="flex items-center justify-between border-b border-gray-200 pb-3">
                        <div>
                            <p class="font-medium text-gray-900">
                                @if($movimento->tipo === 'RESERVA')
                                    Reserva criada
                                @elseif($movimento->tipo === 'CANCELAMENTO')
                                    Reserva cancelada
                                @elseif($movimento->tipo === 'FALTA')
                                    Falta não justificada
                                @endif
                            </p>
                            <p class="text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($movimento->created_at)->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        <div class="text-right">
                            @if($movimento->pontos > 0)
                                <span class="text-2xl font-bold text-green-600">+{{ $movimento->pontos }}</span>
                            @else
                                <span class="text-2xl font-bold text-red-600">{{ $movimento->pontos }}</span>
                            @endif
                            <p class="text-xs text-gray-500">pontos</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-600">Sem movimentos de pontos associados</p>
        @endif
    </div>
    
    <!-- QR Code for Security -->
    @if($reserva->estado === 'ATIVA' && \Carbon\Carbon::parse($reserva->data)->isToday())
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="font-bold text-gray-900 mb-4 text-center">Código de Validação</h3>
            <div class="text-center">
                <div class="inline-block bg-gray-100 p-8 rounded-lg">
                    <!-- QR Code would go here - você pode gerar com uma biblioteca -->
                    <div class="w-48 h-48 bg-white border-4 border-gray-300 flex items-center justify-center">
                        <p class="text-4xl font-bold text-gray-400">QR</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mt-4">
                    Mostre este código à segurança para validar sua entrada
                </p>
                <p class="text-2xl font-mono font-bold text-gray-900 mt-2">
                    {{ strtoupper(substr(md5($reserva->id . $reserva->data), 0, 8)) }}
                </p>
            </div>
        </div>
    @endif
    
</div>
@endsection
