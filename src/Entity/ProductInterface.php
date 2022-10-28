<?php
declare(strict_types=1);

namespace App\Entity;

use DateTime;
use DateTimeImmutable;

interface ProductInterface
{
    public function getId(): int;
    
    public function getName(): string;

    public function setName(string $name): self;

    public function getSku(): string;

    public function setSku(string $sku): self;
    
    public function getDescription(): string;
    
    public function setDescription(string $description): self;
    
    public function getPrice(): float;
    
    public function setPrice(float $price): self;

    public function getCreatedAt(): DateTimeImmutable;
    
    public function setCreatedAt(DateTimeImmutable $createdAt): self;
    
    public function getUpdatedAt(): DateTime;
    
    public function setUpdatedAt(DateTime $updatedAt): self;
    
    public function jsonSerialize(): array;
}
