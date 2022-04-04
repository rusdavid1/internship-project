<?php

declare(strict_types=1);

namespace App\Controller;

use App\Mailer\AnnounceMailer;
use App\Message\SmsNotification;
use App\Repository\UserRepository;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/api")
 */
class SendMessageController implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private MessageBusInterface $messageBus;

    private AnnounceMailer $announceMailer;

    private UserRepository $userRepository;

    public function __construct(
        MessageBusInterface $messageBus,
        AnnounceMailer $announceMailer,
        UserRepository $userRepository
    ) {
        $this->messageBus = $messageBus;
        $this->announceMailer = $announceMailer;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route(path="/messages")
     */
    public function sendSmsAction()
    {
        $users = $this->userRepository->findAll();

        $this->messageBus->dispatch(new Envelope(new SmsNotification('Well, hello there')));
        foreach ($users as $user) {
            $this->announceMailer->sendAnnouncementEmail($user);
        }
        $this->logger->info('The message was sent to all of our users through email and sms');

        return new Response('Messages successfully sent', Response::HTTP_OK);
    }
}
