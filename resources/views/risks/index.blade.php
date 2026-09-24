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

<div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Nome</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Setor</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Categoria</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Severidade</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Ativo</th>
                <th class="px-6 py-3 text-right text-sm font-medium text-gray-500 uppercase">Ações</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($risks as $risk)
                <tr>
                    <td class="px-6 py-4">{{ $risk->nome }}</td>
                    <td class="px-6 py-4">{{ $risk->setor ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $risk->categoria ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $risk->severidade ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $risk->ativo ? 'Sim' : 'Não' }}</td>
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
                <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Nenhum risco encontrado.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
