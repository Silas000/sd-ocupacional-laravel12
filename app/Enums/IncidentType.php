<?php

namespace App\Enums;

enum IncidentType: string
{
    case Acidente = 'acidente';
    case Incidente = 'incidente';
    case QuaseAcidente = 'quase_acidente';

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
            self::Acidente => 'Acidente de trabalho',
            self::Incidente => 'Incidente',
            self::QuaseAcidente => 'Quase acidente',
        };
    }

    /**
     * Incidentes e quase acidentes são Near Miss e exigem tratamento
     * preventivo; acidentes demandam comunicação imediata.
     */
    public function requiresImmediateNotification(): bool
    {
        return $this === self::Acidente;
    }
}
