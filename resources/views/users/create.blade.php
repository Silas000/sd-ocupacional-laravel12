@extends('layouts.app')

@section('header', 'Novo Usuário')

@section('content')
<div class="max-w-2xl">
    <h2 class="text-xl font-semibold mb-4">Cadastrar Usuário</h2>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('users.store') }}" method="POST" class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700">Nome</label>
            <input type="text" name="name" value="{{ old('name') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Senha</label>
                <input type="password" name="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                <p class="mt-1 text-xs text-gray-500">Mínimo de 8 caracteres, com maiúscula, minúscula, número e símbolo.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Confirmar Senha</label>
                <input type="password" name="password_confirmation" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Perfil de acesso</label>
            <select name="role" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                <option value="">Selecione</option>
                @foreach($roles as $role)
                    <option value="{{ $role->value }}" {{ old('role') === $role->value ? 'selected' : '' }}>{{ $role->label() }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">CPF</label>
            <input type="text" name="cpf" value="{{ old('cpf') }}" placeholder="000.000.000-00" maxlength="20" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <p class="mt-1 text-xs text-gray-500">O CPF é armazenado cifrado e não pode ser repetido.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Cargo</label>
            <input type="text" name="cargo" value="{{ old('cargo') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Setor</label>
            <input type="text" name="setor" value="{{ old('setor') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Data de Admissão</label>
                <input type="date" name="data_admissao" value="{{ old('data_admissao') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Data de Demissão</label>
                <input type="date" name="data_demissao" value="{{ old('data_demissao') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Observações</label>
            <textarea name="observacoes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('observacoes') }}</textarea>
        </div>

        <div class="flex space-x-4">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Salvar</button>
            <a href="{{ route('users.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Cancelar</a>
        </div>
    </form>
</div>
@endsection
