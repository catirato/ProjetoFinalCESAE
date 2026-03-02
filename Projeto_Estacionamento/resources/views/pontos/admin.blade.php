@extends('layouts.master')

@section('title', 'Gestão de Pontos')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Gestão de Pontos</h1>
        <p class="text-gray-600 mt-1">Ajuste manual de pontos por utilizador (apenas administrador).</p>
    </div>

    @if(isset($reportEmAjuste) && $reportEmAjuste)
        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
            <p class="text-blue-900 font-semibold">Ajuste em contexto de relatório</p>
            <p class="text-blue-800 text-sm mt-1">
                Relatório #{{ $reportEmAjuste->id }} de {{ $reportEmAjuste->utilizador->nome ?? 'N/A' }}.
                Após aplicar o ajuste, o relatório será marcado como concluído.
            </p>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-lg p-4 mb-6">
        <form method="GET" action="{{ route('admin.pontos.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
            @if(isset($reportEmAjuste) && $reportEmAjuste)
                <input type="hidden" name="report_id" value="{{ $reportEmAjuste->id }}">
            @endif
            <div class="md:col-span-2">
                <label for="filtro_nome" class="block text-sm font-medium text-gray-700 mb-1">Filtrar por nome</label>
                <input type="text"
                       id="filtro_nome"
                       name="filtro_nome"
                       value="{{ $filtroNome ?? request('filtro_nome') }}"
                       placeholder="Ex.: Maria"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="filtro_funcao" class="block text-sm font-medium text-gray-700 mb-1">Filtrar por função</label>
                <select id="filtro_funcao"
                        name="filtro_funcao"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todas</option>
                    <option value="COLAB" {{ ($filtroFuncao ?? request('filtro_funcao')) === 'COLAB' ? 'selected' : '' }}>Colaborador</option>
                    <option value="SEGURANCA" {{ ($filtroFuncao ?? request('filtro_funcao')) === 'SEGURANCA' ? 'selected' : '' }}>Segurança</option>
                    <option value="ADMIN" {{ ($filtroFuncao ?? request('filtro_funcao')) === 'ADMIN' ? 'selected' : '' }}>Administrador</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Filtrar
                </button>
                <a href="{{ isset($reportEmAjuste) && $reportEmAjuste ? route('admin.pontos.index', ['report_id' => $reportEmAjuste->id]) : route('admin.pontos.index') }}"
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Limpar
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pontos atuais</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ajuste</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($utilizadores as $u)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $u->nome }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $u->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-800 text-sm font-semibold">
                                {{ $u->pontos }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <form action="{{ route('admin.pontos.adjust', $u->id) }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                @if(isset($reportEmAjuste) && $reportEmAjuste)
                                    <input type="hidden" name="report_id" value="{{ $reportEmAjuste->id }}">
                                @endif
                                <input type="number"
                                       name="ajuste"
                                       required
                                       min="-100"
                                       max="100"
                                       step="1"
                                       placeholder="+5 / -5"
                                       class="w-28 px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <button type="submit"
                                        class="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                                    Aplicar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">Sem utilizadores.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $utilizadores->links() }}
    </div>
</div>
@endsection
