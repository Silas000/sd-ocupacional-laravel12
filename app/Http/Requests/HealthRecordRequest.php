<?php

namespace App\Http\Requests;

use App\Models\HealthRecord;

class HealthRecordRequest extends BaseRequest
{
    public function authorize(): bool
    {
        $record = $this->route('health');

        return $record instanceof HealthRecord
            ? $this->user()->can('update', $record)
            : $this->user()->can('create', HealthRecord::class);
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'exam_id' => ['nullable', 'exists:exams,id'],
            'data_registro' => ['required', 'date'],
            'descricao' => ['required', 'string'],
            'tipo' => ['required', 'string', 'max:100'],
            'observacoes' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function attributeNames(): array
    {
        return [
            'exam_id' => 'exame',
            'descricao' => 'descrição',
            'tipo' => 'tipo',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'exam_id.exists' => 'O exame selecionado não existe.',
        ];
    }
}
