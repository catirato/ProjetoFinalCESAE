@extends('layouts.master')

@section('title', 'Histórico de Utilizadores')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">📋 Histórico de Utilizadores</h1>
        <p class="text-gray-600 mt-1">Visão geral de todos os utilizadores, com filtros e pesquisa</p>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <form method="GET" action="{{ url('/historico') }}" class="flex flex-col md:flex-row gap-4 items-end">
            <!-- Pesquisa por nome -->
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Pesquisar por nome</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Escreva o nome..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Filtro por role -->
            <div class="w-full md:w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Função</label>
                <select name="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todas</option>
                    <option value="ADMIN" {{ request('role') === 'ADMIN' ? 'selected' : '' }}>ADMIN</option>
                    <option value="SEGURANCA" {{ request('role') === 'SEGURANCA' ? 'selected' : '' }}>SEGURANÇA</option>
                    <option value="COLAB" {{ request('role') === 'COLAB' ? 'selected' : '' }}>COLAB</option>
                </select>
            </div>

            <!-- Botões -->
            <div class="flex gap-2">
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">
                    🔍 Filtrar
                </button>
                <a href="{{ url('/historico') }}"
                   class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition">
                    Limpar
                </a>
            </div>
        </form>
    </div>

    <!-- Tabela de utilizadores -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-xl font-bold text-gray-900">
                Todos os Utilizadores
                <span class="text-sm font-normal text-gray-500">({{ $utilizadores->total() }} resultados)</span>
            </h2>
        </div>

        @if($utilizadores->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <th class="px-6 py-3">Nome</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3 text-center">Função</th>
                            <th class="px-6 py-3 text-center">Pontos</th>
                            <th class="px-6 py-3 text-center">Reservas</th>
                            <th class="px-6 py-3 text-center">Faltas</th>
                            <th class="px-6 py-3 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($utilizadores as $user)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-bold text-sm">
                                            {{ strtoupper(substr($user->nome, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $user->nome }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600 text-sm">{{ $user->email }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if($user->role === 'ADMIN')
                                        <span class="inline-block px-3 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">ADMIN</span>
                                    @elseif($user->role === 'SEGURANCA')
                                        <span class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">SEGURANÇA</span>
                                    @else
                                        <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">COLAB</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center text-sm font-bold {{ $user->pontos >= 20 ? 'text-green-600' : ($user->pontos >= 10 ? 'text-yellow-600' : 'text-red-600') }}">
                                        ⭐ {{ $user->pontos }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-block px-3 py-1 bg-blue-50 text-blue-700 text-sm font-semibold rounded-full">
                                        {{ $user->reservas_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($user->faltas_count > 0)
                                        <span class="inline-block px-3 py-1 bg-red-50 text-red-700 text-sm font-semibold rounded-full">
                                            {{ $user->faltas_count }}
                                        </span>
                                    @else
                                        <span class="inline-block px-3 py-1 bg-green-50 text-green-700 text-sm font-semibold rounded-full">
                                            0
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ url('/historico/' . $user->id) }}"
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                                        Ver Detalhe →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            @if($utilizadores->hasPages())
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $utilizadores->links() }}
                </div>
            @endif
        @else
            <div class="px-6 py-12 text-center">
                <div class="text-6xl mb-4">🔍</div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Nenhum utilizador encontrado</h3>
                <p class="text-gray-600">Tente alterar os filtros de pesquisa</p>
            </div>
        @endif
    </div>
</div>
@endsection
