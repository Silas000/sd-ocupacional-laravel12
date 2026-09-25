<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class HealthRecord extends Model
{
    use Auditable, SoftDeletes;

    protected $table = 'health_records';

    protected $fillable = [
        'user_id',
        'data_registro',
        'descricao',
        'tipo',
        'observacoes',
        'exam_id',
    ];

    protected function casts(): array
    {
        return [
            'data_registro' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Descrição encurtada para as listagens.
     */
    public function resumo(int $limite = 60): string
    {
        return Str::limit((string) $this->descricao, $limite);
    }

    /**
     * @param  array{q?: ?string, tipo?: ?string, de?: ?string, ate?: ?string}  $filtros
     * @return Builder<HealthRecord>
     */
    public function scopeFiltrar(Builder $query, array $filtros): Builder
    {
        return $query
            ->when($filtros['q'] ?? null, fn (Builder $q, $termo) => $q
                ->where(fn (Builder $interno) => $interno
                    ->where('descricao', 'like', '%'.$termo.'%')
                    ->orWhere('observacoes', 'like', '%'.$termo.'%')
                    ->orWhereHas('user', fn (Builder $u) => $u->pesquisa($termo))))
            ->when($filtros['tipo'] ?? null, fn (Builder $q, $valor) => $q->where('tipo', $valor))
            ->when($filtros['de'] ?? null, fn (Builder $q, $valor) => $q->whereDate('data_registro', '>=', $valor))
            ->when($filtros['ate'] ?? null, fn (Builder $q, $valor) => $q->whereDate('data_registro', '<=', $valor));
    }
}
