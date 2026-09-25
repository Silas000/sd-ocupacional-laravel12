@extends('layouts.app')

@section('header', 'Riscos')

@section('content')
<div class="flex justify-between items-center mb-4">
    <h2 class="text-xl font-semibold">Lista de Riscos</h2>
    <a href="{{ route('risks.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Novo Risco</a>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif

<x-filter-bar :action="route('risks.index')">
    <div class="flex-1 min-w-52">
        <label class="block text-xs font-medium text-gray-600 mb-1">Buscar</label>
        <input type="search" name="q" value="{{ $filtros['q'] ?? '' }}" placeholder="Nome do risco, descrição ou responsável"
               class="w-full border-gray-300 rounded-md shadow-sm text-sm">
    </div>

    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Severidade</label>
        <select name="severidade" class="border-gray-300 rounded-md shadow-sm text-sm">
            <option value="">Todas</option>
            @foreach($severidades as $severidade)
                <option value="{{ $severidade->value }}" {{ ($filtros['severidade'] ?? '') === $severidade->value ? 'selected' : '' }}>{{ $severidade->label() }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Categoria</label>
        <select name="categoria" class="border-gray-300 rounded-md shadow-sm text-sm">
            <option value="">Todas</option>
            @foreach($categorias as $categoria)
                <option value="{{ $categoria->value }}" {{ ($filtros['categoria'] ?? '') === $categoria->value ? 'selected' : '' }}>{{ $categoria->label() }}</option>
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
        <select name="ativo" class="border-gray-300 rounded-md shadow-sm text-sm">
            <option value="">Todas</option>
            <option value="1" {{ ($filtros['ativo'] ?? '') === '1' ? 'selected' : '' }}>Ativos</option>
            <option value="0" {{ ($filtros['ativo'] ?? '') === '0' ? 'selected' : '' }}>Inativos</option>
        </select>
    </div>
</x-filter-bar>

<div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Nome</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Setor</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Categoria</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Severidade</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Responsável</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Situação</th>
                <th class="px-6 py-3 text-right text-sm font-medium text-gray-500 uppercase">Ações</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($risks as $risk)
                <tr>
                    <td class="px-6 py-4">{{ $risk->nome }}</td>
                    <td class="px-6 py-4">{{ $risk->setor ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $risk->categoriaEnum()?->label() ?? $risk->categoria }}</td>
                    <td class="px-6 py-4">
                        <x-badge :color="$risk->severidadeEnum()?->badgeClass()">
                            {{ $risk->severidadeEnum()?->label() ?? '-' }}
                        </x-badge>
                    </td>
                    <td class="px-6 py-4">{{ $risk->user->name ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <x-badge :color="$risk->ativo ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">
                            {{ $risk->ativo ? 'Ativo' : 'Inativo' }}
                        </x-badge>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('risks.show', $risk) }}" class="text-blue-600 hover:underline">Ver</a>
                        <a href="{{ route('risks.edit', $risk) }}" class="text-indigo-600 hover:underline">Editar</a>
                        <form action="{{ route('risks.destroy', $risk) }}" method="POST" class="inline" onsubmit="return confirm('Excluir risco?');">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline">Excluir</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">Nenhum risco encontrado.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<x-pagination-summary :paginator="$risks" />
@endsection
