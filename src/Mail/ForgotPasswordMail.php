<?php

declare(strict_types=1);

namespace App\Mail;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Uid\Uuid;

class ForgotPasswordMail implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private MailerInterface $mailer;

    private UrlGeneratorInterface $generator;

    public function __construct(MailerInterface $mailer, UrlGeneratorInterface $generator)
    {
        $this->mailer = $mailer;
        $this->generator = $generator;
    }

    public function sendResetPasswordMail(string $emailAddress, Uuid $resetToken)
    {
        $route = $this->generator->generate('reset_password');
        $resetPasswordUrl = "http://internship.local$route?resetToken=$resetToken";

        $email = (new Email())
            ->from('rusdavid99@gmail.com')
            ->to($emailAddress)
            ->subject('Password Reset')
            ->text("Someone tried to reset you account's password. If you did access this link:")
            ->html("<a href=$resetPasswordUrl>Reset password</a>");

        $this->mailer->send($email);

        $this->logger->info('Reset password email successfully sent', ['to' => $emailAddress]);
    }
}
