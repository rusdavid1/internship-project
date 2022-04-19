<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Programme\ProgrammeRequestContentType;
use App\Repository\ProgrammeRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Serializer\SerializerInterface;

class ResponseContentSubscriber implements EventSubscriberInterface
{
    private ProgrammeRepository $programmeRepository;

    private ProgrammeRequestContentType $programmeRequestContentType;

    private SerializerInterface $serializer;

    public function __construct(
        ProgrammeRepository $programmeRepository,
        ProgrammeRequestContentType $programmeRequestContentType,
        SerializerInterface $serializer
    ) {
        $this->programmeRepository = $programmeRepository;
        $this->programmeRequestContentType = $programmeRequestContentType;
        $this->serializer = $serializer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
          ViewEvent::class => 'encodeResponseData'
        ];
    }

    public function encodeResponseData(ViewEvent $event)
    {
        $acceptedContentSubtypes = ['json', 'xml', 'yaml'];
        $acceptedCustomTypes = ['gigel'];
        $groups = ['groups' => 'api:programme:all'];
        $request = $event->getRequest();
        $queries = $request->query->all();

        $programmes = $event->getControllerResult();

        $types = $this->programmeRequestContentType->getRequestType($request);

        if (in_array($types, $acceptedCustomTypes)) {
            $event->setResponse(new Response("Hello from $types"));

            return;
        }

        if (null === $types || count($types) !== 2 || !in_array($types[1], $acceptedContentSubtypes)) {
            $event->setResponse(new Response('Unaccepted content-type', Response::HTTP_BAD_REQUEST));

            return;
        }

        $subType = $types[1];

        if (count($queries) > 0) {
            $filteredProgrammes = $this->programmeRepository->findByResults($queries);
            $data = $this->serializer->serialize($filteredProgrammes, $subType, $groups);
            if ($subType === 'json') {
                $event->setResponse(new JsonResponse($data, Response::HTTP_OK, [], true));

                return;
            }

            $event->setResponse(new Response($data, Response::HTTP_OK, []));

            return;
        }

        $test = $this->serializer->serialize($programmes, $subType, $groups);
        $event->setResponse(new JsonResponse($test, Response::HTTP_OK, [], true));
    }
}
