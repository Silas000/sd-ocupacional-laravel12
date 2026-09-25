@extends('layouts.app')

@section('header', 'Registros de Saúde')

@section('content')
<div class="flex justify-between items-center mb-4">
    <h2 class="text-xl font-semibold">Lista de Registros de Saúde</h2>
    <a href="{{ route('health.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Novo Registro</a>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif

<x-filter-bar :action="route('health.index')">
    <div class="flex-1 min-w-52">
        <label class="block text-xs font-medium text-gray-600 mb-1">Buscar</label>
        <input type="search" name="q" value="{{ $filtros['q'] ?? '' }}" placeholder="Descrição, observações ou colaborador"
               class="w-full border-gray-300 rounded-md shadow-sm text-sm">
    </div>

    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">De</label>
        <input type="date" name="de" value="{{ $filtros['de'] ?? '' }}" class="border-gray-300 rounded-md shadow-sm text-sm">
    </div>

    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Até</label>
        <input type="date" name="ate" value="{{ $filtros['ate'] ?? '' }}" class="border-gray-300 rounded-md shadow-sm text-sm">
    </div>
</x-filter-bar>

<div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Funcionário</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Tipo</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Data do Registro</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Descrição</th>
                <th class="px-6 py-3 text-right text-sm font-medium text-gray-500 uppercase">Ações</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($records as $record)
                <tr>
                    <td class="px-6 py-4">{{ $record->user->name ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $record->tipo }}</td>
                    <td class="px-6 py-4">{{ $record->data_registro?->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">{{ $record->resumo() }}</td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('health.show', $record) }}" class="text-blue-600 hover:underline">Ver</a>
                        <a href="{{ route('health.edit', $record) }}" class="text-indigo-600 hover:underline">Editar</a>
                        <form action="{{ route('health.destroy', $record) }}" method="POST" class="inline" onsubmit="return confirm('Excluir registro?');">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline">Excluir</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Nenhum registro encontrado.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<x-pagination-summary :paginator="$records" />
@endsection
