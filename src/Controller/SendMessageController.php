<?php

declare(strict_types=1);

namespace App\Controller;

use App\Message\SmsNotification;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/api/message")
 */
class SendMessageController
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }


    public function test()
    {
        $this->messageBus->dispatch(new SmsNotification('Well, hello there'));
    }
}