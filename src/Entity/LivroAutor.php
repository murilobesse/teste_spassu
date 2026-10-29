<?php

namespace App\Entity;

use App\Repository\LivroAutorRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LivroAutorRepository::class)]
class LivroAutor
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Livro::class, inversedBy: 'autores')]
    #[ORM\JoinColumn(name: 'Livro_Codl', referencedColumnName: 'codl')]
    private ?Livro $livro = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Autor::class, inversedBy: 'livros')]
    #[ORM\JoinColumn(name: 'Autor_CodAu', referencedColumnName: 'codAu')]
    private ?Autor $autor = null;
}