<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(AuthorizationCheckerInterface $authChecker): Response
    {
        if ($authChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('app_livro_index');
        }

        return $this->redirectToRoute('app_login');
    }
}
