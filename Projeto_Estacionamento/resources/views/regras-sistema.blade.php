@extends('layouts.master')

@section('title', 'Regras do Sistema')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="card bg-base-100 shadow-md">
        <div class="card-body">
            <h1 class="card-title text-2xl">📋 Regras do Sistema</h1>
        </div>
    </div>

    <div class="card bg-base-100 shadow-md">
        <div class="card-body">
            <h2 class="card-title">🚗 Lugares de Estacionamento</h2>
            <ul class="list-disc list-inside space-y-1">
                <li>O parque tem 10 lugares.</li>
                <li>3 lugares são fixos (viaturas do CESAE).</li>
                <li>7 lugares são rotativos para colaboradores.</li>
                <li>O ADMIN pode adicionar, remover ou bloquear lugares.</li>
            </ul>
        </div>
    </div>

    <div class="card bg-base-100 shadow-md">
        <div class="card-body space-y-3">
            <h2 class="card-title">👤 Utilizadores</h2>

            <div>
                <h3 class="font-semibold">👑 Administrador</h3>
                <ul class="list-disc list-inside space-y-1">
                    <li>Gere utilizadores e lugares.</li>
                    <li>Define regras, pontos e penalizações.</li>
                    <li>Analisa reports.</li>
                    <li>Consulta histórico e estatísticas.</li>
                </ul>
            </div>

            <div>
                <h3 class="font-semibold">🛂 Segurança</h3>
                <ul class="list-disc list-inside space-y-1">
                    <li>Valida a chegada dos colaboradores.</li>
                    <li>Consulta as reservas do dia.</li>
                    <li>Pode fazer reports.</li>
                    <li>Não pode criar reservas nem aplicar penalizações.</li>
                </ul>
            </div>

            <div>
                <h3 class="font-semibold">👨‍💼 Colaborador</h3>
                <ul class="list-disc list-inside space-y-1">
                    <li>Pode reservar e cancelar lugares.</li>
                    <li>Pode entrar em lista de espera.</li>
                    <li>Pode consultar pontos, histórico e penalizações.</li>
                    <li>Pode submeter relatórios.</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow-md">
        <div class="card-body">
            <h2 class="card-title">📅 Reservas</h2>
            <p>As reservas podem ser feitas:</p>
            <ul class="list-disc list-inside space-y-1">
                <li>Para o próprio dia (até às 10h).</li>
                <li>Para o resto da semana atual.</li>
                <li>Para a semana seguinte.</li>
            </ul>
            <p>Cada reserva custa 3 pontos.</p>
        </div>
    </div>

    <div class="card bg-base-100 shadow-md">
        <div class="card-body">
            <h2 class="card-title">⏰ Regra das 10h30</h2>
            <p>O colaborador deve ser validado pelo segurança até às 10h30.</p>
            <p>Se não for validado:</p>
            <ul class="list-disc list-inside space-y-1">
                <li>A reserva é marcada como falta de comparência.</li>
                <li>O lugar é libertado.</li>
                <li>É aplicada penalização automática (−10 pontos).</li>
                <li>O lugar passa para a lista de espera.</li>
            </ul>
        </div>
    </div>

    <div class="card bg-base-100 shadow-md">
        <div class="card-body">
            <h2 class="card-title">📌 Cancelamentos e Penalizações</h2>
            <ul class="list-disc list-inside space-y-1">
                <li>Cancelamento voluntário de reserva até ás 10h: −2 pontos (recupera os 3 da reserva).</li>
                <li>Falta de comparência (não validado até às 10h30): −10 pontos adicionais.</li>
                <li>É preferível cancelar do que faltar.</li>
                <li>O utilizador pode ficar com saldo negativo.</li>
            </ul>
        </div>
    </div>

    <div class="card bg-base-100 shadow-md">
        <div class="card-body">
            <h2 class="card-title">🔄 Lista de Espera</h2>
            <p>Quando um lugar fica livre:</p>
            <ul class="list-disc list-inside space-y-1">
                <li>Todos na lista recebem notificação.</li>
                <li>Fica com o lugar quem confirmar primeiro.</li>
            </ul>
            <p>Só pode reservar quem tiver pontos disponíveis.</p>
        </div>
    </div>

    <div class="card bg-base-100 shadow-md">
        <div class="card-body">
            <h2 class="card-title">🎯 Sistema de Pontos</h2>
            <ul class="list-disc list-inside space-y-1">
                <li>Todos começam com 30 pontos.</li>
                <li>Cada reserva custa 3 pontos.</li>
                <li>Os pontos renovam no último dia de cada mês (30 pontos).</li>
                <li>Penalizações reduzem pontos.</li>
                <li>Pode existir saldo negativo.</li>
            </ul>
        </div>
    </div>

    <div class="card bg-base-100 shadow-md">
        <div class="card-body">
            <h2 class="card-title">🚫 Estacionamento Indevido</h2>
            <p>Estacionar sem reserva pode originar penalização (definida pelo ADMIN).</p>
            <p>O caso será analisado pelo ADMIN.</p>
        </div>
    </div>

    <div class="card bg-base-100 shadow-md">
        <div class="card-body">
            <h2 class="card-title">📝 Reports</h2>
            <p>Podem ser reportadas situações como:</p>
            <ul class="list-disc list-inside space-y-1">
                <li>Lugar ocupado indevidamente.</li>
                <li>Veículo sem reserva.</li>
                <li>Problemas no estacionamento.</li>
                <li>Acidentes.</li>
            </ul>
            <p>Os reports são analisados pelo ADMIN.</p>
        </div>
    </div>
</div>
@endsection
