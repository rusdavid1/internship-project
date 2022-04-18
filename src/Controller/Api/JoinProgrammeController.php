<?php

declare(strict_types=1);

namespace App\Controller\Api;

use _PHPStan_c0c409264\Nette\Neon\Entity;
use App\Repository\ProgrammeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JoinProgrammeController extends AbstractController
{
    private ProgrammeRepository $programmeRepository;

    private EntityManagerInterface $entityManager;

    public function __construct(ProgrammeRepository $programmeRepository, EntityManagerInterface $entityManager)
    {
        $this->programmeRepository = $programmeRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route (path="/api/join", methods={"POST"})
     */
    public function index(Request $request): Response
    {
//        iau useru logat

        $user = $this->getUser();

//        iau id-u programme-ului

        $programmeId = $request->toArray()['id'];

        $programmeToBeJoined = $this->programmeRepository->findOneBy(['id' => $programmeId]);

        $programmeToBeJoined->addCustomer($user);

        $this->entityManager->persist($programmeToBeJoined);

        $this->entityManager->flush();

        return new Response('Joined programme successfully', Response::HTTP_OK);

    }
}
