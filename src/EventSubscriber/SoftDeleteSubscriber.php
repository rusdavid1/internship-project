<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Repository\UserRepository;
use Doctrine\Common\EventSubscriber;
use Gedmo\SoftDeleteable\SoftDeleteableListener;
use Symfony\Contracts\EventDispatcher\Event;

class SoftDeleteSubscriber implements EventSubscriber
{
    public $preDeleteInvoked = false;

//    private UserRepository $userRepository;
//
//    public function __construct(UserRepository $userRepository)
//    {
//        $this->userRepository = $userRepository;
//    }

    public function getSubscribedEvents(): array
    {
        return [SoftDeleteableListener::PRE_SOFT_DELETE];
    }

    public function preSoftDelete(): void
    {
        $arr = [1, 2 ,3 , 4];
        foreach ($arr as $i) {
            echo $i;
        }
    }


}
