@extends('layouts.app')

@section('header', 'Relatórios')

@section('content')
<div class="max-w-5xl">
    <h2 class="text-xl font-semibold mb-1">Relatórios</h2>
    <p class="text-sm text-gray-600 mb-6">
        Escolha um relatório para revisar os dados na tela e exportar em PDF ou Excel.
        Toda exportação fica registrada na auditoria.
    </p>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($disponiveis as $relatorio)
            <a href="{{ route('reports.show', $relatorio->slug) }}"
               class="block bg-white shadow-sm sm:rounded-lg p-5 hover:shadow-md transition">
                <div class="flex items-start gap-3">
                    <span class="shrink-0 w-10 h-10 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center">
                        <x-icon :name="$relatorio->icone" />
                    </span>
                    <div class="min-w-0">
                        <h3 class="font-semibold text-gray-800">{{ $relatorio->titulo }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ $relatorio->descricao }}</p>
                    </div>
                </div>
            </a>
        @empty
            <p class="text-sm text-gray-600">Nenhum relatório disponível para o seu papel.</p>
        @endforelse
    </div>
</div>
@endsection
