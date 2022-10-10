<?php
declare(strict_types=1);

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

abstract class AbstractRepository extends ServiceEntityRepository
{
    protected const ENTITY_CLASS = '';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, static::ENTITY_CLASS);
    }
    
}
