<?php

declare(strict_types=1);

namespace App\Controller;

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
        $userToDelete = $this->userRepository->findOneBy(['id' => $id]);

        if (null === $userToDelete) {
            return new Response('User doesn\'t exist', Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($userToDelete);
        $this->entityManager->flush();

        return new Response('Account removed', Response::HTTP_OK);
    }

    /**
     * @Route (path="/recover", methods={"POST"})
     */
    public function recoverUserAction(Request $request): Response
    {
        $email = $request->toArray()['email'];

        if (null === $email) {
            return new Response('You need to specify the email of the deleted account', Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->getFilters()->disable('softdeleteable');
        $deletedUser = $this->userRepository->findOneBy(['email' => $email]);

        if (null === $deletedUser) {
            return new Response('Invalid user', Response::HTTP_BAD_REQUEST);
        }

        $deletedUser->setDeletedAt(null);
        $this->entityManager->flush();

        return new Response('Account recovered', Response::HTTP_OK);
    }

//    TODO Delete before PR
    /**
     * @Route (path="/recover-all-users", methods={"GET"})
     */
    public function index()
    {
        $this->entityManager->getFilters()->disable('softdeleteable');

        $users = $this->userRepository->findAll();

        foreach ($users as $user) {
            $user->setDeletedAt(null);
            $this->entityManager->flush();
        }

        return new Response('users recovered', Response::HTTP_OK);
    }
}
