<?php

namespace App\Controller\ArgumentResolver;

use App\Controller\Dto\UserDto;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class UserArgumentResolver implements ArgumentValueResolverInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return $argument->getType() === UserDto::class;
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $data = $request->getContent();
        $decodedData = json_decode($data, true);

        $newUser = new UserDto();
        $newUser->cnp = $decodedData['cnp'];
        $newUser->firstName = $decodedData['firstName'];
        $newUser->lastName = $decodedData['lastName'];
        $newUser->email = $decodedData['email'];
        $newUser->password = $decodedData['password'];
        $newUser->confirmedPassword = $decodedData['confirmedPassword'];

        yield $newUser;
    }
}
