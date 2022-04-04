<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class PasswordFormProcessor
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }


    public function processPasswordForm(FormInterface $form, Request $request, User $forgottenUser)
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            $this->userRepository->changePassword($forgottenUser, $plainPassword);
        }
    }
}
