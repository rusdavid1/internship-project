<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserCreateFormType;
use App\Form\UserCreateFormProcessor;
use App\Form\UserUpdateFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class UserController
{
    private UserRepository $userRepository;

    private EntityManagerInterface $entityManager;

    private UserCreateFormProcessor $userFormProcessor;

    private FormFactoryInterface $formFactory;

    private Environment $twig;

    private FlashBagInterface $flashBag;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserCreateFormProcessor $userFormProcessor,
        FormFactoryInterface $formFactory,
        Environment $twig,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->userFormProcessor = $userFormProcessor;
        $this->formFactory = $formFactory;
        $this->twig = $twig;
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @Route(path="/users", methods={"GET"}, name="list_users")
     */
    public function listAllUsers(Request $request): Response
    {
        $page = $request->query->get('page') === null ? '1' : $request->query->get('page');

        $users = $this->userRepository->pagination($page);
        return new Response($this->twig->render('admin/listUsers.html.twig', [
            'users' => $users,
            'currentPage' => $page,
        ]));
    }

    /**
     * @Route(path="/users/{id}", methods={"GET", "POST"}, name="update_user")
     */
    public function updateUserAction(int $id, Request $request): Response
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);
        $form = $this->formFactory->create(UserUpdateFormType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (null === $user) {
                return new Response('User not found', Response::HTTP_NOT_FOUND);
            }

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->flashBag->add('success', 'The user was edited successfully');

            return new RedirectResponse($this->urlGenerator->generate('admin_list_users'));
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->flashBag->add('error', 'The user was not edited');
        }

        return new Response($this->twig->render('admin/updateUser.html.twig', [
            'form' => $form->createView(),
        ]));
    }

    /**
     * @Route(path="/users/delete/{id}", methods={"GET"}, name="delete_user")
     */
    public function deleteUserAction(int $id): Response
    {
        $userToDelete = $this->userRepository->findOneBy(['id' => $id]);

        if (null === $userToDelete) {
            $this->flashBag->add('error', 'The user was not found');

            return new Response('User not found', Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($userToDelete);
        $this->entityManager->flush();

        $this->flashBag->add('success', 'The user was deleted successfully');

        return new RedirectResponse($this->urlGenerator->generate('admin_list_users'));
    }

    /**
     * @Route(path="/create", methods={"GET", "POST"}, name="create_user")
     */
    public function createUserAction(Request $request): Response
    {
        $user = new User();

        $form = $this->formFactory->create(UserCreateFormType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->userFormProcessor->processCreateUserFormData($form);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->flashBag->add('success', 'The user was created successfully');

            return new RedirectResponse($this->urlGenerator->generate('admin_list_users'));
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->flashBag->add('error', 'The user was not created');
        }

        return new Response($this->twig->render('admin/createUser.html.twig', [
            'form' => $form->createView(),
        ]));
    }
}
