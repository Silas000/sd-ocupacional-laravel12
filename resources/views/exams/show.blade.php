@extends('layouts.app')

@section('header', 'Detalhes do Exame')

@section('content')
<div class="max-w-3xl">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold">Exame #{{ $exam->id }}</h2>
        <div class="space-x-2">
            <a href="{{ route('exams.edit', $exam) }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Editar</a>
            <a href="{{ route('exams.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Voltar</a>
        </div>
    </div>

    <div class="bg-white shadow-sm sm:rounded-lg p-6">
        <dl class="grid grid-cols-1 gap-4">
            <div><dt class="text-sm font-medium text-gray-500">Paciente</dt><dd class="text-gray-900">{{ $exam->user->name ?? '-' }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Tipo</dt><dd class="text-gray-900">{{ $exam->tipo }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Data do Exame</dt><dd class="text-gray-900">{{ $exam->data_exame?->format('d/m/Y') }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Data de Vencimento</dt><dd class="text-gray-900">{{ $exam->data_vencimento?->format('d/m/Y') ?? '-' }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Status</dt><dd class="text-gray-900">{{ $exam->status ?? '-' }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Médico Responsável</dt><dd class="text-gray-900">{{ $exam->medico_responsavel ?? '-' }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Resultado</dt><dd class="text-gray-900">{{ $exam->resultado ?? '-' }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Observações</dt><dd class="text-gray-900">{{ $exam->observacoes ?? '-' }}</dd></div>
        </dl>
    </div>
</div>
@endsection
