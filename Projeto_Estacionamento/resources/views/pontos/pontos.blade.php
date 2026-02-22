@extends('layouts.master')

@section('title', 'Meus Pontos')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Meus Pontos</h1>
        <p class="text-gray-600 mt-1">Acompanhe o seu saldo e histórico de pontos</p>
    </div>

    <!-- Points Card -->
    <div class="bg-gradient-to-br from-yellow-400 to-yellow-500 rounded-xl shadow-xl p-8 mb-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-yellow-100 text-sm font-medium uppercase tracking-wide">Saldo Atual</p>
                <p class="text-6xl font-bold mt-2">{{ auth('utilizador')->user()->pontos }}</p>
                <p class="text-yellow-100 mt-2">pontos disponíveis</p>
            </div>
            <div class="text-8xl opacity-80">⭐</div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-600">Pontos Ganhos</h3>
                <div class="text-2xl">📈</div>
            </div>
            <p class="text-3xl font-bold text-green-600">+{{ $pontosGanhos ?? 0 }}</p>
            <p class="text-sm text-gray-500 mt-1">Este mês</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-600">Pontos Gastos</h3>
                <div class="text-2xl">📉</div>
            </div>
            <p class="text-3xl font-bold text-red-600">-{{ $pontosGastos ?? 0 }}</p>
            <p class="text-sm text-gray-500 mt-1">Este mês</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-600">Próximo Reset</h3>
                <div class="text-2xl">🔄</div>
            </div>
            <p class="text-3xl font-bold text-blue-600">{{ $diasProximoReset ?? 0 }}</p>
            <p class="text-sm text-gray-500 mt-1">dias restantes</p>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
            <div class="flex items-start">
                <div class="text-3xl mr-4">💡</div>
                <div>
                    <h3 class="font-bold text-blue-900 mb-2">Como Ganha Pontos</h3>
                    <ul class="text-blue-800 space-y-1 text-sm">
                        <li>• Reset mensal: +30 pontos no último dia de cada mês</li>
                        <li>• Cancelar com antecedência (até ás 10h do próprio dia): recupera 1 ponto</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="bg-red-50 border border-red-200 rounded-xl p-6">
            <div class="flex items-start">
                <div class="text-3xl mr-4">⚠️</div>
                <div>
                    <h3 class="font-bold text-red-900 mb-2">Como Perde Pontos</h3>
                    <ul class="text-red-800 space-y-1 text-sm">
                        <li>• Fazer reserva: -3 pontos por reserva</li>
                        <li>• Não comparecer: -10 pontos adicionais</li>
                        <li>• Cancelar após as 10h do próprio dia: -2 pontos</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Histórico -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-xl font-bold text-gray-900">Histórico de Movimentos</h2>
        </div>

        @if(isset($movimentos) && $movimentos->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($movimentos as $movimento)
                    <div class="px-6 py-4 hover:bg-gray-50 transition">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <!-- Icon based on type -->
                                @if($movimento->tipo === 'RESERVA')
                                    <div class="bg-blue-100 text-blue-600 p-3 rounded-lg">
                                        🅿️
                                    </div>
                                @elseif($movimento->tipo === 'CANCELAMENTO')
                                    <div class="bg-yellow-100 text-yellow-600 p-3 rounded-lg">
                                        ↩️
                                    </div>
                                @elseif($movimento->tipo === 'FALTA')
                                    <div class="bg-red-100 text-red-600 p-3 rounded-lg">
                                        ⚠️
                                    </div>
                                @elseif($movimento->tipo === 'RESET_MENSAL')
                                    <div class="bg-green-100 text-green-600 p-3 rounded-lg">
                                        🔄
                                    </div>
                                @else
                                    <div class="bg-gray-100 text-gray-600 p-3 rounded-lg">
                                        ⚙️
                                    </div>
                                @endif

                                <div>
                                    <p class="font-semibold text-gray-900">
                                        @if($movimento->tipo === 'RESERVA')
                                            Reserva criada
                                        @elseif($movimento->tipo === 'CANCELAMENTO')
                                            Reserva cancelada
                                        @elseif($movimento->tipo === 'FALTA')
                                            Falta não justificada
                                        @elseif($movimento->tipo === 'RESET_MENSAL')
                                            Reset mensal de pontos
                                        @elseif($movimento->tipo === 'AJUSTE')
                                            Ajuste manual
                                        @endif
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($movimento->created_at)->format('d/m/Y H:i') }}
                                    </p>
                                    @if($movimento->reserva)
                                        <a href="{{ url('/reservas/' . $movimento->reserva_id) }}"
                                           class="text-xs text-blue-600 hover:text-blue-800">
                                            Ver reserva →
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <div class="text-right">
                                @if($movimento->pontos > 0)
                                    <span class="text-2xl font-bold text-green-600">
                                        +{{ $movimento->pontos }}
                                    </span>
                                @else
                                    <span class="text-2xl font-bold text-red-600">
                                        {{ $movimento->pontos }}
                                    </span>
                                @endif
                                <p class="text-xs text-gray-500 mt-1">pontos</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($movimentos->hasPages())
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $movimentos->links() }}
                </div>
            @endif
        @else
            <div class="px-6 py-12 text-center">
                <div class="text-6xl mb-4">📊</div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Sem movimentos</h3>
                <p class="text-gray-600">Ainda não há movimentos de pontos no seu histórico</p>
            </div>
        @endif
    </div>

</div>
@endsection
