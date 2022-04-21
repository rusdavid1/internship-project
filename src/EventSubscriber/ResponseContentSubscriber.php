<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Exception\UnacceptedContentTypeException;
use App\Programme\ProgrammeRequestContentType;
use App\Repository\ProgrammeRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Serializer\SerializerInterface;

class ResponseContentSubscriber implements EventSubscriberInterface
{
    public array $acceptedContentTypes = ['json','xml','gigel'];

    public static function getSubscribedEvents(): array
    {
        return [
          ViewEvent::class => 'encodeResponseData'
        ];
    }

    public function encodeResponseData(ViewEvent $event): void
    {
        $contentTypes = $event->getRequest()->headers->get('accept');

        if (count(explode('/', $contentTypes)) > 1) {
            $contentTypes = explode('/', $contentTypes)[1];
        }

        if (false === in_array($contentTypes, $this->acceptedContentTypes)) {
            $event->setResponse(
                new Response(
                    'The application doesn\'t accept this type of content',
                    Response::HTTP_NOT_ACCEPTABLE)
            );
        }
    }
}
