<?php

namespace App\Support;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Model;

/**
 * Descrição declarativa de um relatório.
 *
 * Tudo o que muda entre um relatório e outro fica aqui: o model de
 * origem, quem pode ver, quais filtros existem, quais colunas saem na
 * tela, no PDF e no Excel. A implementação é única para todos eles, em
 * App\Services\ReportService e App\Services\ReportExporter.
 */
final class ReportDefinition
{
    /**
     * @param  array<int, UserRole>  $papeis  papéis autorizados a abrir o relatório
     * @param  array<int, string>  $relacoes  relações carregadas com eager loading
     * @param  array<string, array{rotulo: string, tipo: string, opcoes?: string}>  $filtros
     * @param  array<string, array{rotulo: string, valor: callable(Model): string}>  $colunas
     * @param  class-string<Model>  $model
     */
    public function __construct(
        public readonly string $slug,
        public readonly string $titulo,
        public readonly string $descricao,
        public readonly string $icone,
        public readonly array $papeis,
        public readonly string $model,
        public readonly array $relacoes,
        public readonly array $filtros,
        public readonly array $colunas,
        public readonly string $ordem,
        public readonly string $direcao = 'desc',
    ) {}

    /**
     * Rótulos das colunas, na ordem em que aparecem.
     *
     * @return array<int, string>
     */
    public function cabecalho(): array
    {
        return array_values(array_map(
            static fn (array $coluna): string => $coluna['rotulo'],
            $this->colunas
        ));
    }

    /**
     * @return array<int, string>
     */
    public function chavesDosFiltros(): array
    {
        return array_keys($this->filtros);
    }

    public function permite(UserRole $papel): bool
    {
        return in_array($papel, $this->papeis, true);
    }

    /**
     * Nome do arquivo de exportação, sem extensão.
     */
    public function nomeDoArquivo(?string $sufixo = null): string
    {
        $base = 'relatorio-'.$this->slug;
        $data = now()->format('Y-m-d');

        return $sufixo === null || $sufixo === ''
            ? "{$base}-{$data}"
            : "{$base}-{$sufixo}-{$data}";
    }
}
