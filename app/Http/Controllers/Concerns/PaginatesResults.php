<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;

trait PaginatesResults
{
    private const PAGINA_PADRAO = 15;

    private const PAGINA_MAXIMA = 100;

    /**
     * Quantidade de registros por página, com teto para evitar que um
     * parâmetro da URL carregue a tabela inteira.
     */
    protected function perPage(Request $request, int $padrao = self::PAGINA_PADRAO): int
    {
        $solicitado = (int) $request->integer('per_page', $padrao);

        return max(5, min($solicitado, self::PAGINA_MAXIMA));
    }

    /**
     * Lê um conjunto de filtros da requisição, ignorando valores vazios.
     *
     * @param  array<int, string>  $chaves
     * @return array<string, string>
     */
    protected function filtros(Request $request, array $chaves): array
    {
        $valores = [];

        foreach ($chaves as $chave) {
            $valor = $request->input($chave);

            if (is_string($valor) && trim($valor) !== '') {
                $valores[$chave] = trim($valor);
            }
        }

        return $valores;
    }

    /**
     * Indica se algum filtro foi aplicado, para a view esconder o
     * botão de limpar.
     *
     * @param  array<string, string>  $filtros
     */
    protected function temFiltro(array $filtros): bool
    {
        return $filtros !== [];
    }
}
