<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Observers\UserObserver;
use App\Support\Cpf;
use App\Traits\Auditable;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use Auditable, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'cpf',
        'cargo',
        'setor',
        'data_admissao',
        'data_demissao',
        'observacoes',
        'must_change_password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'cpf_hash',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'data_admissao' => 'date',
            'data_demissao' => 'date',
            'must_change_password' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::observe(UserObserver::class);
    }

    /**
     * @return array<int, string>
     */
    protected function auditRedactedAttributes(): array
    {
        return array_merge($this->auditRedacted, ['cpf', 'cpf_hash']);
    }

    public function setCpfAttribute(?string $value): void
    {
        $digits = Cpf::normalize($value);

        $this->attributes['cpf'] = $digits === null ? null : Cpf::encrypt($digits);
        $this->attributes['cpf_hash'] = Cpf::fingerprint($digits);
    }

    public function getCpfAttribute(): ?string
    {
        $raw = $this->attributes['cpf'] ?? null;

        if ($raw === null || $raw === '') {
            return null;
        }

        return Cpf::decrypt($raw) ?? $raw;
    }

    public function cpfFormatado(): ?string
    {
        return Cpf::format($this->cpf);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function risks()
    {
        return $this->hasMany(Risk::class);
    }

    public function healthRecords()
    {
        return $this->hasMany(HealthRecord::class);
    }

    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }

    public function passwordHistory()
    {
        return $this->hasMany(PasswordHistory::class);
    }

    public function roleEnum(): UserRole
    {
        return UserRole::tryFrom((string) $this->role) ?? UserRole::Funcionario;
    }

    public function hasRole(UserRole $role): bool
    {
        return $this->roleEnum() === $role;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(UserRole::Admin);
    }

    public function isMedico(): bool
    {
        return $this->hasRole(UserRole::Medico);
    }

    public function isTecnico(): bool
    {
        return $this->hasRole(UserRole::Tecnico);
    }

    public function isFuncionario(): bool
    {
        return $this->hasRole(UserRole::Funcionario);
    }

    public function isAtivo(): bool
    {
        return $this->data_demissao === null || $this->data_demissao->isFuture();
    }

    public function isDemitido(): bool
    {
        return ! $this->isAtivo();
    }

    /**
     * @param  Builder<User>  $query
     * @return Builder<User>
     */
    public function scopeAtivos(Builder $query): Builder
    {
        return $query->where(fn (Builder $q) => $q->whereNull('data_demissao')->orWhere('data_demissao', '>', now()));
    }

    /**
     * @param  Builder<User>  $query
     * @return Builder<User>
     */
    public function scopeDoSetor(Builder $query, ?string $setor): Builder
    {
        return $query->where('setor', $setor);
    }

    /**
     * Busca textual. Um termo com 11 dígitos é tratado como CPF e
     * comparado pelo HMAC, já que o valor gravado está cifrado.
     *
     * @param  Builder<User>  $query
     * @return Builder<User>
     */
    public function scopePesquisa(Builder $query, ?string $termo): Builder
    {
        $termo = trim((string) $termo);

        if ($termo === '') {
            return $query;
        }

        $digitos = preg_replace('/\D/', '', $termo) ?? '';
        $fingerprint = strlen($digitos) === 11 ? Cpf::fingerprint($digitos) : null;

        return $query->where(function (Builder $q) use ($termo, $fingerprint) {
            $q->where('name', 'like', '%'.$termo.'%')
                ->orWhere('email', 'like', '%'.$termo.'%')
                ->orWhere('cargo', 'like', '%'.$termo.'%')
                ->when($fingerprint !== null, fn (Builder $interno) => $interno->orWhere('cpf_hash', $fingerprint));
        });
    }

    /**
     * @param  array{q?: ?string, role?: ?string, setor?: ?string, situacao?: ?string}  $filtros
     * @return Builder<User>
     */
    public function scopeFiltrar(Builder $query, array $filtros): Builder
    {
        return $query
            ->pesquisa($filtros['q'] ?? null)
            ->when($filtros['role'] ?? null, fn (Builder $q, $valor) => $q->where('role', $valor))
            ->when($filtros['setor'] ?? null, fn (Builder $q, $valor) => $q->where('setor', $valor))
            ->when(($filtros['situacao'] ?? null) === 'ativos', fn (Builder $q) => $q->ativos())
            ->when(($filtros['situacao'] ?? null) === 'demitidos', fn (Builder $q) => $q->whereNotNull('data_demissao')->where('data_demissao', '<=', now()));
    }
}
