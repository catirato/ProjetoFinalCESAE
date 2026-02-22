@extends('layouts.master')

@section('title', 'Reservas Validadas - Segurança')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Segurança - Validadas Hoje</h1>
        <p class="text-gray-600 mt-1">Reservas de hoje já validadas pela segurança.</p>
    </div>

    <div class="mb-6 flex gap-3">
        <a href="{{ route('seguranca.reservas.hoje') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
            Todas de Hoje
        </a>
        <a href="{{ route('seguranca.reservas.pendentes') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
            Pendentes
        </a>
        <a href="{{ route('seguranca.reservas.validadas') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg">
            Validadas
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Colaborador</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lugar</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($reservasHoje as $reserva)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $reserva->utilizador->nome ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div>Lugar {{ $reserva->lugar->numero ?? 'N/A' }}</div>
                            @if(($reserva->modo_reserva ?? 'COLAB') === 'ADMIN')
                                <span class="mt-1 inline-flex px-2 py-1 text-xs leading-5 font-semibold rounded-full bg-rose-100 text-rose-800">
                                    Reserva administrativa execional
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                ✓ Validado
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-gray-500">Sem reservas validadas hoje.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
