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

    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <form method="GET" action="{{ route('admin.relatorios.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Pesquisar por nome</label>
                <input type="text"
                       id="search"
                       name="search"
                       value="{{ request('search', $search ?? '') }}"
                       placeholder="Ex: Cristina"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Função</label>
                <select id="role"
                        name="role"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todas</option>
                    <option value="ADMIN" {{ request('role', $role ?? '') === 'ADMIN' ? 'selected' : '' }}>ADMIN</option>
                    <option value="COLAB" {{ request('role', $role ?? '') === 'COLAB' ? 'selected' : '' }}>COLAB</option>
                    <option value="SEGURANCA" {{ request('role', $role ?? '') === 'SEGURANCA' ? 'selected' : '' }}>SEGURANCA</option>
                </select>
            </div>
            <div class="flex gap-3">
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">
                    Filtrar
                </button>
                <a href="{{ route('admin.relatorios.index') }}"
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition">
                    Limpar filtros
                </a>
            </div>
        </form>
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
                        <td class="px-6 py-4 text-sm">
                            <div class="flex items-center gap-2 flex-wrap">
                                <a href="{{ route('admin.relatorios.show', $report->id) }}"
                                   class="inline-flex items-center px-3 py-2 rounded-lg bg-blue-100 text-blue-800 hover:bg-blue-200 transition font-medium">
                                    Ver
                                </a>
                                <a href="{{ route('admin.relatorios.edit', $report->id) }}"
                                   class="inline-flex items-center px-3 py-2 rounded-lg bg-amber-100 text-amber-800 hover:bg-amber-200 transition font-medium">
                                    Editar
                                </a>
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

@endsection
