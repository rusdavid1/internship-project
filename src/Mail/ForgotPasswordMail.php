<?php

declare(strict_types=1);

namespace App\Mail;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Uid\Uuid;

class ForgotPasswordMail
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendResetPasswordMail(string $emailAddress, Uuid $resetToken)
    {
        $resetPasswordUrl = "http://internship.local/users/reset-password?resetToken=$resetToken";
        //TODO router generate

        $email = (new Email())
            ->from('rusdavid99@gmail.com')
            ->to($emailAddress)
            ->subject('Password Reset')
            ->text("Someone tried to reset you account's password. If you did access this link:")
            ->html("<a href=$resetPasswordUrl>Reset password</a>");


        $this->mailer->send($email);
    }
}
