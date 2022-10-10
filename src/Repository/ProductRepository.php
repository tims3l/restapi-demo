<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Product;

class ProductRepository extends AbstractRepository
{
    protected const ENTITY_CLASS = Product::class;
    
}
