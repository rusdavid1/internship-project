<?php

declare(strict_types=1);

namespace App\Form;

use App\Mailer\Mailer;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Uid\Uuid;

class EmailFormProcessor
{
    private UserRepository $userRepository;

    private Mailer $mailer;

    public function __construct(
        UserRepository $userRepository,
        Mailer $mailer
    ) {
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
    }

    public function processEmailForm(FormInterface $form): void
    {
        $emailAddress = $form->get('email')->getData();

        $resetToken = Uuid::v4();
        $this->userRepository->setUserResetToken($emailAddress, $resetToken);

        $this->mailer->sendResetPasswordMail($emailAddress, $resetToken);
    }
}
