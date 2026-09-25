<?php

namespace App\Http\Requests;

use App\Enums\IncidentSeverity;
use App\Enums\IncidentType;
use App\Models\Incident;
use Illuminate\Validation\Rule;

class IncidentRequest extends BaseRequest
{
    public function authorize(): bool
    {
        $incident = $this->route('incident');

        return $incident instanceof Incident
            ? $this->user()->can('update', $incident)
            : $this->user()->can('create', Incident::class);
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'data_ocorrencia' => ['required', 'date'],
            'local' => ['required', 'string', 'max:255'],
            'descricao' => ['required', 'string'],
            'severidade' => ['required', Rule::enum(IncidentSeverity::class)],
            'tipo' => ['required', Rule::enum(IncidentType::class)],
            'medidas_corretivas' => ['nullable', 'string'],
            'observacoes' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function attributeNames(): array
    {
        return [
            'local' => 'local',
            'descricao' => 'descrição',
            'severidade' => 'severidade',
            'tipo' => 'tipo',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'severidade.in' => 'A severidade informada não existe.',
            'tipo.in' => 'O tipo de ocorrência informado não existe.',
        ];
    }
}
