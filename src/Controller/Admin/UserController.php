<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserUpdateFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private UserRepository $userRepository;

    private EntityManagerInterface $entityManager;

    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route(path="/users", methods={"GET"}, name="list_users")
     */
    public function listAllUsers(Request $request): Response
    {
        $query = $request->query->get('page') === null ? '1' : $request->query->get('page');

        $users = $this->userRepository->pagination($query);

        return $this->render('admin/listUsers.html.twig', [
            'users' => $users,
            'currentPage' => $query,
        ]);
    }

    /**
     * @Route(path="/users/{id}", methods={"GET", "POST"}, name="update_user")
     */
    public function updateUserAction(int $id, Request $request): Response
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);
        $form = $this->createForm(UserUpdateFormType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (null === $user) {
                return new Response('User not found', Response::HTTP_NOT_FOUND);
            }

            $firstName = $form->get('firstName')->getData();
            $lastName = $form->get('lastName')->getData();
            $email = $form->get('email')->getData();
            $phoneNumber = $form->get('phoneNumber')->getData();

            $user->firstName = $firstName;
            $user->lastName = $lastName;
            $user->email = $email;
            $user->phoneNumber = $phoneNumber;

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', 'The user was edited successfully');

            return $this->redirectToRoute('admin_list_users');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'The user was not edited');
        }

        return $this->render('admin/updateUser.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/users/delete/{id}", methods={"GET"}, name="delete_user")
     */
    public function deleteUserAction(int $id): Response
    {
        $userToDelete = $this->userRepository->findOneBy(['id' => $id]);

        if (null === $userToDelete) {
            return new Response('User not found', Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($userToDelete);
        $this->entityManager->flush();

        $this->addFlash('success', 'The user was deleted successfully');

        return $this->redirectToRoute('admin_list_users');
    }
}
