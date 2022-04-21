<?php

declare(strict_types=1);

namespace App\Import;

use App\Decryptor\CaesarDecryption;
use App\Entity\Programme;
use App\Repository\RoomRepository;

class ImportProgramme
{
    private const IS_ONLINE_BOOL = [
        'da' => true,
        'nu' => false
    ];

    private CaesarDecryption $caesarDecryption;

    private RoomRepository $roomRepository;

    public function __construct(CaesarDecryption $caesarDecryption, RoomRepository $roomRepository)
    {
        $this->caesarDecryption = $caesarDecryption;
        $this->roomRepository = $roomRepository;
    }

    public function importFromApi(array $apiProgramme): Programme
    {
        $programme = new Programme();
        $programme->name = $this->caesarDecryption->decipher($apiProgramme['name'], 8);
        $programme->description = $this->caesarDecryption->decipher($apiProgramme['description'], 8);
        $programme->isOnline = $apiProgramme['isOnline'];
        $programme->maxParticipants = $apiProgramme['maxParticipants'];
        $programme->setStartDate(new \DateTime($apiProgramme['startDate']));
        $programme->setEndDate(new \DateTime($apiProgramme['endDate']));

        $this->roomRepository->assignRoom($programme, $programme->getStartDate(), $programme->getEndDate());

        return $programme;
    }

    public function importFromCsv(array $csvProgramme): Programme
    {
         $programme = new Programme();
         $programme->name = $csvProgramme[0];
         $programme->description = $csvProgramme[1];
         $programme->isOnline = self::IS_ONLINE_BOOL[strtolower($csvProgramme[4])];
         $programme->maxParticipants = (int)$csvProgramme[5];
         $programme->setStartDate(new \DateTime($csvProgramme[2]));
         $programme->setEndDate(new \DateTime($csvProgramme[3]));

        $this->roomRepository->assignRoom($programme, $programme->getStartDate(), $programme->getEndDate());

        return $programme;
    }
}
