<?php

namespace App\Controller;

use App\Controller\Dto\UserDto;
use App\Entity\User;
use App\Traits\ValidatorTrait;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route(path="/api/user")
 */
class UserController implements LoggerAwareInterface
{
    use ValidatorTrait;
    use LoggerAwareTrait;

    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    /**
     * @Route(methods={"POST"})
     */
    public function register(UserDto $userDto): Response
    {
        $errorsDto = $this->validator->validate($userDto);
        if (count($errorsDto) > 0) {
            return $this->displayErrors($errorsDto);
        }

        $user = User::createUserFromDto($userDto);

        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            return $this->displayErrors($errors);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $userDto = UserDto::createUserFromClass($user);

        $this->logger->info('User registered successfully!', ['name' => "$userDto->firstName $userDto->lastName"]);

        return new JsonResponse($userDto, Response::HTTP_CREATED);
    }
}
