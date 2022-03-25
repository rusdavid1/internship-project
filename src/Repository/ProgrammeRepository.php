<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class ProgrammeRepository implements ServiceEntityRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findBy(array $parameters)
    {
        if (count($parameters) > 1) {
            $parameterValues = [];
            $parameterNames = [];

            foreach ($parameters as $parameter => $parameterValue) {
                $parameterValues[] = $parameterValue;
                $parameterNames[] = $parameter;
                continue;
            }
            $parameterName = $parameterNames[0];
            return $this->$parameterName(...$parameterValues);
        }

        foreach ($parameters as $parameter => $parameterValue) {
            return $this->$parameter($parameterValue);
        }
    }

    public function filterBy(string $name)
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

    public function page(string $page): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $query = $qb
            ->select('p')
            ->from('App:Programme', 'p')
            ->orderBy('p.name')
            ->setFirstResult(((int)$page * 10) - 10)
            ->setMaxResults(10)
            ->getQuery();

        return $query->execute();
    }

    public function sortBy(string $name, string $order): array
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
