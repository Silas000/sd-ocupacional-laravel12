<?php

namespace App\Http\Controllers;

use App\Enums\ExamStatus;
use App\Enums\ExamType;
use App\Enums\IncidentSeverity;
use App\Enums\IncidentType;
use App\Enums\RiskCategory;
use App\Enums\RiskSeverity;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Lista enxuta de colaboradores para os selects de formulário: apenas
     * pessoas ativas, com o mínimo de dados necessário.
     *
     * @return Collection<int, User>
     */
    protected function selectableUsers(): Collection
    {
        return User::query()
            ->ativos()
            ->orderBy('name')
            ->get(['id', 'name', 'setor', 'cargo']);
    }

    /**
     * Setores existentes no cadastro, para alimentar os filtros de listagem.
     *
     * @return array<int, string>
     */
    protected function setoresExistentes(): array
    {
        return User::query()
            ->whereNotNull('setor')
            ->where('setor', '<>', '')
            ->distinct()
            ->orderBy('setor')
            ->pluck('setor')
            ->all();
    }

    /*
     * As opções de enum são entregues prontas às views para que o Blade
     * nunca precise referenciar classes de \App\Enums.
     */

    /**
     * @return array<int, \BackedEnum>
     */
    protected function examTypeOptions(): array
    {
        return ExamType::cases();
    }

    /**
     * @return array<int, \BackedEnum>
     */
    protected function examStatusOptions(): array
    {
        return ExamStatus::cases();
    }

    /**
     * @return array<int, \BackedEnum>
     */
    protected function riskSeverityOptions(): array
    {
        return RiskSeverity::cases();
    }

    /**
     * @return array<int, \BackedEnum>
     */
    protected function riskCategoryOptions(): array
    {
        return RiskCategory::cases();
    }

    /**
     * @return array<int, \BackedEnum>
     */
    protected function incidentTypeOptions(): array
    {
        return IncidentType::cases();
    }

    /**
     * @return array<int, \BackedEnum>
     */
    protected function incidentSeverityOptions(): array
    {
        return IncidentSeverity::cases();
    }

    /**
     * @return array<int, \BackedEnum>
     */
    protected function roleOptions(): array
    {
        return UserRole::cases();
    }
}
