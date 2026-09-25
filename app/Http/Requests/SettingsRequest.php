<?php

namespace App\Http\Requests;

use App\Support\Theme;
use Illuminate\Validation\Rule;

class SettingsRequest extends BaseRequest
{
    /**
     * A rota já exige o papel de administrador; a checagem aqui mantém a
     * tela fechada mesmo se a rota mudar no futuro.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'app_name' => ['required', 'string', 'min:3', 'max:80'],
            'app_tagline' => ['nullable', 'string', 'max:120'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'remover_logo' => ['nullable', 'boolean'],
            'nav_color' => ['required', Rule::in(array_keys(Theme::NAV))],
            'accent_color' => ['required', Rule::in(array_keys(Theme::DESTAQUE))],
            'login_background' => ['required', Rule::in(array_keys(Theme::FUNDOS_LOGIN))],
            'icons' => ['required', 'array'],
            'icons.*' => ['required', Rule::in(Theme::icones())],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function attributeNames(): array
    {
        return [
            'app_name' => 'nome do sistema',
            'app_tagline' => 'subtítulo',
            'logo' => 'logotipo',
            'remover_logo' => 'remoção do logotipo',
            'nav_color' => 'cor do menu',
            'accent_color' => 'cor de destaque',
            'login_background' => 'fundo da tela de login',
            'icons' => 'ícones do menu',
            'icons.*' => 'ícone',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'logo.image' => 'O logotipo deve ser uma imagem.',
            'logo.mimes' => 'O logotipo deve ser PNG, JPG ou WebP.',
            'logo.max' => 'O logotipo deve ter no máximo 2 MB.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function validated($key = null, $default = null): array
    {
        /** @var array<string, mixed> $dados */
        $dados = parent::validated($key, $default);

        unset($dados['logo'], $dados['remover_logo']);

        return $dados;
    }

    /**
     * @return array<string, string>
     */
    public function icones(): array
    {
        /** @var array<string, string> $icones */
        $icones = $this->validated()['icons'] ?? [];

        return array_map('strval', $icones);
    }
}
