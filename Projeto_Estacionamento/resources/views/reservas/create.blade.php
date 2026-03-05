@extends('layouts.master')

@section('title', 'Nova Reserva')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    @php
        $isAdmin = auth('utilizador')->user()->role === 'ADMIN';
        $modoReservaOld = old('modo_reserva', '');
        $tipoPeriodoOld = old('tipo_periodo', 'UNICO');
        $colabMinDate = \Carbon\Carbon::today()->format('Y-m-d');
        $colabMaxDate = \Carbon\Carbon::today()->addWeek()->endOfWeek(\Carbon\Carbon::FRIDAY)->format('Y-m-d');
        $periodosOld = old('periodos');
        if (!is_array($periodosOld) || empty($periodosOld)) {
            $periodosOld = [[
                'data_inicio' => old('data_inicio', ''),
                'data_fim' => old('data_fim', ''),
            ]];
        }
    @endphp

    <!-- Header -->
    <div class="mb-8">
        <a href="{{ url('/dashboard') }}" class="text-blue-600 hover:text-blue-800 flex items-center mb-4">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Voltar ao Painel de Controlo
        </a>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Nova Reserva</h1>
        <p class="text-gray-600 mt-1">Reserve uma vaga de estacionamento</p>

    </div>

    <!-- User Points Card -->
    <div id="points-card" class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center">
                <div class="text-4xl mr-3 sm:mr-4">⭐</div>
                <div>
                    <p class="text-sm text-yellow-800 font-medium">Seus Pontos Disponíveis</p>
                    <p class="text-2xl sm:text-3xl font-bold text-yellow-900">{{ auth('utilizador')->user()->pontos }}</p>
                </div>
            </div>
            <a href="{{ url('/pontos') }}" class="text-yellow-700 hover:text-yellow-900 text-sm font-medium self-start sm:self-auto">
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

            <div id="periodo-section" class="bg-white rounded-xl shadow-lg p-6 {{ $modoReservaOld === 'ADMIN' ? '' : 'hidden' }}">
                <h2 class="text-xl font-bold text-gray-900 mb-4">3. Período da Reserva</h2>
                <p class="text-sm text-gray-600 mb-4">Pretende reservar para um único dia ou para vários dias?</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="border rounded-lg p-4 cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="tipo_periodo" value="UNICO" class="mr-2" {{ $tipoPeriodoOld === 'UNICO' ? 'checked' : '' }}>
                        <span class="font-semibold text-gray-900">1 dia</span>
                    </label>
                    <label class="border rounded-lg p-4 cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="tipo_periodo" value="INTERVALO" class="mr-2" {{ $tipoPeriodoOld === 'INTERVALO' ? 'checked' : '' }}>
                        <span class="font-semibold text-gray-900">Vários dias</span>
                    </label>
                </div>
            </div>
        @endif

        <!-- Data Selection -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Escolha a Data</h2>

            <div id="single-date-fields">
                <label for="data" class="block text-sm font-medium text-gray-700 mb-2">
                    Data da Reserva
                </label>
                <input type="date"
                       id="data"
                       name="data"
                       value="{{ old('data') }}"
                       min="{{ $modoReservaOld === 'ADMIN' ? '' : $colabMinDate }}"
                       max="{{ $modoReservaOld === 'ADMIN' ? '' : $colabMaxDate }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       onchange="checkAvailability()">
            </div>

            @if($isAdmin)
                <div id="range-date-fields" class="{{ $tipoPeriodoOld === 'INTERVALO' && $modoReservaOld === 'ADMIN' ? '' : 'hidden' }}">
                    <div id="periodos-container" class="space-y-4">
                        @foreach($periodosOld as $index => $periodoOld)
                            <div class="periodo-row grid grid-cols-1 md:grid-cols-11 gap-3 items-end" data-index="{{ $index }}">
                                <div class="md:col-span-5">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Data de Início
                                    </label>
                                    <input type="date"
                                           name="periodos[{{ $index }}][data_inicio]"
                                           value="{{ $periodoOld['data_inicio'] ?? '' }}"
                                           class="periodo-start-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           onchange="checkAvailability()">
                                </div>
                                <div class="md:col-span-5">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Data de Fim
                                    </label>
                                    <input type="date"
                                           name="periodos[{{ $index }}][data_fim]"
                                           value="{{ $periodoOld['data_fim'] ?? '' }}"
                                           class="periodo-end-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           onchange="checkAvailability()">
                                </div>
                                <div class="md:col-span-1">
                                    <button type="button"
                                            onclick="removePeriodoRow(this)"
                                            class="remove-periodo-btn w-full h-[50px] border border-red-300 text-red-700 rounded-lg hover:bg-red-50 {{ count($periodosOld) > 1 ? '' : 'hidden' }}">
                                        -
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-3">
                        <button type="button"
                                id="add-periodo-btn"
                                onclick="addPeriodoRow()"
                                class="inline-flex items-center justify-center w-10 h-10 border border-blue-300 text-blue-700 rounded-full hover:bg-blue-50 text-2xl leading-none">
                            +
                        </button>
                        <span class="text-sm text-gray-600 ml-2">Adicionar outro período</span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mt-2">
                            Só serão considerados dias úteis (segunda a sexta).
                        </p>
                    </div>
                    <div>
                        <p id="periodo-error" class="text-sm text-red-600 mt-2 hidden">
                            Complete início e fim em todos os períodos.
                        </p>
                    </div>
                </div>
            @endif

            <p id="weekend-error" class="text-sm text-red-600 mt-2 hidden">
                Sábado e domingo estão indisponíveis para reserva.
            </p>
        </div>

        <!-- Available Places -->
        <div class="bg-white rounded-xl shadow-lg p-6" id="places-section" style="display: none;">
            <div class="mb-4 flex items-center justify-between gap-3">
                <h2 class="text-xl font-bold text-gray-900">{{ $isAdmin ? '4' : '2' }}. Escolha o Lugar</h2>
                @if($isAdmin)
                    <div id="admin-management-button-wrapper" class="{{ $modoReservaOld === 'ADMIN' ? '' : 'hidden' }}">
                        <button type="button"
                                onclick="openAdminReservasModal()"
                                class="btn btn-outline btn-sm whitespace-nowrap">
                            Gestão de Reservas
                        </button>
                    </div>
                @endif
            </div>

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
            <div class="flex items-start gap-3">
                <div class="text-3xl shrink-0">💰</div>
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
        <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
            <a href="{{ url('/reservas') }}"
               class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition text-center w-full sm:w-auto">
                Cancelar
            </a>
            <button type="submit"
                    id="submit-button"
                    disabled
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition disabled:bg-gray-300 disabled:cursor-not-allowed w-full sm:w-auto">
                Confirmar Reserva
            </button>
        </div>
    </form>

</div>

@if($isAdmin)
    <div id="admin-reservas-modal"
         class="fixed inset-0 z-50 hidden items-center justify-center p-0"
         aria-hidden="true">
        <div class="absolute inset-0 bg-black/50" onclick="closeAdminReservasModal()"></div>
        <div class="relative bg-white rounded-xl shadow-xl flex flex-col overflow-hidden"
             style="width: 70vw; height: 70vh; border: 12px solid #1f2937; box-sizing: border-box;">
            <div class="flex items-center justify-between px-4 py-3 border-b">
                <h2 class="text-lg font-semibold text-gray-900">Gestão de Reservas</h2>
                <div class="flex items-center gap-2">
                    <a href="{{ url('/reservas') }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="btn btn-ghost btn-sm">
                        Abrir em nova aba
                    </a>
                    <button type="button" class="btn btn-primary btn-sm" onclick="closeAdminReservasModal()">
                        Fechar
                    </button>
                </div>
            </div>
            <iframe src="{{ url('/reservas') }}"
                    title="Gestão de Reservas"
                    class="w-full flex-1 border-0"></iframe>
        </div>
    </div>
@endif

<script>
let selectedPlace = null;
let selectedPlaces = new Set();
let selectedPlacesByDay = {};
let intervalDates = [];
let periodRowCounter = 0;
let pendingSelectionRestore = null;
const isAdmin = @json($isAdmin);
const oldLugarId = @json(old('lugar_id'));
const oldLugarIds = @json(old('lugar_ids', []));
const oldLugaresPorDia = @json(old('lugares_por_dia', []));

function openAdminReservasModal() {
    const modal = document.getElementById('admin-reservas-modal');
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('overflow-hidden');
}

function closeAdminReservasModal() {
    const modal = document.getElementById('admin-reservas-modal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    modal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('overflow-hidden');
    checkAvailability(true);
}

document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        closeAdminReservasModal();
    }
});

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

function getTipoPeriodo() {
    if (!isAdmin) return 'UNICO';
    const selected = document.querySelector('input[name="tipo_periodo"]:checked');
    return selected ? selected.value : 'UNICO';
}

function updateFormByMode() {
    const modo = getModoReserva();
    const tipoPeriodo = getTipoPeriodo();
    const dataInput = document.getElementById('data');
    const singleDateFields = document.getElementById('single-date-fields');
    const rangeDateFields = document.getElementById('range-date-fields');
    const periodoSection = document.getElementById('periodo-section');
    const justificacaoSection = document.getElementById('justificacao-section');
    const costInfo = document.getElementById('cost-info');
    const pointsCard = document.getElementById('points-card');
    const adminManagementButtonWrapper = document.getElementById('admin-management-button-wrapper');
    const tipoPeriodoUnico = document.querySelector('input[name="tipo_periodo"][value="UNICO"]');

    if (modo === 'ADMIN') {
        if (adminManagementButtonWrapper) adminManagementButtonWrapper.classList.remove('hidden');
        if (periodoSection) periodoSection.classList.remove('hidden');
        if (singleDateFields) singleDateFields.classList.toggle('hidden', tipoPeriodo === 'INTERVALO');
        if (rangeDateFields) rangeDateFields.classList.toggle('hidden', tipoPeriodo !== 'INTERVALO');

        dataInput.removeAttribute('min');
        dataInput.removeAttribute('max');
        document.querySelectorAll('.periodo-start-input, .periodo-end-input').forEach((input) => {
            input.removeAttribute('min');
            input.removeAttribute('max');
        });
        if (justificacaoSection) justificacaoSection.classList.remove('hidden');
        if (costInfo) costInfo.classList.add('hidden');
        if (pointsCard) pointsCard.classList.add('hidden');
    } else {
        if (adminManagementButtonWrapper) adminManagementButtonWrapper.classList.add('hidden');
        if (periodoSection) periodoSection.classList.add('hidden');
        if (tipoPeriodoUnico) tipoPeriodoUnico.checked = true;
        if (singleDateFields) singleDateFields.classList.remove('hidden');
        if (rangeDateFields) rangeDateFields.classList.add('hidden');

        dataInput.setAttribute('min', "{{ $colabMinDate }}");
        dataInput.setAttribute('max', "{{ $colabMaxDate }}");
        if (justificacaoSection) justificacaoSection.classList.add('hidden');
        if (costInfo) costInfo.classList.remove('hidden');
        if (pointsCard) pointsCard.classList.remove('hidden');
    }

    checkAvailability();
}

function buildSelectionSnapshot(isAdminInterval) {
    if (isAdminInterval) {
        const lugaresPorDia = {};
        Object.keys(selectedPlacesByDay).forEach((date) => {
            lugaresPorDia[date] = Array.from(selectedPlacesByDay[date]);
        });
        return { type: 'interval', lugaresPorDia };
    }

    return {
        type: 'single',
        selectedPlace: selectedPlace ? Number(selectedPlace) : null,
        selectedPlaces: Array.from(selectedPlaces),
    };
}

function checkAvailability(preserveSelection = false) {
    const data = document.getElementById('data').value;
    const intervalPeriods = getIntervalPeriods();
    const completePeriods = intervalPeriods.filter((periodo) => periodo.data_inicio && periodo.data_fim);
    const hasIncompletePeriods = intervalPeriods.some((periodo) => {
        const hasStart = Boolean(periodo.data_inicio);
        const hasEnd = Boolean(periodo.data_fim);
        return hasStart !== hasEnd;
    });
    const weekendError = document.getElementById('weekend-error');
    const periodoError = document.getElementById('periodo-error');
    const submitButton = document.getElementById('submit-button');
    const placesSection = document.getElementById('places-section');
    const placesLoading = document.getElementById('places-loading');
    const placesContent = document.getElementById('places-content');
    const modo = getModoReserva();
    const tipoPeriodo = getTipoPeriodo();
    const isAdminInterval = isAdmin && modo === 'ADMIN' && tipoPeriodo === 'INTERVALO';
    pendingSelectionRestore = preserveSelection ? buildSelectionSnapshot(isAdminInterval) : null;

    if (!isAdminInterval && !data) {
        weekendError.classList.add('hidden');
        if (periodoError) periodoError.classList.add('hidden');
        submitButton.disabled = true;
        placesSection.style.display = 'none';
        return;
    }

    if (isAdmin && !modo) {
        weekendError.classList.add('hidden');
        if (periodoError) periodoError.classList.add('hidden');
        placesSection.style.display = 'none';
        return;
    }

    selectedPlace = null;
    selectedPlaces = new Set();
    selectedPlacesByDay = {};
    intervalDates = [];
    submitButton.disabled = true;

    if (!isAdminInterval && isWeekend(data)) {
        weekendError.classList.remove('hidden');
        if (periodoError) periodoError.classList.add('hidden');
        placesSection.style.display = 'none';
        return;
    }

    if (isAdminInterval && completePeriods.length === 0) {
        weekendError.classList.add('hidden');
        if (periodoError) periodoError.classList.add('hidden');
        placesSection.style.display = 'none';
        return;
    }

    if (isAdminInterval && hasIncompletePeriods) {
        weekendError.classList.add('hidden');
        if (periodoError) periodoError.classList.remove('hidden');
        submitButton.disabled = true;
        placesSection.style.display = 'none';
        return;
    }

    weekendError.classList.add('hidden');
    if (periodoError) periodoError.classList.add('hidden');

    placesSection.style.display = 'block';
    placesLoading.style.display = 'block';
    placesContent.style.display = 'none';

    const query = new URLSearchParams();
    query.set('modo', modo);
    query.set('modo_reserva', modo);
    query.set('tipo_periodo', isAdminInterval ? 'INTERVALO' : 'UNICO');
    if (isAdminInterval) {
        completePeriods.forEach((periodo, index) => {
            query.set(`periodos[${index}][data_inicio]`, periodo.data_inicio);
            query.set(`periodos[${index}][data_fim]`, periodo.data_fim);
        });
    } else {
        query.set('data', data);
    }

    fetch(`/api/lugares/disponiveis?${query.toString()}`)
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

            if (isAdminInterval) {
                loadPlacesByDay(payload?.dias ?? []);
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

    let html = '<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">';

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

    if (pendingSelectionRestore && pendingSelectionRestore.type === 'single') {
        const idsToRestore = modo === 'ADMIN'
            ? pendingSelectionRestore.selectedPlaces
            : [pendingSelectionRestore.selectedPlace];

        idsToRestore
            .filter((id) => Number.isFinite(Number(id)))
            .forEach((id) => {
                const numericId = Number(id);
                if (document.getElementById('place-' + numericId)) {
                    selectPlace(numericId);
                }
            });

        pendingSelectionRestore = null;
        return;
    }

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

function loadPlacesByDay(days) {
    const placesContent = document.getElementById('places-content');
    document.getElementById('places-loading').style.display = 'none';
    placesContent.style.display = 'block';

    if (!Array.isArray(days) || days.length === 0) {
        intervalDates = [];
        placesContent.innerHTML = `
            <div class="text-center py-8">
                <div class="text-6xl mb-4">⚠️</div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Sem dias úteis no intervalo</h3>
                <p class="text-gray-600">Escolha um intervalo com pelo menos um dia útil.</p>
            </div>
        `;
        document.getElementById('submit-button').disabled = true;
        return;
    }
    intervalDates = days.map((day) => day.data);

    let html = '<div class="space-y-6">';
    days.forEach((day) => {
        const date = day.data;
        const safeDateId = date.replace(/[^0-9]/g, '');
        const places = Array.isArray(day.lugares) ? day.lugares : [];

        html += `
            <div class="border border-gray-200 rounded-xl p-4">
                <h3 class="text-lg font-bold text-gray-900 mb-3">${new Date(date + 'T00:00:00').toLocaleDateString('pt-PT', { weekday: 'long', day: '2-digit', month: '2-digit', year: 'numeric' })}</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
        `;

        places.forEach((place) => {
            if (place.disponivel) {
                html += `
                    <button type="button"
                            onclick="selectPlaceByDay('${date}', ${place.id})"
                            id="place-${safeDateId}-${place.id}"
                            class="place-button-day p-4 border-2 border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition text-center">
                        <div class="text-2xl font-bold text-gray-700">🅿️</div>
                        <div class="text-lg font-bold text-gray-900 mt-1">Lugar ${place.numero}</div>
                        <div class="text-xs text-green-600 font-medium mt-1">✓ Disponível</div>
                    </button>
                `;
            } else {
                const isFixo = place.motivo === 'fixo';
                html += `
                    <div class="p-4 border-2 border-gray-200 rounded-lg bg-gray-100 text-center opacity-50 cursor-not-allowed">
                        <div class="text-2xl font-bold text-gray-400">🅿️</div>
                        <div class="text-lg font-bold text-gray-500 mt-1">Lugar ${place.numero}</div>
                        <div class="text-xs text-red-600 font-medium mt-1">${isFixo ? 'Indisponível (fixo)' : 'Ocupado'}</div>
                    </div>
                `;
            }
        });

        html += `
                </div>
            </div>
        `;
    });
    html += '</div>';
    html += '<div id="selected-places-by-day-inputs"></div>';
    html += '<input type="hidden" name="lugar_id" id="lugar_id">';
    html += '<div id="selected-places-inputs"></div>';
    html += '<p class="text-sm text-gray-600 mt-4">Pode selecionar os lugares que quiser em cada dia útil.</p>';

    placesContent.innerHTML = html;

    if (pendingSelectionRestore && pendingSelectionRestore.type === 'interval') {
        Object.keys(pendingSelectionRestore.lugaresPorDia || {}).forEach((data) => {
            const ids = Array.isArray(pendingSelectionRestore.lugaresPorDia[data])
                ? pendingSelectionRestore.lugaresPorDia[data]
                : [];

            ids.forEach((id) => {
                const numericId = Number(id);
                const btnId = `place-${data.replace(/[^0-9]/g, '')}-${numericId}`;
                if (document.getElementById(btnId)) {
                    selectPlaceByDay(data, numericId);
                }
            });
        });

        pendingSelectionRestore = null;
        refreshHiddenPlacesByDayInputs();
        return;
    }

    Object.keys(oldLugaresPorDia || {}).forEach((data) => {
        const ids = Array.isArray(oldLugaresPorDia[data]) ? oldLugaresPorDia[data] : [];
        ids.forEach((id) => {
            const btnId = `place-${data.replace(/[^0-9]/g, '')}-${id}`;
            if (document.getElementById(btnId)) {
                selectPlaceByDay(data, Number(id));
            }
        });
    });

    refreshHiddenPlacesByDayInputs();
}

function selectPlaceByDay(date, placeId) {
    if (!selectedPlacesByDay[date]) {
        selectedPlacesByDay[date] = new Set();
    }

    const set = selectedPlacesByDay[date];
    const btnId = `place-${date.replace(/[^0-9]/g, '')}-${placeId}`;
    const clickedButton = document.getElementById(btnId);
    if (!clickedButton) return;

    if (set.has(placeId)) {
        set.delete(placeId);
        clickedButton.classList.remove('border-blue-600', 'bg-blue-50');
        clickedButton.classList.add('border-gray-300');
    } else {
        set.add(placeId);
        clickedButton.classList.remove('border-gray-300');
        clickedButton.classList.add('border-blue-600', 'bg-blue-50');
    }

    if (set.size === 0) {
        delete selectedPlacesByDay[date];
    }

    refreshHiddenPlacesByDayInputs();
}

function refreshHiddenPlacesByDayInputs() {
    const wrapper = document.getElementById('selected-places-by-day-inputs');
    if (!wrapper) return;

    wrapper.innerHTML = '';
    Object.keys(selectedPlacesByDay).forEach((date) => {
        Array.from(selectedPlacesByDay[date]).forEach((placeId) => {
            wrapper.insertAdjacentHTML(
                'beforeend',
                `<input type="hidden" name="lugares_por_dia[${date}][]" value="${placeId}">`
            );
        });
    });

    const allDaysCovered = intervalDates.length > 0
        && intervalDates.every((date) => selectedPlacesByDay[date] && selectedPlacesByDay[date].size > 0);
    document.getElementById('submit-button').disabled = !allDaysCovered;
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

function getIntervalPeriods() {
    return Array.from(document.querySelectorAll('.periodo-row')).map((row) => {
        const startInput = row.querySelector('.periodo-start-input');
        const endInput = row.querySelector('.periodo-end-input');
        return {
            data_inicio: startInput ? startInput.value : '',
            data_fim: endInput ? endInput.value : '',
        };
    });
}

function updatePeriodoRemoveButtons() {
    const removeButtons = document.querySelectorAll('.remove-periodo-btn');
    const showRemove = removeButtons.length > 1;
    removeButtons.forEach((btn) => {
        btn.classList.toggle('hidden', !showRemove);
    });
}

function addPeriodoRow() {
    const container = document.getElementById('periodos-container');
    if (!container) return;

    const nextIndex = periodRowCounter++;
    container.insertAdjacentHTML('beforeend', `
        <div class="periodo-row grid grid-cols-1 md:grid-cols-11 gap-3 items-end" data-index="${nextIndex}">
            <div class="md:col-span-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Data de Início
                </label>
                <input type="date"
                       name="periodos[${nextIndex}][data_inicio]"
                       class="periodo-start-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       onchange="checkAvailability()">
            </div>
            <div class="md:col-span-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Data de Fim
                </label>
                <input type="date"
                       name="periodos[${nextIndex}][data_fim]"
                       class="periodo-end-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       onchange="checkAvailability()">
            </div>
            <div class="md:col-span-1">
                <button type="button"
                        onclick="removePeriodoRow(this)"
                        class="remove-periodo-btn w-full h-[50px] border border-red-300 text-red-700 rounded-lg hover:bg-red-50">
                    -
                </button>
            </div>
        </div>
    `);

    updatePeriodoRemoveButtons();
    checkAvailability();
}

function removePeriodoRow(button) {
    const row = button.closest('.periodo-row');
    if (!row) return;
    row.remove();
    updatePeriodoRemoveButtons();
    checkAvailability();
}

document.addEventListener('DOMContentLoaded', function () {
    if (isAdmin) {
        document.querySelectorAll('input[name="modo_reserva"]').forEach(input => {
            input.addEventListener('change', updateFormByMode);
        });
        document.querySelectorAll('input[name="tipo_periodo"]').forEach(input => {
            input.addEventListener('change', updateFormByMode);
        });
    }

    const existingIndexes = Array.from(document.querySelectorAll('.periodo-row'))
        .map((row) => Number(row.dataset.index))
        .filter((value) => Number.isFinite(value));
    periodRowCounter = existingIndexes.length > 0 ? (Math.max(...existingIndexes) + 1) : 1;

    updatePeriodoRemoveButtons();
    updateFormByMode();
});
</script>
@endsection
