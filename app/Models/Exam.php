<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Exam extends Model
{
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
}
