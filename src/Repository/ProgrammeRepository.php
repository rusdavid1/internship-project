<?php

declare(strict_types=1);

namespace App\Repository;

use App\Repository\Exception\InvalidPageException;
use App\Repository\Exception\ProgrammeNameNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class ProgrammeRepository implements ServiceEntityRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function filterProgrammeByName(string $name): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $query = $qb
            ->select('p')
            ->from('App:Programme', 'p')
            ->where('p.name = :name')
            ->setParameter(':name', $name)
            ->getQuery();

            return $query->execute();
    }

    public function getPaginatedProgrammes(int $page, int $limit): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $query = $qb
            ->select('p')
            ->from('App:Programme', 'p')
            ->orderBy('p.name')
            ->setFirstResult(($page * $limit) - $limit)
            ->setMaxResults($limit)
            ->getQuery();

        return $query->execute();
    }

    public function getSortedProgrammes(string $name, string $order): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $query = $qb
            ->select('p')
            ->from('App:Programme', 'p')
            ->orderBy("p.$name", $order)
            ->getQuery();

        return $query->execute();
    }
}
