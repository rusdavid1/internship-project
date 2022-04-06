<?php

declare(strict_types=1);

namespace App\EventSubscriber;

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
        return [SoftDeleteableListener::PRE_SOFT_DELETE];
    }

    public function preSoftDelete(LifecycleEventArgs $args): void
    {
        $user = $args->getObject();

        if ($user->getRoles()[0] === 'ROLE_TRAINER') {
            $programmes = $this->programmeRepository->findAll();

            $programme = $this->programmeRepository->findOneBy(['trainer' => $user->getId()]);
            $programme->setTrainer(null);
            $this->entityManager->flush();

        }
    }
}
