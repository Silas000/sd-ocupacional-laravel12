<?php

namespace App\Enums;

enum RiskCategory: string
{
    case Fisico = 'fisico';
    case Quimico = 'quimico';
    case Biologico = 'biologico';
    case Ergonomico = 'ergonomico';
    case Acidente = 'acidente';

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
            self::Fisico => 'Físico',
            self::Quimico => 'Químico',
            self::Biologico => 'Biológico',
            self::Ergonomico => 'Ergonômico',
            self::Acidente => 'Acidente',
        };
    }
}
