<?php
declare(strict_types=1);

namespace App\Service\RestApi;

use App\Service\Response\Response;
use App\Service\EntityService\EntityServiceInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RestApi implements RestApiInterface
{
    protected string $entityClass;

    private EntityServiceInterface $entityService;
    private ?Request $request;
    private ValidatorInterface $validator;
    private LoggerInterface $logger;
    private ServiceEntityRepositoryInterface $repository;

    public function __construct(EntityServiceInterface $entityService, RequestStack $requestStack, ValidatorInterface $validator, LoggerInterface $logger)
    {
        $this->entityService = $entityService;
        $this->request = $requestStack->getCurrentRequest();
        $this->validator = $validator;
        $this->logger = $logger;
    }
    
    public function index(): JsonResponse
    {
        try {
            return new JsonResponse(
                new Response($this->repository->findAll()),
                JsonResponse::HTTP_OK
            );
        } catch (\Throwable $e) {
            return $this->handleHttpException($e);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            return new JsonResponse(
                new Response($this->getEntity($id)),
                JsonResponse::HTTP_OK
            );
        } catch (\Throwable $e) {
            return $this->handleHttpException($e);
        }
    }

    public function new(): JsonResponse
    {
        return $this->store($this->getEntity(), JsonResponse::HTTP_CREATED);
    }

    public function update(int $id): JsonResponse
    {
        return $this->store($this->getEntity($id));
    }

    public function delete(int $id): JsonResponse
    {
        try {
            $this->entityService->remove($this->getEntity($id));

            return new JsonResponse(new Response(''), JsonResponse::HTTP_NO_CONTENT);

        } catch (\Throwable $e) {
            return $this->handleHttpException($e);
        }
    }

    protected function getEntity(int $id = null): object
    {
        if ($id === null) {
            return new $this->entityClass();
        }

        $entity = $this->repository->find($id);

        if ($entity === null) {
            throw new NotFoundHttpException();
        }

        return $entity;
    }

    protected function store(object $entity, int $statusCode = JsonResponse::HTTP_OK)
    {
        try {
            $this->fillEntityByRequest($entity);

            $errors = $this->validator->validate($entity);

            if (count($errors) > 0) {
                return new JsonResponse(new Response($entity, $errors), JsonResponse::HTTP_BAD_REQUEST);
            }

            $this->entityService->save($entity);

            return new JsonResponse(new Response($entity, $errors), $statusCode);

        } catch (\Throwable $e) {
            return $this->handleHttpException($e);
        }
    }

    protected function fillEntityByRequest(object $entity): void {
        $reflectionExtractor = new ReflectionExtractor();

        foreach ($this->request->request->all() as $property => $value) {
            settype($value, $reflectionExtractor->getTypes($this->entityClass, $property)[0]->getBuiltinType());
            $entity->{$reflectionExtractor->getWriteInfo($this->entityClass, $property)->getName()}($value);
        }
    }

    protected function handleHttpException(\Throwable $e, string $level = LogLevel::CRITICAL): JsonResponse
    {
        $this->logger->log($level, $e->getMessage(), (array)$e);

        if ($e instanceof NotFoundHttpException) {
            return new JsonResponse(new Response(null, [JsonResponse::$statusTexts[404]]), JsonResponse::HTTP_NOT_FOUND);
        }
        
        return new JsonResponse(new Response(null, ['Unknown']), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function setEntityClass(string $class): self
    {
        $this->entityClass = $class;
        $this->repository = $this->entityService->getRepository($class);
        return $this;
    }
}