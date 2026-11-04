<?php

namespace App\Tests\Fixtures;

use App\Entity\Assunto;
use App\Entity\Autor;
use App\Entity\Livro;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TestFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $admin = $this->createUser('admin@teste.com', 'Administrador', '123456', ['ROLE_ADMIN']);
        $manager->persist($admin);

        $user = $this->createUser('user@teste.com', 'Usuário', '123456', ['ROLE_USER']);
        $manager->persist($user);

        $autor1 = $this->createAutor('Albert Camus');
        $manager->persist($autor1);

        $autor2 = $this->createAutor('Miguel de Cervantes');
        $manager->persist($autor2);

        $assunto1 = $this->createAssunto('Ficção');
        $manager->persist($assunto1);

        $assunto2 = $this->createAssunto('Romance');
        $manager->persist($assunto2);

        $livro1 = $this->createLivro('O Estrangeiro', 'Editora Record', 1, '1942', 19.99);
        $livro1->addAutor($autor1);
        $livro1->addAssunto($assunto1);
        $manager->persist($livro1);

        $livro2 = $this->createLivro('Dom Quixote', 'Editora', 2, '1605', 35.78);
        $livro2->addAutor($autor2);
        $livro2->addAssunto($assunto2);
        $manager->persist($livro2);

        $livro3 = $this->createLivro('Livro Teste 3', 'Editora Teste', 1, '2022', 51.99);
        $livro3->addAutor($autor1);
        $livro3->addAutor($autor2);
        $livro3->addAssunto($assunto1);
        $livro3->addAssunto($assunto2);
        $manager->persist($livro3);

        $manager->flush();
    }

    private function createUser(string $email, string $nome, string $password, array $roles = ['ROLE_USER']): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setNome($nome);
        $user->setAtivo(true);
        $user->setRoles($roles);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        return $user;
    }

    private function createAutor(string $nome): Autor
    {
        $autor = new Autor();
        $autor->setNome($nome);

        return $autor;
    }

    private function createAssunto(string $descricao): Assunto
    {
        $assunto = new Assunto();
        $assunto->setDescricao($descricao);

        return $assunto;
    }

    private function createLivro(string $titulo, string $editora, int $edicao, string $anoPublicacao, float $valor): Livro
    {
        $livro = new Livro();
        $livro->setTitulo($titulo);
        $livro->setEditora($editora);
        $livro->setEdicao($edicao);
        $livro->setAnoPublicacao($anoPublicacao);
        $livro->setValor($valor);

        return $livro;
    }
}