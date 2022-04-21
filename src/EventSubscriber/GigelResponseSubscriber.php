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
            ViewEvent::class => 'encodeResponseDataGigel',
        ];
    }

    public function encodeResponseDataGigel(ViewEvent $event): void
    {
        $test = $event->getRequest()->headers->get('accept');

        if ($test !== 'gigel') {
            return;
        }

        $event->setResponse(new Response('Hello from Gigel', Response::HTTP_OK));
    }
}