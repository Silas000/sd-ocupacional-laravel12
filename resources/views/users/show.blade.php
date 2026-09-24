@extends('layouts.app')

@section('header', 'Detalhes do Usuário')

@section('content')
<div class="max-w-3xl">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold">{{ $user->name }}</h2>
        <div class="space-x-2">
            <a href="{{ route('users.edit', $user) }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Editar</a>
            <a href="{{ route('users.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Voltar</a>
        </div>
    </div>

    <div class="bg-white shadow-sm sm:rounded-lg p-6">
        <dl class="grid grid-cols-1 gap-4">
            <div><dt class="text-sm font-medium text-gray-500">Email</dt><dd class="text-gray-900">{{ $user->email }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Função</dt><dd class="text-gray-900">{{ $user->role }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">CPF</dt><dd class="text-gray-900">{{ $user->cpf ?? '-' }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Cargo</dt><dd class="text-gray-900">{{ $user->cargo ?? '-' }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Setor</dt><dd class="text-gray-900">{{ $user->setor ?? '-' }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Data de Admissão</dt><dd class="text-gray-900">{{ $user->data_admissao ? \Carbon\Carbon::parse($user->data_admissao)->format('d/m/Y') : '-' }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Data de Demissão</dt><dd class="text-gray-900">{{ $user->data_demissao ? \Carbon\Carbon::parse($user->data_demissao)->format('d/m/Y') : '-' }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Observações</dt><dd class="text-gray-900">{{ $user->observacoes ?? '-' }}</dd></div>
        </dl>
    </div>
</div>
@endsection
