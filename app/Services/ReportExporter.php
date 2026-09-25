<?php

namespace App\Services;

use App\Support\ReportDefinition;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\AutoFilter;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Writer\XLSX\Properties;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Gera os arquivos de saída dos relatórios.
 *
 * O Excel é montado pelo OpenSpout em um arquivo temporário devolvido
 * como download; o PDF sai do Dompdf de uma view própria, sem o layout
 * da aplicação, porque a paginação impressa difere da tela.
 */
class ReportExporter
{
    /**
     * Linha da planilha em que começam os dados, contando as linhas de
     * identificação do relatório.
     */
    private const PRIMEIRA_LINHA_DE_DADOS = 6;

    private const PASTA = 'relatorios';

    private const FUNDO_CABECALHO = '1F2937';

    private const CINZA_SUAVE = '666666';

    /**
     * @param  array<int, array<string, string>>  $linhas
     * @param  array<string, string>  $meta
     */
    public function excel(ReportDefinition $relatorio, array $linhas, array $meta): BinaryFileResponse
    {
        $caminho = $this->caminhoTemporario($relatorio);

        Storage::disk('local')->makeDirectory(self::PASTA);

        $opcoes = new Options(properties: new Properties(
            title: $relatorio->titulo,
            creator: $meta['sistema'] ?? 'Sistema',
            description: $meta['filtros'] ?? null,
        ));

        $writer = new Writer($opcoes);
        $writer->openToFile($caminho);

        $folha = $writer->getCurrentSheet();
        $folha->setName($this->nomeDaAba($relatorio));

        $this->escreverCabecalho($writer, $relatorio, $meta);

        $chaves = array_keys($relatorio->colunas);

        $writer->addRow(Row::fromValuesWithStyle($relatorio->cabecalho(), $this->estiloCabecalho()));

        $folha->setColumnWidth(22, ...range(0, max(count($chaves) - 1, 0)));
        $folha->setPrintTitleRows((self::PRIMEIRA_LINHA_DE_DADOS - 1).':'.(self::PRIMEIRA_LINHA_DE_DADOS - 1));

        if ($linhas !== []) {
            $folha->setAutoFilter(new AutoFilter(
                fromColumnIndex: 0,
                fromRow: self::PRIMEIRA_LINHA_DE_DADOS - 1,
                toColumnIndex: count($chaves) - 1,
                toRow: count($linhas) + self::PRIMEIRA_LINHA_DE_DADOS - 1
            ));
        }

        foreach ($linhas as $linha) {
            $writer->addRow(Row::fromValues(array_map(
                static fn (string $chave): string => $linha[$chave] ?? '',
                $chaves
            )));
        }

        $writer->close();

        return response()
            ->download($caminho, $relatorio->nomeDoArquivo().'.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])
            ->deleteFileAfterSend(true);
    }

    /**
     * @param  array<int, array<string, string>>  $linhas
     * @param  array<string, string>  $meta
     */
    public function pdf(ReportDefinition $relatorio, array $linhas, array $meta): Response
    {
        return Pdf::loadView('reports.documento', [
            'relatorio' => $relatorio,
            'linhas' => $linhas,
            'meta' => $meta,
        ])
            ->setPaper('a4', 'landscape')
            /*
             * Necessário para o cabeçalho da tabela repetir em cada
             * página. A view é do próprio sistema e não recebe entrada
             * do usuário, então habilitar o PHP inline é seguro aqui.
             */
            ->setOption('isPhpEnabled', true)
            ->download($relatorio->nomeDoArquivo().'.pdf');
    }

    /**
     * @param  array<string, string>  $meta
     */
    private function escreverCabecalho(Writer $writer, ReportDefinition $relatorio, array $meta): void
    {
        $titulo = (new Style)->withFontBold(true)->withFontSize(16);
        $rotulo = (new Style)->withFontBold(true)->withFontSize(12);
        $suave = (new Style)->withFontSize(10)->withFontColor(self::CINZA_SUAVE);

        $writer->addRow(Row::fromValuesWithStyle([$relatorio->titulo], $titulo));
        $writer->addRow(Row::fromValuesWithStyle([$meta['sistema'] ?? ''], $rotulo));
        $writer->addRow(Row::fromValuesWithStyle([$meta['gerado_em'] ?? ''], $suave));
        $writer->addRow(Row::fromValuesWithStyle([$meta['filtros'] ?? ''], $suave));
        $writer->addRow(Row::fromValuesWithStyle([''], $suave));
    }

    private function estiloCabecalho(): Style
    {
        return (new Style)
            ->withFontBold(true)
            ->withFontColor(Color::WHITE)
            ->withBackgroundColor(self::FUNDO_CABECALHO);
    }

    /**
     * Nomes de aba no Excel têm limite de 31 caracteres e não aceitam
     * alguns caracteres de controle.
     */
    private function nomeDaAba(ReportDefinition $relatorio): string
    {
        $nome = mb_substr($relatorio->titulo, 0, 31);

        return str_replace(['\\', '/', '?', '*', '[', ']', ':'], '-', $nome);
    }

    private function caminhoTemporario(ReportDefinition $relatorio): string
    {
        $sufixo = bin2hex(random_bytes(4));

        return Storage::disk('local')->path(
            self::PASTA.'/'.$relatorio->nomeDoArquivo().'-'.$sufixo.'.xlsx'
        );
    }
}
