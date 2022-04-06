<?php

declare(strict_types=1);

namespace App\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Contracts\EventDispatcher\Event;

class SoftDeleteListener
{
    public function test(LifecycleEventArgs $event)
    {
        $test = $event->getObject();

        var_dump($test);
    }
}