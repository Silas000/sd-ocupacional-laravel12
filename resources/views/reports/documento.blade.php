<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>{{ $relatorio->titulo }}</title>

    <style>
        @page {
            margin: 25mm 12mm 22mm 12mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9pt;
            color: #1f2937;
            margin: 0;
        }

        .cabecalho {
            border-bottom: 2px solid #1f2937;
            padding-bottom: 8px;
            margin-bottom: 14px;
        }

        .cabecalho h1 {
            font-size: 15pt;
            margin: 0 0 2px;
        }

        .cabecalho .sistema {
            font-size: 10pt;
            font-weight: bold;
            color: #4b5563;
            margin: 0;
        }

        .cabecalho .meta {
            font-size: 8pt;
            color: #6b7280;
            margin: 1px 0 0;
        }

        .filtros {
            background: #f3f4f6;
            padding: 6px 8px;
            font-size: 8pt;
            color: #374151;
            margin-bottom: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            display: table-header-group;
        }

        th {
            background: #1f2937;
            color: #ffffff;
            text-align: left;
            padding: 5px 6px;
            font-size: 8pt;
            font-weight: bold;
            border: 1px solid #374151;
        }

        td {
            padding: 4px 6px;
            border: 1px solid #d1d5db;
            font-size: 8.5pt;
        }

        tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        .vazio {
            padding: 18px;
            text-align: center;
            color: #6b7280;
            border: 1px solid #d1d5db;
        }

        .rodape-nota {
            margin-top: 10px;
            font-size: 7.5pt;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="cabecalho">
        <h1>{{ $relatorio->titulo }}</h1>
        <p class="sistema">{{ $meta['sistema'] }}</p>
        <p class="meta">{{ $meta['gerado_em'] }} &middot; {{ $meta['gerado_por'] }} &middot; {{ $meta['total'] }}</p>
    </div>

    <div class="filtros">{{ $meta['filtros'] }}</div>

    <table>
        <thead>
            <tr>
                @foreach($relatorio->cabecalho() as $tituloColuna)
                    <th>{{ $tituloColuna }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($linhas as $linha)
                <tr>
                    @foreach($relatorio->colunas as $chave => $coluna)
                        <td>{{ $linha[$chave] }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td class="vazio" colspan="{{ count($relatorio->colunas) }}">
                        Nenhum registro encontrado para os filtros informados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="rodape-nota">
        Documento gerado automaticamente. Contém dados pessoais sens&iacute;veis e deve ser
        guardado em ambiente seguro, conforme a LGPD.
    </p>
</body>
</html>
