@extends('layouts.app')

@section('header', 'Editar Exame')

@section('content')
<div class="max-w-2xl">
    <h2 class="text-xl font-semibold mb-4">Editar Exame</h2>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('exams.update', $exam) }}" method="POST" class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
        @csrf @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700">Paciente</label>
            <select name="user_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                <option value="">Selecione</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('user_id', $exam->user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Tipo</label>
            <input type="text" name="tipo" value="{{ old('tipo', $exam->tipo) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Data do Exame</label>
            <input type="date" name="data_exame" value="{{ old('data_exame', $exam->data_exame) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Data de Vencimento</label>
            <input type="date" name="data_vencimento" value="{{ old('data_vencimento', $exam->data_vencimento) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Status</label>
            <input type="text" name="status" value="{{ old('status', $exam->status) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Médico Responsável</label>
            <input type="text" name="medico_responsavel" value="{{ old('medico_responsavel', $exam->medico_responsavel) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Resultado</label>
            <textarea name="resultado" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('resultado', $exam->resultado) }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Observações</label>
            <textarea name="observacoes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('observacoes', $exam->observacoes) }}</textarea>
        </div>

        <div class="flex space-x-4">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Atualizar</button>
            <a href="{{ route('exams.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Cancelar</a>
        </div>
    </form>
</div>
@endsection
