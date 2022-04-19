<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Programme;
use App\Exception\NoEmptyRoomsLeftException;
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
        $qb = $this->entityManager->createQueryBuilder();

        return $qb
            ->select('r')
            ->from('App:Room', 'r')
            ->orderBy('r.id', 'asc')
            ->getQuery()
            ->getResult();
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
        $occupiedRoomsToString = [];

        foreach ($occupiedRooms as $occupiedRoom) {
            $occupiedRoomsToString[] = implode($occupiedRoom);
        }

        $occupiedRoomsString = implode(', ', $occupiedRoomsToString);

        if ($occupiedRooms) {
            $freeRoomsQuery = $this->entityManager->createQueryBuilder()
                ->select('r.id')
                ->from('App:Room', 'r')
                ->where($qb->expr()->notIn('r.id', $occupiedRoomsString))
                ->getQuery();

            $freeRooms = $freeRoomsQuery->execute();

            if (!count($freeRooms)) {
                throw new NoEmptyRoomsLeftException();
            }

            try {
                $programme->setRoom($rooms[($freeRooms[0]['id']) - 1]);
            } catch (NoEmptyRoomsLeftException $e) {
                echo $e->getMessage();
            }

            return;
        }

        $programme->setRoom($rooms[0]);
    }
}
