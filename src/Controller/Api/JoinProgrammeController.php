<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Repository\ProgrammeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class JoinProgrammeController extends AbstractController
{
    private ProgrammeRepository $programmeRepository;

    private UserRepository $userRepository;

    public function __construct(
        ProgrammeRepository $programmeRepository,
        UserRepository $userRepository
    ) {
        $this->programmeRepository = $programmeRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route (path="/api/programmes/join/{programmeId}", methods={"POST"})
     */
    public function index(Request $request, int $programmeId): Response
    {
        $loggedInUserId = $this->getUser()->getId();
        $userToBeJoinedId = json_decode($request->getContent())->id ?? null;
        $programmeToBeJoined = $this->programmeRepository->findOneBy(['id' => $programmeId]);

        if (null !== $userToBeJoinedId && $loggedInUserId !== $userToBeJoinedId) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');

            try {
                $this->userRepository->joinAProgramme($userToBeJoinedId, $programmeToBeJoined);
            } catch (NotFoundHttpException $e) {
                return new Response('User not found', Response::HTTP_NOT_FOUND);
            }

            return new Response('Joined programme successfully', Response::HTTP_OK);
        }

        $this->userRepository->joinAProgramme($loggedInUserId, $programmeToBeJoined);

        return new Response('Joined programme successfully', Response::HTTP_OK);
    }
}
