<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Programme;
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

    private string $programmesApiUrl;

    protected static $defaultName = 'app:programme:import-api';
    protected static $defaultDescription = 'This command creates new programme entries from an external API';

    public function __construct(
        HttpClientInterface $client,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
        string $programmesApiUrl
    ) {
        $this->client = $client;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
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
            $description = CaesarDecryption::decipher($programme['description'], 8);
            $name = CaesarDecryption::decipher($programme['name'], 8);
            ['startDate' => $startDate] = $programme;
            ['endDate' => $endDate] = $programme;
            ['isOnline' => $isOnline] = $programme;

            $programmeClass = new Programme();
            $programmeClass->name = $name;
            $programmeClass->description = $description;
            $programmeClass->isOnline = $isOnline;
            $programmeClass->setStartDate(new \DateTime($startDate));
            $programmeClass->setEndDate(new \DateTime($endDate));

            $violationList = $this->validator->validate($programmeClass);
            if (count($violationList) > 0) {
                foreach ($violationList as $violation) {
                    $io->error($violation);
                }

                return self::FAILURE;
            }

            $this->entityManager->persist($programmeClass);
            $this->entityManager->flush();
        }
        $io->success('Programme created successful');

        return Command::SUCCESS;
    }
}
