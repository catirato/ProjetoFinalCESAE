@extends('layouts.master')

@section('title', 'Detalhe — ' . $utilizador->nome)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- Voltar -->
    <div class="mb-6">
        <a href="{{ url('/historico') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition">
            ← Voltar à lista
        </a>
    </div>

    <!-- ═══════════════ Info do Utilizador ═══════════════ -->
    <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between">
            <div class="flex items-center space-x-5">
                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-bold text-2xl shadow-lg">
                    {{ strtoupper(substr($utilizador->nome, 0, 1)) }}
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $utilizador->nome }}</h1>
                    <p class="text-gray-500">{{ $utilizador->email }}</p>
                </div>
            </div>
            <div class="flex items-center gap-4 mt-4 md:mt-0">
                @if($utilizador->role === 'ADMIN')
                    <span class="px-4 py-2 bg-red-100 text-red-800 text-sm font-bold rounded-full">ADMIN</span>
                @elseif($utilizador->role === 'SEGURANCA')
                    <span class="px-4 py-2 bg-yellow-100 text-yellow-800 text-sm font-bold rounded-full">SEGURANÇA</span>
                @else
                    <span class="px-4 py-2 bg-blue-100 text-blue-800 text-sm font-bold rounded-full">COLAB</span>
                @endif
                <span class="px-4 py-2 bg-yellow-50 text-yellow-700 text-sm font-bold rounded-full">⭐ {{ $utilizador->pontos }} pontos</span>
            </div>
        </div>
    </div>

    <!-- ═══════════════ Gráficos ═══════════════ -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">

        <!-- Gráfico 1: Reservas por Mês -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">📊 Reservas por Mês</h3>
            <div class="flex items-end justify-between gap-2" style="height: 200px;">
                @foreach($reservasPorMes as $item)
                    <div class="flex flex-col items-center flex-1">
                        <span class="text-xs font-bold text-blue-700 mb-1">{{ $item['total'] }}</span>
                        <div class="w-full rounded-t-lg transition-all duration-500"
                             style="height: {{ $maxReservasMes > 0 ? max(($item['total'] / $maxReservasMes) * 160, 4) : 4 }}px;
                                    background: linear-gradient(180deg, #3b82f6, #1d4ed8);">
                        </div>
                        <span class="text-[10px] text-gray-500 mt-2 text-center leading-tight">{{ $item['mes'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Gráfico 2: Distribuição de Estados -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">📈 Distribuição de Estados</h3>
            <div class="flex items-end justify-between gap-2" style="height: 200px;">
                @php
                    $coresEstados = [
                        'ATIVA'           => ['from' => '#3b82f6', 'to' => '#2563eb'],
                        'PRESENTE'        => ['from' => '#22c55e', 'to' => '#16a34a'],
                        'NAO_COMPARECEU'  => ['from' => '#ef4444', 'to' => '#dc2626'],
                        'CANCELADA'       => ['from' => '#f59e0b', 'to' => '#d97706'],
                    ];
                @endphp
                @foreach($distribuicaoEstados as $item)
                    @php $cor = $coresEstados[$item['estado']] ?? ['from' => '#6b7280', 'to' => '#4b5563']; @endphp
                    <div class="flex flex-col items-center flex-1">
                        <span class="text-xs font-bold mb-1" style="color: {{ $cor['from'] }};">{{ $item['total'] }}</span>
                        <div class="w-full rounded-t-lg transition-all duration-500"
                             style="height: {{ $maxEstado > 0 ? max(($item['total'] / $maxEstado) * 160, 4) : 4 }}px;
                                    background: linear-gradient(180deg, {{ $cor['from'] }}, {{ $cor['to'] }});">
                        </div>
                        <span class="text-[10px] text-gray-500 mt-2 text-center leading-tight">
                            @if($item['estado'] === 'NAO_COMPARECEU')
                                NÃO COMP.
                            @else
                                {{ $item['estado'] }}
                            @endif
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Gráfico 3: Evolução de Pontos (ganhos vs gastos) -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-2">💰 Evolução de Pontos</h3>
            <div class="flex items-center gap-4 mb-3">
                <span class="flex items-center text-xs text-gray-600">
                    <span class="inline-block w-3 h-3 rounded-sm mr-1" style="background: linear-gradient(180deg, #22c55e, #16a34a);"></span> Ganhos
                </span>
                <span class="flex items-center text-xs text-gray-600">
                    <span class="inline-block w-3 h-3 rounded-sm mr-1" style="background: linear-gradient(180deg, #ef4444, #dc2626);"></span> Gastos
                </span>
            </div>
            <div class="flex items-end justify-between gap-1" style="height: 180px;">
                @foreach($evolucaoPontos as $item)
                    <div class="flex flex-col items-center flex-1">
                        <div class="flex gap-[2px] items-end w-full justify-center" style="height: 150px;">
                            {{-- Barra ganhos --}}
                            <div class="w-[45%] rounded-t-md transition-all duration-500"
                                 style="height: {{ $maxPontos > 0 ? max(($item['ganhos'] / $maxPontos) * 140, 2) : 2 }}px;
                                        background: linear-gradient(180deg, #22c55e, #16a34a);"
                                 title="Ganhos: +{{ $item['ganhos'] }}">
                            </div>
                            {{-- Barra gastos --}}
                            <div class="w-[45%] rounded-t-md transition-all duration-500"
                                 style="height: {{ $maxPontos > 0 ? max(($item['gastos'] / $maxPontos) * 140, 2) : 2 }}px;
                                        background: linear-gradient(180deg, #ef4444, #dc2626);"
                                 title="Gastos: -{{ $item['gastos'] }}">
                            </div>
                        </div>
                        <span class="text-[10px] text-gray-500 mt-2 text-center leading-tight">{{ $item['mes'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- ═══════════════ Tabela de Reservas ═══════════════ -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-xl font-bold text-gray-900">🅿️ Reservas</h2>
        </div>

        @if($reservas->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <th class="px-6 py-3">Data</th>
                            <th class="px-6 py-3">Lugar</th>
                            <th class="px-6 py-3 text-center">Estado</th>
                            <th class="px-6 py-3">Validada por</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($reservas as $reserva)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($reserva->data)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ $reserva->lugar->numero ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($reserva->estado === 'ATIVA')
                                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">ATIVA</span>
                                    @elseif($reserva->estado === 'PRESENTE')
                                        <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">PRESENTE</span>
                                    @elseif($reserva->estado === 'NAO_COMPARECEU')
                                        <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">NÃO COMPARECEU</span>
                                    @elseif($reserva->estado === 'CANCELADA')
                                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">CANCELADA</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ $reserva->validadaPor->nome ?? '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($reservas->hasPages())
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $reservas->links() }}
                </div>
            @endif
        @else
            <div class="px-6 py-8 text-center text-gray-500">
                <p class="text-4xl mb-2">🅿️</p>
                <p>Sem reservas registadas</p>
            </div>
        @endif
    </div>

    <!-- ═══════════════ Tabela de Movimentos de Pontos ═══════════════ -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-xl font-bold text-gray-900">⭐ Movimentos de Pontos</h2>
        </div>

        @if($movimentos->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <th class="px-6 py-3">Tipo</th>
                            <th class="px-6 py-3 text-center">Pontos</th>
                            <th class="px-6 py-3">Data</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($movimentos as $mov)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    @if($mov->tipo === 'RESERVA')
                                        <span class="inline-flex items-center gap-1 text-sm"><span class="text-lg">🅿️</span> Reserva</span>
                                    @elseif($mov->tipo === 'CANCELAMENTO')
                                        <span class="inline-flex items-center gap-1 text-sm"><span class="text-lg">↩️</span> Cancelamento</span>
                                    @elseif($mov->tipo === 'FALTA')
                                        <span class="inline-flex items-center gap-1 text-sm"><span class="text-lg">⚠️</span> Falta</span>
                                    @elseif($mov->tipo === 'RESET_MENSAL')
                                        <span class="inline-flex items-center gap-1 text-sm"><span class="text-lg">🔄</span> Reset Mensal</span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-sm"><span class="text-lg">⚙️</span> Ajuste</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($mov->pontos > 0)
                                        <span class="text-lg font-bold text-green-600">+{{ $mov->pontos }}</span>
                                    @else
                                        <span class="text-lg font-bold text-red-600">{{ $mov->pontos }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($mov->created_at)->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($movimentos->hasPages())
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $movimentos->links() }}
                </div>
            @endif
        @else
            <div class="px-6 py-8 text-center text-gray-500">
                <p class="text-4xl mb-2">⭐</p>
                <p>Sem movimentos de pontos</p>
            </div>
        @endif
    </div>

    <!-- ═══════════════ Tabela de Histórico de Eventos ═══════════════ -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-xl font-bold text-gray-900">📋 Histórico de Eventos</h2>
        </div>

        @if($eventos->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <th class="px-6 py-3">Tipo</th>
                            <th class="px-6 py-3">Ação</th>
                            <th class="px-6 py-3">Descrição</th>
                            <th class="px-6 py-3">Data</th>
                            <th class="px-6 py-3 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($eventos as $evento)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    @if($evento->tipo_evento === 'RESERVA')
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">RESERVA</span>
                                    @elseif($evento->tipo_evento === 'LISTA_ESPERA')
                                        <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs font-semibold rounded-full">LISTA ESPERA</span>
                                    @elseif($evento->tipo_evento === 'REPORT')
                                        <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs font-semibold rounded-full">REPORT</span>
                                    @elseif($evento->tipo_evento === 'PONTOS')
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">PONTOS</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($evento->acao === 'CRIADO')
                                        <span class="text-green-700 font-medium">CRIADO</span>
                                    @elseif($evento->acao === 'ATUALIZADO')
                                        <span class="text-blue-700 font-medium">ATUALIZADO</span>
                                    @elseif($evento->acao === 'REMOVIDO')
                                        <span class="text-red-700 font-medium">REMOVIDO</span>
                                    @elseif($evento->acao === 'VALIDADO')
                                        <span class="text-emerald-700 font-medium">VALIDADO</span>
                                    @elseif($evento->acao === 'CANCELADO')
                                        <span class="text-yellow-700 font-medium">CANCELADO</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $evento->descricao }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($evento->created_at)->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ url('/historico/evento/' . $evento->id . '/edit') }}"
                                           class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded-lg hover:bg-yellow-200 transition">
                                            ✏️ Editar
                                        </a>
                                        <form action="{{ url('/historico/evento/' . $evento->id) }}" method="POST"
                                              onsubmit="return confirm('Tem certeza que deseja apagar este evento?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="px-3 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-lg hover:bg-red-200 transition">
                                                🗑️ Apagar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($eventos->hasPages())
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $eventos->links() }}
                </div>
            @endif
        @else
            <div class="px-6 py-8 text-center text-gray-500">
                <p class="text-4xl mb-2">📋</p>
                <p>Sem eventos registados</p>
            </div>
        @endif
    </div>

</div>
@endsection
