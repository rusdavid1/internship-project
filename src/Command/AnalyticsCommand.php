<?php

declare(strict_types=1);

namespace App\Command;

use App\Analytics\LoginCollection;
use App\Analytics\ParseAnalyticsLogs;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AnalyticsCommand extends Command
{
    protected static $defaultName = 'app:analytics';
    protected static $defaultDescription = 'Outputs analytics';

    private ParseAnalyticsLogs $loginsPerUser;

    public function __construct(ParseAnalyticsLogs $loginsPerUser)
    {
        $this->loginsPerUser = $loginsPerUser;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $loginAttempts = $this->loginsPerUser->getLoginAttempts();
//        var_dump($loginAttempts->getNumberOfApiLogins());
        var_dump($loginAttempts->getNumberOfAdminLogins());

        $io->success('Programme created successful');

        return Command::SUCCESS;
    }
}
