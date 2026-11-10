<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261110113245 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE OR REPLACE VIEW vw_relatorio_livros_por_assunto AS
            SELECT 
                a.descricao AS assunto_descricao,
                l.titulo AS livro_titulo,
                l.editora AS livro_editora,
                l.edicao AS livro_edicao,
                l.ano_publicacao AS livro_ano,
                l.valor AS livro_valor
            FROM livro l
            INNER JOIN livro_assunto la ON l.codl = la.livro_codl
            INNER JOIN assunto a ON la.assunto_codas = a.codas
            ORDER BY a.descricao ASC, l.titulo ASC
        ');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP VIEW IF EXISTS vw_relatorio_livros_por_assunto');
    }
}
