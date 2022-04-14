<?php

declare(strict_types=1);

namespace App\Command;

use App\Analytics\NumberOfApiLoginsPerUser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AnalyticsCommand extends Command
{
    protected static $defaultName = 'app:analytics';
    protected static $defaultDescription = 'Outputs analytics';

    private NumberOfApiLoginsPerUser $loginsPerUser;

    public function __construct(NumberOfApiLoginsPerUser $loginsPerUser)
    {
        $this->loginsPerUser = $loginsPerUser;

        parent::__construct();
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        var_dump($this->loginsPerUser->test());

        $io->success('Programme created successful');

        return Command::SUCCESS;
    }
}
