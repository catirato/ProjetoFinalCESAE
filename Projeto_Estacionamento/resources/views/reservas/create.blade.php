@extends('layouts.master')

@section('title', 'Nova Reserva')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    @php
        $isAdmin = auth('utilizador')->user()->role === 'ADMIN';
        $modoReservaOld = old('modo_reserva', '');
    @endphp

    <!-- Header -->
    <div class="mb-8">
        <a href="{{ url('/dashboard') }}" class="text-blue-600 hover:text-blue-800 flex items-center mb-4">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Voltar ao Painel de Controlo
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Nova Reserva</h1>
        <p class="text-gray-600 mt-1">Reserve uma vaga de estacionamento</p>
    </div>

    <!-- User Points Card -->
    <div id="points-card" class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mb-6">
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

        @if($isAdmin)
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">1. Tipo de Reserva</h2>
                <p class="text-sm text-gray-600 mb-4">Escolha como pretende criar esta reserva.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="border rounded-lg p-4 cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="modo_reserva" value="COLAB" class="mr-2" {{ $modoReservaOld === 'COLAB' ? 'checked' : '' }}>
                        <span class="font-semibold text-gray-900">Como Colaborador</span>
                        <p class="text-sm text-gray-600 mt-1">Aplicam-se as regras normais (pontos, datas e lugares).</p>
                    </label>
                    <label class="border rounded-lg p-4 cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="modo_reserva" value="ADMIN" class="mr-2" {{ $modoReservaOld === 'ADMIN' ? 'checked' : '' }}>
                        <span class="font-semibold text-gray-900">Como Administrador</span>
                        <p class="text-sm text-gray-600 mt-1">Permite reserva excecional com justificação obrigatória.</p>
                    </label>
                </div>
            </div>

            <div id="justificacao-section" class="bg-white rounded-xl shadow-lg p-6 {{ $modoReservaOld === 'ADMIN' ? '' : 'hidden' }}">
                <h2 class="text-xl font-bold text-gray-900 mb-4">2. Justificação Administrativa</h2>
                <div class="space-y-4">
                    <div>
                        <label for="justificacao_tipo" class="block text-sm font-medium text-gray-700 mb-2">Motivo</label>
                        <select id="justificacao_tipo"
                                name="justificacao_tipo"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Selecione um motivo</option>
                            <option value="EVENTO" {{ old('justificacao_tipo') === 'EVENTO' ? 'selected' : '' }}>Evento</option>
                            <option value="OBRAS" {{ old('justificacao_tipo') === 'OBRAS' ? 'selected' : '' }}>Obras</option>
                            <option value="MOBILIDADE_REDUZIDA" {{ old('justificacao_tipo') === 'MOBILIDADE_REDUZIDA' ? 'selected' : '' }}>Mobilidade reduzida</option>
                            <option value="OUTRO" {{ old('justificacao_tipo') === 'OUTRO' ? 'selected' : '' }}>Outro motivo</option>
                        </select>
                    </div>
                    <div>
                        <label for="justificacao_detalhe" class="block text-sm font-medium text-gray-700 mb-2">Detalhes</label>
                        <textarea id="justificacao_detalhe"
                                  name="justificacao_detalhe"
                                  rows="3"
                                  maxlength="500"
                                  placeholder="Opcional (obrigatório se escolher 'Outro motivo')"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('justificacao_detalhe') }}</textarea>
                    </div>
                </div>
            </div>
        @endif

        <!-- Data Selection -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">{{ $isAdmin ? '3' : '1' }}. Escolha a Data</h2>

            <div>
                <label for="data" class="block text-sm font-medium text-gray-700 mb-2">
                    Data da Reserva
                </label>
                <input type="date"
                       id="data"
                       name="data"
                       required
                       value="{{ old('data') }}"
                       min="{{ $modoReservaOld === 'ADMIN' ? '' : date('Y-m-d') }}"
                       max="{{ $modoReservaOld === 'ADMIN' ? '' : date('Y-m-d', strtotime('+30 days')) }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       onchange="checkAvailability()">
                <p id="weekend-error" class="text-sm text-red-600 mt-2 hidden">
                    Sábado e domingo estão indisponíveis para reserva.
                </p>
            </div>
        </div>

        <!-- Available Places -->
        <div class="bg-white rounded-xl shadow-lg p-6" id="places-section" style="display: none;">
            <h2 class="text-xl font-bold text-gray-900 mb-4">{{ $isAdmin ? '4' : '2' }}. Escolha o Lugar</h2>

            <div id="places-loading" class="text-center py-8">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                <p class="text-gray-600 mt-4">A carregar lugares disponíveis...</p>
            </div>

            <div id="places-content" style="display: none;">
                <!-- Lugares disponíveis serão carregados aqui via JavaScript -->
            </div>
        </div>

        <!-- Cost Information -->
        <div id="cost-info" class="bg-blue-50 border border-blue-200 rounded-xl p-6">
            <div class="flex items-start">
                <div class="text-3xl mr-4">💰</div>
                <div>
                    <h3 class="font-bold text-blue-900 mb-2">Custo da Reserva</h3>
                    <p class="text-blue-800 mb-2">
                        Esta reserva custará <span class="font-bold">3 pontos</span>
                    </p>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• Ao desmarcar: perde 2 pontos de penalização (com devolução dos 3 da reserva)</li>
                        <li>• Se não comparecer: penalização final de 10 pontos (com devolução dos 3 da reserva)</li>
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
let selectedPlaces = new Set();
const isAdmin = @json($isAdmin);
const oldLugarId = @json(old('lugar_id'));
const oldLugarIds = @json(old('lugar_ids', []));

function isWeekend(dateString) {
    const selectedDate = new Date(dateString + 'T00:00:00');
    const dayOfWeek = selectedDate.getDay();
    return dayOfWeek === 0 || dayOfWeek === 6;
}

function getModoReserva() {
    if (!isAdmin) return 'COLAB';
    const selected = document.querySelector('input[name="modo_reserva"]:checked');
    return selected ? selected.value : '';
}

function updateFormByMode() {
    const modo = getModoReserva();
    const dataInput = document.getElementById('data');
    const justificacaoSection = document.getElementById('justificacao-section');
    const costInfo = document.getElementById('cost-info');
    const pointsCard = document.getElementById('points-card');

    if (modo === 'ADMIN') {
        dataInput.removeAttribute('min');
        dataInput.removeAttribute('max');
        if (justificacaoSection) justificacaoSection.classList.remove('hidden');
        if (costInfo) costInfo.classList.add('hidden');
        if (pointsCard) pointsCard.classList.add('hidden');
    } else {
        dataInput.setAttribute('min', "{{ date('Y-m-d') }}");
        dataInput.setAttribute('max', "{{ date('Y-m-d', strtotime('+30 days')) }}");
        if (justificacaoSection) justificacaoSection.classList.add('hidden');
        if (costInfo) costInfo.classList.remove('hidden');
        if (pointsCard) pointsCard.classList.remove('hidden');
    }

    checkAvailability();
}

function checkAvailability() {
    const data = document.getElementById('data').value;
    const weekendError = document.getElementById('weekend-error');
    const submitButton = document.getElementById('submit-button');
    const placesSection = document.getElementById('places-section');
    const placesLoading = document.getElementById('places-loading');
    const placesContent = document.getElementById('places-content');
    const modo = getModoReserva();

    if (!data) {
        weekendError.classList.add('hidden');
        return;
    }

    if (isAdmin && !modo) {
        weekendError.classList.add('hidden');
        placesSection.style.display = 'none';
        return;
    }

    selectedPlace = null;
    selectedPlaces = new Set();
    submitButton.disabled = true;

    if (isWeekend(data)) {
        weekendError.classList.remove('hidden');
        placesSection.style.display = 'none';
        return;
    }

    weekendError.classList.add('hidden');

    placesSection.style.display = 'block';
    placesLoading.style.display = 'block';
    placesContent.style.display = 'none';

    fetch(`/api/lugares/disponiveis?data=${encodeURIComponent(data)}&modo=${encodeURIComponent(modo)}&modo_reserva=${encodeURIComponent(modo)}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Falha ao obter disponibilidade.');
            }
            return response.json();
        })
        .then(payload => {
            const bloqueado = payload && payload.bloqueado === true;
            if (bloqueado) {
                placesLoading.style.display = 'none';
                placesContent.style.display = 'block';
                placesContent.innerHTML = `
                    <div class="text-center py-8">
                        <div class="text-6xl mb-4">🔒</div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Data bloqueada para nova reserva</h3>
                        <p class="text-gray-600">${payload.mensagem ?? 'Já existe uma reserva para este dia.'}</p>
                    </div>
                `;
                submitButton.disabled = true;
                return;
            }

            const places = Array.isArray(payload) ? payload : (payload?.lugares ?? []);
            loadPlaces(places);
        })
        .catch(() => {
            placesLoading.style.display = 'none';
            placesContent.style.display = 'block';
            placesContent.innerHTML = `
                <div class="alert alert-error">
                    <span>Não foi possível carregar os lugares disponíveis. Tente novamente.</span>
                </div>
            `;
        });
}

function loadPlaces(places) {
    const modo = getModoReserva();
    document.getElementById('places-loading').style.display = 'none';
    document.getElementById('places-content').style.display = 'block';

    if (places.length === 0) {
        document.getElementById('places-content').innerHTML = `
            <div class="text-center py-8">
                <div class="text-6xl mb-4">⚠️</div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Sem lugares configurados</h3>
                <p class="text-gray-600">Não existem lugares ativos no sistema. Contacte um administrador.</p>
            </div>
        `;
        return;
    }

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
                        onclick="selectPlace(${place.id})"
                        id="place-${place.id}"
                        class="place-button p-6 border-2 border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition text-center">
                    <div class="text-3xl font-bold text-gray-700">🅿️</div>
                    <div class="text-2xl font-bold text-gray-900 mt-2">Lugar ${place.numero}</div>
                    <div class="text-sm text-green-600 font-medium mt-1">✓ Disponível</div>
                </button>
            `;
        } else {
            const isFixo = place.motivo === 'fixo';
            const reservadoAdmin = !isFixo && place.ocupado_por_admin;
            html += `
                <div class="p-6 border-2 border-gray-200 rounded-lg bg-gray-100 text-center opacity-50 cursor-not-allowed">
                    <div class="text-3xl font-bold text-gray-400">🅿️</div>
                    <div class="text-2xl font-bold text-gray-500 mt-2">Lugar ${place.numero}</div>
                    <div class="text-sm text-red-600 font-medium mt-1">
                        ${isFixo ? '✗ Indisponível (reservado permanentemente)' : '✗ Ocupado'}
                    </div>
                    ${reservadoAdmin ? '<div class="text-xs text-rose-700 font-medium mt-1">Reserva administrativa execional</div>' : ''}
                </div>
            `;
        }
    });

    html += '</div>';
    html += '<div id="selected-places-inputs"></div>';
    html += '<input type="hidden" name="lugar_id" id="lugar_id">';
    if (modo === 'ADMIN') {
        html += '<p class="text-sm text-gray-600 mt-4">Pode selecionar vários lugares em simultâneo.</p>';
    }

    document.getElementById('places-content').innerHTML = html;

    if (modo === 'ADMIN' && Array.isArray(oldLugarIds) && oldLugarIds.length > 0) {
        oldLugarIds.forEach((id) => {
            const btn = document.getElementById('place-' + id);
            if (btn) {
                selectPlace(Number(id));
            }
        });
    } else if (oldLugarId) {
        const oldButton = document.getElementById('place-' + oldLugarId);
        if (oldButton) {
            selectPlace(Number(oldLugarId));
        }
    }
}

function selectPlace(id) {
    const modo = getModoReserva();
    const hiddenSingle = document.getElementById('lugar_id');
    const hiddenMultiple = document.getElementById('selected-places-inputs');

    if (!hiddenSingle || !hiddenMultiple) return;

    if (modo === 'ADMIN') {
        const clickedButton = document.getElementById('place-' + id);
        if (!clickedButton) return;

        if (selectedPlaces.has(id)) {
            selectedPlaces.delete(id);
            clickedButton.classList.remove('border-blue-600', 'bg-blue-50');
            clickedButton.classList.add('border-gray-300');
        } else {
            selectedPlaces.add(id);
            clickedButton.classList.remove('border-gray-300');
            clickedButton.classList.add('border-blue-600', 'bg-blue-50');
        }

        hiddenSingle.value = '';
        hiddenMultiple.innerHTML = '';
        Array.from(selectedPlaces).forEach((placeId) => {
            hiddenMultiple.insertAdjacentHTML(
                'beforeend',
                `<input type="hidden" name="lugar_ids[]" value="${placeId}">`
            );
        });

        document.getElementById('submit-button').disabled = selectedPlaces.size === 0;
        return;
    }

    selectedPlace = id;
    selectedPlaces = new Set([id]);

    // Remove selection from all places
    document.querySelectorAll('.place-button').forEach(btn => {
        btn.classList.remove('border-blue-600', 'bg-blue-50');
        btn.classList.add('border-gray-300');
    });

    // Add selection to clicked place
    const selectedButton = document.getElementById('place-' + id);
    if (selectedButton) {
        selectedButton.classList.remove('border-gray-300');
        selectedButton.classList.add('border-blue-600', 'bg-blue-50');
    }

    hiddenMultiple.innerHTML = '';
    hiddenSingle.value = id;
    document.getElementById('submit-button').disabled = false;
}

document.addEventListener('DOMContentLoaded', function () {
    if (isAdmin) {
        document.querySelectorAll('input[name="modo_reserva"]').forEach(input => {
            input.addEventListener('change', updateFormByMode);
        });
    }

    updateFormByMode();
});
</script>
@endsection
