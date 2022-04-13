<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Programme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class ProgrammeRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $managerRegistry, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct($managerRegistry, Programme::class);
    }

    public function findByResults(array $parameters)
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

    public function getBusiestHours(): array
    {
        $conn = $this->getEntityManager()->getConnection();

//        $sql = '
//                SELECT programme.id
//                FROM programme
//                LEFT JOIN programmes_customers
//                ON programme.id = programmes_customers.programme_id
//                WHERE
//                   ';
          $sql = 'SELECT HOUR(p.start_date) as hour, COUNT(*)
                    FROM programmes_customers
                    LEFT JOIN programme p on p.id = programmes_customers.programme_id
                    GROUP BY hour';
//        $sql = '
//                SELECT DAY(p.start_date), MONTH(p.start_date), HOUR(p.start_date), COUNT(HOUR(p.start_date)) as reoccuring
//                FROM programme p
//                GROUP BY p.start_date';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }
}
