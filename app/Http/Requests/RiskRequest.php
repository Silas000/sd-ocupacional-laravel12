<?php

namespace App\Http\Requests;

use App\Enums\RiskCategory;
use App\Enums\RiskSeverity;
use App\Models\Risk;
use Illuminate\Validation\Rule;

class RiskRequest extends BaseRequest
{
    public function authorize(): bool
    {
        $risk = $this->route('risk');

        return $risk instanceof Risk
            ? $this->user()->can('update', $risk)
            : $this->user()->can('create', Risk::class);
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'setor' => ['nullable', 'string', 'max:100'],
            'nome' => ['required', 'string', 'max:100'],
            'descricao' => ['nullable', 'string'],
            'severidade' => ['required', Rule::enum(RiskSeverity::class)],
            'categoria' => ['required', Rule::enum(RiskCategory::class)],
            'medidas_preventivas' => ['nullable', 'string'],
            'ativo' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function attributeNames(): array
    {
        return [
            'nome' => 'nome do risco',
            'severidade' => 'severidade',
            'categoria' => 'categoria',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'severidade.in' => 'A severidade informada não existe.',
            'categoria.in' => 'A categoria informada não existe.',
        ];
    }

    /**
     * Checkbox desmarcado não chega à requisição.
     */
    protected function prepareForValidation(): void
    {
        $this->merge(['ativo' => $this->boolean('ativo')]);
    }
}
