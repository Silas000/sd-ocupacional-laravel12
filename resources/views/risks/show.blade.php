@extends('layouts.app')

@section('header', 'Detalhes do Risco')

@section('content')
<div class="max-w-3xl">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold">Risco #{{ $risk->id }}</h2>
        <div class="space-x-2">
            <a href="{{ route('risks.edit', $risk) }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Editar</a>
            <a href="{{ route('risks.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Voltar</a>
        </div>
    </div>

    <div class="bg-white shadow-sm sm:rounded-lg p-6">
        <dl class="grid grid-cols-1 gap-4">
            <div><dt class="text-sm font-medium text-gray-500">Funcionário</dt><dd class="text-gray-900">{{ $risk->user->name ?? '-' }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Setor</dt><dd class="text-gray-900">{{ $risk->setor ?? '-' }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Nome</dt><dd class="text-gray-900">{{ $risk->nome }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Descrição</dt><dd class="text-gray-900">{{ $risk->descricao ?? '-' }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Severidade</dt><dd class="text-gray-900">{{ $risk->severidade ?? '-' }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Categoria</dt><dd class="text-gray-900">{{ $risk->categoria ?? '-' }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Medidas Preventivas</dt><dd class="text-gray-900">{{ $risk->medidas_preventivas ?? '-' }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Ativo</dt><dd class="text-gray-900">{{ $risk->ativo ? 'Sim' : 'Não' }}</dd></div>
        </dl>
    </div>
</div>
@endsection
