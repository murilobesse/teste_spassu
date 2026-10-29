<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\Assunto;
use App\Entity\Autor;
use App\Entity\Livro;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('pt_BR');

        // 1. Gerar Assuntos (Limite: 20 caracteres)
        $assuntos = [];
        for ($i = 0; $i < 15; $i++) {
            $assunto = new Assunto();
            // Gera até 3 palavras e corta para no máximo 20 caracteres
            $assunto->setDescricao(mb_substr($faker->unique()->words(3, true), 0, 20)); 
            $manager->persist($assunto);
            $assuntos[] = $assunto;
        }

        // 2. Gerar Autores (Limite: 40 caracteres)
        $autores = [];
        for ($i = 0; $i < 20; $i++) {
            $autor = new Autor();
            $autor->setNome(mb_substr($faker->unique()->name(), 0, 40));
            $manager->persist($autor);
            $autores[] = $autor;
        }

        // 3. Gerar Livros e relacioná-los
        for ($i = 0; $i < 40; $i++) {
            $livro = new Livro();
            
            // Título (Limite: 40 caracteres)
            $livro->setTitulo(mb_substr($faker->sentence(3), 0, 40));
            
            // Editora (Limite: 40 caracteres)
            $livro->setEditora(mb_substr($faker->company() . ' Editora', 0, 40));
            
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
