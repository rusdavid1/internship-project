<?php

declare(strict_types=1);

namespace App\Mailer;

use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class Mailer
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
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
    }
}
