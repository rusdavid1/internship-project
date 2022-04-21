<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserCreateFormType;
use App\Form\UserFormProcessor;
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

    private UserFormProcessor $userFormProcessor;

    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserFormProcessor $userFormProcessor
    ) {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->userFormProcessor = $userFormProcessor;
    }

    /**
     * @Route(path="/users", methods={"GET"}, name="list_users")
     */
    public function listAllUsers(Request $request): Response
    {
        $page = $request->query->get('page') === null ? '1' : $request->query->get('page');

        $users = $this->userRepository->pagination($page);
        return $this->render('admin/listUsers.html.twig', [
            'users' => $users,
            'currentPage' => $page,
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
            $this->addFlash('error', 'The user was not found');

            return new Response('User not found', Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($userToDelete);
        $this->entityManager->flush();

        $this->addFlash('success', 'The user was deleted successfully');

        return $this->redirectToRoute('admin_list_users');
    }

    /**
     * @Route(path="/create", methods={"GET", "POST"}, name="create_user")
     */
    public function createUserAction(Request $request): Response
    {
        $user = new User();

        $form = $this->createForm(UserCreateFormType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->userFormProcessor->processCreateUserFormData($form);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', 'The user was created successfully');

            return $this->redirectToRoute('admin_list_users');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'The user was not created');
        }

        return $this->render('admin/createUser.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
