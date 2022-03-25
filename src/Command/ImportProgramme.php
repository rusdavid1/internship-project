<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Programme;

class ImportProgramme
{
    private const IS_ONLINE_BOOL = [
        'da' => true,
        'nu' => false
    ];

    public function importFromApi(array $programme): Programme
    {
        $name = CaesarDecryption::decipher($programme['name'], 8);
        $description = CaesarDecryption::decipher($programme['description'], 8);
        ['startDate' => $startDate] = $programme;
        ['endDate' => $endDate] = $programme;
        ['isOnline' => $isOnline] = $programme;
        ['maxParticipants' => $maxParticipants] = $programme;

        $programmeEntity = new Programme();
        $programmeEntity->name = $name;
        $programmeEntity->description = $description;
        $programmeEntity->isOnline = $isOnline;
        $programmeEntity->maxParticipants = $maxParticipants;
        $programmeEntity->setStartDate(new \DateTime($startDate));
        $programmeEntity->setEndDate(new \DateTime($endDate));

        return $programmeEntity;
    }

    public function importFromCsv(array $programme): Programme
    {
         $name = $programme[0];
         $description = $programme[1];
         $startDate = $programme[2];
         $endDate = $programme[3];
         $isOnline = self::IS_ONLINE_BOOL[strtolower($programme[4])];
         $maxParticipants = (int)$programme[5];

         $programmeEntity = new Programme();
         $programmeEntity->name = $name;
         $programmeEntity->description = $description;
         $programmeEntity->isOnline = $isOnline;
         $programmeEntity->maxParticipants = $maxParticipants;
         $programmeEntity->setStartDate(new \DateTime($startDate));
         $programmeEntity->setEndDate(new \DateTime($endDate));

         return $programmeEntity;
    }
}
