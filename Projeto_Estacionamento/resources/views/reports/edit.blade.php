@extends('layouts.master')

@section('title', 'Detalhe do Relatório')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex items-center justify-between gap-3 flex-wrap">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Detalhe do Relatório</h1>
            <p class="text-gray-600 mt-1">Edição completa do tratamento do relatório.</p>
        </div>
        <a href="{{ route('admin.relatorios.index') }}"
           class="text-blue-600 hover:text-blue-800 text-sm font-medium inline-flex items-center">
            Voltar aos Relatórios
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 space-y-5">
        <form method="POST" action="{{ route('admin.relatorios.update', $report->id) }}" class="space-y-5">
            @csrf
            @method('PATCH')

            @if($errors->any())
                <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Data</p>
                <p class="text-gray-900 mt-1">{{ \Carbon\Carbon::parse($report->created_at)->format('d/m/Y H:i') }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Estado</p>
                <select name="estado" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="PENDENTE" {{ old('estado', $report->estado) === 'PENDENTE' ? 'selected' : '' }}>PENDENTE</option>
                    <option value="VALIDADO" {{ old('estado', $report->estado) === 'VALIDADO' ? 'selected' : '' }}>VALIDADO</option>
                    <option value="REJEITADO" {{ old('estado', $report->estado) === 'REJEITADO' ? 'selected' : '' }}>REJEITADO</option>
                </select>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Utilizador</p>
                <p class="text-gray-900 mt-1">{{ $report->utilizador->nome ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Tipo</p>
                <select name="tipo" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="LUGAR_OCUPADO" {{ old('tipo', $report->tipo) === 'LUGAR_OCUPADO' ? 'selected' : '' }}>LUGAR_OCUPADO</option>
                    <option value="SEM_RESERVA" {{ old('tipo', $report->tipo) === 'SEM_RESERVA' ? 'selected' : '' }}>SEM_RESERVA</option>
                    <option value="PROBLEMA" {{ old('tipo', $report->tipo) === 'PROBLEMA' ? 'selected' : '' }}>PROBLEMA</option>
                </select>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Ajuste de Pontos</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                <div>
                    <label for="ajuste_pontos_necessario" class="block text-sm font-medium text-gray-700 mb-1">Precisa de ajuste?</label>
                    <select id="ajuste_pontos_necessario" name="ajuste_pontos_necessario" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="0" {{ (string) old('ajuste_pontos_necessario', (int) ($report->ajuste_pontos_necessario ?? 0)) === '0' ? 'selected' : '' }}>Não</option>
                        <option value="1" {{ (string) old('ajuste_pontos_necessario', (int) ($report->ajuste_pontos_necessario ?? 0)) === '1' ? 'selected' : '' }}>Sim</option>
                    </select>
                </div>
                <div>
                    <label for="ajuste_pontos_concluido" class="block text-sm font-medium text-gray-700 mb-1">Ajuste concluído?</label>
                    <select id="ajuste_pontos_concluido" name="ajuste_pontos_concluido" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="0" {{ (string) old('ajuste_pontos_concluido', (int) ($report->ajuste_pontos_concluido ?? 0)) === '0' ? 'selected' : '' }}>Não</option>
                        <option value="1" {{ (string) old('ajuste_pontos_concluido', (int) ($report->ajuste_pontos_concluido ?? 0)) === '1' ? 'selected' : '' }}>Sim</option>
                    </select>
                </div>
            </div>

            <a href="{{ route('admin.pontos.index', ['report_id' => $report->id]) }}"
               class="inline-flex items-center mt-3 px-3 py-2 rounded-lg bg-blue-100 text-blue-800 hover:bg-blue-200 transition font-medium w-full sm:w-auto justify-center sm:justify-start">
                Ir para Gestão de Pontos
            </a>
        </div>

        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Descrição Completa</p>
            <textarea name="descricao"
                      rows="6"
                      class="mt-2 w-full p-4 rounded-lg bg-gray-50 border border-gray-200 text-gray-900 whitespace-pre-wrap break-words focus:ring-blue-500 focus:border-blue-500">{{ old('descricao', $report->descricao) }}</textarea>
        </div>

            <div class="flex items-center gap-2 flex-wrap pt-2 border-t border-gray-100">
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 transition font-medium">
                    Guardar alterações
                </button>
                <a href="{{ route('admin.relatorios.index') }}"
                   class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-100 text-gray-800 hover:bg-gray-200 transition font-medium">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
