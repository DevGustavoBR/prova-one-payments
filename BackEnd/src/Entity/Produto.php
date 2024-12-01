<?php

namespace App\Entity;

use App\Repository\ProdutoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: ProdutoRepository::class)]
#[ORM\Table(name:"produtos")]
class Produto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\NotBlank(message: "O nome do produto é obrigatório.")]
    #[Assert\Length(
        max: 60,
        maxMessage: "O nome do produto deve ter no máximo {{ limit }} caracteres."
    )]
    private ?string $nome = null;
     
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 3, nullable: true)]
    #[Assert\NotBlank(message: "O preço é obrigatório.")]
    #[Assert\Positive(message: "O preço deve ser maior que zero.")]
    #[Assert\Type(
        type: 'numeric',
        message: "O preço deve ser um número válido."
    )]
    private ?string $preco = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\NotBlank(message: "A descrição é obrigatória.")]
    #[Assert\Length(
        max: 1000,
        maxMessage: "A descrição deve ter no máximo {{ limit }} caracteres."
    )]
    private ?string $descricao = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "A imagem é obrigatória.")]
    #[Assert\Url(message: "A URL da imagem é inválida.")]
    #[Assert\Callback([self::class, "validarImagemProduto"], groups: [])]
    private ?string $imagemProduto = null;
    public function validarImagemProduto(ExecutionContextInterface $contexto){
        if(!filter_var($this->imagemProduto, FILTER_VALIDATE_URL)) {
            $contexto->buildViolation("A URL da imagem é inválida.")->addViolation();
            return;
        }

        $headers = get_headers($this->imagemProduto, 1);
        if(isset($headers["Content-Type"]) && strpos($headers['Content-Type'], 'image/') === false) {
         $contexto->buildViolation('A URL não aponta para uma imagem válida')->addViolation();
        }
    }    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(?string $nome): static
    {
        $this->nome = $nome;

        return $this;
    }

    public function getPreco(): ?string
    {
        return $this->preco;
    }

    public function setPreco(?string $preco): static
    {
        $this->preco = $preco;

        return $this;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(?string $descricao): static
    {
        $this->descricao = $descricao;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
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

    public function getImagemProduto(): ?string
    {
        return $this->imagemProduto;
    }

    public function setImagemProduto(string $imagemProduto): static
    {
        $this->imagemProduto = $imagemProduto;

        return $this;
    }
}
