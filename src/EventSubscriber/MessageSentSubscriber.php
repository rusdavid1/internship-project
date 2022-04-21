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
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MessageSentSubscriber implements EventSubscriberInterface
{
    private UserRepository $userRepository;

    private MessageBusInterface $messageBus;

    private HttpClientInterface $httpClient;

    private Mailer $mailerWrapper;

    public function __construct(
        MessageBusInterface $messageBus,
        Mailer $mailerWrapper,
        UserRepository $userRepository,
        HttpClientInterface $smsClient
    ) {
        $this->messageBus = $messageBus;
        $this->mailerWrapper = $mailerWrapper;
        $this->userRepository = $userRepository;
        $this->httpClient = $smsClient;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MessageSentEvent::class => 'sendMessages',
        ];
    }

    public function sendMessages(): void
    {
        $users = $this->userRepository->findAll();

        $message = 'Well, hello there';

        foreach ($users as $user) {
            $this->messageBus->dispatch(new Envelope(new SmsNotification($message)));
            try {
                $this->httpClient->request(
                    'POST',
                    'messages',
                    ['json' => ['receiver' => $user->phoneNumber,'body' => $message]]
                );
            } catch (TransportExceptionInterface $e) {
                throw new $e();
            }
            $this->mailerWrapper->sendAnnouncementEmail($user);
        }
    }
}
