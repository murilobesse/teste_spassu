<?php

namespace App\Entity;

use App\Repository\AssuntoRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: AssuntoRepository::class)]
class Assunto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'codAs')]
    private ?int $CodAs = null;

    #[ORM\Column(length: 20)]
    private ?string $descricao = null;

    #[ORM\OneToMany(mappedBy: 'assunto', targetEntity: LivroAssunto::class)]
    private ?Collection $livros = null;

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

    public function getLivros()
    {
        return $this->livros;
    }
}
