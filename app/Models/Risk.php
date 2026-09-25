<?php

namespace App\Models;

use App\Enums\RiskCategory;
use App\Enums\RiskSeverity;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Risk extends Model
{
    use Auditable, SoftDeletes;

    protected $fillable = [
        'user_id',
        'setor',
        'nome',
        'descricao',
        'severidade',
        'categoria',
        'medidas_preventivas',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function incidents(): BelongsToMany
    {
        return $this->belongsToMany(Incident::class, 'incident_risk');
    }

    public function severidadeEnum(): ?RiskSeverity
    {
        return RiskSeverity::tryFrom((string) $this->severidade);
    }

    public function categoriaEnum(): ?RiskCategory
    {
        return RiskCategory::tryFrom((string) $this->categoria);
    }

    /**
     * @param  Builder<Risk>  $query
     * @return Builder<Risk>
     */
    public function scopeAtivos(Builder $query): Builder
    {
        return $query->where('ativo', true);
    }

    /**
     * @param  Builder<Risk>  $query
     * @return Builder<Risk>
     */
    public function scopeDoSetor(Builder $query, ?string $setor): Builder
    {
        return $query->where('setor', $setor);
    }

    /**
     * @param  Builder<Risk>  $query
     * @return Builder<Risk>
     */
    public function scopePorSetorComTotais(Builder $query): Builder
    {
        return $query
            ->selectRaw('setor, COUNT(*) as total')
            ->whereNotNull('setor')
            ->where('setor', '<>', '')
            ->groupBy('setor')
            ->orderByDesc('total');
    }

    /**
     * @param  array{q?: ?string, severidade?: ?string, categoria?: ?string, setor?: ?string, ativo?: ?string}  $filtros
     * @return Builder<Risk>
     */
    public function scopeFiltrar(Builder $query, array $filtros): Builder
    {
        return $query
            ->when($filtros['q'] ?? null, fn (Builder $q, $termo) => $q
                ->where(fn (Builder $interno) => $interno
                    ->where('nome', 'like', '%'.$termo.'%')
                    ->orWhere('descricao', 'like', '%'.$termo.'%')
                    ->orWhereHas('user', fn (Builder $u) => $u->pesquisa($termo))))
            ->when($filtros['severidade'] ?? null, fn (Builder $q, $valor) => $q->where('severidade', $valor))
            ->when($filtros['categoria'] ?? null, fn (Builder $q, $valor) => $q->where('categoria', $valor))
            ->when($filtros['setor'] ?? null, fn (Builder $q, $valor) => $q->where('setor', $valor))
            ->when(isset($filtros['ativo']), fn (Builder $q) => $q->where('ativo', $filtros['ativo'] === '1'));
    }
}
