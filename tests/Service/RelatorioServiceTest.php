<?php

namespace App\Tests\Service;

use App\Service\RelatorioService;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RelatorioServiceTest extends TestCase
{
    private Connection&MockObject $connection;
    private RelatorioService $service;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->service = new RelatorioService($this->connection);
    }

    public function testLivroComDoisAutoresApareceEmAmbosOsGrupos(): void
    {
        $rows = [
            [
                'autor_nome' => 'Machado de Assis',
                'livro_titulo' => 'Antologia',
                'livro_editora' => 'Editora X',
                'livro_edicao' => 1,
                'livro_ano' => '2020',
                'livro_valor' => 89.00,
            ],
            [
                'autor_nome' => 'Clarice Lispector',
                'livro_titulo' => 'Antologia',
                'livro_editora' => 'Editora X',
                'livro_edicao' => 1,
                'livro_ano' => '2020',
                'livro_valor' => 89.00,
            ],
        ];

        $this->mockFetchAll($rows);

        $relatorio = $this->service->getRelatorioAgrupadoPorAutor();

        $this->assertCount(2, $relatorio);
        $this->assertArrayHasKey('Machado de Assis', $relatorio);
        $this->assertArrayHasKey('Clarice Lispector', $relatorio);

        $this->assertCount(1, $relatorio['Machado de Assis']);
        $this->assertCount(1, $relatorio['Clarice Lispector']);

        $this->assertSame('Antologia', $relatorio['Machado de Assis'][0]['livro_titulo']);
        $this->assertSame('Antologia', $relatorio['Clarice Lispector'][0]['livro_titulo']);
    }

    public function testListaVaziaRetornaArrayVazio(): void
    {
        $this->mockFetchAll([]);

        $relatorio = $this->service->getRelatorioAgrupadoPorAutor();

        $this->assertSame([], $relatorio);
    }

    public function testAgrupamentoCorretoComMultiplosAutoresELivros(): void
    {
        $rows = [
            ['autor_nome' => 'Clarice Lispector', 'livro_titulo' => 'A Hora da Estrela', 'livro_editora' => 'Ed. A', 'livro_edicao' => 1, 'livro_ano' => '1977', 'livro_valor' => 32.50],
            ['autor_nome' => 'Clarice Lispector', 'livro_titulo' => 'Antologia', 'livro_editora' => 'Ed. B', 'livro_edicao' => 1, 'livro_ano' => '2020', 'livro_valor' => 89.00],
            ['autor_nome' => 'Machado de Assis', 'livro_titulo' => 'Antologia', 'livro_editora' => 'Ed. B', 'livro_edicao' => 1, 'livro_ano' => '2020', 'livro_valor' => 89.00],
            ['autor_nome' => 'Machado de Assis', 'livro_titulo' => 'Dom Casmurro', 'livro_editora' => 'Ed. C', 'livro_edicao' => 2, 'livro_ano' => '1899', 'livro_valor' => 45.90],
        ];

        $this->mockFetchAll($rows);

        $relatorio = $this->service->getRelatorioAgrupadoPorAutor();

        $this->assertCount(2, $relatorio);

        $this->assertCount(2, $relatorio['Clarice Lispector']);

        $this->assertCount(2, $relatorio['Machado de Assis']);

        $titulosClarice = array_column($relatorio['Clarice Lispector'], 'livro_titulo');
        $this->assertContains('A Hora da Estrela', $titulosClarice);
        $this->assertContains('Antologia', $titulosClarice);

        $titulosMachado = array_column($relatorio['Machado de Assis'], 'livro_titulo');
        $this->assertContains('Dom Casmurro', $titulosMachado);
        $this->assertContains('Antologia', $titulosMachado);
    }

    public function testAutorComUmUnicoLivro(): void
    {
        $rows = [
            ['autor_nome' => 'Machado de Assis', 'livro_titulo' => 'Dom Casmurro', 'livro_editora' => 'Ed. A', 'livro_edicao' => 1, 'livro_ano' => '1899', 'livro_valor' => 45.90],
        ];

        $this->mockFetchAll($rows);

        $relatorio = $this->service->getRelatorioAgrupadoPorAutor();

        $this->assertCount(1, $relatorio);
        $this->assertCount(1, $relatorio['Machado de Assis']);
        $this->assertSame('Dom Casmurro', $relatorio['Machado de Assis'][0]['livro_titulo']);
    }

    public function testGetTotalRegistrosRetornaQuantidadeCorreta(): void
    {
        $this->connection
            ->method('fetchOne')
            ->willReturn('4');

        $total = $this->service->getTotalRegistrosAutor();

        $this->assertSame(4, $total);
    }

    public function testGetTotalRegistrosRetornaZeroQuandoVazio(): void
    {
        $this->connection
            ->method('fetchOne')
            ->willReturn('0');

        $total = $this->service->getTotalRegistrosAutor();

        $this->assertSame(0, $total);
    }

    /**
     * @param list<array<string, mixed>> $rows
     */
    private function mockFetchAll(array $rows): void
    {
        $result = $this->createMock(Result::class);
        $result->method('fetchAllAssociative')->willReturn($rows);

        $this->connection
            ->method('executeQuery')
            ->willReturn($result);
    }
}
