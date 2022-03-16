<?php

declare(strict_types=1);

namespace App\Command;

use _PHPStan_ae8980142\Symfony\Component\Console\Input\InputOption;
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

    //TODO Add flag for file path

//    protected function configure()
//    {
//        $this->addOption('file', null, InputOption::VALUE_REQUIRED, '');
//    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        echo $this->programmeMaxTime . PHP_EOL;
        echo $this->programmeMinTime . PHP_EOL;

        $handler = fopen('/home/rdayz/internship-project/ImportFiles/programmes.csv', 'r');

        $csvArray = [];

        fgetcsv($handler);
        while (($data = fgetcsv($handler, null, '|')) !== false) {
            $csvArray[] = $data;
        }

        foreach ($csvArray as $item) {
            //TODO Add validation and CustomException

            $programmeName = $item[0];
            $programmeDescription = $item[1];
            $programmeStartDate = \DateTime::createFromFormat('d.m.Y H:i', $item[2])->format('d.m.Y H:i');
            $programmeEndDate = \DateTime::createFromFormat('d.m.Y H:i', $item[3])->format('d.m.Y H:i');
            $programmeOnline = $item[4];

            $programme = new Programme();
            $programme->name = $programmeName;
            $programme->description = $programmeDescription;
            $programme->setStartDate(new \DateTime($programmeStartDate));
            $programme->setEndDate(new \DateTime($programmeEndDate));

            $this->entityManager->persist($programme);
            $this->entityManager->flush();
        }

        fclose($handler);

        $io->success('Hooorayyyy');
        return Command::SUCCESS;
    }
}