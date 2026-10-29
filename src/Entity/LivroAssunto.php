<?php

namespace App\Entity;

use App\Repository\LivroAssuntoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LivroAssuntoRepository::class)]
class LivroAssunto
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Livro::class)]
    #[ORM\JoinColumn(name: 'Livro_Codl', referencedColumnName: 'codl')]
    private ?Livro $livro = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Assunto::class)]
    #[ORM\JoinColumn(name: 'Assunto_CodAs', referencedColumnName: 'codAs')]
    private ?Assunto $assunto = null;
}