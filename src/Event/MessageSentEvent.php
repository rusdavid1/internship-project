<?php

declare(strict_types=1);

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class MessageSentEvent extends Event
{
    public const NAME = 'message.sent';
}
