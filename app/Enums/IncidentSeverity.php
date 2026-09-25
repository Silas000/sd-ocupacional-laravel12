<?php

namespace App\Enums;

enum IncidentSeverity: string
{
    case Leve = 'leve';
    case Moderado = 'moderado';
    case Grave = 'grave';

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
            self::Leve => 'Leve',
            self::Moderado => 'Moderado',
            self::Grave => 'Grave',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Leve => 'bg-green-100 text-green-800',
            self::Moderado => 'bg-yellow-100 text-yellow-800',
            self::Grave => 'bg-red-100 text-red-800',
        };
    }
}
