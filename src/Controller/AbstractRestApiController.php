<?php
declare(strict_types=1);

namespace App\Controller; 

use App\Service\Response\Response;
use App\Service\EntityService\EntityServiceInterface;
use App\Service\StrUtils;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractRestApiController extends AbstractController implements RestApiInterface
{
    protected string $entityClass;

    private ManagerRegistry $doctrine;
    private EntityServiceInterface $entityService;
    private ValidatorInterface $validator;
    private LoggerInterface $logger;

    protected ServiceEntityRepositoryInterface $repository;
    protected Request $request;

    /**
     * @required
     */
    public function initServices(ManagerRegistry $doctrine, EntityServiceInterface $entityService, RequestStack $requestStack, ValidatorInterface $validator, LoggerInterface $logger)
    {
        $this->doctrine = $doctrine;
        $this->entityService = $entityService;
        $this->request = $requestStack->getCurrentRequest();
        $this->validator = $validator;
        $this->logger = $logger;
    }
    
    #[Route('', methods: ['GET', 'HEAD'])]
    public function index(): JsonResponse
    {
        try {
            return $this->json(
                new Response($this->repository->findAll()),
                JsonResponse::HTTP_OK
            );
        } catch (\Throwable $e) {
            return $this->handleHttpException($e);
        }
    }

    #[Route('/{id}', methods: ['GET', 'HEAD'])]
    public function show(int $id): JsonResponse
    {
        try {
            return $this->json(
                new Response($this->getEntity($id)),
                JsonResponse::HTTP_OK
            );
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
    
    #[Route('', methods: ['POST'])]
    public function new(): JsonResponse
    {
        return $this->store($this->getEntity(), JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(int $id): JsonResponse
    {
        return $this->store($this->getEntity($id));
    }

    protected function store(object $entity, int $statusCode = JsonResponse::HTTP_OK)
    {
        try {
            $this->fillEntityByRequest($entity);

            $errors = $this->validator->validate($entity);

            if (count($errors) > 0) {
                return $this->json(new Response($entity, $errors), JsonResponse::HTTP_BAD_REQUEST);
            }

            $this->entityService->save($entity);

            return $this->json(new Response($entity, $errors), $statusCode);

        } catch (\Throwable $e) {
            return $this->handleHttpException($e);
        }
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->entityService->remove($this->getEntity($id));

            return $this->json(new Response(''), JsonResponse::HTTP_NO_CONTENT);

        } catch (\Throwable $e) {
            return $this->handleHttpException($e);
        }
    }

    protected function fillEntityByRequest(object $entity): void {}

    protected function handleHttpException(\Throwable $e, string $level = LogLevel::CRITICAL): JsonResponse
    {
//        throw $e; // todo: KIVENNI
//        dump($e);
        
        $this->logger->log($level, $e->getMessage(), (array)$e);

        if ($e instanceof NotFoundHttpException) {
            return $this->json(new Response(null, [JsonResponse::$statusTexts[404]]), JsonResponse::HTTP_NOT_FOUND);
        }
        
        return $this->json(new Response(null, ['Unknown']), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }

    public static function getSubscribedServices(): array
    {
        return [];
    }
}