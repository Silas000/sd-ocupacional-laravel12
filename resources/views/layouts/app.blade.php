<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Saúde Ocupacional') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 flex">
            <!-- Sidebar -->
            <aside class="hidden md:flex md:flex-col md:w-64 bg-gray-800 text-white">
                <div class="h-16 flex items-center justify-center border-b border-gray-700">
                    <a href="{{ route('dashboard') }}" class="text-xl font-bold">Saúde Ocupacional</a>
                </div>

                <nav class="flex-1 px-4 py-6 space-y-2">
                    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                        </svg>
                        Dashboard
                    </a>

                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('users.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('users.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 014 4V7.5h1.25a2.25 2.25 0 012.25 2.25v10.5a2.25 2.25 0 01-2.25 2.25h-13.5a2.25 2.25 0 01-2.25-2.25V9.75a2.25 2.25 0 012.25-2.25H10V8.354a4 4 0 014-4z"></path>
                                </svg>
                                Usuários
                            </a>
                        @endif

                        @if(auth()->user()->isAdmin() || auth()->user()->isMedico())
                            <a href="{{ route('exams.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('exams.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H6A2.25 2.25 0 003.75 6v8.25A2.25 2.25 0 006 16.5h.75m3 0v3.75a2.25 2.25 0 01-2.25 2.25H9.75m-3 0V18"></path>
                                </svg>
                                Exames
                            </a>
                            <a href="{{ route('health.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('health.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 8.25c0-2.485-2.099-4.5-4.5-4.5S12 5.765 12 8.25c0 1.186.46 2.26 1.206 3.048l-.694 3.452 3.452-.694A4.475 4.475 0 0016.5 12.75c2.485 0 4.5-2.015 4.5-4.5zM9 12.75l-2.25 2.25m0 0l2.25 2.25M6.75 15v3.75"></path>
                                </svg>
                                Histórico de Saúde
                            </a>
                        @endif

                        @if(auth()->user()->isAdmin() || auth()->user()->isTecnico())
                            <a href="{{ route('risks.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('risks.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"></path>
                                </svg>
                                Riscos
                            </a>
                            <a href="{{ route('incidents.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('incidents.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"></path>
                                </svg>
                                Ocorrências
                            </a>
                        @endif
                    @endauth
                </nav>

                <div class="p-4 border-t border-gray-700">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center w-full px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"></path>
                            </svg>
                            Sair
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col">
                <!-- Top Navigation -->
                <header class="bg-white shadow-sm border-b border-gray-200">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                        <h2 class="text-xl font-semibold text-gray-800">
                            @yield('header', 'Dashboard')
                        </h2>
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-600">{{ Auth::user()->name }}</span>
                            <span class="px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                {{ ucfirst(Auth::user()->role) }}
                            </span>
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
