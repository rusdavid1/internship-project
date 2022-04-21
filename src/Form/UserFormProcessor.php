<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFormProcessor
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function processCreateUserFormData(FormInterface $form): User
    {
        $user = new User();

        $user->firstName = $form->get('firstName')->getData();
        $user->lastName = $form->get('lastName')->getData();
        $user->email = $form->get('email')->getData();
        $user->phoneNumber = $form->get('phoneNumber')->getData();
        $user->cnp = $form->get('cnp')->getData();
        $user->plainPassword = $form->get('plainPassword')->getData();
        $user->password = $this->passwordHasher->hashPassword($user, $user->plainPassword);
        $user->setRoles(['ROLE_USER']);

        return $user;
    }
}