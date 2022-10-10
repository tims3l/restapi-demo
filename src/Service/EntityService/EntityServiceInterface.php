<?php
declare(strict_types=1);

namespace App\Service\EntityService;

interface EntityServiceInterface
{
    public function validate(object $entity);

    public function save(object $entity);
    
    public function remove(object $entity);
}
