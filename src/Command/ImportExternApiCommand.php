<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Programme;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Command\CaesarDecryption;

class ImportExternApiCommand extends Command
{
    private HttpClientInterface $client;
    private ValidatorInterface $validator;
    private EntityManagerInterface $entityManager;
    private ImportProgramme $importProgramme;
    private RoomRepository $roomRepository;

    private string $programmesApiUrl;

    protected static $defaultName = 'app:programme:import-api';
    protected static $defaultDescription = 'This command creates new programme entries from an external API';

    public function __construct(
        HttpClientInterface $client,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
        ImportProgramme $importProgramme,
        RoomRepository $roomRepository,
        string $programmesApiUrl
    ) {
        $this->client = $client;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
        $this->importProgramme = $importProgramme;
        $this->roomRepository = $roomRepository;
        $this->programmesApiUrl = $programmesApiUrl;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $response = $this->client->request(
            'GET',
            $this->programmesApiUrl
        );

        $content = $response->toArray();
        ['data' => $data] = $content;

        foreach ($data as $programme) {
            $programmeEntity = $this->importProgramme->importFromApi($programme);
//            $this->roomRepository->assignRoom($programmeEntity);


            $violationList = $this->validator->validate($programmeEntity);
            if (count($violationList) > 0) {
                foreach ($violationList as $violation) {
                    $io->error($violation);
                }

                return Command::FAILURE;
            }

            $this->entityManager->persist($programmeEntity);
            $this->entityManager->flush();
        }
        $io->success('Programme created successful');

        return Command::SUCCESS;
    }
}
