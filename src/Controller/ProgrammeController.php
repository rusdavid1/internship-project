<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Programme;
use App\Repository\ProgrammeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route (path="/api/programmes")
 */
class ProgrammeController
{
    private EntityManagerInterface $entityManager;

    private SerializerInterface $serializer;

    private ProgrammeRepository $programmeRepository;

    private int $maxProgrammesPerPage;

    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ProgrammeRepository $programmeRepository,
        string $maxProgrammesPerPage
    ) {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->programmeRepository = $programmeRepository;
        $this->maxProgrammesPerPage = (int)$maxProgrammesPerPage;
    }

    /**
     * @Route (methods={"GET"})
     */
    public function getAllProgrammes(Request $request): Response
    {
        $acceptHeader = $request->headers->get('accept');
        $bar = explode('/', $acceptHeader);
        $contentSubtype = $bar[1] ?? 'json';

        if ($contentSubtype !== 'json' && $contentSubtype !== 'xml') {
            return new Response('Invalid Content-Type', Response::HTTP_BAD_REQUEST);
        }

        $queries = $request->query->all();
        if ($queries) {
            $filteredProgrammes = $this->programmeRepository->findBy($queries);
            $filteredProgrammesSerialized =
                $this->
                serializer->
                serialize($filteredProgrammes, $contentSubtype, ['groups' => 'api:programme:all']);

            if ($contentSubtype === 'json') {
                return new JsonResponse($filteredProgrammesSerialized, Response::HTTP_OK, [], true);
            }

            return new Response($filteredProgrammesSerialized, Response::HTTP_OK, []);
        }

        $programmeRepository = $this->entityManager->getRepository(Programme::class);

        $programmes = $programmeRepository->findAll();
        $serializedProgrammes =
            $this->
            serializer->
            serialize($programmes, $contentSubtype, ['groups' => 'api:programme:all']);

        if ($contentSubtype === 'json') {
            return new JsonResponse($serializedProgrammes, Response::HTTP_OK, [], true);
        }

        return new Response($serializedProgrammes, Response::HTTP_OK, []);
    }
}
