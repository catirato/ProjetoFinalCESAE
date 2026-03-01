@extends('layouts.master')

@section('title', 'Validação de Chegadas')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Segurança - Reservas de Hoje</h1>
        <p class="text-gray-600 mt-1">Valide a chegada dos colaboradores e registe relatórios.</p>
    </div>

    <div class="mb-6 flex gap-3">
        <a href="{{ route('seguranca.reservas.hoje') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
            Todas de Hoje
        </a>
        <a href="{{ route('seguranca.reservas.pendentes') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
            Pendentes
        </a>
        <a href="{{ route('seguranca.reservas.validadas') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
            Validadas
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Colaborador</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lugar</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ação</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($reservasHoje as $reserva)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $reserva->utilizador->nome ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div>Lugar {{ $reserva->lugar->numero ?? 'N/A' }}</div>
                            @if(($reserva->modo_reserva ?? 'COLAB') === 'ADMIN')
                                <span class="mt-1 inline-flex px-2 py-1 text-xs leading-5 font-semibold rounded-full bg-rose-100 text-rose-800">
                                    Reserva administrativa execional
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($reserva->estado === 'PRESENTE')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    ✓ Validado
                                </span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    ATIVA
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @if($reserva->estado === 'ATIVA')
                                <form method="POST" action="{{ route('seguranca.reservas.validar', $reserva->id) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                        Validar chegada
                                    </button>
                                </form>
                            @else
                                <span class="text-gray-500">Sem ação</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">Sem reservas para hoje.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Submeter Relatório</h2>
        <form method="POST" action="{{ route('seguranca.reports.store') }}" class="space-y-4">
            @csrf
            <div>
                <label for="tipo" class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                <select id="tipo" name="tipo" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                    <option value="LUGAR_OCUPADO">Lugar ocupado indevidamente</option>
                    <option value="SEM_RESERVA">Veículo sem reserva</option>
                    <option value="PROBLEMA">Problema no estacionamento</option>
                </select>
            </div>
            <div>
                <label for="descricao" class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                <textarea id="descricao" name="descricao" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2" required></textarea>
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Enviar relatório
            </button>
        </form>
    </div>
</div>
@endsection
