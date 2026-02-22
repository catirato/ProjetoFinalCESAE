@extends('layouts.master')

@section('title', 'Editar Reserva')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <a href="{{ url('/reservas/' . $reserva->id) }}" class="text-blue-600 hover:text-blue-800 flex items-center mb-4">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Voltar aos detalhes
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Editar Reserva #{{ $reserva->id }}</h1>
        <p class="text-gray-600 mt-1">Gestão administrativa de reservas</p>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6">
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.reservas.update', $reserva->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Utilizador</label>
                <input type="text"
                       value="{{ $reserva->utilizador->nome ?? 'N/A' }} ({{ $reserva->utilizador->email ?? 'N/A' }})"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-700"
                       disabled>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="data" class="block text-sm font-medium text-gray-700 mb-1">Data</label>
                    <input id="data"
                           name="data"
                           type="date"
                           required
                           value="{{ old('data', \Carbon\Carbon::parse($reserva->data)->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="lugar_id" class="block text-sm font-medium text-gray-700 mb-1">Lugar</label>
                    <select id="lugar_id"
                            name="lugar_id"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        @foreach($lugares as $lugar)
                            <option value="{{ $lugar->id }}"
                                {{ (string) old('lugar_id', $reserva->lugar_id) === (string) $lugar->id ? 'selected' : '' }}>
                                Lugar {{ $lugar->numero }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select id="estado"
                        name="estado"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    @foreach(['ATIVA', 'PRESENTE', 'NAO_COMPARECEU', 'CANCELADA'] as $estado)
                        <option value="{{ $estado }}"
                            {{ old('estado', $reserva->estado) === $estado ? 'selected' : '' }}>
                            {{ $estado }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ url('/reservas/' . $reserva->id) }}"
                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Guardar Alterações
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

