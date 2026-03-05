@extends('layouts.master')

@section('title', 'Lista de Espera')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Lista de Espera</h1>
        <p class="text-gray-600 mt-1">Aguarde por uma vaga disponível</p>
    </div>

    <!-- Info Banner -->
    <div class="bg-purple-50 border border-purple-200 rounded-xl p-6 mb-8">
        <div class="flex items-start">
            <div class="text-4xl mr-4">ℹ️</div>
            <div>
                <h3 class="font-bold text-purple-900 mb-2">Como funciona?</h3>
                <p class="text-purple-800 mb-2">
                    Quando não há vagas disponíveis, pode entrar na lista de espera.
                </p>
                <ul class="text-sm text-purple-700 space-y-1">

                    <li>• Se alguém cancelar e existir um lugar disponível, será notificado por email.</li>
                    <li>• Tem até às 10h para confirmar se a vaga for para o próprio dia.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- My Waiting List Entries -->
    @if(isset($minhasEntradas) && $minhasEntradas->count() > 0)
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Minhas Entradas na Lista</h2>

            <div class="space-y-4">
                @foreach($minhasEntradas as $entrada)
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-gray-900">
                                    📅 {{ \Carbon\Carbon::parse($entrada->data)->format('d/m/Y') }}
                                    ({{ \Carbon\Carbon::parse($entrada->data)->locale('pt')->isoFormat('dddd') }})
                                </p>

                                <!-- Status -->
                                @if($entrada->estado === 'ATIVO')
                                    <span class="inline-block mt-2 px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                                        🕐 Aguardando
                                    </span>
                                @elseif($entrada->estado === 'NOTIFICADO')
                                    <span class="inline-block mt-2 px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">
                                        ✓ Vaga Disponível!
                                    </span>
                                    @if($entrada->expira_em)
                                        <p class="text-sm text-green-700 mt-1">
                                            Confirmar até {{ $entrada->expira_em->format('H:i') }}
                                        </p>
                                    @endif
                                @elseif($entrada->estado === 'ACEITE')
                                    <span class="inline-block mt-2 px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">
                                        ✓ Aceite
                                    </span>
                                @elseif($entrada->estado === 'EXPIRADO')
                                    <span class="inline-block mt-2 px-3 py-1 bg-gray-100 text-gray-800 text-sm font-medium rounded-full">
                                        ⏱️ Expirado
                                    </span>
                                @endif

                            </div>

                            <div class="flex space-x-2">
                                @if($entrada->estado === 'NOTIFICADO')
                                    <form action="{{ url('/lista-espera/' . $entrada->id . '/aceitar') }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                            Aceitar Vaga
                                        </button>
                                    </form>
                                @endif

                                @if($entrada->estado === 'ATIVO')
                                    <form action="{{ url('/lista-espera/' . $entrada->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('Tem certeza que deseja sair da lista de espera?')"
                                                class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition">
                                            Sair da Lista
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Add to Waiting List -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Entrar na Lista de Espera</h2>

        <form action="{{ url('/lista-espera') }}" method="POST">
            @csrf

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-6">
                <label for="data" class="block text-sm font-medium text-gray-700 mb-2">
                    Para que data precisa de vaga?
                </label>
                <input type="date"
                       id="data"
                       name="data"
                       required
                       min="{{ date('Y-m-d') }}"
                       max="{{ date('Y-m-d', strtotime('+30 days')) }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                <p class="mt-2 text-sm text-gray-500">
                    Escolha a data em que precisa de estacionar
                </p>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <div class="text-2xl mr-3">⚠️</div>
                    <div>
                        <p class="text-sm text-yellow-800">
                            <strong>Nota importante:</strong> Entrar na lista de espera não garante uma vaga.
                            Apenas significa que será notificado se uma vaga ficar disponível.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <button type="submit"
                        class="px-6 py-3 bg-purple-600 text-white rounded-lg font-semibold hover:bg-purple-700 transition">
                    Entrar na Lista de Espera
                </button>
            </div>
        </form>
    </div>

    <!-- Current Waiting List (for transparency) -->
    @if(isset($listaCompleta) && $listaCompleta->count() > 0)
        <div class="bg-white rounded-xl shadow-lg p-6 mt-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Lista de Espera Atual</h2>
            <p class="text-sm text-gray-600 mb-4">
                Veja quem está na lista de espera (não existe ordem de preferência)
            </p>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Posição
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Data
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Estado
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($listaCompleta as $entrada)
                            <tr class="hover:bg-gray-50 {{ $entrada->utilizador_id === auth('utilizador')->id() ? 'bg-yellow-50' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full font-semibold">
                                        #{{ $entrada->prioridade }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($entrada->data)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($entrada->estado === 'ATIVO')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Aguardando
                                        </span>
                                    @elseif($entrada->estado === 'NOTIFICADO')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            Notificado
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
@endsection
