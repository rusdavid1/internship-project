<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Programme;
use App\Entity\Room;
use App\Exception\NoEmptyRoomsLeftException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Room::class);
    }

    public function getOccupiedRooms(\DateTime $startDate, \DateTime $endDate): array
    {
        $qb = $this->_em->createQueryBuilder();
        $occupiedRoomsQuery = $qb
            ->select('r.id')
            ->from('App:Programme', 'p')
            ->leftJoin('p.room', 'r')
            ->where('p.startDate = :startDate AND p.endDate = :endDate')
            ->setParameter(':startDate', $startDate)
            ->setParameter(':endDate', $endDate)
            ->getQuery();

        return $occupiedRoomsQuery->execute();
    }

    public function assignRoom(Programme $programme, \DateTime $startDate, \DateTime $endDate): void
    {
        $occupiedRooms = $this->getOccupiedRooms($startDate, $endDate);

        if (empty($occupiedRooms)) {
            $programme->setRoom($this->findOneBy(['id' => 13]));

            return;
        }

        $occupiedRoomsToString = [];
        foreach ($occupiedRooms as $occupiedRoom) {
            $occupiedRoomsToString[] = implode($occupiedRoom);
        }

        $qb = $this->_em->createQueryBuilder();
        $freeRoomsQuery = $this->_em->createQueryBuilder()
            ->select('r.id')
            ->from('App:Room', 'r')
            ->where($qb->expr()->notIn('r.id', implode(', ', $occupiedRoomsToString)))
            ->getQuery();

        $freeRoomsId = $freeRoomsQuery->execute();

        if (count($freeRoomsId) < 1) {
            throw new NoEmptyRoomsLeftException();
        }
        $programme->setRoom($this->findOneBy(['id' => $freeRoomsId[0]['id']]));
    }
}
