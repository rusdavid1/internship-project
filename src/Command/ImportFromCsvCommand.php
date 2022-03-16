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

        while (($data = fgetcsv($handler, null, '|')) !== false) {
            $csvArray[] = $data;
        }

//        var_dump($csvArray);

        $programmeName = '';
        $programmeDescription = '';
        $programmeStartDate = '';
        $programmeEndDate = '';
        $programmeOnline = '';

        foreach ($csvArray as $item) {
            $programmeName = $item[0];
            $programmeDescription = $item[1];
            $programmeStartDate = $item[2];
            $programmeEndDate = $item[3];
            $programmeOnline = $item[4];

            $programme = new Programme();
            $programme->name = $programmeName;
            $programme->description = $programmeDescription;
            $programme->setStartDate(new \DateTime('now'));
            $programme->setEndDate(new \DateTime('+2 hours'));

            $this->entityManager->persist($programme);
            $this->entityManager->flush();
        }

        fclose($handler);

        $io->success('Hooorayyyy');
        return Command::SUCCESS;
    }
}