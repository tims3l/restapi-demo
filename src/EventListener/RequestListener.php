<?php
declare(strict_types=1);

namespace App\EventListener;

use App\Service\ApiAttributeHandler;
use App\Service\EntityService\EntityServiceInterface;
use App\Service\RestApi\RestApiInterface;
use App\Service\StrUtils;
use Spatie\Url\Url;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

#[AsEventListener(priority: 33)]
final class RequestListener
{
    private RequestEvent $event;
    private EntityServiceInterface $entityService;
    private ApiAttributeHandler $apiAttributeHandler;
    private RestApiInterface $api;

    public function __construct(EntityServiceInterface $entityService, ApiAttributeHandler $apiAttributeHandler, RestApiInterface $api)
    {
        $this->apiAttributeHandler = $apiAttributeHandler;
        $this->entityService = $entityService;
        $this->api = $api;
    }
    
    public function __invoke(RequestEvent $event)
    {
        $this->event = $event;

        try {
            $uri = Url::fromString($this->event->getRequest()->getPathInfo());
            
            $this->api->setEntityClass(
                $this->getEntityClassByUriSegment(
                    $uri->getFirstSegment()
                )
            );

            $this->dispatch($uri);
            
        } catch (ResourceNotFoundException $e) {
            return;
        }
    }
    
    private function dispatch(Url $uri): void
    {
        if (is_numeric($id = $uri->getLastSegment())) {
            $response = match($this->event->getRequest()->getMethod()){
                Request::METHOD_GET => $this->api->show((int) $id),
                Request::METHOD_PUT => $this->api->update((int) $id),
                Request::METHOD_DELETE => $this->api->delete((int) $id),
            };
        } else {
            $response = match($this->event->getRequest()->getMethod()){
                Request::METHOD_GET => $this->api->index(),
                Request::METHOD_POST => $this->api->new(),
            };
        }

        $this->event->setResponse($response);
    }
    
    private function getEntityClassByUriSegment(string $uriSegment): string
    {
        foreach ($this->entityService->getAllEntityClassnames() as $class) {
            if ($baseClass = strtolower(StrUtils::getClassBasename($class)) == $uriSegment && $this->apiAttributeHandler->hasAttribute($class)) {
                return $class;
            }
        }
        
        throw new ResourceNotFoundException();
    }
}