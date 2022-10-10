<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/product')]
final class ProductApiController extends AbstractRestApiController
{
    protected string $entityClass = Product::class;

    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Product $entity
     */
    protected function fillEntityByRequest(object $entity): void
    {
        $entity
            ->setName((string)$this->request->request->get('name'))
            ->setSku((string)$this->request->request->get('sku'))
            ->setDescription((string)$this->request->request->get('description'))
            ->setPrice((float)$this->request->request->get('price'));
    }
}
