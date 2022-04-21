<?php

declare(strict_types=1);

namespace App\Command;

use _PHPStan_ae8980142\Symfony\Component\Console\Input\InputOption;
use App\Exception\InvalidFileException;
use App\Exception\FileNotFoundException;
use App\Import\ImportCsv;
use App\Import\ImportProgramme;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ImportFromCsvCommand extends Command
{
    protected static $defaultName = 'app:programme:import-csv';

    private EntityManagerInterface $entityManager;

    private ValidatorInterface $validator;

    private ImportCsv $importCsv;

    private ImportProgramme $importProgramme;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        ImportCsv $importCsv,
        ImportProgramme $importProgramme
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->importCsv = $importCsv;
        $this->importProgramme = $importProgramme;
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

        try {
            $csvArray = $this->importCsv->getContentFromCsv($csvFilePath, 'r', '|');
        } catch (InvalidFileException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        $csvArrayTotal = count($csvArray);

        $invalidCsv = [];
        $programmeCount = 0;

        foreach ($csvArray as $item) {
            $programme = $this->importProgramme->importFromCsv($item);

            if ($this->importCsv->getInvalidCsvLines($programme)) {
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
            $programmeCount++;

            $this->entityManager->persist($programme);
            $this->entityManager->flush();
        }

        try {
            $this->importCsv->putFailedContentInCsv($failedCsvFilePath, 'w', $invalidCsv);
        } catch (InvalidFileException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        $io->success("Successfully imported $programmeCount / $csvArrayTotal programmes");

        return Command::SUCCESS;
    }
}
