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

    public function assignRoom(Programme $programme, \DateTime $startDate, \DateTime $endDate)
    {
        $rooms = $this->findAllRooms();

        foreach ($rooms as $room) {
            if ($programme->maxParticipants < $room->capacity) {
                $programme->setRoom($room);

                return;
            }
        }
    }

    public function checkForOccupiedRoom(\DateTime $startDate, \DateTime $endDate)
    {
        $qb = $this->entityManager->createQueryBuilder();
        $occupiedRooms = $qb
            ->select('r.id')
            ->from('App:Programme', 'p')
            ->innerJoin('p.room', 'r')
            ->where('p.startDate <= :startDate AND p.endDate <= :endDate')
            ->orWhere('p.startDate <= :endDate AND p.endDate <= :startDate')
            ->orWhere($qb->expr()->notIn('r.id', 'p.room'))
            ->setParameter(':startDate', $startDate)
            ->setParameter(':endDate', $endDate);

//        $freeRooms = $qb
//            ->select('r.id')
//            ->from()

//        all rooms occupied in a certain period, select where r.id not in

        $query = $occupiedRooms->getQuery();
        $testData = $query->execute();
        var_dump($testData);
    }
}
