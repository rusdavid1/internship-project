<?php

declare(strict_types=1);

namespace App\Controller;

use App\Message\SmsNotification;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/api")
 */
class SendMessageController
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    /**
     * @Route(path="/messages")
     */
    public function test()
    {
        $this->messageBus->dispatch(new Envelope(new SmsNotification('Well, hello there')));
        return new Response('Hello');
    }
}