<?php

namespace App\Entity;

use App\Repository\AutorRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: AutorRepository::class)]
class Autor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'codAu')]
    private ?int $CodAu = null;

    #[ORM\Column(length: 40)]
    private ?string $nome = null;

    #[ORM\OneToMany(mappedBy: 'autor', targetEntity: LivroAutor::class)]
    private ?Collection $livros = null;

    public function getCodAu(): ?int
    {
        return $this->CodAu;
    }

    public function setCodAu(int $CodAu): static
    {
        $this->CodAu = $CodAu;

        return $this;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): static
    {
        $this->nome = $nome;

        return $this;
    }

    public function getLivros()
    {
        return $this->livros;
    }
}
