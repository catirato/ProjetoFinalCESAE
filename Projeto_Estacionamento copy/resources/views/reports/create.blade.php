@extends('layouts.master')

@section('title', 'Submissão de Relatório')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <a href="{{ url('/dashboard') }}" class="text-blue-600 hover:text-blue-800 flex items-center mb-4">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Voltar ao Painel de Controlo
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Submissão de Relatório</h1>
        <p class="text-gray-600 mt-1">Reporte ocorrências relacionadas com o estacionamento para análise do administrador.</p>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('reports.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="tipo" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Report</label>
                <select id="tipo" name="tipo" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Selecione</option>
                    <option value="LUGAR_OCUPADO" {{ old('tipo') === 'LUGAR_OCUPADO' ? 'selected' : '' }}>Lugar Ocupado</option>
                    <option value="SEM_RESERVA" {{ old('tipo') === 'SEM_RESERVA' ? 'selected' : '' }}>Sem Reserva</option>
                    <option value="PROBLEMA" {{ old('tipo') === 'PROBLEMA' ? 'selected' : '' }}>Problema</option>
                </select>
            </div>

            <div>
                <label for="descricao" class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                <textarea id="descricao" name="descricao" rows="5" required maxlength="2000"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Descreva a ocorrência...">{{ old('descricao') }}</textarea>
            </div>

            <div>
                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-lg font-semibold transition"
                        style="background-color:#4f46e5;color:#ffffff;border:1px solid #4338ca;"
                        onmouseover="this.style.backgroundColor='#4338ca'"
                        onmouseout="this.style.backgroundColor='#4f46e5'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10.5l9 4.5 9-4.5M3 10.5l9-6 9 6M3 10.5V16.5l9 4.5 9-4.5V10.5"></path>
                    </svg>
                    Enviar Relatório ao Admin
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
