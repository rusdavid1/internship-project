<?php

declare(strict_types=1);

namespace App\Command;

use _PHPStan_ae8980142\Symfony\Component\Console\Input\InputOption;
use App\Command\Exception\EmptyFileException;
use App\Command\Exception\FileNotFoundException;
use App\Entity\Programme;
use App\Entity\Room;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Command\ImportCsv;

class ImportFromCsvCommand extends Command
{
    private const ISONLINEBOOL = [
        'da' => true,
        'nu' => false
    ];

    protected static $defaultName = 'app:programme:import-csv';

    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;
    private ImportCsv $importCsv;
    private ImportProgramme $importProgramme;
    private RoomRepository $roomRepository;

    private int $programmeMinTime;
    private int $programmeMaxTime;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        ImportCsv $importCsv,
        ImportProgramme $importProgramme,
        RoomRepository $roomRepository,
        string $programmeMinTime,
        string $programmeMaxTime
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->importCsv = $importCsv;
        $this->importProgramme = $importProgramme;
        $this->roomRepository = $roomRepository;
        $this->programmeMaxTime = (int)$programmeMaxTime;
        $this->programmeMinTime = (int)$programmeMinTime;

        parent::__construct();
    }

    protected function configure()
    {
        $this->addOption('file', null, InputOption::VALUE_REQUIRED, 'File path to csv');
        $this->addOption('output-file', null, InputOption::VALUE_REQUIRED, 'File path to csv');
    }

    /**
     * @throws FileNotFoundException
     * @throws EmptyFileException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $csvFilePath = $input->getOption('file');
        $failedCsvFilePath = $input->getOption('output-file');

        if (!filesize($csvFilePath)) {
            throw new EmptyFileException();
        }

        if (!file_exists($csvFilePath)) {
            throw new FileNotFoundException();
        }

        $csvArray = $this->importCsv->getContentFromCsv($csvFilePath, 'r', '|');
        $csvArrayTotal = count($csvArray);

        $invalidCsv = [];
        $programmeCount = 0;

        foreach ($csvArray as $item) {
            $programme = $this->importProgramme->importFromCsv($item);
            $this->roomRepository->assignRoom($programme, $programme->getStartDate(), $programme->getEndDate());

            if (
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

            $programmeCount++;

            $this->entityManager->persist($programme);
            $this->entityManager->flush();
        }

        $this->importCsv->putFailedContentInCsv($failedCsvFilePath, 'w', $invalidCsv);

        $io->success("Successfully imported $programmeCount / $csvArrayTotal programmes");
        return Command::SUCCESS;
    }
}
