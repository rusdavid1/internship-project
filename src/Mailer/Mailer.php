<?php

declare(strict_types=1);

namespace App\Mailer;

use App\Entity\User;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Uid\Uuid;

class Mailer implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private MailerInterface $mailer;

    private RouterInterface $router;

    public function __construct(MailerInterface $mailer, RouterInterface $router)
    {
        $this->mailer = $mailer;
        $this->router = $router;
    }

    public function sendAnnouncementEmail(User $user): void
    {
        $email = (new Email())
            ->from('hello@example.com')
            ->to($user->email)
            ->subject('Announcement regarding your appointment')
            ->text('Sending emails is fun again!')
            ->html("<p>Dear $user->firstName $user->lastName, We have an announcement to make</p>");

        $this->mailer->send($email);

        $this->logger->info('Announcement mail successfully sent', ['to' => $email]);
    }

    public function sendResetPasswordMail(string $emailAddress, Uuid $resetToken)
    {
        $resetPasswordUrl = $this->router->generate('reset_password', [
            'resetToken' => $resetToken
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $email = (new Email())
            ->from('rus.david1234@gmail.com')
            ->to($emailAddress)
            ->subject('Password Reset')
            ->text("Someone tried to reset you account's password. If you did access this link:")
            ->html("<a href=$resetPasswordUrl>Reset password</a>");

        $this->mailer->send($email);

        $this->logger->info('Reset password email successfully sent', ['to' => $emailAddress]);
    }
}
