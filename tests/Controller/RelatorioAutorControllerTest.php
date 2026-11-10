<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RelatorioAutorControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        
        $container = static::getContainer();
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $passwordHasher = $container->get('security.password_hasher');

        $user = new User();
        $user->setEmail('user_relatorio@teste.com');
        $user->setNome('Usuario Relatorio');
        $user->setRoles(['ROLE_USER']);
        $user->setAtivo(true);
        $user->setPassword($passwordHasher->hashPassword($user, '123456'));

        $entityManager->persist($user);
        $entityManager->flush();

        $this->client->loginUser($user);
    }

    public function testPdfEndpointReturnsPdf(): void
    {
        $this->client->request('GET', '/relatorio/autor/pdf');

        $this->assertResponseIsSuccessful();

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/pdf');
    }

    public function testIndexEndpointReturnsHtml(): void
    {
        $this->client->request('GET', '/relatorio/autor');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1','Relatório de Livros por Autor');
    }
}