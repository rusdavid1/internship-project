<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Programme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class RoomRepository implements ServiceEntityRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findAllRooms()
    {
        $query = $this->entityManager->createQuery(
            'SELECT r FROM App\Entity\Room r ORDER BY r.id ASC'
        );

        return $query->getResult();
    }

    public function assignRoom(Programme $programme, \DateTime $startDate, \DateTime $endDate): void
    {
        $rooms = $this->findAllRooms();

        $qb = $this->entityManager->createQueryBuilder();
        $occupiedRoomsQuery = $qb
            ->select('r.id')
            ->from('App:Programme', 'p')
            ->leftJoin('p.room', 'r')
            ->where('p.startDate <= :startDate AND p.endDate <= :endDate')
            ->orWhere('p.startDate <= :endDate AND p.endDate <= :startDate')
            ->setParameter(':startDate', $startDate)
            ->setParameter(':endDate', $endDate)
            ->getQuery();

        $occupiedRooms = $occupiedRoomsQuery->execute();
        $test = [];

        foreach ($occupiedRooms as $occupiedRoom) {
            $test[] = implode($occupiedRoom);
        }

        $occupiedRoomsString = implode(', ', $test);

        if ($occupiedRooms) {
            $freeRoomsQuery = $this->entityManager->createQueryBuilder()
                ->select('r.id')
                ->from('App:Room', 'r')
                ->where($qb->expr()->notIn('r.id', $occupiedRoomsString))
                ->getQuery();

            $freeRooms = $freeRoomsQuery->execute();
            $programme->setRoom($rooms[($freeRooms[0]['id']) - 1]);

            return;
        }

        $programme->setRoom($rooms[0]);
    }

    public function checkForOccupiedRoom(\DateTime $startDate, \DateTime $endDate)
    {
        $qb = $this->entityManager->createQueryBuilder();

        $testQuery = $qb
            ->select('r.id')
            ->from('App:Programme', 'p')
            ->join('p.room', 'room')
            ->where('p.startDate <= :startDate AND p.endDate <= :endDate')
            ->orWhere('p.startDate <= :endDate AND p.endDate <= :startDate')
//            ->getDQL();
            ->setParameter(':startDate', $startDate)
            ->setParameter(':endDate', $endDate)
            ->getQuery();


        $occupiedRooms = $this->entityManager->createQueryBuilder()
            ->select('r.id')
            ->from('App:Room', 'r')
            ->where($qb->expr()->notIn('r.id', $testQuery))
            ->setParameter(':startDate', $startDate)
            ->setParameter(':endDate', $endDate)
            ->getQuery();

//        $testData = $occupiedRooms->getResult();
        $testData = $testQuery->execute();
        var_dump($testQuery);
//        var_dump($occupiedRooms);
//        var_dump($testData);

    }
}
