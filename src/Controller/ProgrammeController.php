<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Helper\ProgrammeRequestContentType;
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

    private ProgrammeRequestContentType $programmeRequestContentType;

    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ProgrammeRepository $programmeRepository,
        ProgrammeRequestContentType $programmeRequestContentType
    ) {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->programmeRepository = $programmeRepository;
        $this->programmeRequestContentType = $programmeRequestContentType;
    }

    /**
     * @Route (methods={"GET"})
     */
    public function getAllProgrammes(Request $request): array
    {
//        $queries = $request->query->all();
//        if ($queries) {
//            $filteredProgrammes = $this->programmeRepository->findBy($queries);
//            $filteredProgrammesSerialized = $this->serializer->serialize($filteredProgrammes, $contentSubtype, $groups);
//
//            return $this->programmeRequestContentType->getResponse($filteredProgrammesSerialized, $contentSubtype);
//        }

//        TODO Event dispatch for queries

        return $this->programmeRepository->getProgrammes();
    }
}
