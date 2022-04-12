<?php

declare(strict_types=1);

namespace App\Factory;

use App\Programme\ProgrammeRequestContentType;
use App\Repository\ProgrammeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;

class ProgrammeResponseFactory
{
    private SerializerInterface $serializer;

    private ProgrammeRequestContentType $programmeRequestContentType;

    private ProgrammeRepository $programmeRepository;

    public function __construct(
        SerializerInterface $serializer,
        ProgrammeRequestContentType $programmeRequestContentType,
        ProgrammeRepository $programmeRepository
    ) {
        $this->serializer = $serializer;
        $this->programmeRequestContentType = $programmeRequestContentType;
        $this->programmeRepository = $programmeRepository;
    }

    public function getProgrammesResponse(Request $request, array $data): Response
    {
        $acceptedContentSubtypes = ['json', 'xml', 'yaml', 'gigel'];
        $acceptedCustomTypes = ['gigel'];
        $groups = ['groups' => 'api:programme:all'];

        $types = $this->programmeRequestContentType->getRequestType($request);

        if (in_array($types, $acceptedCustomTypes)) {
            return new Response("Hello from $types");
        }

        if (null === $types || count($types) !== 2 || !in_array($types[1], $acceptedContentSubtypes)) {
            return new Response('Unaccepted content-type', Response::HTTP_BAD_REQUEST);
        }

        $subType = $types[1];

        $serializedProgrammes = $this->serializer->serialize($data, $subType, $groups);

        return $this->programmeRequestContentType->getResponse($serializedProgrammes, $subType);
    }

    /**
     * @return Response|void
     */
    public function getFilteredProgrammesResponse(Request $request)
    {
        $groups = ['groups' => 'api:programme:all'];
        $subType = $this->programmeRequestContentType->getRequestType($request);
        $queries = $request->query->all();

        if ($queries) {
            $filteredProgrammes = $this->programmeRepository->findByResults($queries);
            $filteredProgrammesSerialized = $this->serializer->serialize($filteredProgrammes, $subType, $groups);

            return $this->programmeRequestContentType->getResponse($filteredProgrammesSerialized, $subType);
        }
    }
}
