<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Serializer\SerializerInterface;

class AcceptedContentResponseSubscriber implements EventSubscriberInterface
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ViewEvent::class => ['encodeResponseData', 0],
        ];
    }

    public function encodeResponseData(ViewEvent $event): void
    {
        $contentTypes = explode('/', $event->getRequest()->headers->get('accept'));

        $programmes = $this->serializer->serialize(
            $event->getControllerResult(),
            $contentTypes[1],
            ['groups' => 'api:programme:all']
        );
        $event->setResponse(new JsonResponse($programmes, Response::HTTP_OK, [], true));
    }
}
