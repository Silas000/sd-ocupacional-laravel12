<?php

namespace App\Traits;

use App\Models\Audit;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(fn ($model) => $model->recordAuditEvent('created'));
        static::updated(fn ($model) => $model->recordAuditEvent('updated'));
        static::deleted(fn ($model) => $model->recordAuditEvent('deleted'));

        if (in_array(SoftDeletes::class, class_uses_recursive(static::class), true)) {
            static::restored(fn ($model) => $model->recordAuditEvent('restored'));
            static::forceDeleted(fn ($model) => $model->recordAuditEvent('force_deleted'));
        }
    }

    /**
     * Atributos nunca gravados em claro na auditoria.
     *
     * @var array<int, string>
     */
    protected array $auditRedacted = [
        'password',
        'remember_token',
    ];

    protected function recordAuditEvent(string $event): void
    {
        $current = $this->auditSnapshot($this->getOriginal());

        $new = match ($event) {
            'created' => $this->auditSnapshot($this->getAttributes()),
            'updated' => $this->auditSnapshot($this->getChanges()),
            default => $current,
        };

        Audit::create([
            'auditable_type' => $this->getMorphClass(),
            'auditable_id' => $this->getKey(),
            'event' => $event,
            'old_values' => $event === 'created' ? null : ($current ?: null),
            'new_values' => $new ?: null,
            'user_id' => Auth::id(),
            'user_name' => Auth::user()?->name,
            'ip_address' => request()->ip(),
            'user_agent' => mb_substr((string) request()->userAgent(), 0, 255),
            'url' => mb_substr((string) request()->fullUrl(), 0, 255),
            'created_at' => now(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    protected function auditSnapshot(array $attributes): array
    {
        $redacted = $this->auditRedactedAttributes();
        $snapshot = [];

        foreach ($attributes as $key => $value) {
            $snapshot[$key] = in_array($key, $redacted, true) ? '***' : $value;
        }

        return $snapshot;
    }

    /**
     * @return array<int, string>
     */
    protected function auditRedactedAttributes(): array
    {
        return $this->auditRedacted;
    }
}
