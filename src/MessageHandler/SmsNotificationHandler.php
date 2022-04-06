<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\SmsNotification;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SmsNotificationHandler implements MessageHandlerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __invoke(SmsNotification $message): void
    {
        $this->logger->info('Sms sent');
    }
}
