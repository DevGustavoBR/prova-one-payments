<?php

namespace App\Entity;

use App\Repository\TransacaoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransacaoRepository::class)]
#[ORM\Table(name:"transacoes")]
class Transacao
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $idTransacao = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $status = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\Column(nullable: true)]
    private ?array $detalhes = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdTransacao(): ?string
    {
        return $this->idTransacao;
    }

    public function setIdTransacao(?string $idTransacao): static
    {
        $this->idTransacao = $idTransacao;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getDetalhes(): ?array
    {
        return $this->detalhes;
    }

    public function setDetalhes(?array $detalhes): static
    {
        $this->detalhes = $detalhes;

        return $this;
    }
}
