@extends('layouts.app')

@section('header', 'Meu Dashboard')

@section('content')
<div class="space-y-6">
    @if($examesVencidos > 0)
        <div class="bg-red-50 border-l-4 border-red-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">
                        Você possui <strong>{{ $examesVencidos }}</strong> exame(s) vencido(s). Entre em contato com o RH para agendar.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
            <div class="text-gray-500 text-sm">Meus Exames</div>
            <div class="text-3xl font-bold text-gray-900">{{ $examesPessoais->count() }}</div>
        </div>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-red-500">
            <div class="text-gray-500 text-sm">Exames Vencidos</div>
            <div class="text-3xl font-bold text-red-600">{{ $examesVencidos }}</div>
        </div>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
            <div class="text-gray-500 text-sm">Riscos do Meu Setor</div>
            <div class="text-3xl font-bold text-gray-900">{{ $risksSetor->count() }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Meus Exames</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">Tipo</th>
                            <th class="text-left py-2">Data</th>
                            <th class="text-left py-2">Validade</th>
                            <th class="text-left py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($examesPessoais as $exam)
                            <tr class="border-b">
                                <td class="py-2">{{ $exam->tipo }}</td>
                                <td class="py-2">{{ \Carbon\Carbon::parse($exam->data_exame)->format('d/m/Y') }}</td>
                                <td class="py-2">{{ \Carbon\Carbon::parse($exam->data_vencimento)->format('d/m/Y') }}</td>
                                <td class="py-2">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        {{ $exam->data_vencimento < now() ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $exam->data_vencimento < now() ? 'Vencido' : 'Válido' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-4 text-center text-gray-500">Nenhum exame encontrado.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Riscos do Meu Setor</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">Nome</th>
                            <th class="text-left py-2">Severidade</th>
                            <th class="text-left py-2">Descrição</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($risksSetor as $risk)
                            <tr class="border-b">
                                <td class="py-2">{{ $risk->nome }}</td>
                                <td class="py-2">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        {{ $risk->severidade == 'alto' ? 'bg-red-100 text-red-800' : ($risk->severidade == 'medio' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                        {{ $risk->severidade }}
                                    </span>
                                </td>
                                <td class="py-2">{{ $risk->descricao }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4 text-center text-gray-500">Nenhum risco encontrado.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($incidentsPessoais->count() > 0)
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Minhas Ocorrências</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">Local</th>
                            <th class="text-left py-2">Data</th>
                            <th class="text-left py-2">Descrição</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($incidentsPessoais as $incident)
                            <tr class="border-b">
                                <td class="py-2">{{ $incident->local }}</td>
                                <td class="py-2">{{ \Carbon\Carbon::parse($incident->data_ocorrencia)->format('d/m/Y') }}</td>
                                <td class="py-2">{{ $incident->descricao }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
