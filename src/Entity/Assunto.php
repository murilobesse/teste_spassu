<?php

namespace App\Entity;

use App\Repository\AssuntoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AssuntoRepository::class)]
class Assunto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'codAs')]
    private ?int $CodAs = null;

    #[ORM\Column(length: 20)]
    private ?string $descricao = null;

    /** @var Collection<int, Livro> */
    #[ORM\ManyToMany(targetEntity: Livro::class, mappedBy: 'assuntos')]
    private Collection $livros;

    public function __construct()
    {
        $this->livros = new ArrayCollection();
    }

    public function getCodAs(): ?int
    {
        return $this->CodAs;
    }

    public function setCodAs(int $CodAs): static
    {
        $this->CodAs = $CodAs;

        return $this;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(string $descricao): static
    {
        $this->descricao = $descricao;

        return $this;
    }

    /** @return Collection<int, Livro> */
    public function getLivros(): Collection
    {
        return $this->livros;
    }
}
