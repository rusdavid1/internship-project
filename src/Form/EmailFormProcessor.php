<?php

declare(strict_types=1);

namespace App\Form;

use App\Exception\InvalidFromFieldException;
use App\Mail\ForgotPasswordMailer;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Uuid;

class EmailFormProcessor
{
    private UserRepository $userRepository;

    private ForgotPasswordMailer $forgotPasswordMailer;

    public function __construct(UserRepository $userRepository, ForgotPasswordMailer $forgotPasswordMailer)
    {
        $this->userRepository = $userRepository;
        $this->forgotPasswordMailer = $forgotPasswordMailer;
    }


    public function processEmailForm(FormInterface $form, Request $request): void
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && null !== $form->get('email')) {
            $emailAddress = $form->get('email')->getData();

            $resetToken = Uuid::v4();
            $this->userRepository->setUserResetToken($emailAddress, $resetToken);

            $this->forgotPasswordMailer->sendResetPasswordMail($emailAddress, $resetToken);
        }

        throw new InvalidFromFieldException();
    }
}
