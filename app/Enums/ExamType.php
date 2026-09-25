<?php

namespace App\Enums;

enum ExamType: string
{
    case Admissional = 'admissional';
    case Periodico = 'periodico';
    case Demissional = 'demissional';
    case Retorno = 'retorno';
    case Mudanca = 'mudanca';

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
            self::Admissional => 'Admissional',
            self::Periodico => 'Periódico',
            self::Demissional => 'Demissional',
            self::Retorno => 'Retorno ao trabalho',
            self::Mudanca => 'Mudança de função',
        };
    }

    /**
     * Validade padrão do ASO (ASO) em meses por tipo de exame, conforme
     * periodicity usual em saúde ocupacional brasileira.
     */
    public function defaultValidityMonths(): int
    {
        return match ($this) {
            self::Admissional, self::Demissional, self::Retorno, self::Mudanca => 0,
            self::Periodico => 12,
        };
    }
}
