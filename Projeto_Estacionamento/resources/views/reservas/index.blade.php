@extends('layouts.master')

@section('title', 'Minhas Reservas')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- Header -->
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Minhas Reservas</h1>
            <p class="text-gray-600 mt-1">Gerir as suas reservas de estacionamento</p>
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
    
    <!-- Ativas Tab -->
    <div id="content-ativas" class="tab-content">
        @if(isset($reservasAtivas) && $reservasAtivas->count() > 0)
            <div class="grid gap-6">
                @foreach($reservasAtivas as $reserva)
                    <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                            <div class="flex items-start space-x-4 mb-4 md:mb-0">
                                <!-- Place Icon -->
                                <div class="bg-blue-100 text-blue-600 p-4 rounded-lg text-2xl font-bold">
                                    {{ $reserva->lugar->numero }}
                                </div>
                                
                                <!-- Info -->
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">
                                        Lugar {{ $reserva->lugar->numero }}
                                    </h3>
                                    <p class="text-gray-600 mt-1">
                                        📅 {{ \Carbon\Carbon::parse($reserva->data)->format('d/m/Y') }}
                                        ({{ \Carbon\Carbon::parse($reserva->data)->locale('pt')->isoFormat('dddd') }})
                                    </p>
                                    
                                    <!-- Status Badge -->
                                    @if($reserva->estado === 'ATIVA')
                                        <span class="inline-block mt-2 px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                                            ✓ Reserva Ativa
                                        </span>
                                    @elseif($reserva->estado === 'PRESENTE')
                                        <span class="inline-block mt-2 px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">
                                            ✓ Presente
                                        </span>
                                    @endif
                                    
                                    @if(\Carbon\Carbon::parse($reserva->data)->isToday())
                                        <span class="inline-block mt-2 ml-2 px-3 py-1 bg-yellow-100 text-yellow-800 text-sm font-medium rounded-full">
                                            🔥 Hoje!
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex space-x-2">
                                <a href="{{ url('/reservas/' . $reserva->id) }}" 
                                   class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                                    Ver Detalhes
                                </a>
                                
                                @if($reserva->estado === 'ATIVA' && \Carbon\Carbon::parse($reserva->data)->isFuture())
                                    <form action="{{ url('/reservas/' . $reserva->id . '/cancelar') }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                onclick="return confirm('Tem certeza que deseja cancelar esta reserva?')"
                                                class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition">
                                            Cancelar
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                <div class="text-6xl mb-4">🅿️</div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Sem reservas ativas</h3>
                <p class="text-gray-600 mb-6">Ainda não tem nenhuma reserva ativa</p>
                <a href="{{ url('/reservas/criar') }}" 
                   class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                    Criar Primeira Reserva
                </a>
            </div>
        @endif
    </div>
    
    <!-- Histórico Tab -->
    <div id="content-historico" class="tab-content hidden">
        @if(isset($reservasHistorico) && $reservasHistorico->count() > 0)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Lugar
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Data
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($reservasHistorico as $reserva)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="bg-gray-100 text-gray-600 px-3 py-1 rounded font-bold">
                                            {{ $reserva->lugar->numero }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($reserva->data)->format('d/m/Y') }}
                                </td>
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
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ url('/reservas/' . $reserva->id) }}" 
                                       class="text-blue-600 hover:text-blue-900">
                                        Ver detalhes
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($reservasHistorico->hasPages())
                <div class="mt-6">
                    {{ $reservasHistorico->links() }}
                </div>
            @endif
        @else
            <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                <div class="text-6xl mb-4">📋</div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Sem histórico</h3>
                <p class="text-gray-600">Ainda não tem reservas anteriores</p>
            </div>
        @endif
    </div>
    
</div>

<script>
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
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
</script>
@endsection
