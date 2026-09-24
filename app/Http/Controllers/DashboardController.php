<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Incident;
use App\Models\Risk;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return match ($user->role) {
            'admin' => $this->adminDashboard(),
            'medico' => $this->medicoDashboard(),
            'tecnico' => $this->tecnicoDashboard(),
            'funcionario' => $this->funcionarioDashboard(),
            default => view('dashboard'),
        };
    }

    private function adminDashboard()
    {
        $usersCount = User::count();
        $examsCount = Exam::count();
        $risksCount = Risk::count();
        $incidentsCount = Incident::count();

        $recentExams = Exam::with('user')->latest()->take(5)->get();
        $recentIncidents = Incident::with('user')->latest()->take(5)->get();

        $examsByStatus = Exam::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $risksBySeverity = Risk::selectRaw('severidade, COUNT(*) as total')
            ->groupBy('severidade')
            ->pluck('total', 'severidade');

        $examsVencidos = Exam::where('data_exame', '<', now()->subMonths(12))
            ->orWhere('data_vencimento', '<', now())
            ->count();

        return view('admin.dashboard', compact(
            'usersCount',
            'examsCount',
            'risksCount',
            'incidentsCount',
            'recentExams',
            'recentIncidents',
            'examsByStatus',
            'risksBySeverity',
            'examsVencidos'
        ));
    }

    private function medicoDashboard()
    {
        $examesPendentes = Exam::where('status', 'pendente')->count();
        $examesVencidos = Exam::where('data_vencimento', '<', now())->count();

        $examesRecentes = Exam::with('user')
            ->whereHas('user', function ($query) {
                $query->where('setor', auth()->user()->setor);
            })
            ->latest()
            ->take(10)
            ->get();

        $risksBySetor = Risk::selectRaw('setor, COUNT(*) as total')
            ->groupBy('setor')
            ->pluck('total', 'setor');

        return view('medico.dashboard', compact(
            'examesPendentes',
            'examesVencidos',
            'examesRecentes',
            'risksBySetor'
        ));
    }

    private function tecnicoDashboard()
    {
        $risks = Risk::all();
        $incidents = Incident::all();

        $risksBySetor = Risk::selectRaw('setor, COUNT(*) as total')
            ->groupBy('setor')
            ->pluck('total', 'setor');

        $risksBySeverity = Risk::selectRaw('severidade, COUNT(*) as total')
            ->groupBy('severidade')
            ->pluck('total', 'severidade');

        $incidentsByMonth = Incident::selectRaw('MONTH(data_ocorrencia) as mes, COUNT(*) as total')
            ->whereYear('data_ocorrencia', now()->year)
            ->groupBy('mes')
            ->pluck('total', 'mes');

        $recentIncidents = Incident::with('user')->latest()->take(5)->get();

        return view('tecnico.dashboard', compact(
            'risks',
            'incidents',
            'risksBySetor',
            'risksBySeverity',
            'incidentsByMonth',
            'recentIncidents'
        ));
    }

    private function funcionarioDashboard()
    {
        $user = auth()->user();

        $examesPessoais = Exam::where('user_id', $user->id)->latest()->get();

        $examesVencidos = Exam::where('user_id', $user->id)
            ->where('data_vencimento', '<', now())
            ->count();

        $risksSetor = Risk::where('setor', $user->setor)->get();

        $incidentsPessoais = Incident::where('user_id', $user->id)->latest()->get();

        return view('funcionario.dashboard', compact(
            'examesPessoais',
            'examesVencidos',
            'risksSetor',
            'incidentsPessoais'
        ));
    }
}

