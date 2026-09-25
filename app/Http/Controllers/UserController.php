<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Controllers\Concerns\PaginatesResults;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    use PaginatesResults;

    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $filtros = $this->filtros($request, ['q', 'role', 'setor', 'situacao']);

        $users = User::query()
            ->filtrar($filtros)
            ->orderBy('name')
            ->paginate($this->perPage($request))
            ->withQueryString();

        return view('users.index', [
            'users' => $users,
            'filtros' => $filtros,
            'roles' => $this->roleOptions(),
            'setores' => $this->setoresExistentes(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        return view('users.create', ['roles' => $this->roleOptions()]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        User::create(array_merge(
            $request->validated(),
            ['must_change_password' => true]
        ));

        return redirect()->route('users.index')
            ->with('success', 'Usuário criado com sucesso. A senha deverá ser alterada no primeiro acesso.');
    }

    public function show(User $user): View
    {
        $this->authorize('view', $user);

        $user->load(['exams' => fn ($query) => $query->latest('data_exame')->limit(20),
            'incidents' => fn ($query) => $query->latest('data_ocorrencia')->limit(20),
            'risks' => fn ($query) => $query->latest('id')->limit(20)]);

        return view('users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        return view('users.edit', [
            'user' => $user,
            'roles' => $this->roleOptions(),
        ]);
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())
            && $user->isAdmin()
            && $request->input('role') !== UserRole::Admin->value) {
            return back()
                ->withErrors(['role' => 'Você não pode remover o próprio acesso de administrador.'])
                ->withInput();
        }

        $data = $request->validated();

        if (! $request->filled('password')) {
            unset($data['password']);
        }

        $user->fill($data)->save();

        return redirect()->route('users.index')->with('success', 'Usuário atualizado com sucesso.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'Não é possível excluir o próprio usuário.');
        }

        if ($user->isAdmin() && ! $this->hasOtherAdmin($user)) {
            return back()->with('error', 'Não é possível excluir o último administrador do sistema.');
        }

        $this->authorize('delete', $user);

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Usuário excluído com sucesso.');
    }

    public function restore(int $user): RedirectResponse
    {
        $registro = User::withTrashed()->findOrFail($user);

        $this->authorize('restore', $registro);

        $registro->restore();

        return redirect()->route('users.index')->with('success', 'Usuário restaurado com sucesso.');
    }

    private function hasOtherAdmin(User $user): bool
    {
        return User::query()
            ->where('role', UserRole::Admin->value)
            ->where($user->getKeyName(), '!=', $user->getKey())
            ->exists();
    }
}
