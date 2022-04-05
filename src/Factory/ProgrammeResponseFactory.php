<?php

declare(strict_types=1);

namespace App\Factory;

use App\Controller\Helper\ProgrammeRequestContentType;
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
        $acceptedContentSubtypes = ['json', 'xml', 'yaml'];
        $acceptedCustomTypes = ['gigel'];
        $groups = ['groups' => 'api:programme:all'];

        $subType = $this->programmeRequestContentType->getRequestType($request, $acceptedCustomTypes);

        if (in_array($subType, $acceptedCustomTypes)) {
            return new Response("Hello from $subType");
        }

        if (!in_array($subType, $acceptedContentSubtypes)) {
            return new Response('Unaccepted content-type', Response::HTTP_BAD_REQUEST);
        }

        $test = $this->serializer->serialize($data, $subType, $groups);

        return $this->programmeRequestContentType->getResponse($test, $subType);
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
