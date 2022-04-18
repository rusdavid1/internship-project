<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\User;
<<<<<<< HEAD
use App\Form\UserCreateFormType;
=======
>>>>>>> master
use App\Form\UserUpdateFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
<<<<<<< HEAD
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
=======
>>>>>>> master
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private UserRepository $userRepository;

    private EntityManagerInterface $entityManager;

<<<<<<< HEAD
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
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
=======
    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route(path="/users", methods={"GET"}, name="list_users")
     */
    public function listAllUsers(): Response
    {
        $users = $this->userRepository->findAll();

        return $this->render('admin/listUsers.html.twig', [
            'users' => $users,
>>>>>>> master
        ]);
    }

    /**
     * @Route(path="/users/{id}", methods={"GET", "POST"}, name="update_user")
     */
    public function updateUserAction(int $id, Request $request): Response
    {
<<<<<<< HEAD
        $user = $this->userRepository->findOneBy(['id' => $id]);
        $form = $this->createForm(UserUpdateFormType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
=======
        $user = new User();

        $form = $this->createForm(UserUpdateFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->userRepository->findOneBy(['id' => $id]);

>>>>>>> master
            if (null === $user) {
                return new Response('User not found', Response::HTTP_NOT_FOUND);
            }

<<<<<<< HEAD
            $firstName = $form->get('firstName')->getData();
            $lastName = $form->get('lastName')->getData();
            $email = $form->get('email')->getData();
            $phoneNumber = $form->get('phoneNumber')->getData();

            $user->firstName = $firstName;
            $user->lastName = $lastName;
            $user->email = $email;
            $user->phoneNumber = $phoneNumber;
=======
            $formData = $form->getData();

            $user->firstName = $formData['firstName'];
            $user->lastName = $formData['lastName'];
            $user->email = $formData['email'];
            $user->phoneNumber = $formData['phoneNumber'];
>>>>>>> master

            $this->entityManager->persist($user);
            $this->entityManager->flush();

<<<<<<< HEAD
            $this->addFlash('success', 'The user was edited successfully');

            return $this->redirectToRoute('admin_list_users');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'The user was not edited');
        }

=======
            return $this->redirectToRoute('admin_list_users');
        }

>>>>>>> master
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
<<<<<<< HEAD
=======
            $this->addFlash('error', 'The user was not found');

>>>>>>> master
            return new Response('User not found', Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($userToDelete);
        $this->entityManager->flush();

<<<<<<< HEAD
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
            $user = new User();

            $firstName = $form->get('firstName')->getData();
            $lastName = $form->get('lastName')->getData();
            $email = $form->get('email')->getData();
            $phoneNumber = $form->get('phoneNumber')->getData();
            $cnp = $form->get('cnp')->getData();
            $password = $form->get('password')->getData();

            $user->firstName = $firstName;
            $user->lastName = $lastName;
            $user->email = $email;
            $user->phoneNumber = $phoneNumber;
            $user->cnp = $cnp;
            $user->plainPassword = $password;
            $user->password = $this->passwordHasher->hashPassword($user, $user->plainPassword);

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
=======
        return $this->redirectToRoute('admin_list_users');
    }
>>>>>>> master
}
