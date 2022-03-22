<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Programme;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route (path="/api/programmes")
 */
class ProgrammeController
{
    private EntityManagerInterface $entityManager;
    private SerializerInterface $serializer;

    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

    /**
     * @Route (methods={"GET"})
     */
    public function getAllProgrammes(): Response
    {
        $programmeRepository = $this->entityManager->getRepository(Programme::class);

        $programmes = $programmeRepository->findAll();
        $data = $this->serializer->serialize($programmes, 'json', ['groups' => 'api:programme:all']);
//        $dataNew = $this->serializer->deserialize($data, 'App\Programme', 'json');


//        $serializerTest = new Serializer(
//            [new GetSetMethodNormalizer(), new ArrayDenormalizer()],
//            [new JsonEncoder()]
//        );
//
//        $persons = $serializerTest->deserialize($data, 'App\Programme[]', 'json');
//        var_dump($persons);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }
}