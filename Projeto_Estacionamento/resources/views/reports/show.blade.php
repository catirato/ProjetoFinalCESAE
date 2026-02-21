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
           class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-100 text-gray-800 hover:bg-gray-200 transition font-medium">
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

        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Descrição Completa</p>
            <div class="mt-2 p-4 rounded-lg bg-gray-50 border border-gray-200 text-gray-900 whitespace-pre-wrap break-words">
                {{ $report->descricao }}
            </div>
        </div>

        @if($report->estado === 'PENDENTE')
            <div class="flex items-center gap-2 flex-wrap pt-2 border-t border-gray-100">
                <form id="validar-report-{{ $report->id }}" method="POST" action="{{ route('admin.relatorios.validar', $report->id) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="ajustar_pontos" value="0">
                    <button type="button"
                            onclick="confirmarAjustePontos('validar-report-{{ $report->id }}')"
                            class="inline-flex items-center px-3 py-2 rounded-lg bg-green-100 text-green-800 hover:bg-green-200 transition font-medium">
                        Validar
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.relatorios.rejeitar', $report->id) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="inline-flex items-center px-3 py-2 rounded-lg bg-red-100 text-red-800 hover:bg-red-200 transition font-medium">
                        Rejeitar
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>

<script>
    function confirmarAjustePontos(formId) {
        const precisaAjustar = window.confirm('É necessário algum ajuste de pontos para este relatório?');

        const form = document.getElementById(formId);
        if (!form) return;

        const inputAjuste = form.querySelector('input[name="ajustar_pontos"]');
        if (inputAjuste) {
            inputAjuste.value = precisaAjustar ? '1' : '0';
        }

        form.submit();
    }
</script>
@endsection
