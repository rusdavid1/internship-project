<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class SuccessfulLoginSubscriber implements EventSubscriberInterface
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

    public function addLogsForSuccessfulLogin(LoginSuccessEvent $event): void
    {
        $routeAttribute = $event->getRequest()->attributes->get('_route');
        if (null === $routeAttribute) {
            return;
        }
        $loggedInUser = $event->getUser();

        if (strpos($routeAttribute, 'api') === 0) {
            $this->analyticsLogger->info('Successfully logged in', [
                'email' => $loggedInUser->email,
                'login_from' => 'api',
                'login_type' => 'successful',
            ]);

            return;
        }

        $this->analyticsLogger->info('Successfully logged in', [
            'email' => $loggedInUser->email,
            'login_from' => 'admin',
            'login_type' => 'successful',
        ]);
    }

    public function addLogsForFailedLogin(LoginFailureEvent $event): void
    {
        $routeAttribute = $event->getRequest()->attributes->get('_route');
        if (null === $routeAttribute) {
            return;
        }

        $failedLoginUserIdentifier = $event->getPassport()->getBadge(UserBadge::class)->getUserIdentifier();

        if (strpos($routeAttribute, 'api') === 0) {
            $this->analyticsLogger->info('Log in failed', [
                'email' => $failedLoginUserIdentifier,
                'login_from' => 'api',
                'login_type' => 'failed',
            ]);

            return;
        }

        $this->analyticsLogger->info('Log in failed', [
            'email' => $failedLoginUserIdentifier,
            'login_from' => 'admin',
            'login_type' => 'failed',
        ]);
    }
}
