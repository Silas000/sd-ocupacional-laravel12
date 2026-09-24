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
                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($record->data_registro)->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">{{ \Illuminate\Support\Str::limit($record->descricao, 60) }}</td>
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
@endsection
