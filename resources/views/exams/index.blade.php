@extends('layouts.app')

@section('header', 'Exames')

@section('content')
<div class="flex justify-between items-center mb-4">
    <h2 class="text-xl font-semibold">Lista de Exames</h2>
    <a href="{{ route('exams.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Novo Exame</a>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif

<x-filter-bar :action="route('exams.index')">
    <div class="flex-1 min-w-52">
        <label class="block text-xs font-medium text-gray-600 mb-1">Buscar</label>
        <input type="search" name="q" value="{{ $filtros['q'] ?? '' }}" placeholder="Nome, e-mail ou CPF do paciente"
               class="w-full border-gray-300 rounded-md shadow-sm text-sm">
    </div>

    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
        <select name="status" class="border-gray-300 rounded-md shadow-sm text-sm">
            <option value="">Todos</option>
            @foreach($statuses as $status)
                <option value="{{ $status->value }}" {{ ($filtros['status'] ?? '') === $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Tipo</label>
        <select name="tipo" class="border-gray-300 rounded-md shadow-sm text-sm">
            <option value="">Todos</option>
            @foreach($tipos as $tipo)
                <option value="{{ $tipo->value }}" {{ ($filtros['tipo'] ?? '') === $tipo->value ? 'selected' : '' }}>{{ $tipo->label() }}</option>
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
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Paciente</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Tipo</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Data</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Médico</th>
                <th class="px-6 py-3 text-right text-sm font-medium text-gray-500 uppercase">Ações</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($exams as $exam)
                <tr>
                    <td class="px-6 py-4">{{ $exam->user->name ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $exam->tipoEnum()?->label() ?? $exam->tipo }}</td>
                    <td class="px-6 py-4">{{ $exam->data_exame?->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">
                        <x-badge :color="$exam->statusEnum()?->badgeClass()">
                            {{ $exam->statusEnum()?->label() ?? '-' }}
                        </x-badge>
                        @if($exam->isVencido() && $exam->statusEnum()?->value !== 'cancelado')
                            <x-badge color="bg-red-100 text-red-800">Vencido</x-badge>
                        @endif
                    </td>
                    <td class="px-6 py-4">{{ $exam->medico_responsavel ?? '-' }}</td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('exams.show', $exam) }}" class="text-blue-600 hover:underline">Ver</a>
                        <a href="{{ route('exams.edit', $exam) }}" class="text-indigo-600 hover:underline">Editar</a>
                        <form action="{{ route('exams.destroy', $exam) }}" method="POST" class="inline" onsubmit="return confirm('Excluir exame?');">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline">Excluir</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Nenhum exame encontrado.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<x-pagination-summary :paginator="$exams" />
@endsection
