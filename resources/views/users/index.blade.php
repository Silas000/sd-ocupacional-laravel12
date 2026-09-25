@extends('layouts.app')

@section('header', 'Usuários')

@section('content')
<div class="flex justify-between items-center mb-4">
    <h2 class="text-xl font-semibold">Lista de Usuários</h2>
    <a href="{{ route('users.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Novo Usuário</a>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
@endif

<x-filter-bar :action="route('users.index')">
    <div class="flex-1 min-w-52">
        <label class="block text-xs font-medium text-gray-600 mb-1">Buscar</label>
        <input type="search" name="q" value="{{ $filtros['q'] ?? '' }}" placeholder="Nome, e-mail, cargo ou CPF"
               class="w-full border-gray-300 rounded-md shadow-sm text-sm">
    </div>

    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Perfil</label>
        <select name="role" class="border-gray-300 rounded-md shadow-sm text-sm">
            <option value="">Todos</option>
            @foreach($roles as $role)
                <option value="{{ $role->value }}" {{ ($filtros['role'] ?? '') === $role->value ? 'selected' : '' }}>{{ $role->label() }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Setor</label>
        <select name="setor" class="border-gray-300 rounded-md shadow-sm text-sm">
            <option value="">Todos</option>
            @foreach($setores as $setor)
                <option value="{{ $setor }}" {{ ($filtros['setor'] ?? '') === $setor ? 'selected' : '' }}>{{ $setor }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Situação</label>
        <select name="situacao" class="border-gray-300 rounded-md shadow-sm text-sm">
            <option value="">Todas</option>
            <option value="ativos" {{ ($filtros['situacao'] ?? '') === 'ativos' ? 'selected' : '' }}>Ativos</option>
            <option value="demitidos" {{ ($filtros['situacao'] ?? '') === 'demitidos' ? 'selected' : '' }}>Demitidos</option>
        </select>
    </div>
</x-filter-bar>

<div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Nome</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Perfil</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Setor</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Situação</th>
                <th class="px-6 py-3 text-right text-sm font-medium text-gray-500 uppercase">Ações</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($users as $user)
                <tr>
                    <td class="px-6 py-4">
                        {{ $user->name }}
                        @if($user->must_change_password)
                            <x-badge color="bg-yellow-100 text-yellow-800">senha pendente</x-badge>
                        @endif
                    </td>
                    <td class="px-6 py-4">{{ $user->email }}</td>
                    <td class="px-6 py-4">{{ $user->roleEnum()->label() }}</td>
                    <td class="px-6 py-4">{{ $user->setor ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <x-badge :color="$user->isAtivo() ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">
                            {{ $user->isAtivo() ? 'Ativo' : 'Demitido' }}
                        </x-badge>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('users.show', $user) }}" class="text-blue-600 hover:underline">Ver</a>
                        <a href="{{ route('users.edit', $user) }}" class="text-indigo-600 hover:underline">Editar</a>
                        @if(auth()->id() !== $user->id)
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Excluir usuário? O histórico de saúde será preservado.');">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline">Excluir</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Nenhum usuário encontrado.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<x-pagination-summary :paginator="$users" />
@endsection
