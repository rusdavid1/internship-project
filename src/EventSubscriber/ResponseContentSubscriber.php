<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Factory\ProgrammeResponseFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;

class ResponseContentSubscriber implements EventSubscriberInterface
{
    private ProgrammeResponseFactory $programmeResponseFactory;

    public function __construct(ProgrammeResponseFactory $programmeResponseFactory)
    {
        $this->programmeResponseFactory = $programmeResponseFactory;
    }

    public static function getSubscribedEvents(): array
    {
        return [
          ViewEvent::class => 'encodeResponseData'
        ];
    }

    public function encodeResponseData(ViewEvent $event)
    {
        $request = $event->getRequest();
        $queries = $request->query->all();

        if (count($queries) > 0) {
            $event->setResponse($this->programmeResponseFactory->getFilteredProgrammesResponse($request));

            return;
        }

        $programmes = $event->getControllerResult();
        $event->setResponse($this->programmeResponseFactory->getProgrammesResponse($request, $programmes));
    }
}