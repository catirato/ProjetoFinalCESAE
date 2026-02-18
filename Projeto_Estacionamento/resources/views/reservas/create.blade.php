@extends('layouts.master')

@section('title', 'Nova Reserva')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ url('/reservas') }}" class="text-blue-600 hover:text-blue-800 flex items-center mb-4">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Voltar às reservas
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Nova Reserva</h1>
        <p class="text-gray-600 mt-1">Reserve uma vaga de estacionamento</p>
    </div>
    
    <!-- User Points Card -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="text-4xl mr-4">⭐</div>
                <div>
                    <p class="text-sm text-yellow-800 font-medium">Seus Pontos Disponíveis</p>
                    <p class="text-3xl font-bold text-yellow-900">{{ auth('utilizador')->user()->pontos }}</p>
                </div>
            </div>
            <a href="{{ url('/pontos') }}" class="text-yellow-700 hover:text-yellow-900 text-sm font-medium">
                Ver histórico →
            </a>
        </div>
    </div>
    
    <form action="{{ url('/reservas') }}" method="POST" class="space-y-6">
        @csrf
        
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <!-- Data Selection -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">1. Escolha a Data</h2>
            
            <div>
                <label for="data" class="block text-sm font-medium text-gray-700 mb-2">
                    Data da Reserva
                </label>
                <input type="date" 
                       id="data" 
                       name="data" 
                       required
                       min="{{ date('Y-m-d') }}"
                       max="{{ date('Y-m-d', strtotime('+30 days')) }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       onchange="checkAvailability()">
                <p class="mt-2 text-sm text-gray-500">
                    Pode reservar até 30 dias com antecedência
                </p>
            </div>
        </div>
        
        <!-- Available Places -->
        <div class="bg-white rounded-xl shadow-lg p-6" id="places-section" style="display: none;">
            <h2 class="text-xl font-bold text-gray-900 mb-4">2. Escolha o Lugar</h2>
            
            <div id="places-loading" class="text-center py-8">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                <p class="text-gray-600 mt-4">A carregar lugares disponíveis...</p>
            </div>
            
            <div id="places-content" style="display: none;">
                <!-- Lugares disponíveis serão carregados aqui via JavaScript -->
            </div>
        </div>
        
        <!-- Cost Information -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
            <div class="flex items-start">
                <div class="text-3xl mr-4">💰</div>
                <div>
                    <h3 class="font-bold text-blue-900 mb-2">Custo da Reserva</h3>
                    <p class="text-blue-800 mb-2">
                        Esta reserva custará <span class="font-bold">5 pontos</span>
                    </p>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• Se cancelar com 24h de antecedência: recupera 3 pontos</li>
                        <li>• Se não comparecer: perde 10 pontos adicionais</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Submit Button -->
        <div class="flex justify-end space-x-4">
            <a href="{{ url('/reservas') }}" 
               class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition">
                Cancelar
            </a>
            <button type="submit" 
                    id="submit-button"
                    disabled
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition disabled:bg-gray-300 disabled:cursor-not-allowed">
                Confirmar Reserva
            </button>
        </div>
    </form>
    
</div>

<script>
let selectedPlace = null;

function checkAvailability() {
    const data = document.getElementById('data').value;
    if (!data) return;
    
    document.getElementById('places-section').style.display = 'block';
    document.getElementById('places-loading').style.display = 'block';
    document.getElementById('places-content').style.display = 'none';
    
    // Simulate API call - você vai substituir por uma chamada AJAX real
    setTimeout(() => {
        loadPlaces([
            { id: 1, numero: 1, disponivel: true },
            { id: 2, numero: 2, disponivel: false },
            { id: 3, numero: 3, disponivel: true },
            { id: 4, numero: 4, disponivel: true },
            { id: 5, numero: 5, disponivel: false },
            { id: 6, numero: 6, disponivel: true },
            { id: 7, numero: 7, disponivel: true },
        ]);
    }, 1000);
}

function loadPlaces(places) {
    document.getElementById('places-loading').style.display = 'none';
    document.getElementById('places-content').style.display = 'block';
    
    const availablePlaces = places.filter(p => p.disponivel);
    
    if (availablePlaces.length === 0) {
        document.getElementById('places-content').innerHTML = `
            <div class="text-center py-8">
                <div class="text-6xl mb-4">😔</div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Sem vagas disponíveis</h3>
                <p class="text-gray-600 mb-4">Todos os lugares estão reservados para esta data</p>
                <a href="${window.location.origin}/lista-espera" 
                   class="inline-block bg-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-purple-700">
                    Entrar na Lista de Espera
                </a>
            </div>
        `;
        return;
    }
    
    let html = '<div class="grid grid-cols-2 md:grid-cols-4 gap-4">';
    
    places.forEach(place => {
        if (place.disponivel) {
            html += `
                <button type="button" 
                        onclick="selectPlace(${place.id}, ${place.numero})"
                        id="place-${place.id}"
                        class="place-button p-6 border-2 border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition text-center">
                    <div class="text-3xl font-bold text-gray-700">🅿️</div>
                    <div class="text-2xl font-bold text-gray-900 mt-2">Lugar ${place.numero}</div>
                    <div class="text-sm text-green-600 font-medium mt-1">✓ Disponível</div>
                </button>
            `;
        } else {
            html += `
                <div class="p-6 border-2 border-gray-200 rounded-lg bg-gray-100 text-center opacity-50 cursor-not-allowed">
                    <div class="text-3xl font-bold text-gray-400">🅿️</div>
                    <div class="text-2xl font-bold text-gray-500 mt-2">Lugar ${place.numero}</div>
                    <div class="text-sm text-red-600 font-medium mt-1">✗ Ocupado</div>
                </div>
            `;
        }
    });
    
    html += '</div>';
    html += '<input type="hidden" name="lugar_id" id="lugar_id" required>';
    
    document.getElementById('places-content').innerHTML = html;
}

function selectPlace(id, numero) {
    selectedPlace = id;
    
    // Remove selection from all places
    document.querySelectorAll('.place-button').forEach(btn => {
        btn.classList.remove('border-blue-600', 'bg-blue-50');
        btn.classList.add('border-gray-300');
    });
    
    // Add selection to clicked place
    document.getElementById('place-' + id).classList.remove('border-gray-300');
    document.getElementById('place-' + id).classList.add('border-blue-600', 'bg-blue-50');
    
    // Set hidden input value
    document.getElementById('lugar_id').value = id;
    
    // Enable submit button
    document.getElementById('submit-button').disabled = false;
}
</script>
@endsection