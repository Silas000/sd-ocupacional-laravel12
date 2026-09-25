<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $appSettings->nome() }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 flex">
            <x-menu-lateral />

            <!-- Main Content -->
            <div class="flex-1 flex flex-col min-w-0">
                <!-- Marca em telas pequenas, onde o menu lateral fica oculto -->
                <div class="md:hidden flex items-center gap-3 px-4 h-16 {{ $appSettings->nav()['fundo'] }} {{ $appSettings->nav()['texto'] }}">
                    @if($appSettings->logoUrl())
                        <img src="{{ $appSettings->logoUrl() }}" alt="" class="h-8 max-w-28 object-contain">
                    @endif
                    <span class="font-bold">{{ $appSettings->nome() }}</span>
                </div>

                <!-- Top Navigation -->
                <header class="bg-white shadow-sm border-b border-gray-200">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center gap-4">
                        <h2 class="text-xl font-semibold text-gray-800">
                            @yield('header', 'Dashboard')
                        </h2>

                        <div class="flex items-center space-x-4">
                            @auth
                                @if(auth()->user()->isAdmin() || auth()->user()->isMedico() || auth()->user()->isTecnico())
                                    <a href="{{ route('reports.index') }}" title="Relatórios"
                                       class="text-sm {{ $appSettings->destaque()['texto'] }} hover:underline">
                                        Relatórios
                                    </a>
                                @endif

                                <a href="{{ route('profile.edit') }}" class="text-sm text-gray-600 hover:text-gray-900">
                                    Perfil
                                </a>
                            @endauth

                            <span class="hidden sm:inline text-sm text-gray-600">{{ Auth::user()->name }}</span>
                            <x-badge color="bg-blue-100 text-blue-800">
                                {{ auth()->user()->roleEnum()->label() }}
                            </x-badge>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 p-6">
                    @yield('content')
                </main>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
