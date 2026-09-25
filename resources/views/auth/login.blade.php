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
    @php
        $fundo = $appSettings->fundoLogin();
        $destaque = $appSettings->destaque();
    @endphp
    <body class="font-sans antialiased {{ $fundo['classes'] }}">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4">
            <div class="mb-6 text-center">
                @if($appSettings->logoUrl())
                    <img src="{{ $appSettings->logoUrl() }}" alt="{{ $appSettings->nome() }}"
                         class="mx-auto mb-4 h-16 object-contain">
                @else
                    <x-icon :name="$appSettings->icone('dashboard')" class="mx-auto mb-4 w-12 h-12 {{ $appSettings->destaque()['texto'] }}" />
                @endif

                <h1 class="text-3xl font-bold {{ $appSettings->destaque()['texto'] }}">{{ $appSettings->nome() }}</h1>

                @if($appSettings->tagline() !== '')
                    <p class="text-sm {{ $fundo['texto'] }}">{{ $appSettings->tagline() }}</p>
                @endif
            </div>

            <div class="w-full sm:max-w-md px-6 py-8 bg-white shadow-xl rounded-2xl">
                <div class="mb-6 text-center">
                    <h2 class="text-2xl font-bold text-gray-900">Entrar</h2>
                    <p class="text-sm text-gray-600 mt-1">Acesse sua conta para continuar</p>
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg {{ $destaque['foco'] }} transition duration-150 ease-in-out @error('email') border-red-500 @enderror" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg {{ $destaque['foco'] }} transition duration-150 ease-in-out @error('password') border-red-500 @enderror" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-between mb-4">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox" class="rounded border-gray-300 {{ $destaque['texto'] }} shadow-sm focus:ring-2" name="remember">
                            <span class="ms-2 text-sm text-gray-600">Lembrar-me</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="underline text-sm {{ $destaque['texto'] }} hover:underline rounded-md focus:outline-none focus:ring-2"
                               href="{{ route('password.request') }}">
                                Esqueceu a senha?
                            </a>
                        @endif
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white {{ $destaque['normal'] }} {{ $destaque['hover'] }} focus:outline-none focus:ring-2 focus:ring-offset-2 transition duration-150 ease-in-out">
                            Entrar
                        </button>
                    </div>
                </form>

                <p class="mt-6 text-center text-xs text-gray-500">
                    O acesso é concedido pelo administrador do sistema.
                </p>
            </div>
        </div>
    </body>
</html>
