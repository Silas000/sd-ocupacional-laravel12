@extends('layouts.app')

@section('header', 'Dashboard Técnico de Segurança')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
            <div class="text-gray-500 text-sm">Total de Riscos</div>
            <div class="text-3xl font-bold text-gray-900">{{ $risks->count() }}</div>
        </div>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
            <div class="text-gray-500 text-sm">Total de Ocorrências</div>
            <div class="text-3xl font-bold text-gray-900">{{ $incidents->count() }}</div>
        </div>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
            <div class="text-gray-500 text-sm">Ocorrências este Ano</div>
            <div class="text-3xl font-bold text-gray-900">{{ $incidentsByMonth->sum() }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Riscos por Setor</h2>
            <div class="relative h-64">
                <canvas id="risksBySetorChart"></canvas>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Riscos por Severidade</h2>
            <div class="relative h-64">
                <canvas id="risksBySeverityChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Ocorrências por Mês</h2>
            <div class="relative h-64">
                <canvas id="incidentsByMonthChart"></canvas>
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
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const risksBySetorCtx = document.getElementById('risksBySetorChart');
        if (risksBySetorCtx) {
            new Chart(risksBySetorCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($risksBySetor->keys()) !!},
                    datasets: [{
                        label: 'Quantidade de Riscos',
                        data: {!! json_encode($risksBySetor->values()) !!},
                        backgroundColor: '#10B981',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        }

        const risksBySeverityCtx = document.getElementById('risksBySeverityChart');
        if (risksBySeverityCtx) {
            new Chart(risksBySeverityCtx, {
                type: 'doughnut',
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

        const incidentsByMonthCtx = document.getElementById('incidentsByMonthChart');
        if (incidentsByMonthCtx) {
            const months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
            const incidentsData = {!! json_encode(array_values($incidentsByMonth->toArray())) !!};
            
            new Chart(incidentsByMonthCtx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Ocorrências',
                        data: incidentsData,
                        borderColor: '#EF4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        }
    });
</script>
@endpush
