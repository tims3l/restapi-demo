<?php
declare(strict_types=1);

namespace App\Entity;

use App\Attribute\Api;
use App\Repository\ProductRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use JsonSerializable;
use Symfony\Component\Validator\Constraints as Assert;

#[Api]
#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Index(name: "name_idx", fields: ["name"])]
#[ORM\Index(name: "price_idx", fields: ["price"])]
#[ORM\Index(name: "created_at_idx", fields: ["createdAt"])]
#[ORM\Index(name: "updated_at_idx", fields: ["updatedAt"])]
class Product implements ProductInterface, JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255, nullable: false)]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(length: 255, nullable: false, unique: true)]
    #[Assert\NotBlank]
    private string $sku;

    #[ORM\Column]
    private string $description;

    #[ORM\Column(type: Types::FLOAT, nullable: false)]
    #[Assert\NotBlank]
    private float $price;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_IMMUTABLE, options: ["default" => 'CURRENT_TIMESTAMP'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE, options: ["default" => 'CURRENT_TIMESTAMP'])]
    private \DateTime $updatedAt;
    
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable;
        $this->updatedAt = new \DateTime;
    }
    
    /**
     * @ORM\PreUpdate
     */
    public function preUpdate(): void
    {
        $this->setUpdatedAt(new DateTime('now'));
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function setSku(string $sku): self
    {
        $this->sku = $sku;
        return $this;
    }
    
    public function getDescription(): string
    {
        return $this->description;
    }
    
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }
    
    public function getPrice(): float
    {
        return $this->price;
    }
    
    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
    
    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
    
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }
    
    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id ?? null,
            'name' => $this->name,
            'sku' => $this->sku,
            'description' => $this->description,
            'price' => $this->price,
        ];
    }
}
