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
        $sql = '
                SELECT DISTINCT(t.day), DAYNAME(t.date) as dayName, t.hour, participants
                FROM (SELECT DATE_FORMAT(p.start_date, "%d-%m-%Y")                                                            as day,
                             HOUR(p.start_date)                                                                               as hour,
                             COUNT(pc.user_id)                                                                                as participants,
                             p.start_date as date,
                             RANK() over (PARTITION BY DATE_FORMAT(p.start_date, "%d-%m-%Y") order by COUNT(pc.user_id) desc) as position
                      FROM programme p
                               LEFT JOIN programmes_customers pc on p.id = pc.programme_id
                      GROUP BY day, hour, date) AS t
                WHERE t.position = 1 && t.participants != 0
                ORDER BY participants DESC
                LIMIT 5
                ';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        return $resultSet->fetchAllAssociative();
    }
}
