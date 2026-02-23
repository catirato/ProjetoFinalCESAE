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

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
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
