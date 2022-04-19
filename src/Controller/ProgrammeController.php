<?php

declare(strict_types=1);

namespace App\Controller;

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

    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ProgrammeRepository $programmeRepository
    ) {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->programmeRepository = $programmeRepository;
    }

    /**
     * @Route (methods={"GET"}, name="api_get_programmes")
     */
    public function getAllProgrammes(Request $request): Response
    {
        $queries = $request->query->all();
        if ($queries) {
            $test = $this->programmeRepository->findByResults($queries);
            $testSerialized = $this->serializer->serialize($test, 'json', ['groups' => 'api:programme:all']);

            return new JsonResponse($testSerialized, Response::HTTP_OK, [], true);
        }

        $programmes = $this->programmeRepository->findAll();
        $serializedProgrammes = $this->serializer->serialize($programmes, 'json', ['groups' => 'api:programme:all']);

        return new JsonResponse($serializedProgrammes, Response::HTTP_OK, [], true);
    }
}
