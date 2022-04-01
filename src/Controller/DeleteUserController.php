<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route (path="/api/users")
 */
class DeleteUserController extends AbstractController
{
    private UserRepository $userRepository;

    private EntityManagerInterface $entityManager;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }


    /**
     * @Route (path="/{id}", methods={"DELETE"})
     */
    public function deleteUserAction(int $id): Response
    {
        $deletedUser = $this->userRepository->findOneBy(['id' => $id]);

        if (null === $deletedUser) {
            return new Response('User doesn\'t exist');
        }

        $this->entityManager->remove($deletedUser);
        $this->entityManager->flush();

        return new Response('Account removed', Response::HTTP_OK);
    }

    /**
     * @Route (path="/recover", methods={"POST"})
     */
    public function recoverUserAction(Request $request): Response
    {
        $email = $request->toArray()['email'];
        $this->entityManager->getFilters()->disable('softdeleteable');
        $deletedUser = $this->userRepository->findOneBy(['email' => $email]);

        if (null === $deletedUser) {
            return new Response('Invalid user', Response::HTTP_BAD_REQUEST);
        }

        $deletedUser->setDeletedAt(null);
        $this->entityManager->flush();

        return new Response('Account recovered', Response::HTTP_OK);
    }
}
