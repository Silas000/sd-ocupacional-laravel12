<?php

namespace App\Http\Controllers;

use App\Services\DashboardPresenter;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $presenter = new DashboardPresenter(request()->user());

        [$view, $dados] = match (request()->user()->roleEnum()->value) {
            'admin' => ['admin.dashboard', $presenter->admin()],
            'medico' => ['medico.dashboard', $presenter->medico()],
            'tecnico' => ['tecnico.dashboard', $presenter->tecnico()],
            default => ['funcionario.dashboard', $presenter->funcionario()],
        };

        return view($view, $dados);
    }
}
