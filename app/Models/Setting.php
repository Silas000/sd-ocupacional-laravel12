<?php

namespace App\Models;

use App\Services\AppSettings;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    use Auditable;

    protected $fillable = [
        'key',
        'value',
        'type',
        'updated_by',
    ];

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Toda escrita invalida o cache: as configurações são lidas em todas
     * as telas, e uma alteração precisa valer na requisição seguinte.
     */
    protected static function booted(): void
    {
        $invalidar = fn () => app(AppSettings::class)->flush();

        static::saved($invalidar);
        static::deleted($invalidar);
    }
}
