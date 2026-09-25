@extends('layouts.app')

@section('header', 'Editar Ocorrência')

@section('content')
<div class="max-w-2xl">
    <h2 class="text-xl font-semibold mb-4">Editar Ocorrência</h2>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('incidents.update', $incident) }}" method="POST" class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
        @csrf @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700">Funcionário</label>
            <select name="user_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                <option value="">Selecione</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ (string) old('user_id', $incident->user_id) === (string) $user->id ? 'selected' : '' }}>
                        {{ $user->name }}{{ $user->setor ? ' — '.$user->setor : '' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Data da Ocorrência</label>
            <input type="date" name="data_ocorrencia" value="{{ old('data_ocorrencia', $incident->data_ocorrencia?->format('Y-m-d\TH:i')) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Local</label>
            <input type="text" name="local" value="{{ old('local', $incident->local) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Tipo</label>
            <select name="tipo" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                @foreach($tipos as $tipo)
                    <option value="{{ $tipo->value }}" {{ old('tipo', $incident->tipo) === $tipo->value ? 'selected' : '' }}>{{ $tipo->label() }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Severidade</label>
            <select name="severidade" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                @foreach($severidades as $severidade)
                    <option value="{{ $severidade->value }}" {{ old('severidade', $incident->severidade) === $severidade->value ? 'selected' : '' }}>{{ $severidade->label() }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Descrição</label>
            <textarea name="descricao" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>{{ old('descricao', $incident->descricao) }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Medidas Corretivas</label>
            <textarea name="medidas_corretivas" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('medidas_corretivas', $incident->medidas_corretivas) }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Observações</label>
            <textarea name="observacoes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('observacoes', $incident->observacoes) }}</textarea>
        </div>

        <div class="flex space-x-4">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Atualizar</button>
            <a href="{{ route('incidents.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Cancelar</a>
        </div>
    </form>
</div>
@endsection
