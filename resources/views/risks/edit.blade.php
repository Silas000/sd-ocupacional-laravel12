@extends('layouts.app')

@section('header', 'Editar Risco')

@section('content')
<div class="max-w-2xl">
    <h2 class="text-xl font-semibold mb-4">Editar Risco</h2>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('risks.update', $risk) }}" method="POST" class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
        @csrf @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700">Funcionário</label>
            <select name="user_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                <option value="">Selecione</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('user_id', $risk->user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Setor</label>
            <input type="text" name="setor" value="{{ old('setor', $risk->setor) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Nome</label>
            <input type="text" name="nome" value="{{ old('nome', $risk->nome) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Descrição</label>
            <textarea name="descricao" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('descricao', $risk->descricao) }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Severidade</label>
            <input type="text" name="severidade" value="{{ old('severidade', $risk->severidade) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Categoria</label>
            <input type="text" name="categoria" value="{{ old('categoria', $risk->categoria) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Medidas Preventivas</label>
            <textarea name="medidas_preventivas" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('medidas_preventivas', $risk->medidas_preventivas) }}</textarea>
        </div>

        <div class="flex items-center">
            <input type="checkbox" name="ativo" value="1" {{ old('ativo', $risk->ativo ?? '1') == '1' ? 'checked' : '' }} class="h-4 w-4 text-blue-600 border-gray-300 rounded">
            <label class="ml-2 text-sm text-gray-700">Ativo</label>
        </div>

        <div class="flex space-x-4">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Atualizar</button>
            <a href="{{ route('risks.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Cancelar</a>
        </div>
    </form>
</div>
@endsection
