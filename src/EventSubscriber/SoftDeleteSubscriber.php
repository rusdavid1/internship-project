<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Gedmo\SoftDeleteable\SoftDeleteableListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\Event;

class SoftDeleteSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return[
          SoftDeleteableListener::PRE_SOFT_DELETE => 'checkForTrainer'
        ];
    }

    public function checkForTrainer(Event $event)
    {
        var_dump($event);
//        get user

//        check role

//        if trainer check programmes he's assigned to

//        all programmes he's assigned to will get null trainer
    }
}
