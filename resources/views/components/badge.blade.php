@props([
    'value' => null,
    'color' => 'bg-gray-100 text-gray-800',
])

<span {{ $attributes->merge(['class' => 'inline-block px-2 py-1 text-xs font-medium rounded-full '.$color]) }}>
    {{ $slot }}
</span>
