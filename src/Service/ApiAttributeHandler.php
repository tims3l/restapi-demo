<?php
declare(strict_types=1);

namespace App\Service;

class ApiAttributeHandler
{
    private const ATTR = 'App\\Attribute\\Api';

    public function hasAttribute(string $class): bool
    {
        foreach ((new \ReflectionClass($class))->getAttributes() as $attribute) {
            if ($attribute->getName() == self::ATTR) {
                return true;
            }
        }

        return false;
    }
}