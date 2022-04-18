<?php

declare(strict_types=1);

namespace App\Controller\Api;

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
     * @Route (path="/api/programmes/join/{programmeId}", methods={"POST"})
     */
    public function index(Request $request, string $programmeId): Response
    {
        $loggedInUser = $this->getUser();
        $userToBeJoinedId = $request->toArray()['id'];
        $programmeToBeJoined = $this->programmeRepository->findOneBy(['id' => $programmeId]);

        if ($userToBeJoinedId !== $loggedInUser->getId()) {

        }

//
//        $programmeToBeJoined->addCustomer($user);
//
//        $this->entityManager->persist($programmeToBeJoined);
//        $this->entityManager->flush();

        return new Response('Joined programme successfully', Response::HTTP_OK);
    }
}
