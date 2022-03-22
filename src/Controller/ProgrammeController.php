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
    )
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->programmeRepository = $programmeRepository;
        $this->maxProgrammesPerPage = (int)$maxProgrammesPerPage;
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
     * @Route (path="/filter", methods={"GET"})
     */
    public function filterProgrammeByName(Request $request): Response
    {
        $query = $request->query->get('name');

        $data = $this->programmeRepository->filterProgrammeByName($query);
        $filteredProgrammes = $this->serializer->serialize($data, 'json', ['groups' => 'api:programme:all']);

        return new JsonResponse($filteredProgrammes, Response::HTTP_OK, [], true);
    }

    /**
     * @Route (path="/page", methods={"GET"})
     */
    public function paginateProgrammes(Request $request)
    {
        $query = $request->query->get('number');

        $data = $this->programmeRepository->getPaginatedProgrammes((int)$query, $this->maxProgrammesPerPage);
        $paginatedProgrammes = $this->serializer->serialize($data, 'json', ['groups' => 'api:programme:all']);

        return new JsonResponse($paginatedProgrammes, Response::HTTP_OK, [], true);

    }

}