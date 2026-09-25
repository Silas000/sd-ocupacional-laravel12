<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingsRequest;
use App\Services\AppSettings;
use App\Support\Theme;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __construct(private readonly AppSettings $settings) {}

    public function edit(Request $request): View
    {
        $this->authorize('viewAny', self::class);

        return view('settings.edit', [
            'atual' => $this->settings->all(),
            'coresNav' => Theme::NAV,
            'coresDestaque' => Theme::DESTAQUE,
            'fundosLogin' => Theme::FUNDOS_LOGIN,
            'itens' => $this->settings->itensDoMenu(),
            'icones' => Theme::rotulosIcones(),
        ]);
    }

    public function update(SettingsRequest $request): RedirectResponse
    {
        $dados = $request->validated();

        $this->authorize('update', self::class);

        $usuarioId = $request->user()?->id;

        $this->settings->setMany([
            AppSettings::APP_NAME => trim((string) $dados['app_name']),
            AppSettings::APP_TAGLINE => trim((string) ($dados['app_tagline'] ?? '')),
            AppSettings::NAV_COLOR => $dados['nav_color'],
            AppSettings::ACCENT_COLOR => $dados['accent_color'],
            AppSettings::LOGIN_BACKGROUND => $dados['login_background'],
            AppSettings::NAV_ICONS => $request->icones(),
        ], $usuarioId);

        $this->tratarLogotipo($request, $usuarioId);

        return redirect()
            ->route('settings.edit')
            ->with('success', 'Configurações salvas com sucesso.');
    }

    /**
     * Upload e remoção do logotipo ficam fora do que é persistido em
     * SettingsRequest::validated, porque envolvem arquivo.
     */
    private function tratarLogotipo(SettingsRequest $request, ?int $usuarioId): void
    {
        $anterior = $this->settings->get(AppSettings::LOGO_PATH);
        $remover = $request->boolean('remover_logo');

        if ($remover) {
            $this->apagarArquivo(is_string($anterior) ? $anterior : null);
            $this->settings->forget(AppSettings::LOGO_PATH);

            if (! $request->hasFile('logo')) {
                return;
            }
        }

        $arquivo = $request->file('logo');

        if ($arquivo === null || ! $arquivo->isValid()) {
            return;
        }

        $caminho = $arquivo->store('branding', 'public');

        if ($caminho === false) {
            return;
        }

        if (! $remover) {
            $this->apagarArquivo(is_string($anterior) ? $anterior : null);
        }

        $this->settings->set(AppSettings::LOGO_PATH, $caminho, $usuarioId);
    }

    private function apagarArquivo(?string $caminho): void
    {
        if ($caminho === null || $caminho === '' || ! str_starts_with($caminho, 'branding/')) {
            return;
        }

        Storage::disk('public')->delete($caminho);
    }
}
