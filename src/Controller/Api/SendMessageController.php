<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Event\MessageSentEvent;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/api")
 */
class SendMessageController implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private EventDispatcherInterface $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @Route(path="/messages", name="api_send_messages")
     */
    public function sendMessageAction(): Response
    {
        $event = new MessageSentEvent();
        $this->dispatcher->dispatch($event, MessageSentEvent::class);

        $this->logger->info('The message was sent to all of our users through email and sms');

        return new Response('Messages successfully sent', Response::HTTP_OK);
    }
}
