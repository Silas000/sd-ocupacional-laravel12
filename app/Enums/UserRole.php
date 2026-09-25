<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Medico = 'medico';
    case Tecnico = 'tecnico';
    case Funcionario = 'funcionario';

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrador',
            self::Medico => 'Médico do Trabalho',
            self::Tecnico => 'Técnico de Segurança',
            self::Funcionario => 'Funcionário',
        };
    }

    /**
     * Papéis com acesso à área de saúde (exames e prontuário).
     */
    public function hasHealthAccess(): bool
    {
        return $this === self::Admin || $this === self::Medico;
    }

    /**
     * Papéis com acesso à área de segurança (riscos e ocorrências).
     */
    public function hasSafetyAccess(): bool
    {
        return $this === self::Admin || $this === self::Tecnico;
    }
}
