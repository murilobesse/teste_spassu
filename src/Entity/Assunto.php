<?php

namespace App\Entity;

use App\Repository\AssuntoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AssuntoRepository::class)]
class Assunto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'codAs')]
    private ?int $CodAs = null;

    #[ORM\Column(length: 20)]
    private ?string $Descricao = null;

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
        return $this->Descricao;
    }

    public function setDescricao(string $Descricao): static
    {
        $this->Descricao = $Descricao;

        return $this;
    }
}
