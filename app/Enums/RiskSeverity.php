<?php

namespace App\Enums;

enum RiskSeverity: string
{
    case Baixo = 'baixo';
    case Medio = 'medio';
    case Alto = 'alto';

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
            self::Baixo => 'Baixo',
            self::Medio => 'Médio',
            self::Alto => 'Alto',
        };
    }

    /**
     * Peso para o cálculo de criticidade (NGR — Numeric Rating Scale).
     */
    public function weight(): int
    {
        return match ($this) {
            self::Baixo => 1,
            self::Medio => 2,
            self::Alto => 3,
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Baixo => 'bg-green-100 text-green-800',
            self::Medio => 'bg-yellow-100 text-yellow-800',
            self::Alto => 'bg-red-100 text-red-800',
        };
    }
}
