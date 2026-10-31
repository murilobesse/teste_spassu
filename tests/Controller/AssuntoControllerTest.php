<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Entity\Assunto;
use App\Repository\AssuntoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

final class AssuntoControllerTest extends WebTestCase
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

    public function testeListaDeAssuntos(): void
    {
        $this->client->request('GET', '/assunto/');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('h1','Lista de Assuntos');
    }

    public function testInsertAssunto(): void
    {
        $crawler = $this->client->request('GET', '/assunto/');
        $form = $crawler->selectButton('Cadastrar')->form(['assunto[descricao]' => 'Ficção']);

        $this->client->submit($form);
        $this->assertResponseRedirects('/assunto/');
        $this->client->followRedirect();

        $assunto = static::getContainer()
            ->get(AssuntoRepository::class)
            ->findOneBy(['descricao' => 'Ficção']);

        $this->assertNotNull($assunto);
    }

    public function testEditAssunto(): void
    {
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $assunto = new Assunto();
        $assunto->setDescricao('Ficção');
        $entityManager->persist($assunto);
        $entityManager->flush();

        $id = $assunto->getCodAs();

        $crawler = $this->client->request('GET', '/assunto/');

        // Pegar o CSRF token, pois não enviou automático
        $token = $crawler->filter(
            '#formEditarDinamico input[name="assunto[_token]"]'
        )->attr('value');

        $this->client->request(
            'POST',
            '/assunto/' . $id . '/editar',
            [
                'assunto' => [
                    'descricao' => 'Ficção Edit',
                    '_token' => $token,
                ]
            ]
        );

        $this->assertResponseRedirects('/assunto/');

        $entityManager->clear();
        
        $assuntoAtualizado = $entityManager->getRepository(Assunto::class)->find($id);

        $this->assertSame('Ficção Edit', $assuntoAtualizado->getDescricao());
    }

    public function testDeleteAssunto(): void
    {
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $assunto = new Assunto();
        $assunto->setDescricao('Ficção');
        $entityManager->persist($assunto);
        $entityManager->flush();

        $id = $assunto->getCodAs();

        $crawler = $this->client->request('GET', '/assunto/');

        $form = $crawler->selectButton('Excluir')->form();
        $token = $form->get('_token')->getValue();

        $this->client->request(
            'POST',
            '/assunto/' . $id,
            [
                '_token' => $token
            ]
        );

        $this->assertResponseRedirects('/assunto/');
        
        $entityManager->clear();

        $assuntoExcluido = $entityManager
            ->getRepository(Assunto::class)
            ->find($id);

        $this->assertNull($assuntoExcluido);
    }

    public function testCriarAssuntoSemDescricao(): void
    {
        $crawler = $this->client->request('GET', '/assunto/');

        $form = $crawler->selectButton('Cadastrar')->form([
            'assunto[descricao]' => '',
        ]);

        $this->client->submit($form);
        
        $assunto = static::getContainer()
            ->get(AssuntoRepository::class)
            ->findOneBy(['descricao' => '']);

        $this->assertNull($assunto);
    }
}
