<?php

declare(strict_types=1);

namespace App\Programme;

use App\Entity\Programme;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ImportProgramme extends Command
{
    private ValidatorInterface $validator;
    private EntityManagerInterface $entityManager;

    public function __construct(
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
    ) {
        $this->validator = $validator;
        $this->entityManager = $entityManager;

        parent::__construct();
    }

//    TODO Figure a way out to implement this in the commands

    protected function import(array $dataProgramme, OutputInterface $output): int
    {

        foreach ($dataProgramme as $programme) {
            ['description' => $description] = $programme;
            ['name' => $name] = $programme;
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

            $violationList = $this->validator->validate($programmeEntity);
            if (count($violationList) > 0) {
                foreach ($violationList as $violation) {
                    echo $violation;
                }
            }

            $this->entityManager->persist($programmeEntity);
            $this->entityManager->flush();
        }
    }
}
