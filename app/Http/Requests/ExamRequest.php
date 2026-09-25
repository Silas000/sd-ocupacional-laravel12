<?php

namespace App\Http\Requests;

use App\Enums\ExamStatus;
use App\Enums\ExamType;
use App\Models\Exam;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class ExamRequest extends BaseRequest
{
    public function authorize(): bool
    {
        $exam = $this->route('exam');

        return $exam instanceof Exam
            ? $this->user()->can('update', $exam)
            : $this->user()->can('create', Exam::class);
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'tipo' => ['required', Rule::enum(ExamType::class)],
            'data_exame' => ['required', 'date'],
            'data_vencimento' => ['nullable', 'date', 'after_or_equal:data_exame'],
            'status' => ['required', Rule::enum(ExamStatus::class)],
            'medico_responsavel' => ['nullable', 'string', 'max:100'],
            'resultado' => ['nullable', 'string'],
            'observacoes' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function attributeNames(): array
    {
        return [
            'tipo' => 'tipo do exame',
            'status' => 'status do exame',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tipo.in' => 'O tipo do exame informado não existe.',
            'status.in' => 'O status do exame informado não existe.',
            'data_vencimento.after_or_equal' => 'A data de vencimento deve ser igual ou posterior à data do exame.',
        ];
    }

    /**
     * Quando o vencimento não é informado, calcula-o a partir da
     * periodicidade do tipo de exame.
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled('tipo') && ! $this->filled('data_vencimento') && $this->filled('data_exame')) {
            $months = ExamType::tryFrom((string) $this->input('tipo'))?->defaultValidityMonths() ?? 0;

            if ($months > 0) {
                $this->merge([
                    'data_vencimento' => Carbon::parse($this->input('data_exame'))->addMonths($months)->toDateString(),
                ]);
            }
        }
    }
}
