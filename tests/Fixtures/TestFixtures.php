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
        $admin = new User();
        $admin->setEmail('admin@teste.com');
        $admin->setNome('Administrador');
        $admin->setAtivo(true);
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, '123456'));
        $manager->persist($admin);

        $user = new User();
        $user->setEmail('user@teste.com');
        $user->setNome('Usuário');
        $user->setAtivo(true);
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher->hashPassword($user, '123456'));
        $manager->persist($user);

        $autor1 = new Autor();
        $autor1->setNome('Albert Camus');
        $manager->persist($autor1);

        $autor2 = new Autor();
        $autor2->setNome('Miguel de Cervantes');
        $manager->persist($autor2);

        $assunto1 = new Assunto();
        $assunto1->setDescricao('Ficção');
        $manager->persist($assunto1);

        $assunto2 = new Assunto();
        $assunto2->setDescricao('Romance');
        $manager->persist($assunto2);

        $livro1 = new Livro();
        $livro1->setTitulo('O Estrangeiro');
        $livro1->setEditora('Editora Record');
        $livro1->setEdicao(1);
        $livro1->setAnoPublicacao('1942');
        $livro1->setValor(19.99);
        $livro1->addAutor($autor1);
        $livro1->addAssunto($assunto1);
        $manager->persist($livro1);

        $livro2 = new Livro();
        $livro2->setTitulo('Dom Quixote');
        $livro2->setEditora('Editora');
        $livro2->setEdicao(2);
        $livro2->setAnoPublicacao('1605');
        $livro2->setValor(35.78);
        $livro2->addAutor($autor2);
        $livro2->addAssunto($assunto2);
        $manager->persist($livro2);

        $livro3 = new Livro();
        $livro3->setTitulo('Livro Teste 3');
        $livro3->setEditora('Editora Teste');
        $livro3->setEdicao(1);
        $livro3->setAnoPublicacao('2022');
        $livro3->setValor(51.99);
        $livro3->addAutor($autor1);
        $livro3->addAutor($autor2);
        $livro3->addAssunto($assunto1);
        $livro3->addAssunto($assunto2);
        $manager->persist($livro3);

        $manager->flush();
    }
}