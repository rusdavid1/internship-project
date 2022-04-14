<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class SuccessfulLoginSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $analyticsLogger;

    public function __construct(LoggerInterface $analyticsLogger)
    {
        $this->analyticsLogger = $analyticsLogger;
    }

    public static function getSubscribedEvents()
    {
        return [
            LoginSuccessEvent::class => 'addLogsForLogin'
        ];
    }

    public function addLogsForLogin(LoginSuccessEvent $event)
    {
        $loggedInUser = $event->getUser();
//        TODO Account for API or Admin login
        $this->analyticsLogger->info('Successfully logged in', ['email' => $loggedInUser->email]);
    }
}
