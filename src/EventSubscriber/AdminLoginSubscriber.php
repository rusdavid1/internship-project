<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class AdminLoginSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $analyticsLogger;

    public function __construct(LoggerInterface $analyticsLogger)
    {
        $this->analyticsLogger = $analyticsLogger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'addLogsForSuccessfulLogin',
            LoginFailureEvent::class => 'addLogsForFailedLogin'
        ];
    }

    public function addLogsForSuccessfulLogin(LoginSuccessEvent $event)
    {
        $loggedInUser = $event->getUser();

        $this->analyticsLogger->info('Successfully logged in', [
            'email' => $loggedInUser->email,
            'firewall' => 'admin',
            'result' => 'successful',
            'type' => 'login',
        ]);
    }

    public function addLogsForFailedLogin(LoginFailureEvent $event)
    {
        $failedLoginUserIdentifier = $event->getPassport()->getBadge(UserBadge::class)->getUserIdentifier();

        $this->analyticsLogger->info('Log in failed', [
            'email' => $failedLoginUserIdentifier,
            'firewall' => 'admin',
            'result' => 'failed',
            'type' => 'login',
        ]);
    }
}
