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

    public function getBookedProgrammesDays(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
                SELECT DISTINCT DAY(p.start_date) AS day
                FROM programmes_customers
                LEFT JOIN programme p on p.id = programmes_customers.programme_id
                ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        return $resultSet->fetchAllAssociative();
    }

    public function getBusiestHours(string $programmeDay): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
                SELECT hour
                FROM
                (SELECT HOUR(p.start_date) as hour, COUNT(pc.user_id) AS participants
                FROM programmes_customers pc
                LEFT JOIN programme p on p.id = pc.programme_id
                WHERE DAY(p.start_date) = :programmeDay
                GROUP BY hour) AS t
                ORDER BY participants DESC
                LIMIT 1
                ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['programmeDay' => $programmeDay]);

        return $resultSet->fetchAllAssociative();
    }
}
