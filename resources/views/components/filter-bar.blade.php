@props(['action', 'method' => 'GET', 'reset' => true])

<form method="GET" action="{{ $action }}" class="bg-white shadow-sm sm:rounded-lg p-4 mb-4">
    <div class="flex flex-wrap gap-3 items-end">
        {{ $slot }}

        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">Filtrar</button>

            @if($reset)
                <a href="{{ $action }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-300">Limpar</a>
            @endif
        </div>
    </div>
</form>
