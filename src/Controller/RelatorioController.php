<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\DBAL\ParameterType;

use Doctrine\DBAL\Connection;

class RelatorioController extends AbstractController
{
    #[Route('/relatorio', name: 'app_relatorio_index', methods: ['GET'])]
    public function index(
        Request $request,
        Connection $connection
    ): Response {
        $limit = 10;
        $page = max(1, $request->query->getInt('page', 1));

        $total = (int) $connection->fetchOne(
            'SELECT COUNT(*) FROM vw_relatorio_livros_por_autor'
        );

        $totalPages = max(1, (int) ceil($total / $limit));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $limit;

        $sql = '
            SELECT autor_nome, livro_titulo, livro_editora, livro_edicao, livro_ano, livro_valor
            FROM vw_relatorio_livros_por_autor
            ORDER BY autor_nome ASC, livro_titulo ASC
            LIMIT :limit OFFSET :offset
        ';

        $relatorio = $connection->executeQuery(
            $sql,
            ['limit' => $limit, 'offset' => $offset],
            ['limit' => ParameterType::INTEGER, 'offset' => ParameterType::INTEGER]
        )->fetchAllAssociative();

        return $this->render('relatorio/index.html.twig', [
            'relatorio'   => $relatorio,
            'page'        => $page,
            'totalPages'  => $totalPages,
            'total'       => $total,
            'limit'       => $limit,
        ]);
    }

    #[Route('/relatorio/pdf', name: 'app_relatorio_pdf', methods: ['GET'])]
    public function pdf(Connection $connection): Response
    {
        $sql = '
            SELECT autor_nome, livro_titulo, livro_editora, livro_edicao, livro_ano, livro_valor
            FROM vw_relatorio_livros_por_autor
            ORDER BY autor_nome ASC, livro_titulo ASC
        ';

        $rows = $connection->executeQuery($sql)->fetchAllAssociative();
        
        $relatorioAgrupado = [];
        foreach ($rows as $row) {
            $autor = $row['autor_nome'];
            if (!isset($relatorioAgrupado[$autor])) {
                $relatorioAgrupado[$autor] = [];
            }
            $relatorioAgrupado[$autor][] = $row;
        }

        $html = $this->renderView('relatorio/pdf.html.twig', [
            'relatorio' => $relatorioAgrupado,
            'totalAutores' => count($relatorioAgrupado),
            'totalLivros' => count($rows),
            'dataGeracao' => date('d/m/Y H:i:s'),
        ]);

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
        ]);

        $mpdf->SetTitle('Relatório de Livros por Autor');
        $mpdf->SetAuthor('Sistema de Gestão de Livros');
        
        $mpdf->WriteHTML($html);

        return new Response(
            $mpdf->Output('relatorio_livros_autor.pdf', \Mpdf\Output\Destination::INLINE),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="relatorio_livros_autor.pdf"',
            ]
        );
    }
}