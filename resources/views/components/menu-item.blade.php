@props(['href', 'icone' => null, 'active' => false])

@php($nav = $appSettings->nav())

<a href="{{ $href }}"
   @if($active) aria-current="page" @endif
   {{ $attributes->merge(['class' => 'flex items-center px-4 py-3 rounded-lg transition '.($active ? $nav['ativo'].' font-semibold' : $nav['hover'].' opacity-80 hover:opacity-100')]) }}>
    <x-icon :name="$icone" class="w-5 h-5 mr-3 shrink-0" />
    <span class="truncate">{{ $slot }}</span>
</a>
