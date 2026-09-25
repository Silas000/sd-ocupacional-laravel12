<?php

namespace App\Models;

use App\Enums\ExamStatus;
use App\Enums\ExamType;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends Model
{
    use Auditable, SoftDeletes;

    protected $fillable = [
        'user_id',
        'tipo',
        'data_exame',
        'data_vencimento',
        'status',
        'medico_responsavel',
        'resultado',
        'observacoes',
    ];

    protected function casts(): array
    {
        return [
            'data_exame' => 'date',
            'data_vencimento' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function healthRecords(): HasMany
    {
        return $this->hasMany(HealthRecord::class);
    }

    public function isVencido(): bool
    {
        return $this->data_vencimento !== null && $this->data_vencimento->isPast();
    }

    public function statusEnum(): ?ExamStatus
    {
        return ExamStatus::tryFrom((string) $this->status);
    }

    public function tipoEnum(): ?ExamType
    {
        return ExamType::tryFrom((string) $this->tipo);
    }

    /**
     * @param  Builder<Exam>  $query
     * @return Builder<Exam>
     */
    public function scopePendentes(Builder $query): Builder
    {
        return $query->where('status', ExamStatus::Pendente->value);
    }

    /**
     * @param  Builder<Exam>  $query
     * @return Builder<Exam>
     */
    public function scopeVencidos(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->where(function (Builder $interno) {
                $interno->whereNotNull('data_vencimento')->where('data_vencimento', '<', now());
            })->orWhere(function (Builder $interno) {
                $interno->whereNull('data_vencimento')
                    ->where('data_exame', '<', now()->subYear())
                    ->where('status', '!=', ExamStatus::Cancelado->value);
            });
        });
    }

    /**
     * @param  Builder<Exam>  $query
     * @return Builder<Exam>
     */
    public function scopeDoUsuario(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Exames cujo paciente pertence ao setor informado.
     *
     * @param  Builder<Exam>  $query
     * @return Builder<Exam>
     */
    public function scopeDoSetorDoUsuario(Builder $query, string $setor): Builder
    {
        return $query->whereHas('user', fn (Builder $user) => $user->doSetor($setor));
    }

    /**
     * Filtro de listagem: busca textual no paciente e, opcionalmente,
     * intervalo de datas do exame.
     *
     * @param  array{q?: ?string, status?: ?string, tipo?: ?string, setor?: ?string, de?: ?string, ate?: ?string}  $filtros
     * @return Builder<Exam>
     */
    public function scopeFiltrar(Builder $query, array $filtros): Builder
    {
        return $query
            ->when($filtros['q'] ?? null, fn (Builder $q, $termo) => $q->whereHas('user', fn (Builder $u) => $u->pesquisa($termo)))
            ->when($filtros['status'] ?? null, fn (Builder $q, $valor) => $q->where('status', $valor))
            ->when($filtros['tipo'] ?? null, fn (Builder $q, $valor) => $q->where('tipo', $valor))
            ->when($filtros['setor'] ?? null, fn (Builder $q, $valor) => $q->whereHas('user', fn (Builder $u) => $u->doSetor($valor)))
            ->when($filtros['de'] ?? null, fn (Builder $q, $valor) => $q->whereDate('data_exame', '>=', $valor))
            ->when($filtros['ate'] ?? null, fn (Builder $q, $valor) => $q->whereDate('data_exame', '<=', $valor));
    }
}
