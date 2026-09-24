@extends('layouts.app')

@section('header', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
        <div class="text-gray-500 text-sm">Usuários</div>
        <div class="text-3xl font-bold text-gray-900">{{ $usersCount }}</div>
    </div>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
        <div class="text-gray-500 text-sm">Exames</div>
        <div class="text-3xl font-bold text-gray-900">{{ $examsCount }}</div>
    </div>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
        <div class="text-gray-500 text-sm">Riscos</div>
        <div class="text-3xl font-bold text-gray-900">{{ $risksCount }}</div>
    </div>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-red-500">
        <div class="text-gray-500 text-sm">Ocorrências</div>
        <div class="text-3xl font-bold text-gray-900">{{ $incidentsCount }}</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <h2 class="text-lg font-semibold mb-4">Exames Recentes</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2">Paciente</th>
                        <th class="text-left py-2">Tipo</th>
                        <th class="text-left py-2">Data</th>
                        <th class="text-left py-2">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentExams as $exam)
                        <tr class="border-b">
                            <td class="py-2">{{ $exam->user->name ?? '-' }}</td>
                            <td class="py-2">{{ $exam->tipo }}</td>
                            <td class="py-2">{{ \Carbon\Carbon::parse($exam->data_exame)->format('d/m/Y') }}</td>
                            <td class="py-2">{{ $exam->status ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-4 text-center text-gray-500">Nenhum exame encontrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <h2 class="text-lg font-semibold mb-4">Ocorrências Recentes</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2">Funcionário</th>
                        <th class="text-left py-2">Local</th>
                        <th class="text-left py-2">Data</th>
                        <th class="text-left py-2">Severidade</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentIncidents as $incident)
                        <tr class="border-b">
                            <td class="py-2">{{ $incident->user->name ?? '-' }}</td>
                            <td class="py-2">{{ $incident->local }}</td>
                            <td class="py-2">{{ \Carbon\Carbon::parse($incident->data_ocorrencia)->format('d/m/Y') }}</td>
                            <td class="py-2">{{ $incident->severidade ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-4 text-center text-gray-500">Nenhuma ocorrência encontrada.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection