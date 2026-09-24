@extends('layouts.app')

@section('header', 'Detalhes do Registro de Saúde')

@section('content')
<div class="max-w-3xl">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold">Registro #{{ $health->id }}</h2>
        <div class="space-x-2">
            <a href="{{ route('health.edit', $health) }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Editar</a>
            <a href="{{ route('health.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Voltar</a>
        </div>
    </div>

    <div class="bg-white shadow-sm sm:rounded-lg p-6">
        <dl class="grid grid-cols-1 gap-4">
            <div><dt class="text-sm font-medium text-gray-500">Funcionário</dt><dd class="text-gray-900">{{ $health->user->name ?? '-' }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Tipo</dt><dd class="text-gray-900">{{ $health->tipo }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Data do Registro</dt><dd class="text-gray-900">{{ \Carbon\Carbon::parse($health->data_registro)->format('d/m/Y') }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Descrição</dt><dd class="text-gray-900">{{ $health->descricao }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Exame Relacionado</dt><dd class="text-gray-900">{{ $health->exam ? $health->exam->tipo : '-' }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Observações</dt><dd class="text-gray-900">{{ $health->observacoes ?? '-' }}</dd></div>
        </dl>
    </div>
</div>
@endsection
