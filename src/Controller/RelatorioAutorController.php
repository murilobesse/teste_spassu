<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\DBAL\ParameterType;

use App\Service\RelatorioService;

class RelatorioAutorController extends AbstractController
{
    #[Route('/relatorio/autor', name: 'app_relatorio_autor', methods: ['GET'])]
    public function index(
        Request $request,
        RelatorioService $relatorioService
    ): Response {
        $limit = 10;
        $page = max(1, $request->query->getInt('page', 1));

        $relatorioCompleto = $relatorioService->getRelatorioAgrupadoPorAutor();
        $total = $relatorioService->getTotalRegistrosAutor();

        $relatorioPlano = [];
        foreach ($relatorioCompleto as $autor => $livros) {
            foreach ($livros as $livro) {
                $relatorioPlano[] = $livro;
            }
        }

        $totalPages = max(1, (int) ceil($total / $limit));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $limit;

        $relatorioPaginado = array_slice($relatorioPlano, $offset, $limit);

        return $this->render('relatorio/autor.html.twig', [
            'relatorio' => $relatorioPaginado,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
            'limit' => $limit,
        ]);
    }

    #[Route('/relatorio/autor/pdf', name: 'app_relatorio_autor_pdf', methods: ['GET'])]
    public function pdf(RelatorioService $relatorioService): Response
    {
        $relatorio = $relatorioService->getRelatorioAgrupadoPorAutor();

        $totalAutores = count($relatorio);
        $totalLivros = array_sum(array_map('count', $relatorio));

        $html = $this->renderView('relatorio/autor/pdf.html.twig', [
            'relatorio' => $relatorio,
            'totalAutores' => $totalAutores,
            'totalLivros' => $totalLivros,
            'dataGeracao' => date('d/m/Y H:i:s'),
        ]);

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 20,
            'margin_bottom' => 15,
            'tempDir' => '/tmp/mpdf',
        ]);
        

        $mpdf->SetTitle('Relatório de Livros por Autor');
        $mpdf->SetAuthor('Sistema de Gestão de Livros');
        
        $mpdf->WriteHTML($html);

        $pdfContent = $mpdf->Output('relatorio_livros_autor.pdf',\Mpdf\Output\Destination::INLINE);

        return new Response(
            $pdfContent,
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="relatorio_livros_autor.pdf"',
            ]
        );
    }
}