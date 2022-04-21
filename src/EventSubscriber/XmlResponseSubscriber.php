<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Serializer\SerializerInterface;

class XmlResponseSubscriber implements EventSubscriberInterface
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ViewEvent::class => 'encodeResponseDataXml',
        ];
    }

    public function encodeResponseDataXml(ViewEvent $event): void
    {
        $contentTypes = explode('/', $event->getRequest()->headers->get('accept'));

        if ($contentTypes[1] !== 'xml') {
            return;
        }

        $programmes = $this->serializer->serialize($event->getControllerResult(), $contentTypes[1], ['groups' => 'api:programme:all']);
        $event->setResponse(new Response($programmes, Response::HTTP_OK, [], true));
    }
}