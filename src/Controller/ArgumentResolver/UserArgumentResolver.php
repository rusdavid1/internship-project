<?php

declare(strict_types=1);

namespace App\Controller\ArgumentResolver;

use App\Controller\Dto\UserDto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class UserArgumentResolver implements ArgumentValueResolverInterface
{
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return $argument->getType() === UserDto::class;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $data = $request->getContent();
        $decodedData = json_decode($data, true);

        $newUser = new UserDto();
        $newUser->cnp = $decodedData['cnp'];
        $newUser->firstName = $decodedData['firstName'];
        $newUser->lastName = $decodedData['lastName'];
        $newUser->email = $decodedData['email'];
        $newUser->phoneNumber = $decodedData['phoneNumber'];
        $newUser->password = $decodedData['password'];
        $newUser->confirmedPassword = $decodedData['confirmedPassword'];

        yield $newUser;
    }
}
