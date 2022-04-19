<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class HeaderResponseSubscriber implements EventSubscriberInterface
{
    private string $apiVersion;

    public function __construct(string $apiVersion)
    {
        $this->apiVersion = $apiVersion;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ResponseEvent::class => 'addVersionHeader',
        ];
    }

    public function addVersionHeader(ResponseEvent $event): void
    {
        $routeAttribute = $event->getRequest()->attributes->get('_route');

        if (null === $routeAttribute || !strpos('api', $routeAttribute)) {
            return;
        }

        $response = $event->getResponse();
        $response->headers->add(['X-API-VERSION' => $this->apiVersion]);

        $response->send();
    }
}
