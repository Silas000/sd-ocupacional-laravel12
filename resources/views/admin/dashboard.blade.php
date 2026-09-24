@extends('layouts.app')

@section('header', 'Dashboard Administrativo')

@section('content')
<div class="space-y-6">
    <!-- Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="flex-1">
                    <div class="text-gray-500 text-sm">Usuários</div>
                    <div class="text-3xl font-bold text-gray-900">{{ $usersCount }}</div>
                </div>
                <div class="text-blue-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 014 4V7.5h1.25a2.25 2.25 0 012.25 2.25v10.5a2.25 2.25 0 01-2.25 2.25h-13.5a2.25 2.25 0 01-2.25-2.25V9.75a2.25 2.25 0 012.25-2.25H10V8.354a4 4 0 014-4z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="flex-1">
                    <div class="text-gray-500 text-sm">Exames</div>
                    <div class="text-3xl font-bold text-gray-900">{{ $examsCount }}</div>
                </div>
                <div class="text-green-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H6A2.25 2.25 0 003.75 6v8.25A2.25 2.25 0 006 16.5h.75m3 0v3.75a2.25 2.25 0 01-2.25 2.25H9.75m-3 0V18"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="flex-1">
                    <div class="text-gray-500 text-sm">Riscos</div>
                    <div class="text-3xl font-bold text-gray-900">{{ $risksCount }}</div>
                </div>
                <div class="text-yellow-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008zM12 15.75h.007v.008H12v-.008zM12 15.75h.007v.008H12v-.008z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-red-500">
            <div class="flex items-center">
                <div class="flex-1">
                    <div class="text-gray-500 text-sm">Ocorrências</div>
                    <div class="text-3xl font-bold text-gray-900">{{ $incidentsCount }}</div>
                </div>
                <div class="text-red-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas -->
    <div class="bg-red-50 border-l-4 border-red-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700">
                    <strong>{{ $examsVencidos }}</strong> exame(s) vencido(s) no sistema.
                </p>
            </div>
        </div>
    </div>

    <!-- Gráficos e Tabelas -->
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
                                <td class="py-2">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        {{ $exam->status == 'realizado' ? 'bg-green-100 text-green-800' : ($exam->status == 'pendente' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $exam->status ?? '-' }}
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
                                <td class="py-2">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        {{ $incident->severidade == 'grave' ? 'bg-red-100 text-red-800' : ($incident->severidade == 'moderado' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                        {{ $incident->severidade ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-4 text-center text-gray-500">Nenhuma ocorrência encontrada.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Exames por Status</h2>
            <div class="relative h-64">
                <canvas id="examsByStatusChart"></canvas>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Riscos por Severidade</h2>
            <div class="relative h-64">
                <canvas id="risksBySeverityChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const examsStatusCtx = document.getElementById('examsByStatusChart');
        if (examsStatusCtx) {
            new Chart(examsStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($examsByStatus->keys()) !!},
                    datasets: [{
                        data: {!! json_encode($examsByStatus->values()) !!},
                        backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444'],
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        }

        const risksSeverityCtx = document.getElementById('risksBySeverityChart');
        if (risksSeverityCtx) {
            new Chart(risksSeverityCtx, {
                type: 'pie',
                data: {
                    labels: {!! json_encode($risksBySeverity->keys()) !!},
                    datasets: [{
                        data: {!! json_encode($risksBySeverity->values()) !!},
                        backgroundColor: ['#EF4444', '#F59E0B', '#10B981'],
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        }
    });
</script>
@endpush
