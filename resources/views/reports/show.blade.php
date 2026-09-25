@extends('layouts.app')

@section('header', $relatorio->titulo)

@section('content')
<div class="mb-4">
    <a href="{{ route('reports.index') }}" class="text-sm text-gray-600 hover:text-gray-900">&larr; Relatórios</a>
</div>

<div class="flex flex-wrap justify-between items-start gap-3 mb-4">
    <div>
        <h2 class="text-xl font-semibold">{{ $relatorio->titulo }}</h2>
        <p class="text-sm text-gray-600">{{ $relatorio->descricao }}</p>
    </div>

    <div class="flex gap-2">
        <a href="{{ route('reports.pdf', array_merge(['relatorio' => $relatorio->slug], $filtros)) }}"
           class="bg-red-600 text-white px-4 py-2 rounded-md text-sm hover:bg-red-700">Exportar PDF</a>

        <a href="{{ route('reports.excel', array_merge(['relatorio' => $relatorio->slug], $filtros)) }}"
           class="bg-emerald-600 text-white px-4 py-2 rounded-md text-sm hover:bg-emerald-700">Exportar Excel</a>
    </div>
</div>

<form method="GET" action="{{ route('reports.show', $relatorio->slug) }}"
      class="bg-white shadow-sm sm:rounded-lg p-4 mb-4">
    <div class="flex flex-wrap gap-3 items-end">
        @foreach($relatorio->filtros as $chave => $filtro)
            <div @class(['flex-1 min-w-52' => ($filtro['tipo'] ?? '') === 'texto'])>
                <label for="filtro-{{ $chave }}" class="block text-xs font-medium text-gray-600 mb-1">
                    {{ $filtro['rotulo'] }}
                </label>

                @if(($filtro['tipo'] ?? '') === 'select')
                    <select id="filtro-{{ $chave }}" name="{{ $chave }}"
                            class="border-gray-300 rounded-md shadow-sm text-sm">
                        <option value="">Todos</option>
                        @foreach(($opcoes[$chave] ?? []) as $valor => $rotulo)
                            <option value="{{ $valor }}" @selected(($filtros[$chave] ?? '') === $valor)>{{ $rotulo }}</option>
                        @endforeach
                    </select>
                @elseif(($filtro['tipo'] ?? '') === 'booleano')
                    <select id="filtro-{{ $chave }}" name="{{ $chave }}"
                            class="border-gray-300 rounded-md shadow-sm text-sm">
                        <option value="">Todos</option>
                        <option value="1" @selected(($filtros[$chave] ?? '') === '1')>Sim</option>
                        <option value="0" @selected(($filtros[$chave] ?? '') === '0')>Não</option>
                    </select>
                @else
                    <input id="filtro-{{ $chave }}" name="{{ $chave }}"
                           type="{{ ($filtro['tipo'] ?? '') === 'data' ? 'date' : 'search' }}"
                           value="{{ $filtros[$chave] ?? '' }}"
                           class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                @endif
            </div>
        @endforeach

        @if($podeVerExcluidos)
            <div>
                <label class="flex items-center gap-2 text-xs font-medium text-gray-600 mb-1 h-6">
                    <input type="checkbox" name="incluir_excluidos" value="1"
                           class="rounded border-gray-300"
                           @checked(($filtros['incluir_excluidos'] ?? '') === '1')>
                    Incluir excluídos
                </label>
            </div>
        @endif

        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">Filtrar</button>
            <a href="{{ route('reports.show', $relatorio->slug) }}"
               class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-300">Limpar</a>
        </div>
    </div>
</form>

@if($resumoFiltros !== [])
    <div class="bg-blue-50 border border-blue-200 text-blue-900 text-sm rounded-md px-4 py-2 mb-4">
        <span class="font-medium">Filtros aplicados:</span>
        @foreach($resumoFiltros as $rotulo => $valor)
            <span class="ml-2">{{ $rotulo }}: <strong>{{ $valor }}</strong></span>
        @endforeach
    </div>
@endif

<div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                @foreach($relatorio->colunas as $coluna)
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ $coluna['rotulo'] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($dados as $linha)
                <tr>
                    @foreach($relatorio->colunas as $chave => $coluna)
                        <td class="px-4 py-3 text-sm">{{ $linha[$chave] }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($relatorio->colunas) }}" class="px-4 py-6 text-center text-gray-500">
                        Nenhum registro encontrado para os filtros informados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<x-pagination-summary :paginator="$linhas" />
@endsection
