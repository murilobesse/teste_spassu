<?php

namespace App\Service;

use Doctrine\DBAL\Connection;

class RelatorioService
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getRelatorioAgrupadoPorAutor(): array
    {
        $sql = '
            SELECT autor_nome, livro_titulo, livro_editora, livro_edicao, livro_ano, livro_valor
            FROM vw_relatorio_livros_por_autor
            ORDER BY autor_nome ASC, livro_titulo ASC
        ';

        $rows = $this->connection->executeQuery($sql)->fetchAllAssociative();

        $relatorioAgrupado = [];
        foreach ($rows as $row) {
            $autor = $row['autor_nome'];
            if (!isset($relatorioAgrupado[$autor])) {
                $relatorioAgrupado[$autor] = [];
            }
            $relatorioAgrupado[$autor][] = $row;
        }

        return $relatorioAgrupado;
    }

    public function getTotalRegistros(): int
    {
        return (int) $this->connection->fetchOne(
            'SELECT COUNT(*) FROM vw_relatorio_livros_por_autor'
        );
    }

}