<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Event\MessageSentEvent;
use App\Mailer\AnnounceMailer;
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

    private AnnounceMailer $announceMailer;

    public function __construct(
        MessageBusInterface $messageBus,
        AnnounceMailer $announceMailer,
        UserRepository $userRepository
    ) {
        $this->messageBus = $messageBus;
        $this->announceMailer = $announceMailer;
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
            $this->announceMailer->sendAnnouncementEmail($user);
        }
    }
}
