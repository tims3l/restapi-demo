<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

interface RestApiInterface
{
    public function index(): JsonResponse;

    public function show(int $id): JsonResponse;

    public function new(): JsonResponse;

    public function update(int $id): JsonResponse;
    
    public function delete(int $id): JsonResponse;
}
