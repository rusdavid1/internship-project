<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Repository\ProgrammeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route (path="/api/programmes")
 */
class ProgrammeController
{
    private ProgrammeRepository $programmeRepository;

    private UserRepository $userRepository;

    private Security $security;

    public function __construct(
        ProgrammeRepository $programmeRepository,
        UserRepository $userRepository,
        Security $security
    ) {
        $this->programmeRepository = $programmeRepository;
        $this->userRepository = $userRepository;
        $this->security = $security;
    }

    /**
     * @Route (path="/", methods={"GET"}, name="api_get_programmes")
     */
    public function getAllProgrammes(): array
    {
        return $this->programmeRepository->findAll();
    }

    /**
     * @Route (path="/{programmeId}", methods={"PATCH"})
     */
    public function joinAProgrammeAction(Request $request, int $programmeId): Response
    {
        $loggedInUserId = $this->security->getUser()->getId();
        $userToBeJoinedId = json_decode($request->getContent())->id ?? null;
        $programmeToBeJoined = $this->programmeRepository->findOneBy(['id' => $programmeId]);

        if (null !== $userToBeJoinedId && $loggedInUserId !== $userToBeJoinedId) {
            if (!$this->security->isGranted('ROLE_ADMIN')) {
                return new Response('Not allowed', Response::HTTP_FORBIDDEN);
            }

            try {
                $this->userRepository->joinAProgramme($userToBeJoinedId, $programmeToBeJoined);
            } catch (EntityNotFoundException $e) {
                return new Response('User not found', Response::HTTP_NOT_FOUND);
            }

            return new Response('Joined programme successfully', Response::HTTP_OK);
        }

        try {
            $this->userRepository->joinAProgramme($loggedInUserId, $programmeToBeJoined);
        } catch (EntityNotFoundException $e) {
            return new Response('User not found', Response::HTTP_NOT_FOUND);
        }

        return new Response('Joined programme successfully', Response::HTTP_OK);
    }
}
