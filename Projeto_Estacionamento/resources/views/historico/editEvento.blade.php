@extends('layouts.master')

@section('title', 'Editar Evento')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- Voltar -->
    <div class="mb-6">
        <a href="{{ url('/historico/' . $utilizador->id) }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition">
            ← Voltar ao detalhe de {{ $utilizador->nome }}
        </a>
    </div>

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">✏️ Editar Evento</h1>
        <p class="text-gray-600 mt-1">Corrigir informação de um registo do histórico de eventos</p>
    </div>

    <!-- Formulário -->
    <div class="bg-white rounded-xl shadow-lg p-8">
        <form method="POST" action="{{ url('/historico/evento/' . $evento->id) }}">
            @csrf
            @method('PUT')

            <!-- Tipo de Evento -->
            <div class="mb-6">
                <label for="tipo_evento" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Evento</label>
                <select name="tipo_evento" id="tipo_evento"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tipo_evento') border-red-500 @enderror">
                    <option value="RESERVA" {{ old('tipo_evento', $evento->tipo_evento) === 'RESERVA' ? 'selected' : '' }}>RESERVA</option>
                    <option value="LISTA_ESPERA" {{ old('tipo_evento', $evento->tipo_evento) === 'LISTA_ESPERA' ? 'selected' : '' }}>LISTA ESPERA</option>
                    <option value="REPORT" {{ old('tipo_evento', $evento->tipo_evento) === 'REPORT' ? 'selected' : '' }}>REPORT</option>
                    <option value="PONTOS" {{ old('tipo_evento', $evento->tipo_evento) === 'PONTOS' ? 'selected' : '' }}>PONTOS</option>
                </select>
                @error('tipo_evento')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Ação -->
            <div class="mb-6">
                <label for="acao" class="block text-sm font-medium text-gray-700 mb-2">Ação</label>
                <select name="acao" id="acao"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('acao') border-red-500 @enderror">
                    <option value="CRIADO" {{ old('acao', $evento->acao) === 'CRIADO' ? 'selected' : '' }}>CRIADO</option>
                    <option value="ATUALIZADO" {{ old('acao', $evento->acao) === 'ATUALIZADO' ? 'selected' : '' }}>ATUALIZADO</option>
                    <option value="REMOVIDO" {{ old('acao', $evento->acao) === 'REMOVIDO' ? 'selected' : '' }}>REMOVIDO</option>
                    <option value="VALIDADO" {{ old('acao', $evento->acao) === 'VALIDADO' ? 'selected' : '' }}>VALIDADO</option>
                    <option value="CANCELADO" {{ old('acao', $evento->acao) === 'CANCELADO' ? 'selected' : '' }}>CANCELADO</option>
                </select>
                @error('acao')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Descrição -->
            <div class="mb-6">
                <label for="descricao" class="block text-sm font-medium text-gray-700 mb-2">Descrição</label>
                <textarea name="descricao" id="descricao" rows="4"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('descricao') border-red-500 @enderror"
                          placeholder="Descrição do evento...">{{ old('descricao', $evento->descricao) }}</textarea>
                @error('descricao')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Info extra (read only) -->
            <div class="mb-6 bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-600">
                    <strong>Utilizador:</strong> {{ $utilizador->nome }} ({{ $utilizador->email }})
                </p>
                <p class="text-sm text-gray-600 mt-1">
                    <strong>Criado em:</strong> {{ \Carbon\Carbon::parse($evento->created_at)->format('d/m/Y H:i') }}
                </p>
                <p class="text-sm text-gray-600 mt-1">
                    <strong>Entidade ID:</strong> {{ $evento->entidade_id }}
                </p>
            </div>

            <!-- Botões -->
            <div class="flex items-center justify-between">
                <a href="{{ url('/historico/' . $utilizador->id) }}"
                   class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">
                    💾 Guardar Alterações
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
