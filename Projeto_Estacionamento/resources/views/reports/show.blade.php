@extends('layouts.master')

@section('title', 'Detalhe do Relatório')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex items-center justify-between gap-3 flex-wrap">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Detalhe do Relatório</h1>
            <p class="text-gray-600 mt-1">Visualização completa (apenas leitura).</p>
        </div>
        <a href="{{ route('admin.relatorios.index') }}"
           class="text-blue-600 hover:text-blue-800 text-sm font-medium inline-flex items-center">
            Voltar aos Relatórios
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 space-y-5">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Data</p>
                <p class="text-gray-900 mt-1">{{ \Carbon\Carbon::parse($report->created_at)->format('d/m/Y H:i') }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Estado</p>
                <p class="text-gray-900 mt-1">{{ $report->estado }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Utilizador</p>
                <p class="text-gray-900 mt-1">{{ $report->utilizador->nome ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Tipo</p>
                <p class="text-gray-900 mt-1">{{ $report->tipo }}</p>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Ajuste de Pontos</p>
            @if($report->estado === 'VALIDADO')
                @if($report->ajuste_pontos_necessario ?? false)
                    @if($report->ajuste_pontos_concluido ?? false)
                        <p class="text-gray-900 mt-1">Sim. O ajuste de pontos foi concluído.</p>
                    @else
                        <p class="text-gray-900 mt-1">Sim. O ajuste de pontos está pendente.</p>
                    @endif
                @else
                    <p class="text-gray-900 mt-1">Não. Este relatório foi validado sem ajuste de pontos.</p>
                @endif
            @elseif($report->estado === 'REJEITADO')
                <p class="text-gray-900 mt-1">Não aplicável. O relatório foi rejeitado.</p>
            @else
                <p class="text-gray-900 mt-1">Ainda não definido. O relatório está pendente.</p>
            @endif

            <a href="{{ route('admin.pontos.index', ['report_id' => $report->id]) }}"
               class="inline-flex items-center mt-3 px-3 py-2 rounded-lg bg-blue-100 text-blue-800 hover:bg-blue-200 transition font-medium">
                Ir para Gestão de Pontos
            </a>
        </div>

        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Descrição Completa</p>
            <div class="mt-2 p-4 rounded-lg bg-gray-50 border border-gray-200 text-gray-900 whitespace-pre-wrap break-words">
                {{ $report->descricao }}
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap pt-2 border-t border-gray-100">
            <a href="{{ route('admin.relatorios.edit', $report->id) }}"
               class="inline-flex items-center px-4 py-2 rounded-lg bg-amber-100 text-amber-800 hover:bg-amber-200 transition font-medium">
                Editar relatório
            </a>
        </div>
    </div>
</div>
@endsection
