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
        <form method="POST" action="{{ route('admin.relatorios.update', $report->id) }}" enctype="multipart/form-data" class="space-y-5">
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

            <button type="button"
               onclick="openAdminPontosModal()"
               class="inline-flex items-center mt-3 px-3 py-2 rounded-lg bg-blue-100 text-blue-800 hover:bg-blue-200 transition font-medium w-full sm:w-auto justify-center sm:justify-start">
                Ir para Gestão de Pontos
            </button>
        </div>

        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Descrição Completa</p>
            <textarea name="descricao"
                      rows="6"
                      class="mt-2 w-full p-4 rounded-lg bg-gray-50 border border-gray-200 text-gray-900 whitespace-pre-wrap break-words focus:ring-blue-500 focus:border-blue-500">{{ old('descricao', $report->descricao) }}</textarea>
        </div>

        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 space-y-3">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Fotos do Relatório</p>

            @if(!empty($report->fotos))
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($report->fotos as $foto)
                        <div class="block rounded-lg border border-gray-200 bg-white p-2">
                            <a href="{{ asset('storage/' . $foto) }}" target="_blank" rel="noopener noreferrer">
                                <img src="{{ asset('storage/' . $foto) }}"
                                     alt="Foto do relatório"
                                     class="w-full h-28 object-cover rounded-md">
                            </a>
                            <div class="mt-2">
                                <label class="inline-flex items-center gap-2 text-sm text-red-700">
                                    <input type="checkbox" name="remove_fotos[]" value="{{ $foto }}" class="rounded border-gray-300">
                                    Remover foto
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500">Sem fotos anexadas.</p>
            @endif

            <div>
                <label for="fotos" class="block text-sm font-medium text-gray-700 mb-1">Adicionar novas fotos (opcional)</label>
                <input id="fotos"
                       name="fotos[]"
                       type="file"
                       accept="image/*"
                       capture="environment"
                       multiple
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white">
                <p class="text-xs text-gray-500 mt-1">Pode tirar foto com a câmara ou anexar da galeria (até 5 fotos, máx. 5MB cada).</p>
            </div>
        </div>

            <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 transition font-medium">
                    Guardar alterações
                </button>
                <a href="{{ route('admin.relatorios.index') }}"
                   class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-100 text-gray-800 hover:bg-gray-200 transition font-medium">
                    Cancelar
                </a>
                <form method="POST" action="{{ route('admin.relatorios.destroy', $report->id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            onclick="return confirm('Tem certeza que deseja apagar este relatório?')"
                            class="inline-flex items-center px-4 py-2 rounded-lg bg-red-100 text-red-800 hover:bg-red-200 transition font-medium">
                        Apagar relatório
                    </button>
                </form>
            </div>
        </form>
    </div>
</div>

<div id="admin-pontos-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center p-0"
     aria-hidden="true">
    <div class="absolute inset-0 bg-black/50" onclick="closeAdminPontosModal()"></div>
    <div class="relative bg-white rounded-xl shadow-xl flex flex-col overflow-hidden"
         style="width: 70vw; height: 70vh; border: 12px solid #1f2937; box-sizing: border-box;">
        <div class="flex items-center justify-between px-4 py-3 border-b">
            <h2 class="text-lg font-semibold text-gray-900">Gestão de Pontos</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.pontos.index', ['report_id' => $report->id]) }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="btn btn-ghost btn-sm">
                    Abrir em nova aba
                </a>
                <button type="button" class="btn btn-primary btn-sm" onclick="closeAdminPontosModal()">
                    Fechar
                </button>
            </div>
        </div>
        <iframe src="{{ route('admin.pontos.index', ['report_id' => $report->id]) }}"
                title="Gestão de Pontos"
                class="w-full flex-1 border-0"></iframe>
    </div>
</div>

<script>
function openAdminPontosModal() {
    const modal = document.getElementById('admin-pontos-modal');
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('overflow-hidden');
}

function closeAdminPontosModal() {
    const modal = document.getElementById('admin-pontos-modal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    modal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('overflow-hidden');
}

document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        closeAdminPontosModal();
    }
});
</script>
@endsection
