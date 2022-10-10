<?php
declare(strict_types=1);

namespace App\Service\EntityService;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EntityService implements EntityServiceInterface
{
    private ValidatorInterface $validator;
    private ManagerRegistry $doctrine;
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $doctrine, ValidatorInterface $validator)
    {
        $this->doctrine = $doctrine;
        $this->entityManager = $this->doctrine->getManager();
        $this->validator = $validator;
    }

    public function validate(object $entity): ConstraintViolationListInterface
    {
        return $this->validator->validate($entity);
    }

    public function save(object $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }
    
    public function remove(object $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }
}