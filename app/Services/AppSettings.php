<?php

namespace App\Services;

use App\Models\Setting;
use App\Support\Theme;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/**
 * Leitura e escrita das configurações visuais do sistema.
 *
 * As configurações são consumidas em todas as telas, por isso ficam em
 * cache e são lidas de uma vez só por requisição. A escrita invalida o
 * cache (veja App\Models\Setting), de modo que a alteração vale na
 * requisição seguinte.
 */
class AppSettings
{
    public const CACHE_KEY = 'app-settings:v1';

    /**
     * Nomes das chaves de personalização.
     */
    public const APP_NAME = 'app_name';

    public const APP_TAGLINE = 'app_tagline';

    public const LOGO_PATH = 'logo_path';

    public const NAV_COLOR = 'nav_color';

    public const ACCENT_COLOR = 'accent_color';

    public const LOGIN_BACKGROUND = 'login_background';

    public const NAV_ICONS = 'nav_icons';

    /**
     * Itens da navegação e o ícone padrão de cada um. A ordem de leitura
     * dos ícones é a mesma dos itens do menu lateral do layout.
     *
     * @return array<string, string>
     */
    public function iconesPadrao(): array
    {
        return [
            'dashboard' => 'grade',
            'exams' => 'clipboard',
            'health' => 'pulseira',
            'risks' => 'triangulo',
            'incidents' => 'escudo',
            'users' => 'usuarios',
            'audits' => 'tabela',
            'reports' => 'relatorio',
            'settings' => 'engrenagem',
        ];
    }

    /**
     * Itens da navegação que recebem ícone, com o rótulo exibido no
     * formulário de configuração.
     *
     * @return array<string, string>
     */
    public function itensDoMenu(): array
    {
        return [
            'dashboard' => 'Painel',
            'exams' => 'Exames',
            'health' => 'Histórico de Saúde',
            'risks' => 'Riscos',
            'incidents' => 'Ocorrências',
            'reports' => 'Relatórios',
            'users' => 'Usuários',
            'audits' => 'Auditoria',
            'settings' => 'Configurações',
        ];
    }

    /**
     * Valores usados enquanto a chave não foi gravada na tabela.
     *
     * @return array<string, mixed>
     */
    public function padroes(): array
    {
        return [
            self::APP_NAME => (string) config('app.name', 'Saúde Ocupacional'),
            self::APP_TAGLINE => 'Sistema de Gestão de Saúde do Trabalhador',
            self::LOGO_PATH => null,
            self::NAV_COLOR => 'graphite',
            self::ACCENT_COLOR => 'indigo',
            self::LOGIN_BACKGROUND => 'blue-light',
            self::NAV_ICONS => $this->iconesPadrao(),
        ];
    }

    /**
     * Todas as configurações, já tipadas e validadas contra o catálogo.
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function (): array {
            $valores = $this->padroes();

            foreach (Setting::query()->get() as $linha) {
                if (! array_key_exists($linha->key, $valores)) {
                    continue;
                }

                $valores[$linha->key] = $this->converter($linha, $valores[$linha->key]);
            }

            return $this->normalizar($valores);
        });
    }

    public function get(string $chave, mixed $padrao = null): mixed
    {
        return $this->all()[$chave] ?? $padrao;
    }

    /**
     * Grava uma configuração e invalida o cache.
     */
    public function set(string $chave, mixed $valor, ?int $usuarioId = null): void
    {
        Setting::updateOrCreate(
            ['key' => $chave],
            [
                'value' => $this->serializar($valor),
                'type' => $this->tipoDe($valor),
                'updated_by' => $usuarioId,
            ]
        );
    }

    /**
     * @param  array<string, mixed>  $valores
     */
    public function setMany(array $valores, ?int $usuarioId = null): void
    {
        foreach ($valores as $chave => $valor) {
            $this->set($chave, $valor, $usuarioId);
        }
    }

    /**
     * Restaura a configuração à ausência de linha, voltando ao padrão.
     *
     * A remoção é feita linha a linha de propósito: um DELETE em massa
     * não dispara os eventos do model, e o cache ficaria com o valor
     * antigo até a próxima expiração.
     */
    public function forget(string $chave): void
    {
        Setting::query()
            ->where('key', $chave)
            ->get()
            ->each(static fn (Setting $linha) => $linha->delete());
    }

    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /*
     * Atalhos usados pelas views. Cada um devolve algo já pronto para
     * imprimir, para o Blade não precisar conhecer o catálogo.
     */

    public function nome(): string
    {
        $valor = trim((string) $this->get(self::APP_NAME, ''));

        return $valor !== '' ? $valor : (string) config('app.name', 'Saúde Ocupacional');
    }

    public function tagline(): string
    {
        return trim((string) $this->get(self::APP_TAGLINE, ''));
    }

    public function logoUrl(): ?string
    {
        $caminho = $this->get(self::LOGO_PATH);

        if (! is_string($caminho) || $caminho === '') {
            return null;
        }

        return Storage::disk('public')->url($caminho);
    }

    /**
     * @return array{label: string, fundo: string, borda: string, texto: string, hover: string, ativo: string}
     */
    public function nav(): array
    {
        return Theme::nav($this->get(self::NAV_COLOR));
    }

    /**
     * @return array{label: string, normal: string, hover: string, texto: string, foco: string}
     */
    public function destaque(): array
    {
        return Theme::destaque($this->get(self::ACCENT_COLOR));
    }

    /**
     * @return array{label: string, classes: string, texto: string}
     */
    public function fundoLogin(): array
    {
        return Theme::fundoLogin($this->get(self::LOGIN_BACKGROUND));
    }

    /**
     * Ícone de um item da navegação, com fallback no padrão caso o
     * administrador tenha enviado uma chave desconhecida.
     */
    public function icone(string $item, ?string $padrao = null): string
    {
        $icones = $this->get(self::NAV_ICONS, []);
        $padroes = $this->iconesPadrao();
        $atual = is_array($icones) ? ($icones[$item] ?? null) : null;

        $candidatos = [$atual, $padrao, $padroes[$item] ?? null, 'grade'];

        foreach ($candidatos as $candidato) {
            if (Theme::iconeValido(is_string($candidato) ? $candidato : null)) {
                return $candidato;
            }
        }

        return 'grade';
    }

    /**
     * Caminho SVG de um ícone do catálogo, para o componente de ícone.
     * Devolve null quando a chave não existe, e o componente não imprime
     * nada nesse caso.
     */
    public function caminhoDoIcone(string $nome): ?string
    {
        return Theme::iconeValido($nome) ? Theme::ICONES[$nome] : null;
    }

    /**
     * @param  array<string, mixed>  $valores
     * @return array<string, mixed>
     */
    private function normalizar(array $valores): array
    {
        $icones = $valores[self::NAV_ICONS];
        $icones = is_array($icones) ? $icones : [];

        $valores[self::NAV_ICONS] = array_merge($this->iconesPadrao(), array_intersect_key($icones, $this->iconesPadrao()));
        $valores[self::NAV_COLOR] = Theme::corNavValida($valores[self::NAV_COLOR]) ? $valores[self::NAV_COLOR] : 'graphite';
        $valores[self::ACCENT_COLOR] = Theme::corDestaqueValida($valores[self::ACCENT_COLOR]) ? $valores[self::ACCENT_COLOR] : 'indigo';
        $valores[self::LOGIN_BACKGROUND] = Theme::fundoLoginValido($valores[self::LOGIN_BACKGROUND]) ? $valores[self::LOGIN_BACKGROUND] : 'blue-light';

        return $valores;
    }

    private function converter(Setting $linha, mixed $padrao): mixed
    {
        return match ($linha->type) {
            'boolean' => filter_var($linha->value, FILTER_VALIDATE_BOOLEAN),
            'array' => json_decode((string) $linha->value, true) ?? $padrao,
            default => $linha->value,
        };
    }

    private function serializar(mixed $valor): string
    {
        return match (true) {
            is_array($valor) => (string) json_encode($valor, JSON_UNESCAPED_UNICODE),
            is_bool($valor) => $valor ? '1' : '0',
            $valor === null => '',
            default => (string) $valor,
        };
    }

    private function tipoDe(mixed $valor): string
    {
        return match (true) {
            is_array($valor) => 'array',
            is_bool($valor) => 'boolean',
            default => 'text',
        };
    }
}
