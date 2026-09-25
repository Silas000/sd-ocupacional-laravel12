<?php

namespace App\Enums;

enum ExamStatus: string
{
    case Pendente = 'pendente';
    case Realizado = 'realizado';
    case Vencido = 'vencido';
    case Cancelado = 'cancelado';

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
            self::Pendente => 'Pendente',
            self::Realizado => 'Realizado',
            self::Vencido => 'Vencido',
            self::Cancelado => 'Cancelado',
        };
    }

    /**
     * Classes Tailwind para badges na interface.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Pendente => 'bg-yellow-100 text-yellow-800',
            self::Realizado => 'bg-green-100 text-green-800',
            self::Vencido => 'bg-red-100 text-red-800',
            self::Cancelado => 'bg-gray-100 text-gray-800',
        };
    }
}
