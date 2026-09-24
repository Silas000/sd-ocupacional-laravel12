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
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-blue-50 to-indigo-100">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-indigo-800">Saúde Ocupacional</h1>
                <p class="text-sm text-gray-600 text-center">Sistema de Gestão de Saúde do Trabalhador</p>
            </div>

            <div class="w-full sm:max-w-lg px-6 py-8 bg-white shadow-xl rounded-2xl">
                <div class="mb-6 text-center">
                    <h2 class="text-2xl font-bold text-gray-900">Criar Conta</h2>
                    <p class="text-sm text-gray-600 mt-1">Preencha os dados para se cadastrar</p>
                </div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome Completo</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out @error('name') border-red-500 @enderror" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out @error('email') border-red-500 @enderror" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Perfil de Acesso</label>
                        <select id="role" name="role" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out @error('role') border-red-500 @enderror">
                            <option value="funcionario" {{ old('role') == 'funcionario' ? 'selected' : '' }}>Funcionário</option>
                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="cpf" class="block text-sm font-medium text-gray-700 mb-1">CPF</label>
                            <input id="cpf" type="text" name="cpf" value="{{ old('cpf') }}" autocomplete="off"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out @error('cpf') border-red-500 @enderror" />
                            <x-input-error :messages="$errors->get('cpf')" class="mt-2" />
                        </div>
                        <div>
                            <label for="cargo" class="block text-sm font-medium text-gray-700 mb-1">Cargo</label>
                            <input id="cargo" type="text" name="cargo" value="{{ old('cargo') }}" autocomplete="off"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out @error('cargo') border-red-500 @enderror" />
                            <x-input-error :messages="$errors->get('cargo')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="setor" class="block text-sm font-medium text-gray-700 mb-1">Setor</label>
                        <input id="setor" type="text" name="setor" value="{{ old('setor') }}" autocomplete="off"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out @error('setor') border-red-500 @enderror" />
                        <x-input-error :messages="$errors->get('setor')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="data_admissao" class="block text-sm font-medium text-gray-700 mb-1">Data de Admissão</label>
                            <input id="data_admissao" type="date" name="data_admissao" value="{{ old('data_admissao') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out @error('data_admissao') border-red-500 @enderror" />
                            <x-input-error :messages="$errors->get('data_admissao')" class="mt-2" />
                        </div>
                        <div>
                            <label for="data_demissao" class="block text-sm font-medium text-gray-700 mb-1">Data de Demissão</label>
                            <input id="data_demissao" type="date" name="data_demissao" value="{{ old('data_demissao') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out @error('data_demissao') border-red-500 @enderror" />
                            <x-input-error :messages="$errors->get('data_demissao')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="observacoes" class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                        <textarea id="observacoes" name="observacoes" rows="2"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out @error('observacoes') border-red-500 @enderror">{{ old('observacoes') }}</textarea>
                        <x-input-error :messages="$errors->get('observacoes')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out @error('password') border-red-500 @enderror" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="mb-6">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Senha</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out" />
                    </div>

                    <div class="flex items-center justify-between">
                        <a class="underline text-sm text-indigo-600 hover:text-indigo-800 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                            Já tem uma conta?
                        </a>

                        <button type="submit" class="flex justify-center py-2 px-6 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                            Cadastrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>
