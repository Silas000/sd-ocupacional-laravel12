@extends('layouts.app')

@section('header', 'Detalhes da Ocorrência')

@section('content')
<div class="max-w-3xl">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold">Ocorrência #{{ $incident->id }}</h2>
        <div class="space-x-2">
            <a href="{{ route('incidents.edit', $incident) }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Editar</a>
            <a href="{{ route('incidents.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Voltar</a>
        </div>
    </div>

    <div class="bg-white shadow-sm sm:rounded-lg p-6">
        <dl class="grid grid-cols-1 gap-4">
            <div><dt class="text-sm font-medium text-gray-500">Funcionário</dt><dd class="text-gray-900">{{ $incident->user->name ?? '-' }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Data da Ocorrência</dt><dd class="text-gray-900">{{ \Carbon\Carbon::parse($incident->data_ocorrencia)->format('d/m/Y H:i') }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Local</dt><dd class="text-gray-900">{{ $incident->local }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Tipo</dt><dd class="text-gray-900">{{ $incident->tipo ?? '-' }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Severidade</dt><dd class="text-gray-900">{{ $incident->severidade ?? '-' }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Descrição</dt><dd class="text-gray-900">{{ $incident->descricao }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Medidas Corretivas</dt><dd class="text-gray-900">{{ $incident->medidas_corretivas ?? '-' }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Observações</dt><dd class="text-gray-900">{{ $incident->observacoes ?? '-' }}</dd></div>
        </dl>
    </div>
</div>
@endsection
