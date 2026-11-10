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

    /**
     * @return array<string, list<array{
     *     autor_nome: string,
     *     livro_titulo: string,
     *     livro_editora: string,
     *     livro_edicao: int,
     *     livro_ano: string,
     *     livro_valor: numeric-string
     * }>>
     */
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

    public function getTotalRegistrosAutor(): int
    {
        return (int) $this->connection->fetchOne(
            'SELECT COUNT(*) FROM vw_relatorio_livros_por_autor'
        );
    }

    /**
     * @return array<string, list<array{
     *     assunto_descricao: string,
     *     livro_titulo: string,
     *     livro_editora: string,
     *     livro_edicao: int,
     *     livro_ano: string,
     *     livro_valor: numeric-string
     * }>>
     */
    public function getRelatorioAgrupadoPorAssunto(): array
    {
        $sql = '
            SELECT assunto_descricao, livro_titulo, livro_editora, livro_edicao, livro_ano, livro_valor
            FROM vw_relatorio_livros_por_assunto
            ORDER BY assunto_descricao ASC, livro_titulo ASC
        ';

        $rows = $this->connection->executeQuery($sql)->fetchAllAssociative();

        $relatorioAgrupado = [];
        foreach ($rows as $row) {
            $assunto = $row['assunto_descricao'];
            if (!isset($relatorioAgrupado[$assunto])) {
                $relatorioAgrupado[$assunto] = [];
            }
            $relatorioAgrupado[$assunto][] = $row;
        }

        return $relatorioAgrupado;
    }

    public function getTotalRegistrosAssunto(): int
    {
        return (int) $this->connection->fetchOne(
            'SELECT COUNT(*) FROM vw_relatorio_livros_por_assunto'
        );
    }
}
