<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

abstract class BaseRequest extends FormRequest
{
    public function attributes(): array
    {
        return array_merge($this->defaultAttributes(), $this->attributeNames());
    }

    /**
     * Nomes amigáveis usados nas mensagens de validação.
     *
     * @return array<string, string>
     */
    protected function defaultAttributes(): array
    {
        return [
            'user_id' => 'funcionário',
            'data_exame' => 'data do exame',
            'data_vencimento' => 'data de vencimento',
            'data_registro' => 'data do registro',
            'data_ocorrencia' => 'data da ocorrência',
            'data_admissao' => 'data de admissão',
            'data_demissao' => 'data de demissão',
            'medico_responsavel' => 'médico responsável',
            'medidas_preventivas' => 'medidas preventivas',
            'medidas_corretivas' => 'medidas corretivas',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function attributeNames(): array
    {
        return [];
    }

    /**
     * @throws ValidationException
     */
    protected function deny(string $message): never
    {
        throw ValidationException::withMessages(['authorization' => $message]);
    }
}
