<?php

namespace App\Controller;

use App\Entity\Assunto;
use App\Form\AssuntoType;
use App\Repository\AssuntoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/assunto')]
final class AssuntoController extends AbstractController
{
    #[Route('/', name: 'app_assunto_index', methods: ['GET'])]
    public function index(Request $request, AssuntoRepository $assuntoRepository, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $termo = $request->query->get('q');
        $sort = $request->query->get('sortBy', 'codas');
        $order = $request->query->get('sortDir', 'desc');

        $novoAssunto = new Assunto();
        $formNovo = $this->createForm(AssuntoType::class, $novoAssunto);
        $formNovo->handleRequest($request);

        if ($formNovo->isSubmitted() && $formNovo->isValid()) {
            $entityManager->persist($novoAssunto);
            $entityManager->flush();
            $this->addFlash('success', 'Assunto criado com sucesso!');

            return $this->redirectToRoute('app_assunto_index', [], Response::HTTP_SEE_OTHER);
        }

        $formEditar = $this->createForm(AssuntoType::class, new Assunto())->createView();

        $queryBuilder = $assuntoRepository->findByDescricao($termo, $sort, $order);
        $query = $queryBuilder->getQuery();
        $assuntos = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('assunto/index.html.twig', [
            'assuntos' => $assuntos,
            'form_novo' => $formNovo->createView(),
            'form_editar' => $formEditar,
            'termo' => $termo,
            'currentSort' => $sort,
            'currentOrder' => strtolower($order),
        ]);
    }

    #[Route('/novo', name: 'app_assunto_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AssuntoRepository $assuntoRepository, EntityManagerInterface $entityManager): Response
    {
        $assunto = new Assunto();
        $form = $this->createForm(AssuntoType::class, $assunto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($assunto);
            $entityManager->flush();

            $this->addFlash('success', 'Assunto criado com sucesso!');

            return $this->redirectToRoute('app_assunto_index', [], Response::HTTP_SEE_OTHER);
        }
        $errors = $form->getErrors(true);
        $errorMessages = [];

        foreach ($errors as $error) {
            $errorMessages[] = $error->getMessage();
        }

        $mensagemErro = !empty($errorMessages) ? implode(' | ', $errorMessages) : 'Erro desconhecido ao validar o formulário.';
        $this->addFlash('error', 'Erro ao criar assunto: '.$mensagemErro);

        return $this->redirectToRoute('app_assunto_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{codAs}/editar', name: 'app_assunto_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $codAs, AssuntoRepository $assuntoRepository, EntityManagerInterface $entityManager): Response
    {
        $assunto = $assuntoRepository->find($codAs);

        if (!$assunto) {
            throw $this->createNotFoundException('Assunto não encontrado.');
        }

        $form = $this->createForm(AssuntoType::class, $assunto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Assunto atualizado com sucesso!');

            return $this->redirectToRoute('app_assunto_index', [], Response::HTTP_SEE_OTHER);
        }
        $errors = $form->getErrors(true);
        $errorMessages = [];

        foreach ($errors as $error) {
            $errorMessages[] = $error->getMessage();
        }

        $mensagemErro = !empty($errorMessages) ? implode(' | ', $errorMessages) : 'Erro desconhecido ao validar o formulário.';
        $this->addFlash('error', 'Erro ao editar assunto: '.$mensagemErro);

        return $this->redirectToRoute('app_assunto_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{codAs}', name: 'app_assunto_delete', methods: ['POST'])]
    public function delete(Request $request, int $codAs, AssuntoRepository $assuntoRepository, EntityManagerInterface $entityManager): Response
    {
        $assunto = $assuntoRepository->find($codAs);

        if (!$assunto) {
            throw $this->createNotFoundException('Assunto não encontrado.');
        }

        if ($assunto->getLivros()->count() > 0) {
            $this->addFlash('error', 'Não é possível excluir este assunto, pois ele possui livros vinculados.');

            return $this->redirectToRoute('app_assunto_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($this->isCsrfTokenValid('delete'.$assunto->getCodAs(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($assunto);
            $entityManager->flush();
            $this->addFlash('success', 'Assunto excluído com sucesso!');
        }

        return $this->redirectToRoute('app_assunto_index', [], Response::HTTP_SEE_OTHER);
    }
}
