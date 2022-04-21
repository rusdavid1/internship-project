<?php

declare(strict_types=1);

namespace App\Command;

use App\Analytic\AnalyticsLogsParser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AnalyticsCommand extends Command
{
    protected static $defaultName = 'app:analytics';
    protected static $defaultDescription = 'Outputs analytics';

    private AnalyticsLogsParser $analyticsLogsParser;

    public function __construct(AnalyticsLogsParser $analyticsLogsParser)
    {
        $this->analyticsLogsParser = $analyticsLogsParser;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $loginAttempts = $this->analyticsLogsParser->getLoginAttempts();
        $io->section('Total number of api logins');
        var_dump($loginAttempts->getApiLoginsPerUser());
        $io->section('Number of admin logins grouped per day');
        var_dump($loginAttempts->getAdminLoginsPerDay());
        $io->section('Pie chart showing the percentage of roles distributed between new users');
        var_dump($loginAttempts->getNewAccountsPercentage());
        $io->section('Number of failed logins grouped by day showing the users and how many tries there were');
        var_dump($loginAttempts->getFailedLoginsPerDay());

        $io->success('Analyzed successfully');

        return Command::SUCCESS;
    }
}
