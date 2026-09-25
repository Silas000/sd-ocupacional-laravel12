<?php

namespace App\Support;

/**
 * Catálogo fechado de cores e ícones aceitos na configuração visual.
 *
 * A lista é propositalmente fixa: o administrador escolhe entre opções
 * conhecidas, nunca informa uma classe CSS ou uma URL arbitrária. Isso
 * mantém XSS e classes injetadas fora do alcance mesmo com a conta
 * administrativa comprometida. As chaves são sempre ASCII porque viajam
 * em query string e na tabela de configurações.
 *
 * Todas as classes aqui listadas aparecem em telas renderizadas, então o
 * Tailwind as compila; não é preciso safelist.
 */
final class Theme
{
    /**
     * Cores da barra de navegação.
     *
     * @var array<string, array{label: string, fundo: string, borda: string, texto: string, hover: string, ativo: string}>
     */
    public const NAV = [
        'graphite' => [
            'label' => 'Grafite',
            'fundo' => 'bg-gray-800',
            'borda' => 'border-gray-700',
            'texto' => 'text-gray-100',
            'hover' => 'hover:bg-gray-700',
            'ativo' => 'bg-gray-900',
        ],
        'slate' => [
            'label' => 'Ardósia',
            'fundo' => 'bg-slate-900',
            'borda' => 'border-slate-700',
            'texto' => 'text-slate-100',
            'hover' => 'hover:bg-slate-800',
            'ativo' => 'bg-slate-950',
        ],
        'blue' => [
            'label' => 'Azul',
            'fundo' => 'bg-blue-900',
            'borda' => 'border-blue-700',
            'texto' => 'text-blue-50',
            'hover' => 'hover:bg-blue-800',
            'ativo' => 'bg-blue-950',
        ],
        'navy' => [
            'label' => 'Marinho',
            'fundo' => 'bg-indigo-900',
            'borda' => 'border-indigo-700',
            'texto' => 'text-indigo-50',
            'hover' => 'hover:bg-indigo-800',
            'ativo' => 'bg-indigo-950',
        ],
        'emerald' => [
            'label' => 'Verde',
            'fundo' => 'bg-emerald-900',
            'borda' => 'border-emerald-700',
            'texto' => 'text-emerald-50',
            'hover' => 'hover:bg-emerald-800',
            'ativo' => 'bg-emerald-950',
        ],
        'teal' => [
            'label' => 'Petróleo',
            'fundo' => 'bg-teal-900',
            'borda' => 'border-teal-700',
            'texto' => 'text-teal-50',
            'hover' => 'hover:bg-teal-800',
            'ativo' => 'bg-teal-950',
        ],
        'rose' => [
            'label' => 'Vinho',
            'fundo' => 'bg-rose-900',
            'borda' => 'border-rose-700',
            'texto' => 'text-rose-50',
            'hover' => 'hover:bg-rose-800',
            'ativo' => 'bg-rose-950',
        ],
        'amber' => [
            'label' => 'Âmbar',
            'fundo' => 'bg-amber-950',
            'borda' => 'border-amber-800',
            'texto' => 'text-amber-50',
            'hover' => 'hover:bg-amber-900',
            'ativo' => 'bg-amber-900',
        ],
    ];

    /**
     * Fundos disponíveis para a tela de login.
     *
     * @var array<string, array{label: string, classes: string, texto: string}>
     */
    public const FUNDOS_LOGIN = [
        'light' => [
            'label' => 'Claro',
            'classes' => 'bg-gray-100',
            'texto' => 'text-gray-600',
        ],
        'blue-light' => [
            'label' => 'Azul claro',
            'classes' => 'bg-gradient-to-br from-blue-50 to-indigo-100',
            'texto' => 'text-gray-600',
        ],
        'green-light' => [
            'label' => 'Verde claro',
            'classes' => 'bg-gradient-to-br from-emerald-50 to-teal-100',
            'texto' => 'text-gray-600',
        ],
        'neutral' => [
            'label' => 'Neutro',
            'classes' => 'bg-gradient-to-br from-gray-50 to-gray-200',
            'texto' => 'text-gray-600',
        ],
        'dark' => [
            'label' => 'Escuro',
            'classes' => 'bg-gradient-to-br from-gray-800 to-gray-900',
            'texto' => 'text-gray-300',
        ],
    ];

    /**
     * Cores de destaque: botões, links, foco de inputs e título do login.
     *
     * @var array<string, array{label: string, normal: string, hover: string, texto: string, foco: string}>
     */
    public const DESTAQUE = [
        'blue' => [
            'label' => 'Azul',
            'normal' => 'bg-blue-600',
            'hover' => 'hover:bg-blue-700',
            'texto' => 'text-blue-600',
            'foco' => 'focus:ring-blue-500 focus:border-blue-500',
        ],
        'indigo' => [
            'label' => 'Índigo',
            'normal' => 'bg-indigo-600',
            'hover' => 'hover:bg-indigo-700',
            'texto' => 'text-indigo-600',
            'foco' => 'focus:ring-indigo-500 focus:border-indigo-500',
        ],
        'emerald' => [
            'label' => 'Verde',
            'normal' => 'bg-emerald-600',
            'hover' => 'hover:bg-emerald-700',
            'texto' => 'text-emerald-600',
            'foco' => 'focus:ring-emerald-500 focus:border-emerald-500',
        ],
        'amber' => [
            'label' => 'Âmbar',
            'normal' => 'bg-amber-600',
            'hover' => 'hover:bg-amber-700',
            'texto' => 'text-amber-600',
            'foco' => 'focus:ring-amber-500 focus:border-amber-500',
        ],
        'rose' => [
            'label' => 'Vermelho',
            'normal' => 'bg-rose-600',
            'hover' => 'hover:bg-rose-700',
            'texto' => 'text-rose-600',
            'foco' => 'focus:ring-rose-500 focus:border-rose-500',
        ],
        'gray' => [
            'label' => 'Cinza',
            'normal' => 'bg-gray-600',
            'hover' => 'hover:bg-gray-700',
            'texto' => 'text-gray-600',
            'foco' => 'focus:ring-gray-500 focus:border-gray-500',
        ],
    ];

    /**
     * Ícones disponíveis, todos desenhados no mesmo traço de 24px.
     *
     * @var array<string, string>
     */
    public const ICONES = [
        'grade' => 'M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z',
        'clipboard' => 'M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h2.25m-2.25 3h2.25m-2.25 3h2.25m3-6.75h5.25m-5.25 3h5.25m-5.25 3h5.25',
        'pulseira' => 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z',
        'triangulo' => 'M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z',
        'escudo' => 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z',
        'usuarios' => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z',
        'cadeado' => 'M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z',
        'engrenagem' => 'M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828c-.424-.35-.534-.954-.26-1.43l1.297-2.247a1.125 1.125 0 011.369-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.281zM15 12a3 3 0 11-6 0 3 3 0 016 0z',
        'relatorio' => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z',
        'tabela' => 'M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0112 18.375m9.75-12.75V5.625c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125m0 0h1.5c.621 0 1.125-.504 1.125-1.125m-1.5 0h7.5m6-12.75v1.5c0 .621.504 1.125 1.125 1.125M21 12.75h-7.5m0-6.75h3M12 3v3m0 0h3m-3 0h-3m3 3h-3',
        'envelope' => 'M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75',
        'estrela' => 'M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.562.562 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z',
        'bussola' => 'M12 21a9 9 0 100-18 9 9 0 000 18zm0 0a8.949 8.949 0 004.951-1.488A3.987 3.987 0 0013 16h-2a3.987 3.987 0 00-3.951 3.512A8.949 8.949 0 0012 21zM12 14l3.75-3.75-3.75-1.5-3.75 3.75 3.75 1.5z',
        'balanca' => 'M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0012 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 01-2.031.352 5.988 5.988 0 01-2.031-.352c-.483-.174-.711-.703-.59-1.202L18.75 4.971zm-16.5.52c.99-.203 1.99-.377 3-.52m0 0l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.989 5.989 0 01-2.031.352 5.989 5.989 0 01-2.031-.352c-.483-.174-.711-.703-.59-1.202L5.25 4.971z',
    ];

    /**
     * Rótulos amigáveis dos ícones, para o formulário de configuração.
     *
     * @var array<string, string>
     */
    public const ROTULOS_ICONES = [
        'grade' => 'Painel',
        'clipboard' => 'Prancheta',
        'pulseira' => 'Colaborador',
        'triangulo' => 'Alerta',
        'escudo' => 'Proteção',
        'usuarios' => 'Equipe',
        'cadeado' => 'Segurança',
        'engrenagem' => 'Configurações',
        'relatorio' => 'Relatório',
        'tabela' => 'Tabela',
        'envelope' => 'Comunicado',
        'estrela' => 'Destaque',
        'bussola' => 'Bússola',
        'balanca' => 'Balança',
    ];

    /**
     * @return array<int, string>
     */
    public static function icones(): array
    {
        return array_keys(self::ICONES);
    }

    /**
     * @return array<string, string>
     */
    public static function rotulosIcones(): array
    {
        return self::ROTULOS_ICONES;
    }

    public static function corNavValida(?string $cor): bool
    {
        return $cor !== null && array_key_exists($cor, self::NAV);
    }

    public static function corDestaqueValida(?string $cor): bool
    {
        return $cor !== null && array_key_exists($cor, self::DESTAQUE);
    }

    public static function fundoLoginValido(?string $fundo): bool
    {
        return $fundo !== null && array_key_exists($fundo, self::FUNDOS_LOGIN);
    }

    public static function iconeValido(?string $icone): bool
    {
        return $icone !== null && array_key_exists($icone, self::ICONES);
    }

    /**
     * @return array{label: string, classes: string, texto: string}
     */
    public static function nav(?string $cor): array
    {
        return self::NAV[$cor] ?? self::NAV['graphite'];
    }

    /**
     * @return array{label: string, normal: string, hover: string, texto: string, foco: string}
     */
    public static function destaque(?string $cor): array
    {
        return self::DESTAQUE[$cor] ?? self::DESTAQUE['indigo'];
    }

    /**
     * @return array{label: string, classes: string, texto: string}
     */
    public static function fundoLogin(?string $fundo): array
    {
        return self::FUNDOS_LOGIN[$fundo] ?? self::FUNDOS_LOGIN['blue-light'];
    }
}
