<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Programme;
use App\Repository\ProgrammeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    )
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->programmeRepository = $programmeRepository;
    }

    /**
     * @Route (methods={"GET"})
     */
    public function getAllProgrammes(): Response
    {
        $programmeRepository = $this->entityManager->getRepository(Programme::class);

        $programmes = $programmeRepository->findAll();
        $data = $this->serializer->serialize($programmes, 'json', ['groups' => 'api:programme:all']);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Route (path="/filter/{name}/")
     */
    public function filterName(string $name): Response
    {
        $data = $this->programmeRepository->filterProgrammeByName($name);
        $filteredProgrammes = $this->serializer->serialize($data, 'json', ['groups' => 'api:programme:all']);

        return new JsonResponse($filteredProgrammes, Response::HTTP_OK, [], true);
    }

}