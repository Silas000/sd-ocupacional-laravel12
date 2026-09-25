<?php

namespace App\Models;

use App\Enums\IncidentSeverity;
use App\Enums\IncidentType;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Incident extends Model
{
    use Auditable, SoftDeletes;

    protected $fillable = [
        'user_id',
        'data_ocorrencia',
        'local',
        'descricao',
        'severidade',
        'tipo',
        'medidas_corretivas',
        'observacoes',
    ];

    protected function casts(): array
    {
        return [
            'data_ocorrencia' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function risks(): BelongsToMany
    {
        return $this->belongsToMany(Risk::class, 'incident_risk');
    }

    public function severidadeEnum(): ?IncidentSeverity
    {
        return IncidentSeverity::tryFrom((string) $this->severidade);
    }

    public function tipoEnum(): ?IncidentType
    {
        return IncidentType::tryFrom((string) $this->tipo);
    }

    /**
     * @param  Builder<Incident>  $query
     * @return Builder<Incident>
     */
    public function scopeDoSetor(Builder $query, ?string $setor): Builder
    {
        return $query->whereHas('user', fn (Builder $user) => $user->doSetor($setor));
    }

    /**
     * @param  array{q?: ?string, severidade?: ?string, tipo?: ?string, setor?: ?string, de?: ?string, ate?: ?string}  $filtros
     * @return Builder<Incident>
     */
    public function scopeFiltrar(Builder $query, array $filtros): Builder
    {
        return $query
            ->when($filtros['q'] ?? null, fn (Builder $q, $termo) => $q
                ->where(fn (Builder $interno) => $interno
                    ->where('descricao', 'like', '%'.$termo.'%')
                    ->orWhere('local', 'like', '%'.$termo.'%')
                    ->orWhereHas('user', fn (Builder $u) => $u->pesquisa($termo))))
            ->when($filtros['severidade'] ?? null, fn (Builder $q, $valor) => $q->where('severidade', $valor))
            ->when($filtros['tipo'] ?? null, fn (Builder $q, $valor) => $q->where('tipo', $valor))
            ->when($filtros['setor'] ?? null, fn (Builder $q, $valor) => $q->doSetor($valor))
            ->when($filtros['de'] ?? null, fn (Builder $q, $valor) => $q->whereDate('data_ocorrencia', '>=', $valor))
            ->when($filtros['ate'] ?? null, fn (Builder $q, $valor) => $q->whereDate('data_ocorrencia', '<=', $valor));
    }
}
