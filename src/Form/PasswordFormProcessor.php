<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormInterface;

class PasswordFormProcessor
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function processPasswordForm(FormInterface $form, User $forgottenUser): void
    {
        $plainPassword = $form->get('plainPassword')->getData();
        $this->userRepository->changePassword($forgottenUser, $plainPassword);
    }
}
