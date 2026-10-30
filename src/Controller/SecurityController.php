<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils, TranslatorInterface $translator): Response
    {
        if($this->getUser()){
            return $this->redirectToRoute('app_home');
        }

        $error = $authenticationUtils->getLastAuthenticationError();

        if ($error) {
            $errorMessage = $translator->trans(
                $error->getMessageKey(),
                $error->getMessageData(),
                'security'
            );
            $this->addFlash('error', $errorMessage);
        }

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        
    }
}
