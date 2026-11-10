<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\Livro;
use App\Form\LivroType;
use App\Repository\LivroRepository;
use Doctrine\ORM\EntityManagerInterface;
use SortDirection;

use Knp\Component\Pager\PaginatorInterface;

#[Route('/livro')]
final class LivroController extends AbstractController
{
    #[Route('/', name: 'app_livro_index', methods: ['GET'])]
    public function index(Request $request, LivroRepository $livroRepository, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $termo = $request->query->get('q');
        $sort = $request->query->get('sortBy', 'codl');
        $order = $request->query->get('sortDir', 'desc');

        $novoLivro = new Livro();
        $formNovo = $this->createForm(LivroType::class, $novoLivro);
        $formNovo->handleRequest($request);

        if ($formNovo->isSubmitted() && $formNovo->isValid()) {
            $entityManager->persist($novoLivro);
            $entityManager->flush();
            $this->addFlash('success', 'Livro criado com sucesso!');
            return $this->redirectToRoute('app_livro_index', [], Response::HTTP_SEE_OTHER);
        }

        $formEditar = $this->createForm(LivroType::class, new Livro())->createView();

        $queryBuilder = $livroRepository->findByTitulo($termo, $sort, $order);
        $query = $queryBuilder->getQuery();
        $livros = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );
        

        return $this->render('livro/index.html.twig', [
            'livros' => $livros,
            'form_novo' => $formNovo->createView(),
            'form_editar' => $formEditar,
            'termo' => $termo,
            'currentSort' => $sort,
            'currentOrder' => strtolower($order),
        ]);
    }

    #[Route('/novo', name: 'app_livro_new', methods: ['GET', 'POST'])]
    public function new(Request $request, LivroRepository $livroRepository, EntityManagerInterface $entityManager): Response
    {
        $livro = new Livro();
        $form = $this->createForm(LivroType::class, $livro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($livro);
            $entityManager->flush();

            $this->addFlash('success', 'Livro criado com sucesso!');
            return $this->redirectToRoute('app_livro_index', [], Response::HTTP_SEE_OTHER);
        } else {
            $errors = $form->getErrors(true); 
            $errorMessages = [];
            
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            
            $mensagemErro = !empty($errorMessages) ? implode(' | ', $errorMessages) : 'Erro desconhecido ao validar o formulário.';
            $this->addFlash('error', 'Erro ao criar livro: ' . $mensagemErro);
        }

        return $this->redirectToRoute('app_livro_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{codl}/editar', name: 'app_livro_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $codl, LivroRepository $livroRepository, EntityManagerInterface $entityManager): Response
    {
        $livro = $livroRepository->find($codl);

        if (!$livro) {
            throw $this->createNotFoundException('Livro não encontrado.');
        }

        $form = $this->createForm(LivroType::class, $livro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Livro atualizado com sucesso!');
            return $this->redirectToRoute('app_livro_index', [], Response::HTTP_SEE_OTHER);
        } else {
            $errors = $form->getErrors(true); 
            $errorMessages = [];
            
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            
            $mensagemErro = !empty($errorMessages) ? implode(' | ', $errorMessages) : 'Erro desconhecido ao validar o formulário.';
            $this->addFlash('error', 'Erro ao editar livro: ' . $mensagemErro);
        }

        return $this->redirectToRoute('app_livro_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{codl}', name: 'app_livro_delete', methods: ['POST'])]
    public function delete(Request $request, int $codl, LivroRepository $livroRepository, EntityManagerInterface $entityManager): Response
    {
        $livro = $livroRepository->find($codl);

        if (!$livro) {
            throw $this->createNotFoundException('Livro não encontrado.');
        }

        if ($this->isCsrfTokenValid('delete'.$livro->getCodl(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($livro);
            $entityManager->flush();
            $this->addFlash('success', 'Livro excluído com sucesso!');
        }

        return $this->redirectToRoute('app_livro_index', [], Response::HTTP_SEE_OTHER);
    }
}
