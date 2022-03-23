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

        $qb = $this->entityManager->createQueryBuilder();
        $occupiedRooms = $qb
            ->select('r.id')
            ->from('App:Programme', 'p')
            ->leftJoin('p.room', 'r')
            ->where('p.startDate <= :startDate AND p.endDate <= :endDate')
            ->orWhere('p.startDate <= :endDate AND p.endDate <= :startDate')
            ->setParameter(':startDate', $startDate)
            ->setParameter(':endDate', $endDate);

//        $freeRooms = $qb
//            ->select('r.id')
//            ->from()

//        all rooms occupied in a certain period, select where r.id not in

        $query = $occupiedRooms->getQuery();
        $testData = $query->execute();
        var_dump($testData);

        foreach ($rooms as $room) {
//            if ($programme->maxParticipants < $room->capacity) {
//                $programme->setRoom($room);
//
//                return;}

            var_dump($room);

            if (!$programme->maxParticipants < $room->capacity && $testData[0]['id'] === $room->getId()) {
                continue;
            }
            $programme->setRoom($room);
        }
    }

    public function checkForOccupiedRoom(\DateTime $startDate, \DateTime $endDate)
    {

//        get room occupied in the period



        $qb = $this->entityManager->createQueryBuilder();
        $occupiedRooms = $qb
            ->select('r.id')
            ->from('App:Programme', 'p')
            ->leftJoin('p.room', 'r')
            ->where('p.startDate <= :startDate AND p.endDate <= :endDate')
            ->orWhere('p.startDate <= :endDate AND p.endDate <= :startDate')
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
