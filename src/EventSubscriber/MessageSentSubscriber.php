<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Event\MessageSentEvent;
use App\Mailer\Mailer;
use App\Message\SmsNotification;
use App\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\EventDispatcher\Event;

class MessageSentSubscriber implements EventSubscriberInterface
{
    private UserRepository $userRepository;

    private MessageBusInterface $messageBus;

    private Mailer $mailerWrapper;

    public function __construct(
        MessageBusInterface $messageBus,
        Mailer $mailerWrapper,
        UserRepository $userRepository
    ) {
        $this->messageBus = $messageBus;
        $this->mailerWrapper = $mailerWrapper;
        $this->userRepository = $userRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MessageSentEvent::class => 'sendMessages',
        ];
    }

    public function sendMessages(Event $event): void
    {
        $users = $this->userRepository->findAll();

        $this->messageBus->dispatch(new Envelope(new SmsNotification('Well, hello there')));
        foreach ($users as $user) {
            $this->mailerWrapper->sendAnnouncementEmail($user);
        }
    }
}
