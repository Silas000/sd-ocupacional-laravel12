@props(['paginator'])

@if($paginator->hasPages())
    <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-3">
        <p class="text-sm text-gray-600">
            Mostrando
            <span class="font-medium">{{ $paginator->firstItem() }}</span>
            a
            <span class="font-medium">{{ $paginator->lastItem() }}</span>
            de
            <span class="font-medium">{{ $paginator->total() }}</span>
            {{ $paginator->total() === 1 ? 'registro' : 'registros' }}
        </p>

        <div>{{ $paginator->links() }}</div>
    </div>
@endif
