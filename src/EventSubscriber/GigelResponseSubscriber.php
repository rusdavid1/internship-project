<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;

class GigelResponseSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ViewEvent::class => ['encodeResponseDataGigel', 1],
        ];
    }

    public function encodeResponseDataGigel(ViewEvent $event): void
    {
        if ($event->getRequest()->headers->get('accept') !== 'gigel') {
            return;
        }

        $event->setResponse(new Response('Hello from Gigel', Response::HTTP_OK));
    }
}