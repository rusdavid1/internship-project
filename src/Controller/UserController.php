<?php

namespace App\Controller;

use App\Controller\Dto\UserDto;
use App\Entity\User;
use App\Traits\ValidatorTrait;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route(path="/api/users")
 */
class UserController
{
    use ValidatorTrait;

    private EntityManagerInterface $entityManager;

    private ValidatorInterface $validator;

    private UserPasswordHasherInterface $passwordHasher;

    private LoggerInterface $analyticsLogger;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher,
        LoggerInterface $analyticsLogger
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->passwordHasher = $passwordHasher;
        $this->analyticsLogger = $analyticsLogger;
    }

    /**
     * @Route(path="/register", methods={"POST"})
     */
    public function register(UserDto $userDto): Response
    {
        $errorsDto = $this->validator->validate($userDto);
        if (count($errorsDto) > 0) {
            return $this->displayErrors($errorsDto);
        }

        $user = User::createUserFromDto($userDto);
        $user->setPassword($this->passwordHasher->hashPassword($user, $user->plainPassword));

        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            return $this->displayErrors($errors);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $userDto = UserDto::createUserFromClass($user);

        $this->analyticsLogger->info('User registered', ['email' => $userDto->email, 'role' => $userDto->roles[0]]);

        return new JsonResponse($userDto, Response::HTTP_CREATED);
    }
}
