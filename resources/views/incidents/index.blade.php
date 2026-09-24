@extends('layouts.app')

@section('header', 'Ocorrências')

@section('content')
<div class="flex justify-between items-center mb-4">
    <h2 class="text-xl font-semibold">Lista de Ocorrências</h2>
    <a href="{{ route('incidents.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Nova Ocorrência</a>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif

<div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Funcionário</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Data</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Local</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Severidade</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Tipo</th>
                <th class="px-6 py-3 text-right text-sm font-medium text-gray-500 uppercase">Ações</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($incidents as $incident)
                <tr>
                    <td class="px-6 py-4">{{ $incident->user->name ?? '-' }}</td>
                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($incident->data_ocorrencia)->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">{{ $incident->local }}</td>
                    <td class="px-6 py-4">{{ $incident->severidade ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $incident->tipo ?? '-' }}</td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('incidents.show', $incident) }}" class="text-blue-600 hover:underline">Ver</a>
                        <a href="{{ route('incidents.edit', $incident) }}" class="text-indigo-600 hover:underline">Editar</a>
                        <form action="{{ route('incidents.destroy', $incident) }}" method="POST" class="inline" onsubmit="return confirm('Excluir ocorrência?');">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline">Excluir</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Nenhuma ocorrência encontrada.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
