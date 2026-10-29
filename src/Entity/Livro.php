<?php

namespace App\Entity;

use App\Repository\LivroRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: LivroRepository::class)]
class Livro
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'codl')]
    private ?int $Codl = null;

    #[ORM\Column(length: 40)]
    private ?string $Titulo = null;

    #[ORM\Column(length: 40)]
    private ?string $Editora = null;

    #[ORM\Column]
    private ?int $Edicao = null;

    #[ORM\Column(length: 4)]
    private ?string $AnoPublicacao = null;

    #[ORM\OneToMany(mappedBy: 'livro', targetEntity: LivroAutor::class)]
    private ?Collection $autores = null;

    #[ORM\OneToMany(mappedBy: 'livro', targetEntity: LivroAssunto::class)]
    private ?Collection $assuntos = null;

    public function getCodl(): ?int
    {
        return $this->Codl;
    }

    public function setCodl(int $Codl): static
    {
        $this->Codl = $Codl;

        return $this;
    }

    public function getTitulo(): ?string
    {
        return $this->Titulo;
    }

    public function setTitulo(string $Titulo): static
    {
        $this->Titulo = $Titulo;

        return $this;
    }

    public function getEditora(): ?string
    {
        return $this->Editora;
    }

    public function setEditora(string $Editora): static
    {
        $this->Editora = $Editora;

        return $this;
    }

    public function getEdicao(): ?int
    {
        return $this->Edicao;
    }

    public function setEdicao(int $Edicao): static
    {
        $this->Edicao = $Edicao;

        return $this;
    }

    public function getAnoPublicacao(): ?string
    {
        return $this->AnoPublicacao;
    }

    public function setAnoPublicacao(string $AnoPublicacao): static
    {
        $this->AnoPublicacao = $AnoPublicacao;

        return $this;
    }

    public function getAutores()
    {
        return $this->autores;
    }
}
