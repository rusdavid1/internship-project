<?php

declare(strict_types=1);

namespace App\Form;

use App\Mail\ForgotPasswordMail;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Uuid;

class ForgotPasswordForm extends AbstractType
{
    private UserRepository $userRepository;

    private ForgotPasswordMail $forgotPasswordMail;

    public function __construct(UserRepository $userRepository, ForgotPasswordMail $forgotPasswordMail)
    {
        $this->userRepository = $userRepository;
        $this->forgotPasswordMail = $forgotPasswordMail;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
           ->add('email', EmailType::class);
    }

    public function processEmailForm(FormInterface $form, Request $request): void
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $emailAddress = $form->get('email')->getData();

            $resetToken = Uuid::v4();
            $this->userRepository->setUserResetToken($emailAddress, $resetToken);

            $this->forgotPasswordMail->sendResetPasswordMail($emailAddress, $resetToken);
        }
    }
}
