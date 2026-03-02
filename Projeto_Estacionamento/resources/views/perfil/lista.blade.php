@extends('layouts.master')

@section('title', 'Perfis de Utilizadores')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    @php
        $currentSort = request('sort', 'nome');
        $currentDirection = request('direction', 'asc');
        $nextDirection = function ($column) use ($currentSort, $currentDirection) {
            return $currentSort === $column && $currentDirection === 'asc' ? 'desc' : 'asc';
        };
        $sortIcon = function ($column) use ($currentSort, $currentDirection) {
            if ($currentSort !== $column) return '';
            return $currentDirection === 'asc' ? '↑' : '↓';
        };
    @endphp

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Perfis de Utilizadores</h1>
        <p class="text-gray-600 mt-1">Visualização global de perfis (apenas administrador).</p>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <form method="GET" action="{{ route('admin.perfis.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Pesquisar por nome</label>
                <input type="text"
                       id="search"
                       name="search"
                       value="{{ request('search', $search ?? '') }}"
                       placeholder="Ex: Cristina"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Função</label>
                <select id="role"
                        name="role"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todas</option>
                    <option value="ADMIN" {{ request('role', $role ?? '') === 'ADMIN' ? 'selected' : '' }}>ADMIN</option>
                    <option value="COLAB" {{ request('role', $role ?? '') === 'COLAB' ? 'selected' : '' }}>COLAB</option>
                    <option value="SEGURANCA" {{ request('role', $role ?? '') === 'SEGURANCA' ? 'selected' : '' }}>SEGURANCA</option>
                </select>
            </div>
            <div class="flex gap-3">
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">
                    Filtrar
                </button>
                <a href="{{ route('admin.perfis.index') }}"
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition">
                    Limpar filtros
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'nome', 'direction' => $nextDirection('nome'), 'page' => 1]) }}" class="hover:text-gray-700">
                            Nome {{ $sortIcon('nome') }}
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'email', 'direction' => $nextDirection('email'), 'page' => 1]) }}" class="hover:text-gray-700">
                            Email {{ $sortIcon('email') }}
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'telemovel', 'direction' => $nextDirection('telemovel'), 'page' => 1]) }}" class="hover:text-gray-700">
                            Telemóvel {{ $sortIcon('telemovel') }}
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'role', 'direction' => $nextDirection('role'), 'page' => 1]) }}" class="hover:text-gray-700">
                            Perfil {{ $sortIcon('role') }}
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($utilizadores as $u)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $u->nome }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $u->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $u->telemovel ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $u->role }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-4">
                                <a href="{{ route('admin.perfis.show', $u->id) }}" class="text-blue-600 hover:text-blue-900">
                                    Ver perfil
                                </a>
                                <a href="{{ route('admin.perfis.historico', $u->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                    Ver histórico
                                </a>
                                @if($u->id !== auth('utilizador')->id())
                                    <form action="{{ route('admin.perfis.delete', $u->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('Tem certeza que deseja apagar este utilizador?')"
                                                class="text-red-600 hover:text-red-800">
                                            Apagar
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">Sem utilizadores.</td>
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
