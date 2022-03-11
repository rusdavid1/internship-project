<?php

namespace App\Controller;

use App\Controller\Dto\UserDto;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route(path="/api/user")
 */
class UserController
{
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
        $user = User::createUserFromDto($userDto);

        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            $errorArr = [];
            foreach ($errors as $error) {
                /**
                 * @var ConstraintViolation $error
                 */
                $errorArr = [
                    $error->getPropertyPath() => $error->getMessage()
                ];
                return new JsonResponse($errorArr);
            }
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $userDto = UserDto::createUserFromClass($user);

        return new JsonResponse($userDto, Response::HTTP_CREATED);
    }
}
