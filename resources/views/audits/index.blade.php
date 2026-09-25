@extends('layouts.app')

@section('header', 'Auditoria')

@section('content')
<div class="flex justify-between items-center mb-4">
    <h2 class="text-xl font-semibold">Trilha de Auditoria</h2>
</div>

<p class="text-sm text-gray-600 mb-4">
    Registro imutável de criações, alterações e exclusões dos dados de saúde ocupacional,
    com autor, data, origem e valores anteriores e novos.
</p>

@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
@endif

<form method="GET" action="{{ route('audits.index') }}" class="bg-white shadow-sm sm:rounded-lg p-4 mb-4 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Registro</label>
        <select name="auditable_type" class="border-gray-300 rounded-md shadow-sm text-sm">
            <option value="">Todos</option>
            @foreach($types as $value => $label)
                <option value="{{ $value }}" {{ request('auditable_type') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Ação</label>
        <select name="event" class="border-gray-300 rounded-md shadow-sm text-sm">
            <option value="">Todas</option>
            @foreach(['created' => 'Criação', 'updated' => 'Alteração', 'deleted' => 'Exclusão', 'restored' => 'Restauração', 'force_deleted' => 'Exclusão definitiva'] as $value => $label)
                <option value="{{ $value }}" {{ request('event') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="flex gap-2">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">Filtrar</button>
        <a href="{{ route('audits.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-300">Limpar</a>
    </div>
</form>

<div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">Quando</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">Autor</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">Registro</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">Ação</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">Campos alterados</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">Origem</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($audits as $linha)
                <tr>
                    <td class="px-4 py-3 whitespace-nowrap">{{ $linha['quando'] }}</td>
                    <td class="px-4 py-3">{{ $linha['autor'] }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">{{ $linha['registro'] }}</td>
                    <td class="px-4 py-3">
                        <x-badge>{{ $linha['event'] }}</x-badge>
                    </td>
                    <td class="px-4 py-3">
                        @if(empty($linha['campos']))
                            <span class="text-xs text-gray-400">—</span>
                        @else
                            <details>
                                <summary class="cursor-pointer text-xs text-blue-600">{{ count($linha['campos']) }} campo(s)</summary>
                                <ul class="mt-1 text-xs space-y-0.5">
                                    @foreach($linha['campos'] as $campo)
                                        <li>
                                            <span class="font-medium">{{ $campo['campo'] }}:</span>
                                            <span class="text-gray-500 line-through">{{ $campo['antes'] }}</span>
                                            <span class="text-gray-400">→</span>
                                            <span>{{ $campo['depois'] }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </details>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $linha['ip'] }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-4 text-center text-gray-500">Nenhum registro de auditoria encontrado.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
