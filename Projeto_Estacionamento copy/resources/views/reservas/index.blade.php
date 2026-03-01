@extends('layouts.master')

@section('title', auth('utilizador')->user()->role === 'ADMIN' ? 'Reservas (Admin)' : 'Minhas Reservas')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    @php
        $isAdmin = auth('utilizador')->user()->role === 'ADMIN';

        $sortAtivas = request('sort_ativas', 'data');
        $directionAtivas = request('direction_ativas', 'asc');
        $sortHistorico = request('sort_historico', 'data');
        $directionHistorico = request('direction_historico', 'desc');
        $activeTab = request('tab', 'ativas');

        $nextDirectionAtivas = function ($column) use ($sortAtivas, $directionAtivas) {
            return $sortAtivas === $column && $directionAtivas === 'asc' ? 'desc' : 'asc';
        };

        $nextDirectionHistorico = function ($column) use ($sortHistorico, $directionHistorico) {
            return $sortHistorico === $column && $directionHistorico === 'asc' ? 'desc' : 'asc';
        };

        $sortIconAtivas = function ($column) use ($sortAtivas, $directionAtivas) {
            if ($sortAtivas !== $column) return '';
            return $directionAtivas === 'asc' ? '↑' : '↓';
        };

        $sortIconHistorico = function ($column) use ($sortHistorico, $directionHistorico) {
            if ($sortHistorico !== $column) return '';
            return $directionHistorico === 'asc' ? '↑' : '↓';
        };
    @endphp

    
    <!-- Header -->
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                {{ auth('utilizador')->user()->role === 'ADMIN' ? 'Reservas de Todos os Utilizadores' : 'Minhas Reservas' }}
            </h1>
            <p class="text-gray-600 mt-1">
                {{ auth('utilizador')->user()->role === 'ADMIN' ? 'Histórico e gestão global de reservas' : 'Gerir as suas reservas de estacionamento' }}
            </p>
        </div>
        <a href="{{ url('/reservas/criar') }}" 
        class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nova Reserva
        </a>
    </div>
    
    <!-- Tabs -->
    <div class="bg-white rounded-xl shadow-lg mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button onclick="showTab('ativas')" 
                        id="tab-ativas"
                        class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-blue-600 text-blue-600">
                    Ativas ({{ $reservasAtivas->count() ?? 0 }})
                </button>
                <button onclick="showTab('historico')" 
                        id="tab-historico"
                        class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700">
                    Histórico ({{ $reservasHistorico->count() ?? 0 }})
                </button>
            </nav>
        </div>
    </div>

    @if($isAdmin)
        <div class="bg-white rounded-xl shadow-lg p-4 mb-6">
            <form method="GET" action="{{ url('/reservas') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
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
                    <a href="{{ url('/reservas') }}"
                       class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        Limpar
                    </a>
                </div>
            </form>
        </div>
    @endif
    
    <!-- Ativas Tab -->
    <div id="content-ativas" class="reservas-tab-content">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ request()->fullUrlWithQuery(['sort_ativas' => 'lugar', 'direction_ativas' => $nextDirectionAtivas('lugar'), 'tab' => 'ativas']) }}" class="hover:text-gray-700">
                                Lugar Reservado {{ $sortIconAtivas('lugar') }}
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ request()->fullUrlWithQuery(['sort_ativas' => 'data', 'direction_ativas' => $nextDirectionAtivas('data'), 'tab' => 'ativas']) }}" class="hover:text-gray-700">
                                Data da Reserva {{ $sortIconAtivas('data') }}
                            </a>
                        </th>
                        @if($isAdmin)
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort_ativas' => 'utilizador', 'direction_ativas' => $nextDirectionAtivas('utilizador'), 'tab' => 'ativas']) }}" class="hover:text-gray-700">
                                    Utilizador {{ $sortIconAtivas('utilizador') }}
                                </a>
                            </th>
                        @endif
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ request()->fullUrlWithQuery(['sort_ativas' => 'estado', 'direction_ativas' => $nextDirectionAtivas('estado'), 'tab' => 'ativas']) }}" class="hover:text-gray-700">
                                Estado {{ $sortIconAtivas('estado') }}
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ações
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($reservasAtivas ?? [] as $reserva)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="bg-blue-100 text-blue-700 px-3 py-1 rounded font-bold inline-block">
                                        Lugar {{ $reserva->lugar->numero ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($reserva->data)->format('d/m/Y') }}
                                    ({{ \Carbon\Carbon::parse($reserva->data)->locale('pt')->isoFormat('dddd') }})
                                </td>
                                @if($isAdmin)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $reserva->utilizador->nome ?? 'N/A' }}
                                    </td>
                                @endif
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($reserva->estado === 'ATIVA')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            ✓ Reserva Ativa
                                        </span>
                                    @elseif($reserva->estado === 'PRESENTE')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            ✓ Presente
                                        </span>
                                    @endif

                                    @if(\Carbon\Carbon::parse($reserva->data)->isToday())
                                        <span class="ml-2 px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            🔥 Hoje
                                        </span>
                                    @endif

                                    @if(($reserva->modo_reserva ?? 'COLAB') === 'ADMIN')
                                        <span class="ml-2 px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-rose-100 text-rose-800">
                                            Reserva administrativa execional
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ url('/reservas/' . $reserva->id) }}" class="text-blue-600 hover:text-blue-900">
                                            Ver detalhes
                                        </a>

                                        @if($isAdmin)
                                            <a href="{{ route('admin.reservas.edit', $reserva->id) }}"
                                               class="text-indigo-600 hover:text-indigo-800">
                                                Editar
                                            </a>
                                            <form action="{{ route('admin.reservas.cancel', $reserva->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        onclick="return confirm('Tem certeza que deseja cancelar esta reserva?')"
                                                        class="text-red-600 hover:text-red-800">
                                                    Cancelar
                                                </button>
                                            </form>
                                        @elseif($reserva->estado === 'ATIVA' && \Carbon\Carbon::parse($reserva->data)->isFuture())
                                            <form action="{{ url('/reservas/' . $reserva->id . '/cancelar') }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        onclick="return confirm('Tem certeza que deseja cancelar esta reserva?')"
                                                        class="text-red-600 hover:text-red-800">
                                                    Cancelar
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isAdmin ? '5' : '4' }}" class="px-6 py-8 text-center text-gray-500">
                                Sem reservas ativas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Histórico Tab -->
    <div id="content-historico" class="reservas-tab-content hidden">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ request()->fullUrlWithQuery(['sort_historico' => 'lugar', 'direction_historico' => $nextDirectionHistorico('lugar'), 'tab' => 'historico', 'page' => 1]) }}" class="hover:text-gray-700">
                                Lugar Reservado {{ $sortIconHistorico('lugar') }}
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ request()->fullUrlWithQuery(['sort_historico' => 'data', 'direction_historico' => $nextDirectionHistorico('data'), 'tab' => 'historico', 'page' => 1]) }}" class="hover:text-gray-700">
                                Data da Reserva {{ $sortIconHistorico('data') }}
                            </a>
                        </th>
                        @if($isAdmin)
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort_historico' => 'utilizador', 'direction_historico' => $nextDirectionHistorico('utilizador'), 'tab' => 'historico', 'page' => 1]) }}" class="hover:text-gray-700">
                                    Utilizador {{ $sortIconHistorico('utilizador') }}
                                </a>
                            </th>
                        @endif
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ request()->fullUrlWithQuery(['sort_historico' => 'estado', 'direction_historico' => $nextDirectionHistorico('estado'), 'tab' => 'historico', 'page' => 1]) }}" class="hover:text-gray-700">
                                Estado {{ $sortIconHistorico('estado') }}
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ações
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($reservasHistorico ?? [] as $reserva)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="bg-gray-100 text-gray-600 px-3 py-1 rounded font-bold">
                                            {{ $reserva->lugar->numero ?? 'N/A' }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($reserva->data)->format('d/m/Y') }}
                                </td>
                                @if($isAdmin)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $reserva->utilizador->nome ?? 'N/A' }}
                                    </td>
                                @endif
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($reserva->estado === 'PRESENTE')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            ✓ Presente
                                        </span>
                                    @elseif($reserva->estado === 'NAO_COMPARECEU')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            ✗ Não compareceu
                                        </span>
                                    @elseif($reserva->estado === 'CANCELADA')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            ⊘ Cancelada
                                        </span>
                                    @endif

                                    @if(($reserva->modo_reserva ?? 'COLAB') === 'ADMIN')
                                        <span class="ml-2 px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-rose-100 text-rose-800">
                                            Reserva administrativa execional
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ url('/reservas/' . $reserva->id) }}"
                                           class="text-blue-600 hover:text-blue-900">
                                            Ver detalhes
                                        </a>
                                        @if($isAdmin)
                                            <a href="{{ route('admin.reservas.edit', $reserva->id) }}"
                                               class="text-indigo-600 hover:text-indigo-800">
                                                Editar
                                            </a>
                                            @if($reserva->estado === 'ATIVA')
                                                <form action="{{ route('admin.reservas.cancel', $reserva->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            onclick="return confirm('Tem certeza que deseja cancelar esta reserva?')"
                                                            class="text-red-600 hover:text-red-800">
                                                        Cancelar
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isAdmin ? '5' : '4' }}" class="px-6 py-8 text-center text-gray-500">
                                Sem histórico de reservas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if(isset($reservasHistorico) && method_exists($reservasHistorico, 'hasPages') && $reservasHistorico->hasPages())
            <div class="mt-6">
                {{ $reservasHistorico->links() }}
            </div>
        @endif
    </div>
    
</div>

<script>
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.reservas-tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Remove active style from all buttons
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('border-blue-600', 'text-blue-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab
    document.getElementById('content-' + tabName).classList.remove('hidden');
    
    // Add active style to selected button
    document.getElementById('tab-' + tabName).classList.remove('border-transparent', 'text-gray-500');
    document.getElementById('tab-' + tabName).classList.add('border-blue-600', 'text-blue-600');
}

document.addEventListener('DOMContentLoaded', function () {
    const initialTab = @json($activeTab);
    if (initialTab === 'historico') {
        showTab('historico');
    } else {
        showTab('ativas');
    }
});
</script>
@endsection
