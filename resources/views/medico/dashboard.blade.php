@extends('layouts.app')

@section('header', 'Dashboard Médico')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
            <div class="text-gray-500 text-sm">Exames Pendentes</div>
            <div class="text-3xl font-bold text-yellow-600">{{ $examesPendentes }}</div>
        </div>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-red-500">
            <div class="text-gray-500 text-sm">Exames Vencidos</div>
            <div class="text-3xl font-bold text-red-600">{{ $examesVencidos }}</div>
        </div>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
            <div class="text-gray-500 text-sm">Exames Recentes</div>
            <div class="text-3xl font-bold text-gray-900">{{ $examesRecentes->count() }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Exames Recentes do Setor</h2>
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
                        @forelse($examesRecentes as $exam)
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
            <h2 class="text-lg font-semibold mb-4">Riscos por Setor</h2>
            <div class="relative h-64">
                <canvas id="risksBySetorChart"></canvas>
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
                        backgroundColor: '#3B82F6',
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
