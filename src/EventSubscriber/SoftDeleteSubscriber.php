<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User;
use App\Repository\ProgrammeRepository;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Gedmo\SoftDeleteable\SoftDeleteableListener;

class SoftDeleteSubscriber implements EventSubscriberInterface
{
    private ProgrammeRepository $programmeRepository;

    private EntityManagerInterface $entityManager;

    public function __construct(ProgrammeRepository $programmeRepository, EntityManagerInterface $entityManager)
    {
        $this->programmeRepository = $programmeRepository;
        $this->entityManager = $entityManager;
    }


    public function getSubscribedEvents(): array
    {
        return [SoftDeleteableListener::POST_SOFT_DELETE];
    }

    public function postSoftDelete(LifecycleEventArgs $args): void
    {
        $user = $args->getObject();
        $programmes = $this->programmeRepository->findBy(['trainer' => $user->getId()]);

        if (!$user instanceof User || !($user->getRoles()[0] === 'ROLE_TRAINER') || count($programmes) < 1) {
            return;
        }

        foreach ($programmes as $programme) {
            $programme->setTrainer(null);
        }
        $this->entityManager->flush();
    }
}
