<?php

declare(strict_types=1);

namespace App\Command;

use _PHPStan_ae8980142\Symfony\Component\Console\Input\InputOption;
use App\Command\Exception\EmptyFileException;
use App\Command\Exception\FileNotFoundException;
use App\Entity\Programme;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ImportFromCsvCommand extends Command
{
    protected static $defaultName = 'app:programme:import-csv';

    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;
    private int $programmeMinTime;
    private int $programmeMaxTime;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        string $programmeMinTime,
        string $programmeMaxTime
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->programmeMaxTime = (int)$programmeMaxTime;
        $this->programmeMinTime = (int)$programmeMinTime;

        parent::__construct();
    }

    protected function configure()
    {
        $this->addOption('file', null, InputOption::VALUE_REQUIRED, 'File path to csv');
        $this->addOption('output-file', null, InputOption::VALUE_REQUIRED, 'File path to csv');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $csvFilePath = $input->getOption('file');
        $failedCsvFilePath = $input->getOption('output-file');

        if(!filesize($csvFilePath)) {
            throw new EmptyFileException();
        }

        if(!file_exists($csvFilePath)) {
            throw new FileNotFoundException();
        }

        $csvArray = ImportCsv::getContentFromCsv($csvFilePath, 'r', '|');

        $invalidCsv = [];

        foreach ($csvArray as $item) {
            $programmeName = $item[0];
            $programmeDescription = $item[1];
            $programmeStartDate = \DateTime::createFromFormat('d.m.Y H:i', $item[2])->format('d.m.Y H:i');
            $programmeEndDate = \DateTime::createFromFormat('d.m.Y H:i', $item[3])->format('d.m.Y H:i');
            $programmeOnline = strtolower($item[4]);
//            $programmeMaxParticipants = $item[5];

            if($programmeOnline === 'da') {
                $programmeOnline = true;
            }

            if($programmeOnline === 'nu') {
                $programmeOnline = false;
            }

            $programme = new Programme();
            $programme->name = $programmeName;
            $programme->description = $programmeDescription;
            $programme->isOnline = $programmeOnline;
//            $programme->maxParticipants = $programmeMaxParticipants;
            $programme->setStartDate(new \DateTime($programmeStartDate));
            $programme->setEndDate(new \DateTime($programmeEndDate));

            if(
                $programme->getStartDate()->getTimestamp() > $programme->getEndDate()->getTimestamp() ||
                ($programme->getEndDate()->getTimestamp() - $programme->getStartDate()->getTimestamp()) < 900 ||
                ($programme->getEndDate()->getTimestamp() - $programme->getStartDate()->getTimestamp()) > 21_600
            ) {
                $invalidCsv[] = $item;

                continue;
            }

            $violationList = $this->validator->validate($programme);
            if (count($violationList) > 0) {
                foreach ($violationList as $violation) {
                    $io->error($violation . 'You can find the specific line in failed_programmes.csv');
                }

                $invalidCsv[] = $item;

                continue;
            }

            $this->entityManager->persist($programme);
            $this->entityManager->flush();
        }

        ImportCsv::putFailedContentInCsv($failedCsvFilePath, 'w', $invalidCsv);

        $io->success('Hooorayyyy');
        return Command::SUCCESS;
    }
}
