<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Product;
use Tims3l\RestApi\Repository\AbstractRepository;

class ProductRepository extends AbstractRepository
{
    protected const ENTITY_CLASS = Product::class;
}
