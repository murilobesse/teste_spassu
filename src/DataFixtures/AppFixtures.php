<?php

namespace App\DataFixtures;

use App\Entity\Assunto;
use App\Entity\Autor;
use App\Entity\Livro;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('pt_BR');

        // Gerar Usuário ADMIN
        $admin = new User();
        $admin->setEmail('admin@teste.com');
        $admin->setNome('Administrador');
        $admin->setAtivo(true);
        $admin->setRoles(['ROLE_ADMIN']);

        $hashedPassword = $this->passwordHasher->hashPassword($admin, '123456');
        $admin->setPassword($hashedPassword);

        $manager->persist($admin);

        // Gerar Assuntos
        $assuntos = [];
        for ($i = 0; $i < 15; ++$i) {
            $assunto = new Assunto();
            // Gera até 3 palavras e corta para no máximo 20 caracteres
            $assunto->setDescricao(mb_substr($faker->unique()->words(3, true), 0, 20));
            $manager->persist($assunto);
            $assuntos[] = $assunto;
        }

        // Gerar Autores
        $autores = [];
        for ($i = 0; $i < 20; ++$i) {
            $autor = new Autor();
            $autor->setNome(mb_substr($faker->unique()->name(), 0, 40));
            $manager->persist($autor);
            $autores[] = $autor;
        }

        // Gerar Livros e vincular aos Autores e Assuntos
        for ($i = 0; $i < 40; ++$i) {
            $livro = new Livro();

            // Título
            $livro->setTitulo(mb_substr($faker->sentence(3), 0, 40));

            // Editora
            $livro->setEditora(mb_substr($faker->company().' Editora', 0, 40));

            $livro->setEdicao($faker->numberBetween(1, 5));
            $livro->setAnoPublicacao((string) $faker->year());
            $livro->setValor($faker->randomFloat(2, 20, 150));

            // Adiciona de 1 a 3 autores aleatórios
            $qtdAutores = $faker->numberBetween(1, 3);
            $autoresEmbaralhados = $autores;
            shuffle($autoresEmbaralhados);
            $autoresLivro = array_slice($autoresEmbaralhados, 0, $qtdAutores);

            foreach ($autoresLivro as $autor) {
                $livro->addAutor($autor);
            }

            // Adiciona de 1 a 2 assuntos aleatórios
            $qtdAssuntos = $faker->numberBetween(1, 2);
            $assuntosEmbaralhados = $assuntos;
            shuffle($assuntosEmbaralhados);
            $assuntosLivro = array_slice($assuntosEmbaralhados, 0, $qtdAssuntos);

            foreach ($assuntosLivro as $assunto) {
                $livro->addAssunto($assunto);
            }

            $manager->persist($livro);
        }

        $manager->flush();
    }
}
