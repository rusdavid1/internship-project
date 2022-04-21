<?php

declare(strict_types=1);

namespace App\Controller\Dto;

use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class UserDto
{
    public int $id;

    public string $firstName;

    public string $lastName;

    public string $email;

    public string $cnp;

    public string $password;

    public string $phoneNumber;

    /**
     * @Assert\IdenticalTo(propertyPath="password", message="Confirmed Password doesn't match with the password")
     */
    public string $confirmedPassword;

    public array $roles;

    public static function createUserFromClass(User $user): self
    {
        $userDto = new self();
        $userDto->id = $user->getId();
        $userDto->firstName = $user->firstName;
        $userDto->lastName = $user->lastName;
        $userDto->email = $user->email;
        $userDto->cnp = $user->cnp;
        $userDto->phoneNumber = $user->phoneNumber;
        $userDto->roles = $user->getRoles();

        return $userDto;
    }
}
