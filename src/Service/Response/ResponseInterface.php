<?php
declare(strict_types=1);

namespace App\Service\Response;

interface ResponseInterface {
    
    public function jsonSerialize(): array;
}
