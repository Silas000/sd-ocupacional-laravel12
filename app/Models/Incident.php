<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Incident extends Model
{
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
}
