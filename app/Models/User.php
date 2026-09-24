<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

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

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isMedico(): bool
    {
        return $this->role === 'medico';
    }

    public function isTecnico(): bool
    {
        return $this->role === 'tecnico';
    }

    public function isFuncionario(): bool
    {
        return $this->role === 'funcionario';
    }
}
