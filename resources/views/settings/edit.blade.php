@extends('layouts.app')

@section('header', 'Configurações')

@section('content')
<div class="max-w-5xl">
    <h2 class="text-xl font-semibold mb-1">Aparência do sistema</h2>
    <p class="text-sm text-gray-600 mb-6">
        Estas escolhas valem para todos os usuários e são registradas na auditoria a cada alteração.
    </p>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $erro)
                    <li>{{ $erro }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Identidade</h3>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="app_name" class="block text-sm font-medium text-gray-700 mb-1">
                            Nome do sistema
                        </label>
                        <input type="text" name="app_name" id="app_name" required maxlength="80"
                               value="{{ old('app_name', $atual['app_name']) }}"
                               class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                        <p class="mt-1 text-xs text-gray-500">Aparece no menu, no título da aba e nos relatórios.</p>
                    </div>

                    <div>
                        <label for="app_tagline" class="block text-sm font-medium text-gray-700 mb-1">
                            Subtítulo
                        </label>
                        <input type="text" name="app_tagline" id="app_tagline" maxlength="120"
                               value="{{ old('app_tagline', $atual['app_tagline']) }}"
                               class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                        <p class="mt-1 text-xs text-gray-500">Linha de apoio abaixo do nome na tela de login.</p>
                    </div>
                </div>

                <div class="mt-4">
                    <label for="logo" class="block text-sm font-medium text-gray-700 mb-1">Logotipo</label>
                    <input type="file" name="logo" id="logo" accept="image/png,image/jpeg,image/webp"
                           class="block w-full text-sm text-gray-600 file:mr-4 file:rounded-md file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:text-sm file:font-medium hover:file:bg-gray-200">
                    <p class="mt-1 text-xs text-gray-500">PNG, JPG ou WebP, até 2 MB. Quadrado funciona melhor.</p>

                    @if($appSettings->logoUrl())
                        <div class="mt-3 flex items-center gap-4">
                            <img src="{{ $appSettings->logoUrl() }}" alt="Logotipo atual"
                                 class="h-12 w-12 object-contain border border-gray-200 rounded">
                            <label class="flex items-center gap-2 text-sm text-gray-700">
                                <input type="checkbox" name="remover_logo" value="1"
                                       class="rounded border-gray-300 text-red-600">
                                Remover o logotipo atual
                            </label>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-1">Cores</h3>
                <p class="text-sm text-gray-600 mb-4">Escolha entre as opções da paleta; o resto do layout se ajusta.</p>

                <fieldset class="mb-6">
                    <legend class="block text-sm font-medium text-gray-700 mb-2">Cor do menu</legend>
                    <div class="flex flex-wrap gap-2">
                        @foreach($coresNav as $chave => $cor)
                            <label class="cursor-pointer">
                                <input type="radio" name="nav_color" value="{{ $chave }}"
                                       class="peer sr-only"
                                       @checked(old('nav_color', $atual['nav_color']) === $chave)>
                                <span class="flex items-center gap-2 px-3 py-2 rounded-md border border-gray-300 peer-checked:ring-2 peer-checked:ring-blue-500 peer-checked:border-blue-500">
                                    <span class="w-5 h-5 rounded {{ $cor['fundo'] }}"></span>
                                    {{ $cor['label'] }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('nav_color') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </fieldset>

                <fieldset class="mb-6">
                    <legend class="block text-sm font-medium text-gray-700 mb-2">Cor de destaque</legend>
                    <div class="flex flex-wrap gap-2">
                        @foreach($coresDestaque as $chave => $cor)
                            <label class="cursor-pointer">
                                <input type="radio" name="accent_color" value="{{ $chave }}"
                                       class="peer sr-only"
                                       @checked(old('accent_color', $atual['accent_color']) === $chave)>
                                <span class="flex items-center gap-2 px-3 py-2 rounded-md border border-gray-300 peer-checked:ring-2 peer-checked:ring-blue-500 peer-checked:border-blue-500">
                                    <span class="w-5 h-5 rounded {{ $cor['normal'] }}"></span>
                                    {{ $cor['label'] }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('accent_color') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </fieldset>

                <fieldset>
                    <legend class="block text-sm font-medium text-gray-700 mb-2">Fundo da tela de login</legend>
                    <div class="flex flex-wrap gap-2">
                        @foreach($fundosLogin as $chave => $fundo)
                            <label class="cursor-pointer">
                                <input type="radio" name="login_background" value="{{ $chave }}"
                                       class="peer sr-only"
                                       @checked(old('login_background', $atual['login_background']) === $chave)>
                                <span class="flex items-center gap-2 px-3 py-2 rounded-md border border-gray-300 peer-checked:ring-2 peer-checked:ring-blue-500 peer-checked:border-blue-500">
                                    <span class="w-5 h-5 rounded border border-gray-200 {{ $fundo['classes'] }}"></span>
                                    {{ $fundo['label'] }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('login_background') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </fieldset>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-1">Ícones do menu</h3>
                <p class="text-sm text-gray-600 mb-4">Um ícone para cada item da barra lateral.</p>

                @php($iconesAtuais = old('icons', $atual['nav_icons']))

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($itens as $item => $rotuloItem)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ $rotuloItem }}</label>
                            <select name="icons[{{ $item }}]" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                @foreach($icones as $opcao => $rotuloOpcao)
                                    <option value="{{ $opcao }}"
                                        @selected(($iconesAtuais[$item] ?? null) === $opcao)>
                                        {{ $rotuloOpcao }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                </div>

                @error('icons') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                @error('icons.*') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="bg-blue-600 text-white px-5 py-2.5 rounded-md hover:bg-blue-700 font-medium">
                    Salvar configurações
                </button>

                <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
            </div>
        </div>
    </form>
</div>
@endsection
