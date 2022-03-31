<?php

declare(strict_types=1);

namespace App\Factory;

use App\Controller\Helper\ProgrammeRequestContentType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;


class ProgrammeResponseFactory
{
    private SerializerInterface $serializer;

    private ProgrammeRequestContentType $programmeRequestContentType;

    public function __construct(
        SerializerInterface $serializer,
        ProgrammeRequestContentType $programmeRequestContentType
    ) {
        $this->serializer = $serializer;
        $this->programmeRequestContentType = $programmeRequestContentType;
    }

    public function getResponse(Request $request, array $data): Response
    {
        $acceptedContentSubtypes = ['json', 'xml'];
        $groups = ['groups' => 'api:programme:all'];

        $subType = $this->programmeRequestContentType->getRequestType($request);

        if (!in_array($subType, $acceptedContentSubtypes)) {
            return new Response('Invalid Content-Type', Response::HTTP_BAD_REQUEST);
        }

        $test = $this->serializer->serialize($data, $subType, $groups);

        return $this->programmeRequestContentType->getResponse($test, $subType);
    }
}
