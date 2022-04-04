<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\Event;

class SoftDeleteSubscriber
{
    public function test(Event $event)
    {
        var_dump($event);
    }
}