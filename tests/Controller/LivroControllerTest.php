<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Entity\Livro;
use App\Repository\LivroRepository;
use App\Entity\Autor;
use App\Entity\Assunto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

final class LivroControllerTest extends WebTestCase
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

    public function testeListaDeLivros(): void
    {
        $this->client->request('GET', '/livro/');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('h1','Lista de Livros');
    }

    public function testInsertLivro(): void
    {
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $autor = new Autor();
        $autor->setNome('Albert Camus');
        $entityManager->persist($autor);

        $assunto = new Assunto();
        $assunto->setDescricao('Literatura Francesa');
        $entityManager->persist($assunto);

        $entityManager->flush();

        $crawler = $this->client->request('GET', '/livro/');

        $token = $crawler->filter('input[name="livro[_token]"]')->attr('value');

        $this->client->request('POST', '/livro/novo', [
            'livro' => [
                'titulo' => 'O Estrangeiro',
                'editora' => 'Editora Record',
                'edicao' => 1,
                'valor' => '19.90',
                'anoPublicacao' => 1942,
                'autores' => [$autor->getCodAu()],
                'assuntos' => [$assunto->getCodAs()],
                '_token' => $token,
            ],
        ]);

        $this->assertResponseRedirects('/livro/');

        $entityManager->clear();

        $livro = $entityManager->getRepository(Livro::class)
            ->findOneBy(['titulo' => 'O Estrangeiro']);

        $this->assertNotNull($livro);
        $this->assertCount(1, $livro->getAutores());
        $this->assertCount(1, $livro->getAssuntos());
    }

    public function testEditLivro(): void
    {
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $autor = new Autor();
        $autor->setNome('Albert Camus');
        $entityManager->persist($autor);

        $autor2 = new Autor();
        $autor2->setNome('Machado de Assis');
        $entityManager->persist($autor2);

        $assunto = new Assunto();
        $assunto->setDescricao('Ficção');
        $entityManager->persist($assunto);
        
        $livro = new Livro();
        $livro->setTitulo('O Estrangeiro');
        $livro->setEditora('Editora Record');
        $livro->setEdicao(1);
        $livro->setValor('19.90');
        $livro->setAnoPublicacao(1942);

        $livro->addAutor($autor);
        $livro->addAssunto($assunto);

        $entityManager->persist($livro);
        $entityManager->flush();

        $id = $livro->getCodL();

        $crawler = $this->client->request('GET', '/livro/');

        $token = $crawler->filter(
            '#formEditarLivro input[name="livro[_token]"]'
        )->attr('value');

        $this->client->request(
            'POST',
            '/livro/' . $id . '/editar',
            [
                'livro' => [
                    'titulo' => 'O Estrangeiro - Editado',
                    'editora' => 'Companhia das Letras',
                    'edicao' => 2,
                    'valor' => '29.90',
                    'anoPublicacao' => 1942,
                    'autores' => [$autor->getCodAu(), $autor2->getCodAu()],
                    'assuntos' => [$assunto->getCodAs()],
                    '_token' => $token,
                ]
            ]
        );

        $this->assertResponseRedirects('/livro/');

        $entityManager->clear();

        $livroAtualizado = $entityManager
            ->getRepository(Livro::class)
            ->find($id);

        $this->assertNotNull($livroAtualizado);
        $this->assertSame('O Estrangeiro - Editado', $livroAtualizado->getTitulo());
        $this->assertSame('Companhia das Letras', $livroAtualizado->getEditora());
        $this->assertCount(2, $livroAtualizado->getAutores());
        $this->assertCount(1, $livroAtualizado->getAssuntos());
    }

    public function testDeleteLivro(): void
    {
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $autor = new Autor();
        $autor->setNome('Albert Camus');
        $entityManager->persist($autor);

        $autor2 = new Autor();
        $autor2->setNome('Machado de Assis');
        $entityManager->persist($autor2);

        $assunto = new Assunto();
        $assunto->setDescricao('Ficção');
        $entityManager->persist($assunto);
        
        $livro = new Livro();
        $livro->setTitulo('O Estrangeiro');
        $livro->setEditora('Editora Record');
        $livro->setEdicao(1);
        $livro->setValor('19.90');
        $livro->setAnoPublicacao(1942);

        $livro->addAutor($autor);
        $livro->addAutor($autor2);
        $livro->addAssunto($assunto);

        $entityManager->persist($livro);
        $entityManager->flush();

        $id = $livro->getCodL();

        $livroCadastrado = $entityManager
            ->getRepository(Livro::class)
            ->find($id);
        $this->assertNotNull($livroCadastrado);

        $crawler = $this->client->request('GET', '/livro/');
        $form = $crawler->filter('form[action="/livro/' . $id . '"]')->form();
        $token = $form->get('_token')->getValue();

        $this->client->request(
            'POST',
            '/livro/' . $id,
            [
                '_token' => $token
            ]
        );

        $this->assertResponseRedirects('/livro/');
        
        $entityManager->clear();

        $livroExcluido = $entityManager
            ->getRepository(Livro::class)
            ->find($id);

        $this->assertNull($livroExcluido);
    }
}