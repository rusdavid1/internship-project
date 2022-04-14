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

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'addLogsForLogin'
        ];
    }

    public function addLogsForLogin(LoginSuccessEvent $event): void
    {
        $routeAttribute = $event->getRequest()->attributes->get('_route');
        if (null === $routeAttribute) {
            return;
        }
        $loggedInUser = $event->getUser();

        if (strpos($routeAttribute, 'api') === 0) {
            $this->analyticsLogger->info('Successfully logged in', [
                'email' => $loggedInUser->email,
                'login_type' => 'api',
            ]);

            return;
        }

        $this->analyticsLogger->info('Successfully logged in', [
            'email' => $loggedInUser->email,
            'login_type' => 'admin',
        ]);
    }
}
