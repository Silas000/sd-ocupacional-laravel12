@props(['name' => null])

@php($caminho = $appSettings?->caminhoDoIcone($name))

@if($caminho)
    <svg {{ $attributes->merge(['class' => 'w-5 h-5']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor"
         stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
        <path d="{{ $caminho }}" />
    </svg>
@endif
