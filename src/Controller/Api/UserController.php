<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\Dto\UserDto;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Traits\ValidatorJsonTrait;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route(path="/api/users")
 */
class UserController implements LoggerAwareInterface
{
    use ValidatorJsonTrait;

    use LoggerAwareTrait;

    private EntityManagerInterface $entityManager;

    private ValidatorInterface $validator;

    private UserPasswordHasherInterface $passwordHasher;

    private UserRepository $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->passwordHasher = $passwordHasher;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route(path="/", methods={"POST"}, name="api_register_user")
     */
    public function register(UserDto $userDto): Response
    {
        $errorsDto = $this->validator->validate($userDto);
        if (count($errorsDto) > 0) {
            return $this->displayErrorsAsJson($errorsDto);
        }

        $user = User::createUserFromDto($userDto);

        $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));

        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            return $this->displayErrorsAsJson($errors);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $userDto = UserDto::createUserFromClass($user);


        $this->logger->info('User registered successfully!', ['name' => "$userDto->firstName $userDto->lastName"]);

        return new JsonResponse($userDto, Response::HTTP_CREATED);
    }

    /**
     * @Route (path="/{id}", methods={"DELETE"}, name="api_delete_user")
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
     * @Route (path="/{id}", methods={"PATCH"}, name="api_recover_user")
     */
    public function recoverUserAction(int $id): Response
    {
        $this->entityManager->getFilters()->disable('softdeleteable');
        $deletedUser = $this->userRepository->findOneBy(['id' => $id]);

        if (null === $deletedUser) {
            return new Response('Invalid user', Response::HTTP_BAD_REQUEST);
        }

        $deletedUser->setDeletedAt(null);
        $this->entityManager->flush();

        return new Response('Account recovered', Response::HTTP_OK);
    }
}
