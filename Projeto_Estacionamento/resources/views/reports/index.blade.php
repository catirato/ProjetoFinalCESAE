@extends('layouts.master')

@section('title', 'Reports')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    @php
        $currentSort = request('sort', 'data');
        $currentDirection = request('direction', 'desc');
        $nextDirection = function ($column) use ($currentSort, $currentDirection) {
            return $currentSort === $column && $currentDirection === 'asc' ? 'desc' : 'asc';
        };
        $sortIcon = function ($column) use ($currentSort, $currentDirection) {
            if ($currentSort !== $column) return '';
            return $currentDirection === 'asc' ? '↑' : '↓';
        };
    @endphp

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Relatórios</h1>
        <p class="text-gray-600 mt-1">Lista de relatórios submetidos.</p>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'data', 'direction' => $nextDirection('data'), 'page' => 1]) }}" class="hover:text-gray-700">
                            Data {{ $sortIcon('data') }}
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'utilizador', 'direction' => $nextDirection('utilizador'), 'page' => 1]) }}" class="hover:text-gray-700">
                            Utilizador {{ $sortIcon('utilizador') }}
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'tipo', 'direction' => $nextDirection('tipo'), 'page' => 1]) }}" class="hover:text-gray-700">
                            Tipo {{ $sortIcon('tipo') }}
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'descricao', 'direction' => $nextDirection('descricao'), 'page' => 1]) }}" class="hover:text-gray-700">
                            Descrição {{ $sortIcon('descricao') }}
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'estado', 'direction' => $nextDirection('estado'), 'page' => 1]) }}" class="hover:text-gray-700">
                            Estado {{ $sortIcon('estado') }}
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($reports as $report)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($report->created_at)->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $report->utilizador->nome ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $report->tipo }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ \Illuminate\Support\Str::limit($report->descricao, 110) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $report->estado }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center gap-2 flex-wrap">
                                <a href="{{ route('admin.relatorios.show', $report->id) }}"
                                   class="inline-flex items-center px-3 py-2 rounded-lg bg-blue-100 text-blue-800 hover:bg-blue-200 transition font-medium">
                                    Ver
                                </a>
                                @if($report->estado === 'PENDENTE')
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
                                @else
                                    <span class="text-gray-400">Sem ações</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">Sem reports.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $reports->links() }}
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
