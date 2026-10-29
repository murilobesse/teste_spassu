<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\Autor;
use App\Form\AutorType;
use App\Repository\AutorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

#[Route('/autor')]
final class AutorController extends AbstractController
{
    #[Route('/', name: 'app_autor_index', methods: ['GET'])]
    public function index(AutorRepository $autorRepository): Response
    {
        return $this->render('autor/index.html.twig', [
            'autors' => $autorRepository->findAll(),
        ]);
    }

    #[Route('/novo', name: 'app_autor_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $autor = new Autor();
        $form = $this->createForm(AutorType::class, $autor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($autor);
            $entityManager->flush();

            $this->addFlash('success', 'Autor criado com sucesso!');
            return $this->redirectToRoute('app_autor_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('autor/new.html.twig', [
            'autor' => $autor,
            'form' => $form,
        ]);
    }

    #[Route('/{codAu}/editar', name: 'app_autor_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $codAu, AutorRepository $autorRepository, EntityManagerInterface $entityManager): Response
    {
        $autor = $autorRepository->find($codAu);

        if (!$autor) {
            throw $this->createNotFoundException('Autor não encontrado.');
        }

        $form = $this->createForm(AutorType::class, $autor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Autor atualizado com sucesso!');
            return $this->redirectToRoute('app_autor_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('autor/edit.html.twig', [
            'autor' => $autor,
            'form' => $form,
        ]);
    }

    #[Route('/{codAu}', name: 'app_autor_delete', methods: ['POST'])]
    public function delete(Request $request, int $codAu, AutorRepository $autorRepository, EntityManagerInterface $entityManager): Response
    {
        $autor = $autorRepository->find($codAu);

        if (!$autor) {
            throw $this->createNotFoundException('Autor não encontrado.');
        }
        
        if ($autor->getLivros()->count() > 0) {
            $this->addFlash('error', 'Não é possível excluir este autor, pois ele possui livros vinculados.');
            return $this->redirectToRoute('app_autor_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($this->isCsrfTokenValid('delete'.$autor->getCodAu(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($autor);
            $entityManager->flush();
            $this->addFlash('success', 'Autor excluído com sucesso!');
        }

        return $this->redirectToRoute('app_autor_index', [], Response::HTTP_SEE_OTHER);
    }
}
