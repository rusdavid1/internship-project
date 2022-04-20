<?php

declare(strict_types=1);

namespace App\Import;

use App\Decryptor\CaesarDecryption;
use App\Entity\Programme;

class ImportProgramme
{
    private const IS_ONLINE_BOOL = [
        'da' => true,
        'nu' => false
    ];

    private CaesarDecryption $caesarDecryption;

    public function __construct(CaesarDecryption $caesarDecryption)
    {
        $this->caesarDecryption = $caesarDecryption;
    }

    public function importFromApi(array $programme): Programme
    {
        $programmeEntity = new Programme();
        $programmeEntity->name = $this->caesarDecryption->decipher($programme['name'], 8);
        $programmeEntity->description = $this->caesarDecryption->decipher($programme['description'], 8);
        $programmeEntity->isOnline = $programme['isOnline'];
        $programmeEntity->maxParticipants = $programme['maxParticipants'];
        $programmeEntity->setStartDate(new \DateTime($programme['startDate']));
        $programmeEntity->setEndDate(new \DateTime($programme['endDate']));

        return $programmeEntity;
    }

    public function importFromCsv(array $programme): Programme
    {
         $programmeEntity = new Programme();
         $programmeEntity->name = $programme[0];
         $programmeEntity->description = $programme[1];
         $programmeEntity->isOnline = self::IS_ONLINE_BOOL[strtolower($programme[4])];
         $programmeEntity->maxParticipants = (int)$programme[5];
         $programmeEntity->setStartDate(new \DateTime($programme[2]));
         $programmeEntity->setEndDate(new \DateTime($programme[3]));

         return $programmeEntity;
    }
}
