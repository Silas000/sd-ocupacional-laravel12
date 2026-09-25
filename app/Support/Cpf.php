<?php

namespace App\Support;

use Illuminate\Support\Facades\Crypt;
use RuntimeException;

/**
 * Utilitários de CPF.
 *
 * O CPF é dado pessoal sensível (LGPD): no banco armazenamos apenas a
 * forma cifrada em `cpf` e um HMAC determinístico em `cpf_hash`, que
 * permite unicidade e buscas sem expor o valor em claro.
 */
final class Cpf
{
    /**
     * Remove tudo que não for dígito.
     */
    public static function normalize(?string $cpf): ?string
    {
        if ($cpf === null) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $cpf) ?? '';

        return $digits === '' ? null : $digits;
    }

    /**
     * Valida os dígitos verificadores do CPF.
     */
    public static function isValid(?string $cpf): bool
    {
        $digits = self::normalize($cpf);

        if ($digits === null || strlen($digits) !== 11) {
            return false;
        }

        if (preg_match('/^(\d)\1{10}$/', $digits) === 1) {
            return false;
        }

        for ($position = 9; $position < 11; $position++) {
            $sum = 0;

            for ($digit = 0; $digit < $position; $digit++) {
                $sum += (int) $digits[$digit] * (($position + 1) - $digit);
            }

            $remainder = (($sum * 10) % 11) % 10;

            if ((int) $digits[$position] !== $remainder) {
                return false;
            }
        }

        return true;
    }

    /**
     * Formata os 11 dígitos no padrão 000.000.000-00.
     */
    public static function format(?string $cpf): ?string
    {
        $digits = self::normalize($cpf);

        if ($digits === null || strlen($digits) !== 11) {
            return $cpf;
        }

        return substr($digits, 0, 3).'.'
            .substr($digits, 3, 3).'.'
            .substr($digits, 6, 3).'-'
            .substr($digits, 9, 2);
    }

    /**
     * HMAC determinístico usado para garantir unicidade sem expor o CPF.
     */
    public static function fingerprint(?string $cpf): ?string
    {
        $digits = self::normalize($cpf);

        if ($digits === null) {
            return null;
        }

        return hash_hmac('sha256', $digits, self::encryptionKey());
    }

    /**
     * Cifra o CPF para persistência.
     */
    public static function encrypt(?string $cpf): ?string
    {
        $digits = self::normalize($cpf);

        return $digits === null ? null : Crypt::encryptString($digits);
    }

    /**
     * Decifra o CPF. Retorna null quando o valor não é cifrado, o que
     * mantém a leitura de registros gravados antes da mudança.
     */
    public static function decrypt(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (RuntimeException) {
            return null;
        }
    }

    private static function encryptionKey(): string
    {
        $key = (string) config('app.key');

        return str_starts_with($key, 'base64:') ? base64_decode(substr($key, 7), true) ?: $key : $key;
    }
}
