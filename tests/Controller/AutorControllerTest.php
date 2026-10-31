<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Entity\Autor;
use App\Repository\AutorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

final class AutorControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $passwordHasher = $container->get('security.password_hasher');

        $user = new User();
        $user->setEmail('admin_test@teste.com');
        $user->setNome('Admin Teste');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setAtivo(true);
        $user->setPassword($passwordHasher->hashPassword($user, '123456'));

        $entityManager->persist($user);
        $entityManager->flush();

        $this->client->loginUser($user);
    }

    public function testeListaDeAutores(): void
    {
        $this->client->request('GET', '/autor/');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('h1','Lista de Autores');
    }

    public function testInsertAutor(): void
    {
        $crawler = $this->client->request('GET', '/autor/');
        $form = $crawler->selectButton('Cadastrar')->form(['autor[nome]' => 'Albert Camus']);

        $this->client->submit($form);
        $this->assertResponseRedirects('/autor/');
        $this->client->followRedirect();

        $autor = static::getContainer()
            ->get(AutorRepository::class)
            ->findOneBy(['nome' => 'Albert Camus']);

        $this->assertNotNull($autor);
    }

    public function testEditAutor(): void
    {
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $autor = new Autor();
        $autor->setNome('Albert Camus');
        $entityManager->persist($autor);
        $entityManager->flush();

        $id = $autor->getCodAu();

        $crawler = $this->client->request('GET', '/autor/');

        // Pegar o CSRF token, pois não enviou automático
        $token = $crawler->filter(
            '#formEditarDinamico input[name="autor[_token]"]'
        )->attr('value');

        $this->client->request(
            'POST',
            '/autor/' . $id . '/editar',
            [
                'autor' => [
                    'nome' => 'Albert Camus Edit',
                    '_token' => $token,
                ]
            ]
        );

        $this->assertResponseRedirects('/autor/');

        $entityManager->clear();
        
        $autorAtualizado = $entityManager->getRepository(Autor::class)->find($id);

        $this->assertSame('Albert Camus Edit', $autorAtualizado->getNome());
    }

    public function testDeleteAutor(): void
    {
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $autor = new Autor();
        $autor->setNome('Albert Camus');
        $entityManager->persist($autor);
        $entityManager->flush();

        $id = $autor->getCodAu();

        $crawler = $this->client->request('GET', '/autor/');

        $form = $crawler->selectButton('Excluir')->form();
        $token = $form->get('_token')->getValue();

        $this->client->request(
            'POST',
            '/autor/' . $id,
            [
                '_token' => $token
            ]
        );

        $this->assertResponseRedirects('/autor/');

        $entityManager->clear();

        $autorExcluido = $entityManager
            ->getRepository(Autor::class)
            ->find($id);

        $this->assertNull($autorExcluido);
    }

    public function testCriarAutorSemNome(): void
    {
        $crawler = $this->client->request('GET', '/autor/');

        $form = $crawler->selectButton('Cadastrar')->form([
            'autor[nome]' => '',
        ]);

        $this->client->submit($form);
        
        $autor = static::getContainer()
            ->get(AutorRepository::class)
            ->findOneBy(['nome' => '']);

        $this->assertNull($autor);
    }
}
