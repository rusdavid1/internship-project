<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Serializer\SerializerInterface;

class JsonResponseSubscriber implements EventSubscriberInterface
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ViewEvent::class => 'encodeResponseDataJson',
        ];
    }

    public function encodeResponseDataJson(ViewEvent $event): void
    {
        $test = $event->getRequest()->headers->get('accept');

        if ($test !== 'text/json') {
            return;
        }

        $programmes = $this->serializer->serialize($event->getControllerResult(), 'json');

        var_dump($programmes);
//        $event->setResponse(new JsonResponse($programmes, Response::HTTP_OK, [], true));
        $programmes = $event->getControllerResult();
        $event->setResponse(new Response($programmes, Response::HTTP_OK, []));
    }
}