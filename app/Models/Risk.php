<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Risk extends Model
{
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

    public function incidents()
    {
        return $this->belongsToMany(Incident::class, 'incident_risk');
    }
}
